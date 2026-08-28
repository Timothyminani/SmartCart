<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $categories = Category::latest()->get();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload Image
        |--------------------------------------------------------------------------
        */

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('categories', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Create Category
        |--------------------------------------------------------------------------
        */

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
        ]);


        return back()->with(
            'success',
            'Category created successfully'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Category $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $category->id,
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Keep Existing Image
        |--------------------------------------------------------------------------
        */

        $imagePath = $category->image;


        /*
        |--------------------------------------------------------------------------
        | Replace Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            if (
                $category->image &&
                Storage::disk('public')->exists($category->image)
            ) {
                Storage::disk('public')->delete($category->image);
            }

            $imagePath = $request
                ->file('image')
                ->store('categories', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Update Category
        |--------------------------------------------------------------------------
        */

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'image' => $imagePath,
            'is_featured' => $request->boolean('is_featured'),
        ]);


        return back()->with(
            'success',
            'Category updated successfully'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(Category $category)
    {
        /*
        |--------------------------------------------------------------------------
        | Delete Category Image
        |--------------------------------------------------------------------------
        */

        if (
            $category->image &&
            Storage::disk('public')->exists($category->image)
        ) {
            Storage::disk('public')->delete($category->image);
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Category
        |--------------------------------------------------------------------------
        */

        $category->delete();


        return back()->with(
            'success',
            'Category deleted successfully'
        );
    }
}