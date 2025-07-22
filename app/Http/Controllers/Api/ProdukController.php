<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Get all products
     */
    public function index(Request $request)
    {
        try {
            $query = Produk::with('kategori');
            
            // Filter by category if provided
            if ($request->has('category_id') && $request->category_id) {
                $query->where('kategori_id', $request->category_id);
            }
            
            // Search by name if provided
            if ($request->has('search') && $request->search) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            }
            
            // Sort products
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $produks = $query->get();
            
            // Transform data to match Flutter requirements
            $data = $produks->map(function ($produk) {
                return [
                    'id' => $produk->id,
                    'nama' => $produk->nama ?? '',
                    'harga' => (int) ($produk->harga ?? 0),
                    'deskripsi' => $produk->deskripsi ?? '',
                    'gambar' => $produk->gambar_url ?? '',
                    'kategori_id' => $produk->kategori_id ?? 0,
                    'kategori' => $produk->kategori ? [
                        'id' => $produk->kategori->id,
                        'nama' => $produk->kategori->nama ?? '',
                    ] : [
                        'id' => 0,
                        'nama' => '',
                    ],
                    'stok' => (int) ($produk->stok ?? 0),
                    'created_at' => $produk->created_at ? $produk->created_at->toISOString() : '',
                    'updated_at' => $produk->updated_at ? $produk->updated_at->toISOString() : '',
                ];
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product by ID
     */
    public function show($id)
    {
        try {
            $produk = Produk::with('kategori')->find($id);
            
            if (!$produk) {
                return response()->json([
                    'status' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }
            
            $data = [
                'id' => $produk->id,
                'nama' => $produk->nama ?? '',
                'harga' => (int) ($produk->harga ?? 0),
                'deskripsi' => $produk->deskripsi ?? '',
                'gambar' => $produk->gambar_url ?? '',
                'kategori_id' => $produk->kategori_id ?? 0,
                'kategori' => $produk->kategori ? [
                    'id' => $produk->kategori->id,
                    'nama' => $produk->kategori->nama ?? '',
                ] : [
                    'id' => 0,
                    'nama' => '',
                ],
                'stok' => (int) ($produk->stok ?? 0),
                'created_at' => $produk->created_at ? $produk->created_at->toISOString() : '',
                'updated_at' => $produk->updated_at ? $produk->updated_at->toISOString() : '',
            ];
            
            return response()->json([
                'status' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($kategoriId)
    {
        try {
            $produks = Produk::with('kategori')
                ->where('kategori_id', $kategoriId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $data = $produks->map(function ($produk) {
                return [
                    'id' => $produk->id,
                    'nama' => $produk->nama ?? '',
                    'harga' => (int) ($produk->harga ?? 0),
                    'deskripsi' => $produk->deskripsi ?? '',
                    'gambar' => $produk->gambar_url ?? '',
                    'kategori_id' => $produk->kategori_id ?? 0,
                    'kategori' => $produk->kategori ? [
                        'id' => $produk->kategori->id,
                        'nama' => $produk->kategori->nama ?? '',
                    ] : [
                        'id' => 0,
                        'nama' => '',
                    ],
                    'stok' => (int) ($produk->stok ?? 0),
                    'created_at' => $produk->created_at ? $produk->created_at->toISOString() : '',
                    'updated_at' => $produk->updated_at ? $produk->updated_at->toISOString() : '',
                ];
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Data produk kategori berhasil diambil',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 