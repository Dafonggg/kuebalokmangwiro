<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['orderItems.product', 'orderItems.package.items.product', 'payments'])->latest();

        if ($request->has('status') && $request->status) {
            $query->where('order_status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'orderItems.package.items.product', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:pending,processing,ready,completed,canceled',
        ]);

        $order->update($validated);

        return redirect()->back()
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function confirmPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,qris,bank_transfer,manual',
        ]);

        $order->update(['payment_status' => 'paid']);

        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'payment_method' => $validated['payment_method'],
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }
}
