<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteFile;
use Illuminate\Http\Request;

class QuoteManagementController extends Controller
{
    /**
     * Display all quotes
     */
    public function index()
    {
        $quotes = Quote::with('files', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.quotes.index', compact('quotes'));
    }

    /**
     * Show single quote details
     */
    public function show($id)
    {
        $quote = Quote::with(['files', 'user'])->findOrFail($id);
        
        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Update quote status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,submitted,reviewed,completed,cancelled',
            'admin_notes' => 'nullable|string',
        ]);

        try {
            $quote = Quote::findOrFail($id);
            $quote->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes ?? $quote->admin_notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quote status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a quote
     */
    public function destroy($id)
    {
        try {
            $quote = Quote::findOrFail($id);
            $quote->delete();

            return response()->json([
                'success' => true,
                'message' => 'Quote deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quote',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
