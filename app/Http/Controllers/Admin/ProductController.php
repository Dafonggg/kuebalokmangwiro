<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_url'] = $request->file('photo')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($product->photo_url) {
                Storage::disk('public')->delete($product->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->photo_url) {
            Storage::disk('public')->delete($product->photo_url);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function stock()
    {
        $categories = Category::where('is_active', true)
            ->with('products')
            ->orderBy('name')
            ->get();
        
        return view('admin.products.stock', compact('categories'));
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
        ]);

        foreach ($request->stocks as $categoryId => $stock) {
            Category::where('id', $categoryId)->update(['stock' => $stock]);
        }

        return redirect()->route('admin.products.stock')
            ->with('success', 'Stok berhasil diperbarui.');
    }
}
