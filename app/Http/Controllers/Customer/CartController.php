<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->is_active) {
                $item['product'] = $product;
                $item['subtotal'] = $product->price * $item['quantity'];
                $total += $item['subtotal'];
                $items[$productId] = $item;
            }
        }

        return view('customer.cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        if (!$product->is_active) {
            return redirect()->back()->with('error', 'Product tidak tersedia.');
        }

        $product->load('category');
        $category = $product->category;
        
        if (!$category || !$category->is_active) {
            return redirect()->back()->with('error', 'Kategori produk tidak aktif.');
        }

        if ($category->stock <= 0) {
            return redirect()->back()->with('error', 'Produk sedang tidak tersedia (stok habis).');
        }

        $cart = session('cart', []);
        $quantity = $request->input('quantity', 1);
        
        // Calculate total quantity for this category in cart
        $categoryTotalQuantity = 0;
        foreach ($cart as $cartProductId => $cartItem) {
            $cartProduct = Product::with('category')->find($cartProductId);
            if ($cartProduct && $cartProduct->category_id === $category->id) {
                $categoryTotalQuantity += $cartItem['quantity'];
            }
        }
        
        $currentQuantity = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        $newQuantity = $currentQuantity + $quantity;
        $newCategoryTotal = $categoryTotalQuantity - $currentQuantity + $newQuantity;

        if ($newCategoryTotal > $category->stock) {
            return redirect()->back()->with('error', 'Stok tidak cukup untuk kategori ' . $category->name . '. Stok tersedia: ' . $category->stock);
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, $productId)
    {
        $cart = session('cart', []);
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Keranjang diperbarui.');
    }

    public function remove($productId)
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }
}
