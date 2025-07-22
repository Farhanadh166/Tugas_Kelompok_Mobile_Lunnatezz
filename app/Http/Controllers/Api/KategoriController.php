<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        try {
            $kategoris = Kategori::orderBy('nama')->get();
            
            // Transform data to match Flutter requirements
            $data = $kategoris->map(function ($kategori) {
                return [
                    'id' => $kategori->id,
                    'nama' => $kategori->nama ?? '',
                    'icon' => $kategori->icon ? url($kategori->icon) : '',
                    'deskripsi' => $kategori->deskripsi ?? '',
                    'created_at' => $kategori->created_at ? $kategori->created_at->toISOString() : '',
                    'updated_at' => $kategori->updated_at ? $kategori->updated_at->toISOString() : '',
                ];
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diambil',
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
     * Get category by ID
     */
    public function show($id)
    {
        try {
            $kategori = Kategori::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }
            
            $data = [
                'id' => $kategori->id,
                'nama' => $kategori->nama ?? '',
                'icon' => $kategori->icon ? url($kategori->icon) : '',
                'deskripsi' => $kategori->deskripsi ?? '',
                'created_at' => $kategori->created_at ? $kategori->created_at->toISOString() : '',
                'updated_at' => $kategori->updated_at ? $kategori->updated_at->toISOString() : '',
            ];
            
            return response()->json([
                'status' => true,
                'message' => 'Data kategori berhasil diambil',
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