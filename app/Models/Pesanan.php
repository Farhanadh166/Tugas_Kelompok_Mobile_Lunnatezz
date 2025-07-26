<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tanggal_pesanan', 'total_harga', 'status', 'metode_bayar', 'alamat_kirim', 'order_number', 'catatan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function shipping()
    {
        return $this->hasOne(\App\Models\Shipping::class, 'pesanan_id');
    }

    /**
     * Get allowed status transitions based on current status
     */
    public function getAllowedStatusTransitions()
    {
        $transitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['shipped'],
            'shipped' => ['completed'],
            'completed' => [], // Final state
            'cancelled' => [], // Final state
        ];

        return $transitions[$this->status] ?? [];
    }

    /**
     * Check if status transition is allowed
     */
    public function canTransitionTo($newStatus)
    {
        return in_array($newStatus, $this->getAllowedStatusTransitions());
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            'pending' => 'warning',
            'paid' => 'info',
            'shipped' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        return $classes[$this->status] ?? 'secondary';
    }
}
