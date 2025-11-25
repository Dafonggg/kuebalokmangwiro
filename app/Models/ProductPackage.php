<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPackage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'photo_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductPackageItem::class, 'package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
