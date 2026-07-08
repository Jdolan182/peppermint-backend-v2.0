<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Page;
use App\Models\Setting;
use App\Support\Modules;
use Illuminate\Http\Request;

/**
 * Serves the built Vue SPA with the page's meta/OG tags injected
 * server-side, so crawlers and link-preview scrapers (which don't run
 * JS) see real titles and descriptions. The SPA hydrates over it.
 */
class SpaController extends Controller
{
    public function __invoke(Request $request)
    {
        $indexPath = config('peppermint.spa_index_path');

        // No build configured (e.g. local dev, where Vite serves the
        // frontend) — keep the old behaviour.
        if (!$indexPath || !is_file($indexPath)) {
            return view('welcome');
        }

        $html = file_get_contents($indexPath);

        [$title, $description] = $this->resolveMeta($request->path());

        return response($this->inject($html, $title, $description, $request->url()))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * @return array{0: string, 1: string} [title, description]
     */
    protected function resolveMeta(string $path): array
    {
        $siteName = Setting::get('site_name', 'Peppermint');
        $path = trim($path, '/');

        if (str_starts_with($path, 'blogs/') && Modules::enabled('blogs')) {
            $blog = Blog::where('slug', substr($path, 6))
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->first();

            if ($blog) {
                return [$blog->title . ' | ' . $siteName, $blog->excerpt ?? ''];
            }
        }

        if (Modules::enabled('pages') && !str_contains($path, '/')) {
            $page = $path === ''
                ? Page::where('is_home', true)->where('is_published', true)->first()
                : Page::where('slug', $path)->where('is_published', true)->first();

            if ($page) {
                return [
                    $page->meta_title ?: ($page->is_home ? $siteName : $page->title . ' | ' . $siteName),
                    $page->meta_description ?? '',
                ];
            }
        }

        return [$siteName, ''];
    }

    protected function inject(string $html, string $title, string $description, string $url): string
    {
        $e = fn ($v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        $tags = '<title>' . $e($title) . '</title>' . "\n";
        $tags .= '<meta property="og:title" content="' . $e($title) . '">' . "\n";
        $tags .= '<meta property="og:type" content="website">' . "\n";
        $tags .= '<meta property="og:url" content="' . $e($url) . '">' . "\n";

        if ($description !== '') {
            $tags .= '<meta name="description" content="' . $e($description) . '">' . "\n";
            $tags .= '<meta property="og:description" content="' . $e($description) . '">' . "\n";
        }

        // Drop the build-time <title> and inject ours at the end of <head>
        $html = preg_replace('/<title>.*?<\/title>/s', '', $html, 1);

        return str_replace('</head>', $tags . '</head>', $html);
    }
}
