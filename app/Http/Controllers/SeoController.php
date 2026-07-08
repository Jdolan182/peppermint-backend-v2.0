<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Page;
use App\Support\Modules;

class SeoController extends Controller
{
    public function sitemap()
    {
        $base = config('peppermint.frontend_url');
        $urls = [];

        if (Modules::enabled('pages')) {
            foreach (Page::where('is_published', true)->get() as $page) {
                $urls[] = [
                    'loc'     => $page->is_home ? $base . '/' : $base . '/' . $page->slug,
                    'lastmod' => $page->updated_at?->toDateString(),
                ];
            }
        }

        if (Modules::enabled('blogs')) {
            $blogs = Blog::whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->get();

            foreach ($blogs as $blog) {
                $urls[] = [
                    'loc'     => $base . '/blogs/' . $blog->slug,
                    'lastmod' => $blog->updated_at?->toDateString(),
                ];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_QUOTES, 'UTF-8') . "</loc>\n";
            if ($url['lastmod']) {
                $xml .= '    <lastmod>' . $url['lastmod'] . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /' . config('peppermint.admin_slug') . '/',
            '',
            'Sitemap: ' . config('peppermint.frontend_url') . '/sitemap.xml',
        ];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
