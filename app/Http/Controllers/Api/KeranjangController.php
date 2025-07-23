<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\ItemKeranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Get or create cart for user
            $keranjang = Keranjang::firstOrCreate(['user_id' => $user->id]);
            
            $items = $keranjang->itemsWithProduct()->get();

            $data = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'keranjang_id' => $item->keranjang_id,
                    'produk_id' => $item->produk_id,
                    'quantity' => (int) $item->jumlah,
                    'total_harga' => (int) ($item->jumlah * $item->produk->harga),
                    'created_at' => $item->created_at ? $item->created_at->toISOString() : '',
                    'updated_at' => $item->updated_at ? $item->updated_at->toISOString() : '',
                    'produk' => [
                        'id' => $item->produk->id,
                        'nama' => $item->produk->nama ?? '',
                        'harga' => (int) ($item->produk->harga ?? 0),
                        'deskripsi' => $item->produk->deskripsi ?? '',
                        'gambar' => $item->produk->gambar_url ?? '',
                        'kategori_id' => $item->produk->kategori_id ?? 0,
                        'kategori' => $item->produk->kategori ? [
                            'id' => $item->produk->kategori->id,
                            'nama' => $item->produk->kategori->nama ?? '',
                        ] : [
                            'id' => 0,
                            'nama' => '',
                        ],
                        'stok' => (int) ($item->produk->stok ?? 0),
                    ]
                ];
            });

            // Calculate cart summary
            $total_items = $keranjang->total_items;
            $total_amount = $keranjang->total_amount;

            return response()->json([
                'status' => true,
                'message' => 'Data keranjang berhasil diambil',
                'data' => $data,
                'summary' => [
                    'total_items' => $total_items,
                    'total_amount' => $total_amount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'produk_id' => 'required|exists:produks,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $user = Auth::user();
            $produk = Produk::find($request->produk_id);

            // Produk tidak ditemukan
            if (!$produk) {
                return response()->json([
                    'status' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            // Validasi stok habis
            if ($produk->stok <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stok produk habis'
                ], 400);
            }

            // Validasi jumlah melebihi stok
            if ($request->quantity > $produk->stok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jumlah melebihi stok yang tersedia'
                ], 400);
            }

            // Get or create cart for user
            $keranjang = Keranjang::firstOrCreate(['user_id' => $user->id]);

            // Check if product already in cart
            $existingItem = ItemKeranjang::where('keranjang_id', $keranjang->id)
                ->where('produk_id', $request->produk_id)
                ->first();

            if ($existingItem) {
                // Update quantity if product already exists
                $newQuantity = $existingItem->jumlah + $request->quantity;
                
                // Check stock again
                if ($produk->stok < $newQuantity) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Stok produk tidak mencukupi untuk quantity yang diminta'
                    ], 400);
                }

                $existingItem->update(['jumlah' => $newQuantity]);
                $cartItem = $existingItem->fresh();
            } else {
                // Create new cart item
                $cartItem = ItemKeranjang::create([
                    'keranjang_id' => $keranjang->id,
                    'produk_id' => $request->produk_id,
                    'jumlah' => $request->quantity
                ]);
            }

            // Load product data
            $cartItem->load(['produk.kategori']);

            $data = [
                'id' => $cartItem->id,
                'keranjang_id' => $cartItem->keranjang_id,
                'produk_id' => $cartItem->produk_id,
                'quantity' => (int) $cartItem->jumlah,
                'total_harga' => (int) ($cartItem->jumlah * $cartItem->produk->harga),
                'created_at' => $cartItem->created_at ? $cartItem->created_at->toISOString() : '',
                'updated_at' => $cartItem->updated_at ? $cartItem->updated_at->toISOString() : '',
                'produk' => [
                    'id' => $cartItem->produk->id,
                    'nama' => $cartItem->produk->nama ?? '',
                    'harga' => (int) ($cartItem->produk->harga ?? 0),
                    'deskripsi' => $cartItem->produk->deskripsi ?? '',
                    'gambar' => $cartItem->produk->gambar_url ?? '',
                    'kategori_id' => $cartItem->produk->kategori_id ?? 0,
                    'kategori' => $cartItem->produk->kategori ? [
                        'id' => $cartItem->produk->kategori->id,
                        'nama' => $cartItem->produk->kategori->nama ?? '',
                    ] : [
                        'id' => 0,
                        'nama' => '',
                    ],
                    'stok' => (int) ($cartItem->produk->stok ?? 0),
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
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
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $user = Auth::user();
            $keranjang = Keranjang::where('user_id', $user->id)->first();
            
            if (!$keranjang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Keranjang tidak ditemukan'
                ], 404);
            }

            $cartItem = ItemKeranjang::where('keranjang_id', $keranjang->id)
                ->where('id', $id)
                ->with('produk')
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ], 404);
            }

            // Check stock availability
            if ($cartItem->produk->stok < $request->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stok produk tidak mencukupi. Stok tersedia: ' . $cartItem->produk->stok
                ], 400);
            }

            $cartItem->update(['jumlah' => $request->quantity]);
            $cartItem->load(['produk.kategori']);

            $data = [
                'id' => $cartItem->id,
                'keranjang_id' => $cartItem->keranjang_id,
                'produk_id' => $cartItem->produk_id,
                'quantity' => (int) $cartItem->jumlah,
                'total_harga' => (int) ($cartItem->jumlah * $cartItem->produk->harga),
                'created_at' => $cartItem->created_at ? $cartItem->created_at->toISOString() : '',
                'updated_at' => $cartItem->updated_at ? $cartItem->updated_at->toISOString() : '',
                'produk' => [
                    'id' => $cartItem->produk->id,
                    'nama' => $cartItem->produk->nama ?? '',
                    'harga' => (int) ($cartItem->produk->harga ?? 0),
                    'deskripsi' => $cartItem->produk->deskripsi ?? '',
                    'gambar' => $cartItem->produk->gambar_url ?? '',
                    'kategori_id' => $cartItem->produk->kategori_id ?? 0,
                    'kategori' => $cartItem->produk->kategori ? [
                        'id' => $cartItem->produk->kategori->id,
                        'nama' => $cartItem->produk->kategori->nama ?? '',
                    ] : [
                        'id' => 0,
                        'nama' => '',
                    ],
                    'stok' => (int) ($cartItem->produk->stok ?? 0),
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Quantity berhasil diupdate',
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
     * Remove item from cart
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $keranjang = Keranjang::where('user_id', $user->id)->first();
            
            if (!$keranjang) {
                return response()->json([
                    'status' => false,
                    'message' => 'Keranjang tidak ditemukan'
                ], 404);
            }

            $cartItem = ItemKeranjang::where('keranjang_id', $keranjang->id)
                ->where('id', $id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'status' => true,
                'message' => 'Item berhasil dihapus dari keranjang'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        try {
            $user = Auth::user();
            $keranjang = Keranjang::where('user_id', $user->id)->first();
            
            if ($keranjang) {
                $keranjang->itemKeranjang()->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 