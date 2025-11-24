<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'order_type',
        'table_number',
        'notes',
        'total_amount',
        'payment_status',
        'order_status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('order_status', 'processing');
    }

    public function scopeReady($query)
    {
        return $query->where('order_status', 'ready');
    }

    public function scopeCompleted($query)
    {
        return $query->where('order_status', 'completed');
    }

    public function isPending(): bool
    {
        return $this->order_status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->order_status === 'processing';
    }

    public function isReady(): bool
    {
        return $this->order_status === 'ready';
    }

    public function isCompleted(): bool
    {
        return $this->order_status === 'completed';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
