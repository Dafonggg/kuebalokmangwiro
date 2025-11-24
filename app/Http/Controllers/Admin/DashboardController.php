<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\OrderCodeGenerator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::pending()->count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $todayRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total_amount');
        $totalProducts = Product::active()->count();

        // Recent orders for quick actions
        $recentOrders = Order::with(['orderItems.product'])
            ->latest()
            ->limit(10)
            ->get();

        // Products and categories for order creation
        $products = Product::active()->with('category')->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.dashboard.index', compact(
            'totalOrders',
            'pendingOrders',
            'todayOrders',
            'totalRevenue',
            'todayRevenue',
            'totalProducts',
            'recentOrders',
            'products',
            'categories'
        ));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_type' => 'required|in:dine-in,takeaway,pickup',
            'table_number' => 'nullable|string|max:10',
            'items' => 'required|array',
        ]);

        $totalAmount = 0;
        $orderItems = [];

        // Filter out items with quantity 0 or missing product_id
        foreach ($request->items as $key => $item) {
            if (!isset($item['product_id']) || !isset($item['quantity'])) {
                continue;
            }

            $quantity = (int) $item['quantity'];
            if ($quantity <= 0) {
                continue;
            }

            $product = Product::find($item['product_id']);
            if (!$product || !$product->is_active) {
                continue;
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
            'total_amount' => $totalAmount,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
        ]);

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        return redirect()->back()
            ->with('success', 'Pesanan berhasil dibuat! Kode: ' . $order->order_code);
    }

    public function quickUpdateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,ready,completed,canceled',
        ]);

        $order->update(['order_status' => $request->order_status]);

        return redirect()->back()
            ->with('success', 'Status pesanan ' . $order->order_code . ' berhasil diperbarui.');
    }

    public function quickConfirmPayment(Request $request, Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Pesanan sudah dibayar.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,qris,bank_transfer,manual',
        ]);

        $order->update(['payment_status' => 'paid']);

        \App\Models\Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'payment_method' => $request->payment_method,
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Pembayaran pesanan ' . $order->order_code . ' berhasil dikonfirmasi.');
    }
}
