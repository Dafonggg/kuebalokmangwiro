<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\OrderCodeGenerator;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->with(['activeProducts'])->get();
        $products = Product::active()->with('category')->get();
        
        return view('admin.cashier.index', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_type' => 'required|in:dine-in,takeaway,pickup',
            'table_number' => 'nullable|string|max:10',
            'payment_method' => 'required|in:cash,qris,bank_transfer,manual',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalAmount = 0;
        $orderItems = [];
        $categoryQuantities = []; // Track quantities per category

        foreach ($request->items as $item) {
            if (!isset($item['product_id']) || !isset($item['quantity'])) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            if ($quantity <= 0) {
                continue;
            }

            $product = Product::with('category')->find($item['product_id']);
            if (!$product || !$product->is_active) {
                continue;
            }

            $category = $product->category;
            if (!$category || !$category->is_active) {
                continue;
            }

            // Track quantity per category
            if (!isset($categoryQuantities[$category->id])) {
                $categoryQuantities[$category->id] = 0;
            }
            $categoryQuantities[$category->id] += $quantity;

            // Check stock availability for category
            if ($category->stock < $categoryQuantities[$category->id]) {
                return redirect()->back()->with('error', 'Stok tidak cukup untuk kategori: ' . $category->name . '. Stok tersedia: ' . $category->stock);
            }

            $price = $product->price;
            $subtotal = $price * $quantity;
            $totalAmount += $subtotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($orderItems)) {
            return redirect()->back()->with('error', 'Pilih minimal satu produk dengan quantity lebih dari 0.');
        }

        $order = Order::create([
            'order_code' => OrderCodeGenerator::generate(),
            'customer_name' => $request->customer_name,
            'order_type' => $request->order_type,
            'table_number' => $request->order_type === 'dine-in' ? $request->table_number : null,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'total_amount' => $totalAmount,
            'payment_status' => 'paid',
            'order_status' => 'pending',
        ]);

        // Decrease stock per category
        foreach ($categoryQuantities as $categoryId => $totalQuantity) {
            $category = Category::find($categoryId);
            if ($category) {
                $category->decrement('stock', $totalQuantity);
            }
        }

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        // Create payment record
        Payment::create([
            'order_id' => $order->id,
            'amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('admin.cashier.receipt', $order)
            ->with('success', 'Pesanan berhasil dibuat! Kode: ' . $order->order_code);
    }

    public function showReceipt(Order $order)
    {
        $order->load(['orderItems.product', 'payments']);
        return view('admin.cashier.receipt', compact('order'));
    }
}
