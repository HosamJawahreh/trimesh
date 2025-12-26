<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrintingOrder;

class TestPrintingOrderStatusEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-printing-status {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test printing order status update email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('🔧 Testing printing order status update email...');
        $this->info('📧 Sending to: ' . $email);
        
        try {
            // Find the last test order we created
            $order = PrintingOrder::whereHas('user', function($query) use ($email) {
                $query->where('email', $email);
            })->latest()->first();
            
            if (!$order) {
                $this->error('❌ No order found for user: ' . $email);
                $this->info('💡 Please run: php artisan email:test-printing-order ' . $email . ' first');
                return 1;
            }
            
            $this->info('📦 Found order: ' . $order->order_number);
            
            // Update order status to simulate status change
            $oldStatus = $order->status;
            $order->status = 'processing';
            $order->save();
            
            $this->info('📝 Updated status from "' . $oldStatus . '" to "processing"');
            
            // Get the admin controller (which has the sendStatusUpdateEmail method)
            $controller = app(\App\Http\Controllers\Admin\PrintingOrderController::class);
            
            // Use reflection to call the protected method
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('sendStatusUpdateEmail');
            $method->setAccessible(true);
            
            $this->info('📤 Sending status update email...');
            $method->invoke($controller, $order);
            
            $this->info('✅ Test status update email sent successfully!');
            $this->info('📬 Please check the inbox for: ' . $email);
            $this->info('📁 Also check spam/junk folder if not in inbox');
            $this->info('');
            $this->info('Order Details:');
            $this->info('  Order Number: ' . $order->order_number);
            $this->info('  New Status: ' . $order->status);
            $this->info('  Total Price: $' . $order->total_price);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->error('');
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            
            return 1;
        }
    }
}
