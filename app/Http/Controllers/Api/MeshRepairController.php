<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MeshRepairService;
use App\Models\ThreeDFile;
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
            // Debug: Log what we received
            \Log::info('Mesh analyze request received', [
                'has_file' => $request->hasFile('file'),
                'has_file_id' => $request->has('file_id'),
                'all_keys' => array_keys($request->all()),
                'files' => array_keys($request->allFiles())
            ]);

            // Validate request
            $validator = Validator::make($request->all(), [
                'file_id' => 'required_without:file|string', // Changed to string to accept file_xxxx format
                'file' => 'required_without:file_id|file|mimes:stl,obj,ply|max:102400' // 100MB
            ]);

            if ($validator->fails()) {
                \Log::error('Mesh analyze validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'debug' => [
                        'has_file' => $request->hasFile('file'),
                        'has_file_id' => $request->has('file_id'),
                        'keys' => array_keys($request->all())
                    ]
                ], 422);
            }

            // If file_id is provided, look up the file
            if ($request->has('file_id')) {
                $fileId = $request->input('file_id');
                $dbFile = ThreeDFile::where('file_id', $fileId)->first();

                if (!$dbFile) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found in database',
                        'file_id' => $fileId
                    ], 404);
                }

                if ($dbFile->isExpired()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File has expired',
                        'file_id' => $fileId
                    ], 410);
                }

                // Use the file from disk
                $filePath = storage_path('app/' . $dbFile->file_path);

                if (!file_exists($filePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found on disk',
                        'file_id' => $fileId
                    ], 404);
                }

                \Log::info('Using file from database', [
                    'file_id' => $fileId,
                    'file_path' => $filePath,
                    'file_name' => $dbFile->file_name
                ]);
            }

            // Check service availability
            if (!$this->meshRepairService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesh repair service is currently unavailable'
                ], 503);
            }

            // Get file path - either from database or uploaded file
            if ($request->has('file_id')) {
                // Already handled above - $filePath and $dbFile are set
                // Use the file from disk
                $analysis = $this->meshRepairService->analyzeMesh($filePath);
            } else {
                // Use uploaded file
                $uploadedFile = $request->file('file');
                $analysis = $this->meshRepairService->analyzeMesh($uploadedFile);
            }

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
            // Validate request - support both file_id (string) and file upload
            $validator = Validator::make($request->all(), [
                'file_id' => 'required_without:file|string', // Accept file_xxxx format
                'file' => 'required_without:file_id|file|mimes:stl,obj,ply|max:102400',
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

            $aggressive = $request->get('aggressive', true);
            $saveResult = $request->get('save_result', true);

            // Get file path - either from database or uploaded file
            if ($request->has('file_id')) {
                $fileId = $request->input('file_id');
                $dbFile = ThreeDFile::where('file_id', $fileId)->first();

                if (!$dbFile) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found in database',
                        'file_id' => $fileId
                    ], 404);
                }

                if ($dbFile->isExpired()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File has expired',
                        'file_id' => $fileId
                    ], 410);
                }

                // Use the file from disk
                $filePath = storage_path('app/' . $dbFile->file_path);

                if (!file_exists($filePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File not found on disk',
                        'file_id' => $fileId
                    ], 404);
                }

                // Repair mesh
                $repairResult = $this->meshRepairService->repairMesh($filePath, $aggressive);
                $file = $dbFile; // Use for saving result
            } else {
                // Use uploaded file
                $uploadedFile = $request->file('file');

                // Repair mesh
                $repairResult = $this->meshRepairService->repairMesh($uploadedFile, $aggressive);
                $file = null; // No database file to associate with
            }

            // Calculate quality score
            $qualityScore = $this->meshRepairService->calculateQualityScore($repairResult);

            // Save to database if requested and we have a database file
            $meshRepair = null;
            if ($saveResult && $file) {
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

                \Log::info('Mesh repair saved to database', [
                    'mesh_repair_id' => $meshRepair->id,
                    'file_id' => $file->id,
                    'holes_filled' => $meshRepair->holes_filled,
                    'quality_score' => $meshRepair->quality_score
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
                'original_stats' => $repairResult['original_stats'],
                'repaired_stats' => $repairResult['repaired_stats'],
                'repair_summary' => $repairResult['repair_summary'],
                'quality_score' => $qualityScore,
                'volume_change_cm3' => $repairResult['repaired_stats']['volume_cm3'] - $repairResult['original_stats']['volume_cm3'],
                'volume_change_percent' => $repairResult['original_stats']['volume_cm3'] > 0
                    ? (($repairResult['repaired_stats']['volume_cm3'] - $repairResult['original_stats']['volume_cm3']) / $repairResult['original_stats']['volume_cm3']) * 100
                    : 0,
                'repair_time_seconds' => $repairResult['repair_time_seconds'] ?? 0,
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
                'file_id' => 'required|integer|exists:three_d_files,id',
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

            $file = ThreeDFile::findOrFail($request->file_id);
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
            $file = ThreeDFile::findOrFail($fileId);

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
