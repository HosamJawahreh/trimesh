<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteFile;
use App\Models\PricingRule;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Upload 3D file and analyze
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:stl,obj,ply|max:51200', // Max 50MB
            'quote_id' => 'nullable|exists:quotes,id',
        ]);

        try {
            DB::beginTransaction();

            // Create or get quote
            $quote = $request->quote_id 
                ? Quote::findOrFail($request->quote_id)
                : Quote::create([
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                ]);

            // Store file
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
            $path = $file->storeAs('models', $fileName, 'public');

            // Create quote file record
            $quoteFile = QuoteFile::create([
                'quote_id' => $quote->id,
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $extension,
                'quantity' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'quote_id' => $quote->id,
                    'file_id' => $quoteFile->id,
                    'file_name' => $originalName,
                    'file_url' => Storage::url($path),
                    'file_type' => $extension,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'File upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze uploaded file and calculate price
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:quote_files,id',
            'volume_mm3' => 'required|numeric|min:0',
            'width_mm' => 'required|numeric|min:0',
            'height_mm' => 'required|numeric|min:0',
            'depth_mm' => 'required|numeric|min:0',
            'surface_area_mm2' => 'nullable|numeric|min:0',
            'material' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            $quoteFile = QuoteFile::findOrFail($request->file_id);

            // Update geometry data
            $quoteFile->update([
                'volume_mm3' => $request->volume_mm3,
                'volume_cm3' => $request->volume_mm3 / 1000,
                'width_mm' => $request->width_mm,
                'height_mm' => $request->height_mm,
                'depth_mm' => $request->depth_mm,
                'surface_area_mm2' => $request->surface_area_mm2 ?? 0,
                'material' => $request->material,
                'quantity' => $request->quantity ?? 1,
            ]);

            // Calculate price
            $priceData = $this->pricingService->calculatePrice(
                $request->volume_mm3,
                $request->surface_area_mm2 ?? 0,
                $request->material,
                $request->quantity ?? 1,
                [
                    'width' => $request->width_mm,
                    'height' => $request->height_mm,
                    'depth' => $request->depth_mm,
                ]
            );

            if ($priceData['success']) {
                $quoteFile->update([
                    'unit_price' => $priceData['unit_price'],
                    'calculated_price' => $priceData['total_price'],
                ]);

                // Update quote total
                $quoteFile->quote->calculateTotal();
            }

            return response()->json([
                'success' => true,
                'data' => array_merge($priceData, [
                    'file_id' => $quoteFile->id,
                    'quote_id' => $quoteFile->quote_id,
                ])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate price for given parameters
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'volume_mm3' => 'required|numeric|min:0',
            'surface_area_mm2' => 'nullable|numeric|min:0',
            'material' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            $priceData = $this->pricingService->calculatePrice(
                $request->volume_mm3,
                $request->surface_area_mm2 ?? 0,
                $request->material,
                $request->quantity ?? 1
            );

            return response()->json($priceData);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calculation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit complete quote
     */
    public function submit(Request $request)
    {
        $request->validate([
            'quote_id' => 'required|exists:quotes,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_notes' => 'nullable|string',
        ]);

        try {
            $quote = Quote::with('files')->findOrFail($request->quote_id);

            if ($quote->files->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quote has no files'
                ], 422);
            }

            $quote->update([
                'status' => 'submitted',
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_notes' => $request->customer_notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quote submitted successfully',
                'data' => [
                    'quote_number' => $quote->quote_number,
                    'total_price' => $quote->total_price,
                    'file_count' => $quote->files->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Submission failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file from quote
     */
    public function deleteFile($fileId)
    {
        try {
            $quoteFile = QuoteFile::findOrFail($fileId);
            $quoteId = $quoteFile->quote_id;
            
            $quoteFile->delete();

            // Update quote total
            $quote = Quote::find($quoteId);
            if ($quote) {
                $quote->calculateTotal();
            }

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available materials
     */
    public function getMaterials()
    {
        $materials = $this->pricingService->getActivePricingRules();

        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    /**
     * Get quote details
     */
    public function getQuote($quoteId)
    {
        try {
            $quote = Quote::with('files')->findOrFail($quoteId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $quote->id,
                    'quote_number' => $quote->quote_number,
                    'total_price' => $quote->total_price,
                    'status' => $quote->status,
                    'files' => $quote->files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'file_name' => $file->file_name,
                            'file_url' => $file->file_url,
                            'volume_cm3' => $file->volume_cm3,
                            'dimensions' => [
                                'width' => $file->width_mm,
                                'height' => $file->height_mm,
                                'depth' => $file->depth_mm,
                            ],
                            'material' => $file->material,
                            'quantity' => $file->quantity,
                            'unit_price' => $file->unit_price,
                            'calculated_price' => $file->calculated_price,
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
