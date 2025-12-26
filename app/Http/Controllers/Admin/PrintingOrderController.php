<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintingOrder;
use App\Traits\MailTrait;
use App\Traits\GlobalInfoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrintingOrderController extends Controller
{
    use MailTrait, GlobalInfoTrait;
    /**
     * Display a listing of the printing orders
     */
    public function index(Request $request)
    {
        $query = PrintingOrder::with(['user', 'address', 'quote']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.printing-orders.index', compact('orders'));
    }

    /**
     * Display the specified printing order
     */
    public function show($id)
    {
        $order = PrintingOrder::with(['user', 'address', 'quote'])->findOrFail($id);
        
        return view('admin.printing-orders.show', compact('order'));
    }

    /**
     * Update the order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,printing,out_for_delivery,delivered,completed,cancelled,on_hold'
        ]);

        $order = PrintingOrder::with('user')->findOrFail($id);
        $order->status = $request->status;
        $order->save();

        Log::info('Order status updated', [
            'order_id' => $id,
            'order_number' => $order->order_number,
            'new_status' => $request->status
        ]);

        // Send status update email to customer
        try {
            $this->sendStatusUpdateEmail($order);
        } catch (\Exception $e) {
            Log::error('❌ Error sending status update email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    /**
     * Update the payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,success,rejected,refund'
        ]);

        $order = PrintingOrder::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();

        Log::info('Payment status updated', [
            'order_id' => $id,
            'order_number' => $order->order_number,
            'new_payment_status' => $request->payment_status
        ]);

        return redirect()->back()->with('success', 'Payment status updated successfully');
    }

    /**
     * Add admin notes to the order
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:2000'
        ]);

        $order = PrintingOrder::findOrFail($id);
        $order->notes = $request->notes;
        $order->save();

        return redirect()->back()->with('success', 'Notes updated successfully');
    }

    /**
     * Display the invoice for the specified printing order
     */
    public function invoice($id)
    {
        $order = PrintingOrder::with(['user', 'address', 'quote'])->findOrFail($id);
        return view('frontend.printing-orders.invoice', compact('order'));
    }

    /**
     * Delete the specified order
     */
    public function destroy($id)
    {
        $order = PrintingOrder::findOrFail($id);
        $orderNumber = $order->order_number;
        
        $order->delete();

        Log::info('Printing order deleted', [
            'order_id' => $id,
            'order_number' => $orderNumber
        ]);

        return redirect()->route('admin.printing-orders.index')
            ->with('success', 'Order deleted successfully');
    }

    /**
     * Send order status update email to customer
     */
    protected function sendStatusUpdateEmail($order)
    {
        $user = $order->user;
        if (!$user || !$user->email) {
            Log::warning('⚠️ Cannot send email: User or email not found for order ' . $order->order_number);
            return;
        }

        $str_replace = [
            'user_name' => $user->name,
            'order_number' => $order->order_number,
            'status' => ucfirst(str_replace('_', ' ', $order->status)),
            'total_files' => $order->total_files,
            'total_volume' => number_format($order->total_volume, 2),
            'total_price' => number_format($order->total_price, 2),
            'company_name' => cache()->get('setting')->app_name ?? config('app.name'),
        ];

        $this->set_mail_config();
        [$mail_subject, $mail_message] = $this->buildEmail('printing_order_status', $str_replace);

        if ($mail_subject && $mail_message) {
            $this->send($user->email, $mail_subject, $mail_message);
            Log::info('✅ Status update email sent to ' . $user->email);
        } else {
            Log::warning('⚠️ Email template "printing_order_status" not found');
        }
    }
}
