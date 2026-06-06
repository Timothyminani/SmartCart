<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
     public function index()
    {
        $categories = Category::latest()->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', 'Category created');
    }





public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => 'required'
    ]);

    $category->update([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
    ]);

    return back()->with('success', 'Category updated');
}



public function destroy(Category $category)
{
    $category->delete();

    return back()->with('success', 'Category deleted');
}



}
