<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itemKeranjang()
    {
        return $this->hasMany(ItemKeranjang::class);
    }

    /**
     * Get cart items with product details
     */
    public function itemsWithProduct()
    {
        return $this->hasMany(ItemKeranjang::class)->with('produk.kategori');
    }

    /**
     * Calculate total items in cart
     */
    public function getTotalItemsAttribute()
    {
        return $this->itemKeranjang->sum('jumlah');
    }

    /**
     * Calculate total amount in cart
     */
    public function getTotalAmountAttribute()
    {
        return $this->itemKeranjang->sum(function ($item) {
            return $item->jumlah * $item->produk->harga;
        });
    }
}
