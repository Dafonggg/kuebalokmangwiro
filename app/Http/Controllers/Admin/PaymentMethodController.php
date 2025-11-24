<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('display_order')->orderBy('name')->get();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.payment-methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:qris,bank_transfer',
            'qr_code_image' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|required_if:type,bank_transfer|string|max:255',
            'account_number' => 'nullable|required_if:type,bank_transfer|string|max:255',
            'account_name' => 'nullable|required_if:type,bank_transfer|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('qr_code_image')) {
            $validated['qr_code_image'] = $request->file('qr_code_image')->store('payment-methods', 'public');
        }

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.show', compact('paymentMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:qris,bank_transfer',
            'qr_code_image' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|required_if:type,bank_transfer|string|max:255',
            'account_number' => 'nullable|required_if:type,bank_transfer|string|max:255',
            'account_name' => 'nullable|required_if:type,bank_transfer|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('qr_code_image')) {
            if ($paymentMethod->qr_code_image) {
                Storage::disk('public')->delete($paymentMethod->qr_code_image);
            }
            $validated['qr_code_image'] = $request->file('qr_code_image')->store('payment-methods', 'public');
        }

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->qr_code_image) {
            Storage::disk('public')->delete($paymentMethod->qr_code_image);
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
