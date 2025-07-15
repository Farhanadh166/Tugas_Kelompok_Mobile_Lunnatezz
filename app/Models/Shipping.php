<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'pesanan_id',
        'shipping_method',
        'shipping_cost',
        'tracking_number',
        'status',
        'shipped_at',
        'delivered_at'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
