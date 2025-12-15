<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ThreeDFileController extends Controller
{
    /**
     * Store a 3D file and return a shareable ID
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required',
                'fileName' => 'required|string',
                'cameraState' => 'nullable|json',
                'metadata' => 'nullable|json'
            ]);

            // Generate unique ID
            $fileId = 'file_' . time() . '_' . Str::random(12);
            
            // Decode base64 file data
            $fileData = $request->input('file');
            if (preg_match('/^data:([^;]+);base64,(.+)$/', $fileData, $matches)) {
                $fileData = base64_decode($matches[2]);
            } elseif (base64_decode($fileData, true) !== false) {
                $fileData = base64_decode($fileData);
            }

            // Create directory structure
            $directory = 'shared-3d-files/' . date('Y-m-d');
            
            // Store the file
            $filePath = $directory . '/' . $fileId . '.dat';
            Storage::disk('public')->put($filePath, $fileData);

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

            return response()->json([
                'success' => true,
                'fileId' => $fileId,
                'expiryTime' => $metadata['expiryTime'],
                'message' => 'File uploaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a 3D file by ID
     */
    public function show($fileId)
    {
        try {
            // Find the file in storage
            $files = Storage::disk('public')->files('shared-3d-files');
            $metadataPath = null;
            
            foreach ($files as $file) {
                if (str_contains($file, $fileId) && str_ends_with($file, '.json')) {
                    $metadataPath = $file;
                    break;
                }
            }

            if (!$metadataPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Get metadata
            $metadata = json_decode(Storage::disk('public')->get($metadataPath), true);

            // Check if expired
            if (time() * 1000 > $metadata['expiryTime']) {
                // Delete expired file
                $this->deleteFile($fileId);
                return response()->json([
                    'success' => false,
                    'message' => 'File has expired'
                ], 410);
            }

            // Get file data
            $filePath = str_replace('.json', '.dat', $metadataPath);
            $fileData = Storage::disk('public')->get($filePath);

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

            // Find metadata file
            $files = Storage::disk('public')->files('shared-3d-files');
            $metadataPath = null;
            
            foreach ($files as $file) {
                if (str_contains($file, $fileId) && str_ends_with($file, '.json')) {
                    $metadataPath = $file;
                    break;
                }
            }

            if (!$metadataPath) {
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
     * Delete a file
     */
    private function deleteFile($fileId)
    {
        $files = Storage::disk('public')->files('shared-3d-files');
        
        foreach ($files as $file) {
            if (str_contains($file, $fileId)) {
                Storage::disk('public')->delete($file);
            }
        }
    }

    /**
     * Clean up expired files (can be called by scheduler)
     */
    public function cleanupExpired()
    {
        try {
            $files = Storage::disk('public')->files('shared-3d-files');
            $deletedCount = 0;

            foreach ($files as $file) {
                if (str_ends_with($file, '.json')) {
                    $metadata = json_decode(Storage::disk('public')->get($file), true);
                    
                    if (time() * 1000 > $metadata['expiryTime']) {
                        $fileId = $metadata['id'];
                        $this->deleteFile($fileId);
                        $deletedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} expired files"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
