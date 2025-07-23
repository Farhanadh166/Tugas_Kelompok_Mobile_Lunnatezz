<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    // List komplain user (atau semua jika admin)
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            $complaints = Complaint::with(['user', 'order'])->orderBy('created_at', 'desc')->get();
        } else {
            $complaints = Complaint::where('user_id', $user->id)->with('order')->orderBy('created_at', 'desc')->get();
        }
        return response()->json([
            'success' => true,
            'data' => $complaints
        ]);
    }

    // Buat komplain baru
    public function store(Request $request, $orderId)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?? 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaint_photos', 'public');
        }
        $complaint = Complaint::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'reason' => $request->reason,
            'description' => $request->description,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Komplain berhasil diajukan',
            'data' => $complaint
        ]);
    }

    // Detail komplain
    public function show($id)
    {
        $complaint = Complaint::with(['user', 'order'])->find($id);
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Komplain tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $complaint
        ]);
    }

    // Admin update status/response
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,diterima,ditolak',
            'response' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?? 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        $complaint = Complaint::find($id);
        if (!$complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Komplain tidak ditemukan'
            ], 404);
        }
        $complaint->status = $request->status;
        $complaint->response = $request->response;
        $complaint->save();
        return response()->json([
            'success' => true,
            'message' => 'Status komplain berhasil diupdate',
            'data' => $complaint
        ]);
    }
} 