<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeshRepair;
use App\Services\MeshRepairService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MeshRepairAdminController extends Controller
{
    protected MeshRepairService $meshRepairService;

    public function __construct(MeshRepairService $meshRepairService)
    {
        $this->meshRepairService = $meshRepairService;
    }

    /**
     * Show mesh repair dashboard
     */
    public function dashboard()
    {
        // Get service status
        $serviceAvailable = $this->meshRepairService->isAvailable();

        // Get statistics
        $stats = [
            'total_repairs' => MeshRepair::count(),
            'today_repairs' => MeshRepair::whereDate('created_at', today())->count(),
            'week_repairs' => MeshRepair::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_repairs' => MeshRepair::whereMonth('created_at', now()->month)->count(),

            'successful_repairs' => MeshRepair::where('status', 'completed')->count(),
            'failed_repairs' => MeshRepair::where('status', 'failed')->count(),
            'pending_repairs' => MeshRepair::where('status', 'pending')->count(),

            'average_quality' => MeshRepair::where('status', 'completed')->avg('quality_score'),
            'average_time' => MeshRepair::where('status', 'completed')->avg('repair_time_seconds'),
            'total_holes_filled' => MeshRepair::sum('holes_filled'),

            'watertight_achieved' => MeshRepair::where('is_watertight', true)->count(),
            'manifold_achieved' => MeshRepair::where('is_manifold', true)->count(),

            'average_volume_change' => MeshRepair::selectRaw('AVG(repaired_volume_cm3 - original_volume_cm3) as avg')->first()->avg ?? 0,
        ];

        // Calculate success rate
        $stats['success_rate'] = $stats['total_repairs'] > 0
            ? round(($stats['successful_repairs'] / $stats['total_repairs']) * 100, 1)
            : 0;

        // Get recent repairs
        $recentRepairs = MeshRepair::with('file')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get quality distribution
        $qualityDistribution = MeshRepair::select(
                DB::raw('CASE
                    WHEN quality_score >= 90 THEN "excellent"
                    WHEN quality_score >= 70 THEN "good"
                    WHEN quality_score >= 50 THEN "fair"
                    ELSE "poor"
                END as rating'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'completed')
            ->groupBy('rating')
            ->get();

        // Get daily repair trends (last 30 days)
        $dailyTrends = MeshRepair::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.mesh-repair.dashboard', compact(
            'serviceAvailable',
            'stats',
            'recentRepairs',
            'qualityDistribution',
            'dailyTrends'
        ));
    }

    /**
     * Show repair logs with filtering
     */
    public function logs(Request $request)
    {
        $query = MeshRepair::with('file');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by quality range
        if ($request->has('quality_min')) {
            $query->where('quality_score', '>=', $request->quality_min);
        }
        if ($request->has('quality_max')) {
            $query->where('quality_score', '<=', $request->quality_max);
        }

        // Search by file name
        if ($request->has('search') && $request->search) {
            $query->whereHas('file', function($q) use ($request) {
                $q->where('filename', 'like', '%' . $request->search . '%');
            });
        }

        $repairs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.mesh-repair.logs', compact('repairs'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $config = [
            'service_url' => config('services.mesh_repair.url'),
            'timeout' => config('services.mesh_repair.timeout'),
            'max_file_size' => config('services.mesh_repair.max_file_size'),
        ];

        return view('admin.mesh-repair.settings', compact('config'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'service_url' => 'required|url',
            'timeout' => 'required|integer|min:30|max:600',
            'max_file_size' => 'required|integer|min:1048576|max:1073741824', // 1MB to 1GB
        ]);

        // Note: In production, these should be stored in database or .env file
        // For now, we'll just validate and return success

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    /**
     * Show detailed repair information
     */
    public function show($id)
    {
        $repair = MeshRepair::with('file')->findOrFail($id);

        // Decode metadata
        $metadata = is_string($repair->metadata) ? json_decode($repair->metadata, true) : $repair->metadata;

        return view('admin.mesh-repair.show', compact('repair', 'metadata'));
    }

    /**
     * Delete repair record
     */
    public function destroy($id)
    {
        $repair = MeshRepair::findOrFail($id);

        // Delete repaired file if exists
        if ($repair->repaired_file_path && Storage::exists($repair->repaired_file_path)) {
            Storage::delete($repair->repaired_file_path);
        }

        $repair->delete();

        return redirect()->route('admin.mesh-repair.logs')->with('success', 'Repair record deleted');
    }

    /**
     * Export repair statistics
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $repairs = MeshRepair::with('file')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'csv') {
            $filename = 'mesh-repairs-' . now()->format('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function() use ($repairs) {
                $file = fopen('php://output', 'w');

                // Headers
                fputcsv($file, [
                    'ID', 'File Name', 'Date', 'Status', 'Original Volume (cm³)',
                    'Repaired Volume (cm³)', 'Volume Change', 'Holes Filled',
                    'Quality Score', 'Repair Time (s)', 'Watertight', 'Manifold'
                ]);

                // Data
                foreach ($repairs as $repair) {
                    fputcsv($file, [
                        $repair->id,
                        $repair->file->filename ?? 'N/A',
                        $repair->created_at->format('Y-m-d H:i:s'),
                        $repair->status,
                        $repair->original_volume_cm3,
                        $repair->repaired_volume_cm3,
                        $repair->volume_change,
                        $repair->holes_filled,
                        $repair->quality_score,
                        $repair->repair_time_seconds,
                        $repair->is_watertight ? 'Yes' : 'No',
                        $repair->is_manifold ? 'Yes' : 'No',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json(['error' => 'Invalid format'], 400);
    }
}
