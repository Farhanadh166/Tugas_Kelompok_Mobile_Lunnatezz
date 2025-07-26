<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id', 'nama', 'deskripsi', 'harga', 'stok', 'gambar'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    public function detailPesanan()
    {
        return $this->hasMany(\App\Models\DetailPesanan::class, 'produk_id');
    } 
    /**
     * Get the image URL attribute
     */
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return null;
        }

        // If gambar already contains full URL, return as is
        if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
            return $this->gambar;
        }

        // If gambar is just filename, construct full URL
        if (strpos($this->gambar, '/') === false) {
            return asset('storage/' . $this->gambar);
        }

        // If gambar has path, construct URL
        return asset($this->gambar);
    }
}