<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->paginate($request->integer('per_page', 50));

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
        ]);

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
        ]);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
