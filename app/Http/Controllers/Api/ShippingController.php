<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipping;
use Illuminate\Support\Facades\Gate;

class ShippingController extends Controller
{
    /**
     * Get shipping methods
     */
    public function methods()
    {
        try {
            $methods = [
                [
                    'id' => 'jne',
                    'name' => 'JNE Regular',
                    'description' => '2-3 hari kerja',
                    'base_cost' => 15000
                ],
                [
                    'id' => 'jne_express',
                    'name' => 'JNE Express',
                    'description' => '1-2 hari kerja',
                    'base_cost' => 25000
                ],
                [
                    'id' => 'jnt',
                    'name' => 'J&T Express',
                    'description' => '2-3 hari kerja',
                    'base_cost' => 12000
                ],
                [
                    'id' => 'sicepat',
                    'name' => 'SiCepat',
                    'description' => '2-3 hari kerja',
                    'base_cost' => 13000
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data metode pengiriman berhasil diambil',
                'data' => $methods
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping cost
     */
    public function cost(Request $request)
    {
        try {
            $request->validate([
                'method' => 'required|string',
                'weight' => 'nullable|numeric|min:0.1',
                'distance' => 'nullable|numeric|min:1'
            ]);

            $method = $request->method;
            $weight = $request->weight ?? 1; // Default 1 kg
            $distance = $request->distance ?? 100; // Default 100 km

            $baseCosts = [
                'jne' => 15000,
                'jne_express' => 25000,
                'jnt' => 12000,
                'sicepat' => 13000
            ];

            $baseCost = $baseCosts[$method] ?? 15000;
            
            // Simple calculation: base cost + (weight * 2000) + (distance * 100)
            $totalCost = $baseCost + ($weight * 2000) + ($distance * 100);

            return response()->json([
                'status' => true,
                'message' => 'Ongkir berhasil dihitung',
                'data' => [
                    'method' => $method,
                    'weight' => $weight,
                    'distance' => $distance,
                    'base_cost' => $baseCost,
                    'total_cost' => (int) $totalCost
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
     * Update shipping status & tracking number (admin/penjual only)
     */
    public function update(Request $request, $id)
    {
        // Hanya admin/penjual yang boleh update
        if (!Gate::allows('is-admin') && !Gate::allows('is-seller')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        $request->validate([
            'status' => 'nullable|in:pending,shipped,delivered,failed',
            'tracking_number' => 'nullable|string|max:100',
            'shipped_at' => 'nullable|date',
            'delivered_at' => 'nullable|date'
        ]);

        $shipping = Shipping::find($id);
        if (!$shipping) {
            return response()->json([
                'status' => false,
                'message' => 'Data shipping tidak ditemukan.'
            ], 404);
        }

        if ($request->has('status')) {
            $shipping->status = $request->status;
        }
        if ($request->has('tracking_number')) {
            $shipping->tracking_number = $request->tracking_number;
        }
        if ($request->has('shipped_at')) {
            $shipping->shipped_at = $request->shipped_at;
        }
        if ($request->has('delivered_at')) {
            $shipping->delivered_at = $request->delivered_at;
        }
        $shipping->save();

        return response()->json([
            'status' => true,
            'message' => 'Data shipping berhasil diupdate',
            'data' => $shipping
        ]);
    }
}
