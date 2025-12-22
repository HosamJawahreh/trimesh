<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MeshRepairService;
use App\Models\File;
use App\Models\MeshRepair;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;

class MeshRepairController extends Controller
{
    protected MeshRepairService $meshRepairService;

    public function __construct(MeshRepairService $meshRepairService)
    {
        $this->meshRepairService = $meshRepairService;
    }

    /**
     * Check mesh repair service status
     *
     * @return JsonResponse
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'available' => $this->meshRepairService->isAvailable(),
            'service_url' => config('services.mesh_repair.url'),
            'max_file_size_mb' => config('services.mesh_repair.max_file_size') / 1024 / 1024
        ]);
    }

    /**
     * Analyze mesh file
     *
     * POST /api/mesh/analyze
     * Body: file_id or uploaded file
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analyze(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'file_id' => 'required_without:file|integer|exists:files,id',
                'file' => 'required_without:file_id|file|mimes:stl,obj,ply|max:102400' // 100MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check service availability
            if (!$this->meshRepairService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesh repair service is currently unavailable'
                ], 503);
            }

            // Get file
            if ($request->has('file_id')) {
                $file = File::findOrFail($request->file_id);
                $filePath = Storage::path($file->path);
            } else {
                $filePath = $request->file('file');
            }

            // Analyze mesh
            $analysis = $this->meshRepairService->analyzeMesh($filePath);

            // Add recommendations
            $recommendations = $this->meshRepairService->getRepairRecommendations($analysis);

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'recommendations' => $recommendations
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Analysis failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Repair mesh file
     *
     * POST /api/mesh/repair
     * Body: file_id, aggressive (bool), save_result (bool)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function repair(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'file_id' => 'required|integer|exists:files,id',
                'aggressive' => 'sometimes|boolean',
                'save_result' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check service availability
            if (!$this->meshRepairService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesh repair service is currently unavailable'
                ], 503);
            }

            $file = File::findOrFail($request->file_id);
            $aggressive = $request->get('aggressive', true);
            $saveResult = $request->get('save_result', true);

            // Get file path
            $filePath = Storage::path($file->path);

            // Repair mesh
            $repairResult = $this->meshRepairService->repairMesh($filePath, $aggressive);

            // Calculate quality score
            $qualityScore = $this->meshRepairService->calculateQualityScore($repairResult);

            // Save to database if requested
            $meshRepair = null;
            if ($saveResult) {
                $meshRepair = MeshRepair::create([
                    'file_id' => $file->id,
                    'original_volume_cm3' => $repairResult['original_stats']['volume_cm3'],
                    'repaired_volume_cm3' => $repairResult['repaired_stats']['volume_cm3'],
                    'holes_filled' => $repairResult['repair_summary']['holes_filled'],
                    'quality_score' => $qualityScore,
                    'repair_time_seconds' => $repairResult['repair_time_seconds'],
                    'status' => 'completed',
                    'aggressive_mode' => $aggressive,
                    'is_watertight' => $repairResult['repaired_stats']['is_watertight'],
                    'is_manifold' => $repairResult['repaired_stats']['is_manifold'],
                    'metadata' => json_encode([
                        'original_stats' => $repairResult['original_stats'],
                        'repaired_stats' => $repairResult['repaired_stats'],
                        'repair_summary' => $repairResult['repair_summary']
                    ])
                ]);

                // Update file record
                $file->update([
                    'repair_status' => 'repaired',
                    'is_watertight' => $repairResult['repaired_stats']['is_watertight'],
                    'last_repair_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'repair_result' => $repairResult,
                'quality_score' => $qualityScore,
                'repair_id' => $meshRepair?->id,
                'message' => 'Mesh repaired successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Repair failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Repair mesh and download result
     *
     * POST /api/mesh/repair-download
     * Body: file_id, aggressive (bool)
     *
     * @param Request $request
     * @return mixed
     */
    public function repairAndDownload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_id' => 'required|integer|exists:files,id',
                'aggressive' => 'sometimes|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!$this->meshRepairService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service unavailable'
                ], 503);
            }

            $file = File::findOrFail($request->file_id);
            $aggressive = $request->get('aggressive', true);

            // Prepare output path
            $extension = pathinfo($file->filename, PATHINFO_EXTENSION);
            $outputFilename = 'repaired_' . time() . '_' . $file->filename;
            $outputPath = Storage::path('repaired/' . $outputFilename);

            // Ensure directory exists
            $dir = dirname($outputPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Repair and download
            $metadata = $this->meshRepairService->repairAndDownload(
                Storage::path($file->path),
                $aggressive,
                $outputPath
            );

            // Save record
            MeshRepair::create([
                'file_id' => $file->id,
                'original_volume_cm3' => $metadata['volume_original'],
                'repaired_volume_cm3' => $metadata['volume_repaired'],
                'status' => 'completed',
                'aggressive_mode' => $aggressive,
                'repaired_file_path' => 'repaired/' . $outputFilename
            ]);

            // Return file download
            return response()->download($outputPath, $outputFilename, [
                'X-Volume-Original' => $metadata['volume_original'],
                'X-Volume-Repaired' => $metadata['volume_repaired'],
                'X-Volume-Change' => $metadata['volume_change']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get repair history for a file
     *
     * GET /api/mesh/history/{fileId}
     *
     * @param int $fileId
     * @return JsonResponse
     */
    public function history(int $fileId): JsonResponse
    {
        try {
            $file = File::findOrFail($fileId);
            
            $repairs = MeshRepair::where('file_id', $fileId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'file' => [
                    'id' => $file->id,
                    'filename' => $file->filename,
                    'repair_status' => $file->repair_status,
                    'is_watertight' => $file->is_watertight
                ],
                'repairs' => $repairs
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get statistics for all repairs
     *
     * GET /api/mesh/stats
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_repairs' => MeshRepair::count(),
                'successful_repairs' => MeshRepair::where('status', 'completed')->count(),
                'average_quality_score' => MeshRepair::where('status', 'completed')->avg('quality_score'),
                'average_volume_change' => MeshRepair::selectRaw('AVG(repaired_volume_cm3 - original_volume_cm3) as avg_change')->first()->avg_change,
                'total_holes_filled' => MeshRepair::sum('holes_filled'),
                'watertight_achieved' => MeshRepair::where('is_watertight', true)->count(),
                'today_repairs' => MeshRepair::whereDate('created_at', today())->count(),
                'this_week_repairs' => MeshRepair::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
