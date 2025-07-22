<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Get user's addresses
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $addresses = Address::where('user_id', $user->id)->get();

            $data = $addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'name' => $address->name ?? '',
                    'phone' => $address->phone ?? '',
                    'address' => $address->address ?? '',
                    'city' => $address->city ?? '',
                    'province' => $address->province ?? '',
                    'postal_code' => $address->postal_code ?? '',
                    'is_primary' => (bool) $address->is_primary,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Data alamat berhasil diambil',
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
     * Store a new address
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'is_primary' => 'boolean'
            ]);

            $user = Auth::user();

            // If this address is primary, unset other primary addresses
            if ($request->is_primary) {
                Address::where('user_id', $user->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $address = Address::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'is_primary' => $request->is_primary ?? false,
            ]);

            $data = [
                'id' => $address->id,
                'name' => $address->name ?? '',
                'phone' => $address->phone ?? '',
                'address' => $address->address ?? '',
                'city' => $address->city ?? '',
                'province' => $address->province ?? '',
                'postal_code' => $address->postal_code ?? '',
                'is_primary' => (bool) $address->is_primary,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Alamat berhasil ditambahkan',
                'data' => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'postal_code' => 'required|string|max:10',
                'is_primary' => 'boolean'
            ]);

            $user = Auth::user();
            $address = Address::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            // If this address is primary, unset other primary addresses
            if ($request->is_primary) {
                Address::where('user_id', $user->id)
                    ->where('id', '!=', $id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $address->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'is_primary' => $request->is_primary ?? false,
            ]);

            $data = [
                'id' => $address->id,
                'name' => $address->name ?? '',
                'phone' => $address->phone ?? '',
                'address' => $address->address ?? '',
                'city' => $address->city ?? '',
                'province' => $address->province ?? '',
                'postal_code' => $address->postal_code ?? '',
                'is_primary' => (bool) $address->is_primary,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Alamat berhasil diperbarui',
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
     * Delete address
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $address = Address::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$address) {
                return response()->json([
                    'status' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $address->delete();

            return response()->json([
                'status' => true,
                'message' => 'Alamat berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
