<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RepairLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RepairLogController extends Controller
{
    /**
     * Store a new repair log
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request for debugging
            Log::info('Repair log store request received', [
                'data' => $request->all()
            ]);

            $validated = $request->validate([
                'filename' => 'required|string|max:255',
                'original_file_path' => 'required|string',
                'repaired_file_path' => 'required|string',
                'holes_filled' => 'required|integer|min:0',
                'original_volume_cm3' => 'required|numeric|min:0',
                'repaired_volume_cm3' => 'required|numeric|min:0',
                'volume_change_cm3' => 'required|numeric',
                'volume_change_percent' => 'required|numeric',
                'original_vertices' => 'required|integer|min:0',
                'repaired_vertices' => 'required|integer|min:0',
                'original_faces' => 'required|integer|min:0',
                'repaired_faces' => 'required|integer|min:0',
                'watertight_achieved' => 'required|boolean',
                'repair_method' => 'nullable|string|max:50',
                'repair_notes' => 'nullable|string|max:1000'
            ]);

            $repairLog = RepairLog::create($validated);

            Log::info('Repair log saved successfully', [
                'id' => $repairLog->id,
                'filename' => $repairLog->filename,
                'holes_filled' => $repairLog->holes_filled
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Repair log saved successfully',
                'data' => $repairLog
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for repair log', [
                'errors' => $e->errors(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to save repair log', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save repair log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all repair logs for admin dashboard
     */
    public function index(Request $request)
    {
        try {
            $query = RepairLog::query()->orderBy('created_at', 'desc');

            // Optional filtering
            if ($request->has('watertight')) {
                $query->where('watertight_achieved', $request->watertight);
            }

            $logs = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch repair logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single repair log
     */
    public function show($id)
    {
        try {
            $log = RepairLog::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $log
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Repair log not found'
            ], 404);
        }
    }
}
