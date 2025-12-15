<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteFile;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function store(Request $request): JsonResponse
    {
        $quote = Quote::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'quote' => $quote,
        ]);
    }

    public function uploadFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:stl,obj,ply|max:51200',
            'quote_id' => 'required|exists:quotes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $quote = Quote::findOrFail($request->quote_id);

            $path = $file->store('models', 'public');
            $fileName = $file->getClientOriginalName();

            $quoteFile = QuoteFile::create([
                'quote_id' => $quote->id,
                'file_name' => $fileName,
                'file_path' => $path,
            ]);

            return response()->json([
                'success' => true,
                'file' => $quoteFile,
                'url' => Storage::url($path),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function analyzeFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_id' => 'required|exists:quote_files,id',
            'volume_mm3' => 'required|numeric|min:0',
            'width_mm' => 'required|numeric|min:0',
            'height_mm' => 'required|numeric|min:0',
            'depth_mm' => 'required|numeric|min:0',
            'material' => 'nullable|string',
            'technology' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = QuoteFile::findOrFail($request->file_id);

            $dimensions = [
                'width' => $request->width_mm,
                'height' => $request->height_mm,
                'depth' => $request->depth_mm,
            ];

            $file->volume_mm3 = $request->volume_mm3;
            $file->save();

            $updatedFile = $this->pricingService->updateQuoteFilePrice(
                $file,
                $dimensions,
                $request->material,
                $request->technology
            );

            $quote = $file->quote->fresh();
            $quote->load('files');

            return response()->json([
                'success' => true,
                'file' => $updatedFile,
                'quote' => $quote,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quote_id' => 'required|exists:quotes,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $quote = Quote::findOrFail($request->quote_id);

            if ($quote->files()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload at least one file',
                ], 422);
            }

            $quote->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
                'status' => 'submitted',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quote submitted successfully',
                'quote' => $quote->load('files'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteFile($fileId): JsonResponse
    {
        try {
            $file = QuoteFile::findOrFail($fileId);
            $quote = $file->quote;

            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            $file->delete();
            $quote->calculateTotalPrice();

            return response()->json([
                'success' => true,
                'quote' => $quote->fresh()->load('files'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMaterials(): JsonResponse
    {
        try {
            $materials = $this->pricingService->getAvailableCombinations();

            return response()->json([
                'success' => true,
                'materials' => $materials,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
