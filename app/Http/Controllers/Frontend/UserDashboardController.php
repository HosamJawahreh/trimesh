<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PrintingOrder;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Show the user dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's printing orders
        $printingOrders = PrintingOrder::with(['address', 'quote'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Pass to existing dashboard with printing orders
        return view('frontend.dashboard.index', compact('printingOrders'));
    }

    /**
     * Show specific printing order details
     */
    public function showOrder($id)
    {
        $order = PrintingOrder::with(['address', 'quote'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        
        return view('frontend.pages.printing-order-detail', compact('order'));
    }
}
