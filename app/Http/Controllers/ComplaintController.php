<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;

class ComplaintController extends Controller
{
    // List semua komplain
    public function index()
    {
        $complaints = Complaint::with(['user', 'order'])->orderBy('created_at', 'desc')->get();
        return view('admin.complaints.index', compact('complaints'));
    }

    // Detail komplain
    public function show($id)
    {
        $complaint = Complaint::with(['user', 'order'])->findOrFail($id);
        return view('admin.complaints.show', compact('complaint'));
    }

    // Update status/response
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak',
            'response' => 'nullable|string'
        ]);
        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->response = $request->response;
        $complaint->save();
        return redirect()->route('admin.complaints.show', $id)->with('success', 'Status komplain berhasil diupdate.');
    }
} 