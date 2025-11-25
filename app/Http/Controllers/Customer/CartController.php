<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPackage;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $key => $item) {
            if (isset($item['item_type']) && $item['item_type'] === 'package') {
                $package = ProductPackage::with('items.product')->find($item['id']);
                if ($package && $package->is_active) {
                    $item['package'] = $package;
                    $item['subtotal'] = $package->price * $item['quantity'];
                    $total += $item['subtotal'];
                    $items[$key] = $item;
                }
            } else {
                // Legacy support for old cart format (product only)
                $productId = is_numeric($key) ? $key : ($item['id'] ?? null);
                $product = Product::find($productId);
                if ($product && $product->is_active) {
                    $item['product'] = $product;
                    $item['item_type'] = 'product';
                    $item['id'] = $productId;
                    $item['subtotal'] = $product->price * $item['quantity'];
                    $total += $item['subtotal'];
                    $items[$key] = $item;
                }
            }
        }

        return view('customer.cart.index', compact('items', 'total'));
    }

    public function addPackage(Request $request, ProductPackage $package)
    {
        if (!$package->is_active) {
            $message = 'Paket tidak tersedia.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'cart_count' => $this->getCartCount()
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $cart = session('cart', []);
        $quantity = $request->input('quantity', 1);
        $cartKey = 'package_' . $package->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'item_type' => 'package',
                'id' => $package->id,
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);
        $cartCount = $this->getCartCount();

        $message = $package->name . ' ditambahkan ke keranjang.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $cartCount
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function add(Request $request, Product $product)
    {
        if (!$product->is_active) {
            $message = 'Product tidak tersedia.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'cart_count' => array_sum(array_column(session('cart', []), 'quantity'))
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $product->load('category');
        $category = $product->category;
        
        if (!$category || !$category->is_active) {
            $message = 'Kategori produk tidak aktif.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'cart_count' => array_sum(array_column(session('cart', []), 'quantity'))
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        if ($category->stock <= 0) {
            $message = 'Produk sedang tidak tersedia (stok habis).';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'cart_count' => array_sum(array_column(session('cart', []), 'quantity'))
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $cart = session('cart', []);
        $quantity = $request->input('quantity', 1);
        $cartKey = 'product_' . $product->id;
        
        // Calculate total quantity for this category in cart
        $categoryTotalQuantity = 0;
        foreach ($cart as $key => $cartItem) {
            if (isset($cartItem['item_type']) && $cartItem['item_type'] === 'package') {
                // For packages, check each item in the package
                $package = ProductPackage::with('items.product')->find($cartItem['id']);
                if ($package) {
                    foreach ($package->items as $packageItem) {
                        if ($packageItem->product->category_id === $category->id) {
                            $categoryTotalQuantity += $cartItem['quantity'] * $packageItem->qty;
                        }
                    }
                }
            } else {
                // Legacy or product items
                $cartProductId = is_numeric($key) ? $key : ($cartItem['id'] ?? null);
                $cartProduct = Product::with('category')->find($cartProductId);
                if ($cartProduct && $cartProduct->category_id === $category->id) {
                    $categoryTotalQuantity += $cartItem['quantity'];
                }
            }
        }
        
        $currentQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newQuantity = $currentQuantity + $quantity;
        $newCategoryTotal = $categoryTotalQuantity - $currentQuantity + $newQuantity;

        if ($newCategoryTotal > $category->stock) {
            $message = 'Stok tidak cukup untuk kategori ' . $category->name . '. Stok tersedia: ' . $category->stock;
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'cart_count' => $this->getCartCount()
                ], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'item_type' => 'product',
                'id' => $product->id,
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);
        $cartCount = $this->getCartCount();

        $message = $product->name . ' ditambahkan ke keranjang.';
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $cartCount
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function update(Request $request, $key)
    {
        $cart = session('cart', []);
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            unset($cart[$key]);
        } else {
            if (isset($cart[$key])) {
                $cart[$key]['quantity'] = $quantity;
            }
        }

        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Keranjang diperbarui.');
    }

    public function remove($key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);

        return redirect()->route('cart.index')->with('success', 'Item dihapus dari keranjang.');
    }

    private function getCartCount()
    {
        $cart = session('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}
