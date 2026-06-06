<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use Illuminate\Support\Str;


class ProductController extends Controller
{

   public function index(Request $request)
{
    $query = Product::with(['category', 'brand', 'images']);

    // 🔍 Search
    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // 📂 Filter by category
    if ($request->category) {
        $query->where('category_id', $request->category);
    }

    // 📄 Pagination
    $products = $query->latest()->paginate(10)->withQueryString();

    return Inertia::render('Admin/Products/Index', [
        'products' => $products,
        'filters' => $request->only(['search', 'category']),
        'categories' => \App\Models\Category::all()
    ]);
}





    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all(),
            'brands' => Brand::all()
        ]);
    }




  public function store(Request $request)
{
    
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:products,slug',
        'description' => 'nullable',
        'price' => 'required|numeric',
        'sale_price' => 'nullable|numeric',
        'stock_quantity' => 'required|integer',
        'category_id' => 'required',
        'brand_id' => 'nullable',

        'images.*' => 'image|mimes:jpg,png,jpeg|max:2048',

        'attributes.*.name' => 'nullable|string',
        'attributes.*.value' => 'nullable|string',
    ]);

    
    // ✅ CREATE PRODUCT
    $product = Product::create([
        'name' => $request->name,
        'slug' => Str::slug($request->slug),
        'description' => $request->description,
        'price' => $request->price,
        'sale_price' => $request->sale_price,
        'stock_quantity' => $request->stock_quantity,
        'category_id' => $request->category_id,
        'brand_id' => $request->brand_id,
        'is_active' => $request->is_active,
        'is_featured' => $request->is_featured,
    ]);

    // ✅ SAVE IMAGES
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $img) {
            $path = $img->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }
    }

    // ✅ SAVE ATTRIBUTES
    if ($request->has('attributes')) {
    foreach ($request->input('attributes') as $attr) {

        if (!empty($attr['name']) && !empty($attr['value'])) {
            ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_name' => $attr['name'],
                'attribute_value' => $attr['value'],
            ]);
        }

    }
}


 return redirect()->route('admin.products.index')
        ->with('success', 'Product created successfully!');
   
}





public function edit(Product $product)
{
    $product->load(['images', 'attributes']);

    return Inertia::render('Admin/Products/Create', [
        'product' => $product,
        'categories' => Category::all(),
        'brands' => Brand::all()
    ]);
}



public function update(Request $request, Product $product)
{


    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:products,slug,' . $product->id,
        'description' => 'nullable',
        'price' => 'required|numeric',
        'sale_price' => 'nullable|numeric',
        'stock_quantity' => 'required|integer',
        'category_id' => 'required',
        'brand_id' => 'nullable',

        'images.*' => 'image|mimes:jpg,png,jpeg|max:2048',

        'attributes.*.name' => 'nullable|string',
        'attributes.*.value' => 'nullable|string',
    ]);

    // ✅ UPDATE PRODUCT
    $product->update([
        'name' => $request->name,
        'slug' => Str::slug($request->slug),
        'description' => $request->description,
        'price' => $request->price,
        'sale_price' => $request->sale_price,
        'stock_quantity' => $request->stock_quantity,
        'category_id' => $request->category_id,
        'brand_id' => $request->brand_id,
        'is_active' => $request->is_active,
        'is_featured' => $request->is_featured,
    ]);

    // ✅ ADD NEW IMAGES (optional)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $img) {
            $path = $img->store('products', 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }
    }

    // ✅ UPDATE ATTRIBUTES (IMPORTANT 🔥)
   // Update attributes safely
$product->attributes()->delete();

foreach ($request->input('attributes', []) as $attr) {
    if (!empty($attr['name']) && !empty($attr['value'])) {
        ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_name' => $attr['name'],
            'attribute_value' => $attr['value'],
        ]);
    }
}

    return redirect()->route('admin.products.index')
        ->with('success', 'Product updated successfully!');
}



public function destroy(Product $product)
{
    // Delete images from storage (optional but recommended)
    foreach ($product->images as $image) {
        \Storage::disk('public')->delete($image->image_path);
    }

    // Delete related data
    $product->images()->delete();
    $product->attributes()->delete();

    // Delete product
    $product->delete();

    return redirect()->back()->with('success', 'Product deleted successfully!');
}



}
