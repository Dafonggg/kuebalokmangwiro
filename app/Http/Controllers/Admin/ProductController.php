<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'is_active' => 'boolean',
        ], [
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, jpg, png, gif, atau webp.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Ensure is_active is always set (default to false if not provided)
        $validated['is_active'] = $request->has('is_active') && $request->is_active == '1';

        // Handle photo upload with error handling
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                
                // Validate file is valid
                if (!$file->isValid()) {
                    return back()->withInput()
                        ->withErrors(['photo' => 'File foto tidak valid.']);
                }

                // Store the file
                $photoPath = $file->store('products', 'public');
                
                if ($photoPath) {
                    // Verify file actually exists
                    $fullPath = storage_path('app/public/' . $photoPath);
                    $fileExists = file_exists($fullPath);
                    $storageExists = Storage::disk('public')->exists($photoPath);
                    
                    if (!$fileExists || !$storageExists) {
                        Log::error('Photo file not found after upload', [
                            'path' => $photoPath,
                            'full_path' => $fullPath,
                            'file_exists' => $fileExists,
                            'storage_exists' => $storageExists,
                        ]);
                        return back()->withInput()
                            ->withErrors(['photo' => 'Gagal menyimpan foto. File tidak ditemukan setelah upload.']);
                    }
                    
                    $validated['photo_url'] = $photoPath;
                    Log::info('Photo uploaded successfully', [
                        'path' => $photoPath,
                        'full_path' => $fullPath,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'file_exists' => $fileExists,
                        'storage_exists' => $storageExists,
                    ]);
                } else {
                    Log::error('Failed to store photo', [
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                    return back()->withInput()
                        ->withErrors(['photo' => 'Gagal menyimpan foto. Silakan coba lagi.']);
        }
            } catch (\Exception $e) {
                Log::error('Error uploading photo', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->withInput()
                    ->withErrors(['photo' => 'Terjadi kesalahan saat mengunggah foto: ' . $e->getMessage()]);
            }
        }

        try {
            // Log validated data before creating product
            Log::info('Creating product with data', [
                'validated_data' => $validated,
                'has_photo_url' => isset($validated['photo_url']),
                'photo_url' => $validated['photo_url'] ?? null,
            ]);
            
            $product = Product::create($validated);
            
            // Log after creation to verify photo_url was saved
            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'photo_url' => $product->photo_url,
            ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            // If product creation fails, delete uploaded photo
            if (isset($validated['photo_url'])) {
                try {
                    Storage::disk('public')->delete($validated['photo_url']);
                } catch (\Exception $deleteException) {
                    Log::error('Failed to delete photo after product creation failure', [
                        'photo_path' => $validated['photo_url'],
                        'error' => $deleteException->getMessage(),
                    ]);
                }
            }
            
            Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage()]);
        }
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
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'is_active' => 'boolean',
        ], [
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, jpg, png, gif, atau webp.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Ensure is_active is always set (default to false if not provided)
        $validated['is_active'] = $request->has('is_active') && $request->is_active == '1';

        // Handle photo upload with error handling
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                
                // Validate file is valid
                if (!$file->isValid()) {
                    return back()->withInput()
                        ->withErrors(['photo' => 'File foto tidak valid.']);
                }

                // Store the new file first
                $photoPath = $file->store('products', 'public');
                
                if ($photoPath) {
                    // Only delete old photo if new photo is successfully stored
            if ($product->photo_url) {
                        try {
                Storage::disk('public')->delete($product->photo_url);
                            Log::info('Old photo deleted', ['path' => $product->photo_url]);
                        } catch (\Exception $deleteException) {
                            Log::warning('Failed to delete old photo', [
                                'photo_path' => $product->photo_url,
                                'error' => $deleteException->getMessage(),
                            ]);
                            // Continue even if old photo deletion fails
                        }
                    }
                    
                    $validated['photo_url'] = $photoPath;
                    Log::info('Photo updated successfully', [
                        'product_id' => $product->id,
                        'new_path' => $photoPath,
                        'old_path' => $product->photo_url,
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                    ]);
                } else {
                    Log::error('Failed to store new photo', [
                        'product_id' => $product->id,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                    return back()->withInput()
                        ->withErrors(['photo' => 'Gagal menyimpan foto. Silakan coba lagi.']);
                }
            } catch (\Exception $e) {
                Log::error('Error uploading photo', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->withInput()
                    ->withErrors(['photo' => 'Terjadi kesalahan saat mengunggah foto: ' . $e->getMessage()]);
            }
        }

        try {
            // Log validated data before updating product
            Log::info('Updating product with data', [
                'product_id' => $product->id,
                'validated_data' => $validated,
                'has_photo_url' => isset($validated['photo_url']),
                'photo_url' => $validated['photo_url'] ?? null,
            ]);

        $product->update($validated);
            
            // Log after update to verify photo_url was saved
            Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'photo_url' => $product->photo_url,
            ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            // If product update fails and we uploaded a new photo, delete it
            if (isset($validated['photo_url']) && $validated['photo_url'] !== $product->photo_url) {
                try {
                    Storage::disk('public')->delete($validated['photo_url']);
                } catch (\Exception $deleteException) {
                    Log::error('Failed to delete photo after product update failure', [
                        'photo_path' => $validated['photo_url'],
                        'error' => $deleteException->getMessage(),
                    ]);
                }
            }
            
            Log::error('Error updating product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage()]);
        }
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
