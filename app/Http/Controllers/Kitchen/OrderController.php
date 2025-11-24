<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::processing()
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        return view('kitchen.orders.index', compact('orders'));
    }

    public function markReady(Order $order)
    {
        if ($order->order_status !== 'processing') {
            return redirect()->back()->with('error', 'Hanya pesanan dengan status processing yang dapat ditandai siap.');
        }

        $order->update(['order_status' => 'ready']);

        return redirect()->back()->with('success', 'Pesanan ditandai sebagai siap.');
    }
}
