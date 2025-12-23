<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepairLog;
use Illuminate\Http\Request;

class RepairLogAdminController extends Controller
{
    /**
     * Display repair logs dashboard
     */
    public function index()
    {
        $logs = RepairLog::orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $stats = [
            'total_repairs' => RepairLog::count(),
            'total_holes_filled' => RepairLog::sum('holes_filled'),
            'watertight_achieved' => RepairLog::where('watertight_achieved', true)->count(),
            'avg_volume_change' => RepairLog::avg('volume_change_percent'),
        ];
        
        return view('admin.repair-logs.index', compact('logs', 'stats'));
    }

    /**
     * Display single repair log details
     */
    public function show($id)
    {
        $log = RepairLog::findOrFail($id);
        return view('admin.repair-logs.show', compact('log'));
    }

    /**
     * Delete a repair log
     */
    public function destroy($id)
    {
        try {
            $log = RepairLog::findOrFail($id);
            $log->delete();
            
            return redirect()->route('admin.repair-logs.index')
                ->with('success', 'Repair log deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.repair-logs.index')
                ->with('error', 'Failed to delete repair log');
        }
    }
}
