<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Exception;

class MeshRepairService
{
    /**
     * Python mesh repair service URL
     */
    protected string $serviceUrl;

    /**
     * Request timeout in seconds
     */
    protected int $timeout;

    /**
     * Maximum file size in bytes
     */
    protected int $maxFileSize;

    public function __construct()
    {
        $this->serviceUrl = config('services.mesh_repair.url', 'http://localhost:8001');
        $this->timeout = config('services.mesh_repair.timeout', 120);
        $this->maxFileSize = config('services.mesh_repair.max_file_size', 100 * 1024 * 1024);
    }

    /**
     * Check if repair service is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->serviceUrl}/health");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Mesh repair service unavailable', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Analyze mesh file
     *
     * @param UploadedFile|string $file File object or path
     * @return array Analysis results
     * @throws Exception
     */
    public function analyzeMesh($file): array
    {
        try {
            // Get file path
            $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
            $fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);

            // Validate file size
            $fileSize = filesize($filePath);
            if ($fileSize > $this->maxFileSize) {
                throw new Exception("File too large: {$fileSize} bytes (max: {$this->maxFileSize})");
            }

            Log::info('Analyzing mesh', ['file' => $fileName, 'size' => $fileSize]);

            // Send to repair service
            $response = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($filePath), $fileName)
                ->post("{$this->serviceUrl}/api/analyze");

            if (!$response->successful()) {
                throw new Exception("Mesh analysis failed: {$response->body()}");
            }

            $data = $response->json();

            Log::info('Mesh analysis complete', [
                'file' => $fileName,
                'vertices' => $data['vertices'],
                'volume_cm3' => $data['volume_cm3'],
                'watertight' => $data['is_watertight'],
                'holes' => $data['holes_count']
            ]);

            return $data;

        } catch (Exception $e) {
            Log::error('Mesh analysis error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Repair mesh file
     *
     * @param UploadedFile|string $file File object or path
     * @param bool $aggressive Use aggressive repair mode
     * @return array Repair results with statistics
     * @throws Exception
     */
    public function repairMesh($file, bool $aggressive = true): array
    {
        try {
            // Get file path
            $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
            $fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);

            // Validate file size
            $fileSize = filesize($filePath);
            if ($fileSize > $this->maxFileSize) {
                throw new Exception("File too large: {$fileSize} bytes");
            }

            Log::info('Repairing mesh', [
                'file' => $fileName,
                'size' => $fileSize,
                'aggressive' => $aggressive
            ]);

            $startTime = microtime(true);

            // Send to repair service
            $response = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($filePath), $fileName)
                ->post("{$this->serviceUrl}/api/repair", [
                    'aggressive' => $aggressive ? 'true' : 'false',
                    'return_file' => 'false'
                ]);

            $repairTime = round(microtime(true) - $startTime, 2);

            if (!$response->successful()) {
                throw new Exception("Mesh repair failed: {$response->body()}");
            }

            $data = $response->json();
            $data['repair_time_seconds'] = $repairTime;

            Log::info('Mesh repair complete', [
                'file' => $fileName,
                'time' => $repairTime,
                'volume_change' => $data['volume_change_cm3'],
                'holes_filled' => $data['repair_summary']['holes_filled'],
                'watertight' => $data['repaired_stats']['is_watertight']
            ]);

            return $data;

        } catch (Exception $e) {
            Log::error('Mesh repair error', [
                'file' => $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Repair mesh and download repaired file
     *
     * @param UploadedFile|string $file File object or path
     * @param bool $aggressive Use aggressive repair
     * @param string $outputPath Where to save repaired file
     * @return array Repair metadata from headers
     * @throws Exception
     */
    public function repairAndDownload($file, bool $aggressive, string $outputPath): array
    {
        try {
            $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
            $fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);

            Log::info('Repairing and downloading mesh', ['file' => $fileName]);

            // Send to repair service
            $response = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($filePath), $fileName)
                ->post("{$this->serviceUrl}/api/repair-download", [
                    'aggressive' => $aggressive ? 'true' : 'false'
                ]);

            if (!$response->successful()) {
                throw new Exception("Mesh repair-download failed: {$response->body()}");
            }

            // Save repaired file
            file_put_contents($outputPath, $response->body());

            // Extract metadata from headers
            $metadata = [
                'volume_original' => $response->header('X-Volume-Original'),
                'volume_repaired' => $response->header('X-Volume-Repaired'),
                'volume_change' => $response->header('X-Volume-Change'),
                'output_path' => $outputPath
            ];

            Log::info('Mesh downloaded', ['path' => $outputPath, 'metadata' => $metadata]);

            return $metadata;

        } catch (Exception $e) {
            Log::error('Mesh repair-download error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Calculate quality score from repair results
     *
     * @param array $repairData Repair result data
     * @return float Quality score 0-100
     */
    public function calculateQualityScore(array $repairData): float
    {
        $score = 100.0;

        // Deduct points if not watertight
        if (!$repairData['repaired_stats']['is_watertight']) {
            $score -= 30;
        }

        // Deduct points if not manifold
        if (!$repairData['repaired_stats']['is_manifold']) {
            $score -= 20;
        }

        // Deduct points for remaining holes
        $remainingHoles = $repairData['repaired_stats']['holes_count'] ?? 0;
        if ($remainingHoles > 0) {
            $score -= min(20, $remainingHoles * 2);
        }

        // Deduct points for excessive volume change
        $volumeChangePercent = abs($repairData['volume_change_percent'] ?? 0);
        if ($volumeChangePercent > 10) {
            $score -= min(15, ($volumeChangePercent - 10) * 2);
        }

        // Deduct points if multiple components (should be one solid)
        $components = $repairData['repaired_stats']['connected_components'] ?? 1;
        if ($components > 1) {
            $score -= min(15, ($components - 1) * 5);
        }

        return max(0, min(100, round($score, 2)));
    }

    /**
     * Get repair recommendations based on analysis
     *
     * @param array $analysisData Analysis result
     * @return array Recommendations
     */
    public function getRepairRecommendations(array $analysisData): array
    {
        $recommendations = [];

        if (!$analysisData['is_watertight']) {
            $recommendations[] = [
                'severity' => 'high',
                'message' => "Model is not watertight ({$analysisData['holes_count']} holes detected)",
                'action' => 'Use aggressive repair mode'
            ];
        }

        if (!$analysisData['is_manifold']) {
            $recommendations[] = [
                'severity' => 'high',
                'message' => 'Model has non-manifold geometry',
                'action' => 'Repair required for 3D printing'
            ];
        }

        if ($analysisData['connected_components'] > 1) {
            $recommendations[] = [
                'severity' => 'medium',
                'message' => "Model has {$analysisData['connected_components']} separate parts",
                'action' => 'Consider if multiple parts are intended'
            ];
        }

        $genus = $analysisData['genus'] ?? 0;
        if ($genus > 0) {
            $recommendations[] = [
                'severity' => 'low',
                'message' => "Model has complex topology (genus: {$genus})",
                'action' => 'Review repair results carefully'
            ];
        }

        if ($analysisData['volume_cm3'] < 0.1) {
            $recommendations[] = [
                'severity' => 'low',
                'message' => 'Very small volume detected',
                'action' => 'Verify model scale is correct'
            ];
        }

        return $recommendations;
    }
}
