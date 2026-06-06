<?php

namespace App\Http\Controllers\Admin;
use App\Models\Brand;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
     public function index()
    {
        $brands = Brand::latest()->get();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands
        ]);
    }



   public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('brands', 'public');
    }

    Brand::create([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
        'logo' => $logoPath,
    ]);

    return back()->with('success', 'Brand created successfully!');
}



public function update(Request $request, Brand $brand)
{
    $request->validate([
        'name' => 'required',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($request->hasFile('logo')) {
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->logo = $request->file('logo')->store('brands', 'public');
    }

    $brand->update([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
    ]);

    return back()->with('success', 'Brand updated successfully!');
}



public function destroy(Brand $brand)
{
    // Delete logo if exists
    if ($brand->logo) {
        Storage::disk('public')->delete($brand->logo);
    }

    // Delete brand
    $brand->delete();

    return back()->with('success', 'Brand deleted successfully!');
}






}
