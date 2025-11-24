<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'type',
        'qr_code_image',
        'bank_name',
        'account_number',
        'account_name',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeQris($query)
    {
        return $query->where('type', 'qris');
    }

    public function scopeBankTransfer($query)
    {
        return $query->where('type', 'bank_transfer');
    }
}
