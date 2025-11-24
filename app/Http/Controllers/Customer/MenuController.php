<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();
        $categories = Category::where('is_active', true)->get();

        return view('customer.menu.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        return view('customer.menu.show', compact('product'));
    }
}
