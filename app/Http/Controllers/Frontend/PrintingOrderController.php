<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PrintingOrder;
use App\Models\PrintingOrderAddress;
use App\Traits\MailTrait;
use App\Traits\GlobalInfoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrintingOrderController extends Controller
{
    use MailTrait, GlobalInfoTrait;
    
    /**
     * Show the order review page
     */
    public function review(Request $request)
    {
        // Handle POST request to save quote data to session
        if ($request->isMethod('post')) {
            $quoteData = $request->input('quote_data');
            
            \Log::info('📥 Received quote data:', $quoteData);
            
            // Ensure numeric values are properly cast
            if (isset($quoteData['total_price'])) {
                $quoteData['total_price'] = (float) $quoteData['total_price'];
            }
            if (isset($quoteData['total_volume'])) {
                $quoteData['total_volume'] = (float) $quoteData['total_volume'];
            }
            if (isset($quoteData['total_files'])) {
                $quoteData['total_files'] = (int) $quoteData['total_files'];
            }
            
            // Cast file values
            if (isset($quoteData['files']) && is_array($quoteData['files'])) {
                foreach ($quoteData['files'] as &$file) {
                    if (isset($file['volume'])) {
                        $file['volume'] = (float) $file['volume'];
                    }
                    if (isset($file['price'])) {
                        $file['price'] = (float) $file['price'];
                    }
                }
            }
            
            \Log::info('✅ Processed quote data:', $quoteData);
            
            session(['quote_data' => $quoteData]);
            return response()->json(['success' => true]);
        }
        
        // Get quote data from session
        $quoteData = session('quote_data');
        
        \Log::info('📂 Quote data from session:', $quoteData ?? ['empty' => true]);
        
        if (!$quoteData) {
            return redirect()->route('quote.viewer')->with('error', 'No quote data found. Please calculate your quote first.');
        }
        
        return view('frontend.pages.printing-order-review', compact('quoteData'));
    }

    /**
     * Store the printing order
     */
    public function store(Request $request)
    {
        // Check authentication
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to submit an order.');
        }

        $validated = $request->validate([
            'quote_id' => 'nullable|exists:quotes,id',
            'viewer_type' => 'required|in:general,medical,dental',
            'viewer_link' => 'nullable|url',
            'total_price' => 'required|numeric|min:0',
            'total_volume' => 'required|numeric|min:0',
            'total_files' => 'required|integer|min:1',
            'technology' => 'nullable|string',
            'material' => 'nullable|string',
            'color' => 'nullable|string',
            'quality' => 'nullable|string',
            'files_data' => 'required|string',
            'customer_note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,paypal',
            
            // Shipping address fields (all optional for logged-in users)
            'shipping_first_name' => 'nullable|string|max:255',
            'shipping_last_name' => 'nullable|string|max:255',
            'shipping_email' => 'nullable|email|max:255',
            'shipping_phone' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:500',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_province' => 'nullable|string|max:255',
            'shipping_zip_code' => 'nullable|string|max:255',
            'shipping_country' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $order = PrintingOrder::create([
                'order_number' => PrintingOrder::generateOrderNumber(),
                'user_id' => auth()->id(),
                'quote_id' => $validated['quote_id'] ?? null,
                'viewer_type' => $validated['viewer_type'],
                'viewer_link' => $validated['viewer_link'],
                'total_price' => $validated['total_price'],
                'total_volume' => $validated['total_volume'],
                'total_files' => $validated['total_files'],
                'technology' => $validated['technology'] ?? null,
                'material' => $validated['material'] ?? null,
                'color' => $validated['color'] ?? null,
                'quality' => $validated['quality'] ?? null,
                'files_data' => json_decode($validated['files_data'], true),
                'customer_note' => $validated['customer_note'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'shipping_method' => 'standard',
                'shipping_charge' => 0, // Can be calculated based on location
                'status' => 'pending'
            ]);

            Log::info('✅ Printing order created:', ['order_number' => $order->order_number, 'id' => $order->id]);

            // Create shipping address only if shipping info provided
            if ($validated['shipping_first_name'] || $validated['shipping_email']) {
                PrintingOrderAddress::create([
                    'printing_order_id' => $order->id,
                    'shipping_first_name' => $validated['shipping_first_name'] ?? '',
                    'shipping_last_name' => $validated['shipping_last_name'] ?? '',
                    'shipping_email' => $validated['shipping_email'] ?? '',
                    'shipping_phone' => $validated['shipping_phone'] ?? '',
                    'shipping_address' => $validated['shipping_address'] ?? '',
                    'shipping_city' => $validated['shipping_city'] ?? '',
                    'shipping_province' => $validated['shipping_province'] ?? '',
                    'shipping_zip_code' => $validated['shipping_zip_code'] ?? '',
                    'shipping_country' => $validated['shipping_country'] ?? '',
                    // Billing same as shipping for now
                    'billing_first_name' => $validated['shipping_first_name'] ?? '',
                    'billing_last_name' => $validated['shipping_last_name'] ?? '',
                    'billing_email' => $validated['shipping_email'] ?? '',
                    'billing_phone' => $validated['shipping_phone'] ?? '',
                    'billing_address' => $validated['shipping_address'] ?? '',
                    'billing_city' => $validated['shipping_city'] ?? '',
                    'billing_province' => $validated['shipping_province'] ?? '',
                    'billing_zip_code' => $validated['shipping_zip_code'] ?? '',
                    'billing_country' => $validated['shipping_country'] ?? '',
                ]);
            }


            DB::commit();

            // Send order confirmation email
            try {
                $this->sendOrderConfirmationEmail($order);
            } catch (\Exception $e) {
                Log::error('❌ Error sending order confirmation email: ' . $e->getMessage());
            }

            // Clear session data
            session()->forget('quote_data');

            return redirect()->route('printing-order.success', $order->id)
                ->with('success', 'Your printing order has been submitted successfully!');
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error creating printing order: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Failed to submit order. Please try again.');
        }
    }

    /**
     * Show success page after order submission
     */
    public function success($id)
    {
        $order = PrintingOrder::findOrFail($id);
        
        // Check if user owns this order (or is admin)
        if ($order->user_id && $order->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }
        
        return view('frontend.pages.printing-order-success', compact('order'));
    }

    /**
     * Send order confirmation email to customer
     */
    protected function sendOrderConfirmationEmail($order)
    {
        $user = $order->user;
        if (!$user || !$user->email) {
            Log::warning('⚠️ Cannot send email: User or email not found for order ' . $order->order_number);
            return;
        }

        // Build files list HTML
        $filesHtml = '<ul>';
        $files = $order->files_data ?? [];
        foreach ($files as $file) {
            $filesHtml .= '<li><strong>' . ($file['name'] ?? 'File') . '</strong> - ' 
                        . ($file['technology'] ?? 'N/A') . ' / ' 
                        . ($file['material'] ?? 'N/A') . ' / ' 
                        . number_format($file['volume'] ?? 0, 2) . ' cm³ - $' 
                        . number_format($file['price'] ?? 0, 2) . '</li>';
        }
        $filesHtml .= '</ul>';

        $str_replace = [
            'user_name' => $user->name,
            'order_number' => $order->order_number,
            'viewer_type' => ucfirst($order->viewer_type),
            'total_files' => $order->total_files,
            'total_volume' => number_format($order->total_volume, 2),
            'total_price' => number_format($order->total_price, 2),
            'payment_method' => ucfirst(str_replace('_', ' ', $order->payment_method)),
            'payment_status' => ucfirst($order->payment_status),
            'status' => ucfirst(str_replace('_', ' ', $order->status)),
            'order_date' => $order->created_at->format('F d, Y h:i A'),
            'files_list' => $filesHtml,
            'company_name' => cache()->get('setting')->app_name ?? config('app.name'),
        ];

        $this->set_mail_config();
        [$mail_subject, $mail_message] = $this->buildEmail('printing_order_confirmation', $str_replace);

        if ($mail_subject && $mail_message) {
            $this->send($user->email, $mail_subject, $mail_message);
            Log::info('✅ Order confirmation email sent to ' . $user->email);
        } else {
            Log::warning('⚠️ Email template "printing_order_confirmation" not found');
        }
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
