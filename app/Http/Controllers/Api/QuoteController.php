<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\ThreeDFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes (for admin)
     */
    public function index(Request $request)
    {
        $query = Quote::query()->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by form type
        if ($request->has('form_type')) {
            $query->where('form_type', $request->form_type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $quotes = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $quotes
        ]);
    }

    /**
     * Store a newly created quote with files
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== QUOTE STORE REQUEST START ===');
            Log::info('Request data:', $request->all());

            // Validate request
            $validator = Validator::make($request->all(), [
                'file_ids' => 'required|array|min:1',
                'file_ids.*' => 'required|string|starts_with:file_',
                'total_volume_cm3' => 'nullable|numeric|min:0',
                'total_price' => 'nullable|numeric|min:0',
                'material' => 'nullable|string',
                'color' => 'nullable|string',
                'quality' => 'nullable|string',
                'quantity' => 'nullable|integer|min:1',
                'pricing_breakdown' => 'nullable|array',
                'customer_name' => 'nullable|string|max:255',
                'customer_email' => 'nullable|email|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'form_type' => 'nullable|in:general,medical',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify all files exist in database
            $fileIds = $request->file_ids;
            $existingFiles = ThreeDFile::whereIn('file_id', $fileIds)->get();
            
            if ($existingFiles->count() !== count($fileIds)) {
                $missingFiles = array_diff($fileIds, $existingFiles->pluck('file_id')->toArray());
                Log::error('Some files not found in database:', $missingFiles);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Some files do not exist in the database',
                    'missing_files' => $missingFiles
                ], 404);
            }

            // Generate quote number
            $quoteNumber = Quote::generateQuoteNumber();

            // Create quote
            $quote = Quote::create([
                'quote_number' => $quoteNumber,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'file_ids' => $fileIds,
                'file_count' => count($fileIds),
                'total_volume_cm3' => $request->total_volume_cm3,
                'total_price' => $request->total_price,
                'material' => $request->material,
                'color' => $request->color,
                'quality' => $request->quality,
                'quantity' => $request->quantity ?? 1,
                'pricing_breakdown' => $request->pricing_breakdown,
                'notes' => $request->notes,
                'status' => 'pending',
                'form_type' => $request->form_type ?? 'general',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info('Quote created successfully:', [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'file_count' => $quote->file_count,
                'total_price' => $quote->total_price
            ]);

            // Return response with viewer link
            return response()->json([
                'success' => true,
                'message' => 'Quote saved successfully',
                'data' => [
                    'quote_id' => $quote->id,
                    'quote_number' => $quote->quote_number,
                    'viewer_link' => $quote->viewer_link,
                    'single_file_link' => $quote->single_file_link,
                    'file_count' => $quote->file_count,
                    'total_price' => $quote->total_price,
                    'status' => $quote->status,
                    'created_at' => $quote->created_at->toISOString(),
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Quote creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save quote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified quote
     */
    public function show(string $id)
    {
        try {
            $quote = Quote::findOrFail($id);

            // Load related files
            $files = $quote->threeDFiles();

            return response()->json([
                'success' => true,
                'data' => [
                    'quote' => $quote,
                    'files' => $files,
                    'viewer_link' => $quote->viewer_link,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found'
            ], 404);
        }
    }

    /**
     * Update the specified quote
     */
    public function update(Request $request, string $id)
    {
        try {
            $quote = Quote::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'nullable|in:pending,reviewed,quoted,accepted,rejected,completed',
                'admin_notes' => 'nullable|string',
                'total_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $quote->update($request->only(['status', 'admin_notes', 'total_price']));

            if ($request->has('status')) {
                $quote->responded_at = now();
                $quote->save();
            }

            Log::info('Quote updated:', [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'status' => $quote->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quote updated successfully',
                'data' => $quote
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quote: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified quote
     */
    public function destroy(string $id)
    {
        try {
            $quote = Quote::findOrFail($id);
            $quoteNumber = $quote->quote_number;

            $quote->delete();

            Log::info('Quote deleted:', ['quote_number' => $quoteNumber]);

            return response()->json([
                'success' => true,
                'message' => 'Quote deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quote'
            ], 500);
        }
    }
}
