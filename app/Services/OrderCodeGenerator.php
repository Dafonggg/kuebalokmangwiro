<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderCodeGenerator
{
    public static function generate(): string
    {
        do {
            $code = 'INV-' . strtoupper(Str::random(4));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}

