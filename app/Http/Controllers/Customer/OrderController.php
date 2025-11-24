<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\OrderCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $validationRules = [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash,qris,bank_transfer,manual',
            'order_type' => 'required|in:dine-in,takeaway,pickup',
            'table_number' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:1000',
        ];

        // Require proof of payment if payment method is not cash
        if ($request->payment_method !== 'cash') {
            $validationRules['proof_of_payment'] = 'required|image|mimes:jpeg,jpg,png,gif|max:5120';
        } else {
            $validationRules['proof_of_payment'] = 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120';
        }

        $request->validate($validationRules);

        $totalAmount = 0;
        $orderItems = [];
        $categoryQuantities = []; // Track quantities per category

        foreach ($cart as $productId => $item) {
            $product = Product::with('category')->find($productId);
            if (!$product || !$product->is_active) {
                continue;
            }

            $category = $product->category;
            if (!$category || !$category->is_active) {
                continue;
            }

            $quantity = $item['quantity'];
            
            // Track quantity per category
            if (!isset($categoryQuantities[$category->id])) {
                $categoryQuantities[$category->id] = 0;
            }
            $categoryQuantities[$category->id] += $quantity;

            // Check stock availability for category
            if ($category->stock < $categoryQuantities[$category->id]) {
                return redirect()->route('cart.index')->with('error', 'Stok tidak cukup untuk kategori: ' . $category->name . '. Stok tersedia: ' . $category->stock);
            }

            $price = $product->price;
            $subtotal = $price * $quantity;
            $totalAmount += $subtotal;

            $orderItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($orderItems)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk valid di keranjang.');
        }

        $order = Order::create([
            'order_code' => OrderCodeGenerator::generate(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'payment_method' => $request->payment_method,
            'order_type' => $request->order_type,
            'table_number' => $request->order_type === 'dine-in' ? $request->table_number : null,
            'notes' => $request->notes,
            'total_amount' => $totalAmount,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
        ]);

        // Handle proof of payment file upload
        $proofOfPaymentPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $proofOfPaymentPath = $file->storeAs('proof-of-payments', $filename, 'public');
        }

        // Create payment record immediately with selected payment method
        Payment::create([
            'order_id' => $order->id,
            'amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'proof_of_payment' => $proofOfPaymentPath,
            'approved_by' => null, // Will be set when admin confirms payment
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

        session()->forget('cart');

        return redirect()->route('orders.show', $order->order_code)
            ->with('success', 'Pesanan berhasil dibuat! Kode pesanan: ' . $order->order_code);
    }

    public function show($orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->with(['orderItems.product'])
            ->firstOrFail();

        return view('customer.order.show', compact('order'));
    }
}
