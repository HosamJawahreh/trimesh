<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\ThreeDFile;

class ThreeDFileController extends Controller
{
    /**
     * Store a 3D file and return a shareable ID
     */
    public function store(Request $request)
    {
        try {
            Log::info('=== 3D FILE UPLOAD START ===');
            Log::info('Request Method: ' . $request->method());
            Log::info('Request Headers: ' . json_encode($request->headers->all()));
            Log::info('Request Content-Type: ' . $request->header('Content-Type'));
            Log::info('Request Size: ' . strlen($request->getContent()) . ' bytes');
            Log::info('Request Has file: ' . ($request->has('file') ? 'YES' : 'NO'));
            Log::info('Request Has fileName: ' . ($request->has('fileName') ? 'YES' : 'NO'));

            // Validate request
            $validator = validator($request->all(), [
                'file' => 'required',
                'fileName' => 'required|string',
                'cameraState' => 'nullable|string',
                'metadata' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed: ' . json_encode($validator->errors()->all()));
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate unique ID
            $fileId = 'file_' . time() . '_' . Str::random(12);
            Log::info('3D File Upload - Generated ID: ' . $fileId);

            // Decode base64 file data
            $fileData = $request->input('file');
            Log::info('3D File Upload - Raw file data length: ' . strlen($fileData));

            if (preg_match('/^data:([^;]+);base64,(.+)$/', $fileData, $matches)) {
                Log::info('3D File Upload - Detected data URL format');
                $fileData = base64_decode($matches[2]);
            } elseif (base64_decode($fileData, true) !== false) {
                Log::info('3D File Upload - Detected plain base64 format');
                $fileData = base64_decode($fileData);
            } else {
                Log::error('3D File Upload - Invalid base64 data');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file data format'
                ], 400);
            }

            Log::info('3D File Upload - Decoded size: ' . strlen($fileData) . ' bytes (' . round(strlen($fileData) / 1024 / 1024, 2) . ' MB)');

            // Create directory structure
            $directory = 'shared-3d-files/' . date('Y-m-d');

            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
                Log::info('3D File Upload - Created directory: ' . $directory);
            }

            // Store the file
            $filePath = $directory . '/' . $fileId . '.dat';
            $stored = Storage::disk('public')->put($filePath, $fileData);

            if (!$stored) {
                Log::error('3D File Upload - Failed to store file!');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save file to storage'
                ], 500);
            }

            $fullPath = Storage::disk('public')->path($filePath);
            Log::info('3D File Upload - File stored: ' . $filePath);
            Log::info('3D File Upload - Full path: ' . $fullPath);
            Log::info('3D File Upload - File exists: ' . (file_exists($fullPath) ? 'YES' : 'NO'));

            // Store metadata
            $metadata = [
                'id' => $fileId,
                'fileName' => $request->input('fileName'),
                'uploadTime' => time() * 1000, // JavaScript timestamp
                'expiryTime' => (time() + (72 * 60 * 60)) * 1000, // 72 hours
                'cameraState' => $request->input('cameraState') ? json_decode($request->input('cameraState'), true) : null,
                'metadata' => $request->input('metadata') ? json_decode($request->input('metadata'), true) : null,
                'fileSize' => strlen($fileData)
            ];

            $metadataPath = $directory . '/' . $fileId . '.json';
            Storage::disk('public')->put($metadataPath, json_encode($metadata));
            Log::info('3D File Upload - Metadata stored: ' . $metadataPath);

            // Store file information in database
            $expiryTime = now()->addHours(72);
            $dbRecord = ThreeDFile::create([
                'file_id' => $fileId,
                'file_path' => $filePath,
                'metadata_path' => $metadataPath,
                'file_name' => $request->input('fileName'),
                'file_size' => strlen($fileData),
                'mime_type' => 'application/octet-stream',
                'expiry_time' => $expiryTime,
            ]);
            Log::info('3D File Upload - Database record created: ID ' . $dbRecord->id);
            Log::info('=== 3D FILE UPLOAD SUCCESS ===');

            return response()->json([
                'success' => true,
                'fileId' => $fileId,
                'expiryTime' => $metadata['expiryTime'],
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('=== 3D FILE UPLOAD ERROR ===');
            Log::error('3D File Upload - Error: ' . $e->getMessage());
            Log::error('3D File Upload - File: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('3D File Upload - Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a 3D file by ID
     */
    public function show($fileId)
    {
        try {
            Log::info('3D File Retrieve - Request for file ID: ' . $fileId);

            // Find file in database
            $fileRecord = ThreeDFile::where('file_id', $fileId)->first();

            if (!$fileRecord) {
                Log::warning('3D File Retrieve - File not found in database: ' . $fileId);
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Check if expired
            if ($fileRecord->isExpired()) {
                Log::info('3D File Retrieve - File expired, deleting: ' . $fileId);
                $this->deleteFileRecord($fileRecord);
                return response()->json([
                    'success' => false,
                    'message' => 'File has expired'
                ], 410);
            }

            // Update last accessed time
            $fileRecord->markAccessed();

            // Get metadata from file
            if (!Storage::disk('public')->exists($fileRecord->metadata_path)) {
                Log::error('3D File Retrieve - Metadata file missing: ' . $fileRecord->metadata_path);
                return response()->json([
                    'success' => false,
                    'message' => 'File metadata not found'
                ], 404);
            }

            $metadata = json_decode(Storage::disk('public')->get($fileRecord->metadata_path), true);

            // Get file data
            if (!Storage::disk('public')->exists($fileRecord->file_path)) {
                Log::error('3D File Retrieve - Data file missing: ' . $fileRecord->file_path);
                return response()->json([
                    'success' => false,
                    'message' => 'File data not found'
                ], 404);
            }

            $fileData = Storage::disk('public')->get($fileRecord->file_path);

            // Encode to base64
            $base64Data = base64_encode($fileData);

            return response()->json([
                'success' => true,
                'fileId' => $metadata['id'],
                'fileName' => $metadata['fileName'],
                'fileData' => $base64Data,
                'uploadTime' => $metadata['uploadTime'],
                'expiryTime' => $metadata['expiryTime'],
                'cameraState' => $metadata['cameraState'] ?? null,
                'metadata' => $metadata['metadata'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('3D File Retrieve - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update camera state for a file
     */
    public function updateCamera(Request $request, $fileId)
    {
        try {
            $request->validate([
                'cameraState' => 'required|json'
            ]);

            Log::info('3D File Camera Update - Request for file ID: ' . $fileId);

            // Find metadata file - search recursively through all subdirectories
            $files = Storage::disk('public')->allFiles('shared-3d-files');
            $metadataPath = null;

            foreach ($files as $file) {
                if (str_contains($file, $fileId) && str_ends_with($file, '.json')) {
                    $metadataPath = $file;
                    Log::info('3D File Camera Update - Found metadata: ' . $metadataPath);
                    break;
                }
            }

            if (!$metadataPath) {
                Log::warning('3D File Camera Update - File not found: ' . $fileId);
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Update metadata
            $metadata = json_decode(Storage::disk('public')->get($metadataPath), true);
            $metadata['cameraState'] = json_decode($request->input('cameraState'), true);

            Storage::disk('public')->put($metadataPath, json_encode($metadata));

            return response()->json([
                'success' => true,
                'message' => 'Camera state updated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update camera state: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a file by ID (legacy method - searches filesystem)
     */
    private function deleteFile($fileId)
    {
        // Search recursively through all subdirectories
        $files = Storage::disk('public')->allFiles('shared-3d-files');

        foreach ($files as $file) {
            if (str_contains($file, $fileId)) {
                Storage::disk('public')->delete($file);
                Log::info('3D File Delete - Deleted: ' . $file);
            }
        }
    }

    /**
     * Delete a file record and its physical files
     */
    private function deleteFileRecord(ThreeDFile $fileRecord)
    {
        try {
            // Delete physical files
            if (Storage::disk('public')->exists($fileRecord->file_path)) {
                Storage::disk('public')->delete($fileRecord->file_path);
                Log::info('3D File Delete - Deleted file: ' . $fileRecord->file_path);
            }

            if (Storage::disk('public')->exists($fileRecord->metadata_path)) {
                Storage::disk('public')->delete($fileRecord->metadata_path);
                Log::info('3D File Delete - Deleted metadata: ' . $fileRecord->metadata_path);
            }

            // Delete database record
            $fileRecord->delete();
            Log::info('3D File Delete - Deleted DB record: ' . $fileRecord->file_id);

        } catch (\Exception $e) {
            Log::error('3D File Delete - Error: ' . $e->getMessage());
        }
    }

    /**
     * Clean up expired files (can be called by scheduler)
     */
    public function cleanupExpired()
    {
        try {
            // Get all expired files from database
            $expiredFiles = ThreeDFile::expired()->get();
            $deletedCount = 0;

            foreach ($expiredFiles as $fileRecord) {
                $this->deleteFileRecord($fileRecord);
                $deletedCount++;
            }

            Log::info("3D File Cleanup - Deleted {$deletedCount} expired files");

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} expired files"
            ]);

        } catch (\Exception $e) {
            Log::error('3D File Cleanup - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
