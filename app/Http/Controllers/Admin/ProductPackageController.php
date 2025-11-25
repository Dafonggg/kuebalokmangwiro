<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPackage;
use App\Models\ProductPackageItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = ProductPackage::with('items.product')->latest()->get();
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('is_active', true)->with('category')->get();
        return view('admin.packages.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active') && $request->is_active == '1';

        if ($request->hasFile('photo')) {
            $validated['photo_url'] = $request->file('photo')->store('packages', 'public');
        }

        $package = ProductPackage::create($validated);

        // Create package items
        foreach ($request->items as $item) {
            ProductPackageItem::create([
                'package_id' => $package->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPackage $package)
    {
        $package->load('items.product');
        return view('admin.packages.show', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPackage $package)
    {
        $package->load('items.product');
        $products = Product::where('is_active', true)->with('category')->get();
        return view('admin.packages.edit', compact('package', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active') && $request->is_active == '1';

        if ($request->hasFile('photo')) {
            if ($package->photo_url) {
                Storage::disk('public')->delete($package->photo_url);
            }
            $validated['photo_url'] = $request->file('photo')->store('packages', 'public');
        }

        $package->update($validated);

        // Delete existing items
        $package->items()->delete();

        // Create new items
        foreach ($request->items as $item) {
            ProductPackageItem::create([
                'package_id' => $package->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPackage $package)
    {
        if ($package->photo_url) {
            Storage::disk('public')->delete($package->photo_url);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }
}
