<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PrintingOrder;
use Illuminate\Http\Request;

class UserPrintingOrderController extends Controller
{
    /**
     * Display a listing of the user's printing orders
     */
    public function index(Request $request)
    {
        $orders = PrintingOrder::with(['address', 'quote'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.printing-orders.index', compact('orders'));
    }

    /**
     * Display the specified printing order
     */
    public function show($id)
    {
        $order = PrintingOrder::with(['address', 'quote'])
            ->where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->firstOrFail();
        
        return view('frontend.printing-orders.show', compact('order'));
    }

    /**
     * Display the invoice for the specified printing order
     */
    public function invoice($id)
    {
        $order = PrintingOrder::with(['user', 'address', 'quote'])
            ->where('user_id', auth()->user()->id)
            ->where('id', $id)
            ->firstOrFail();
        
        return view('frontend.printing-orders.invoice', compact('order'));
    }
}
