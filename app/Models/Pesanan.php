<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tanggal_pesanan', 'total_harga', 'status', 'metode_bayar', 'alamat_kirim', 'order_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function shipping()
    {
        return $this->hasOne(\App\Models\Shipping::class, 'pesanan_id');
    }
}
