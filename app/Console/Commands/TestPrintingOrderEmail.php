<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrintingOrder;
use App\Models\User;

class TestPrintingOrderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-printing-order {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test printing order confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('🔧 Testing printing order email...');
        $this->info('📧 Sending to: ' . $email);
        
        try {
            // Find or create a test user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->warn('⚠️ User not found with email: ' . $email);
                $this->info('Creating temporary test user...');
                
                $user = User::create([
                    'name' => 'Test User',
                    'email' => $email,
                    'password' => bcrypt('test123'),
                    'email_verified_at' => now(),
                ]);
                
                $this->info('✅ Test user created');
            }
            
            // Create a mock printing order and save it
            $order = PrintingOrder::create([
                'order_number' => 'TEST-' . strtoupper(uniqid()),
                'user_id' => $user->id,
                'viewer_type' => 'dental',
                'viewer_link' => 'https://example.com/quote?viewer=dental',
                'total_price' => 125.50,
                'total_volume' => 45.75,
                'total_files' => 2,
                'technology' => 'FDM',
                'material' => 'Dental Resin',
                'color' => 'White',
                'quality' => 'High',
                'files_data' => [
                    [
                        'name' => 'dental_crown.stl',
                        'technology' => 'FDM',
                        'material' => 'Dental Resin',
                        'volume' => 22.50,
                        'price' => 62.75
                    ],
                    [
                        'name' => 'dental_bridge.stl',
                        'technology' => 'FDM',
                        'material' => 'Dental Resin',
                        'volume' => 23.25,
                        'price' => 62.75
                    ]
                ],
                'customer_note' => 'Test order for email verification',
                'payment_method' => 'cash_on_delivery',
                'payment_status' => 'pending',
                'shipping_method' => 'standard',
                'shipping_charge' => 0,
                'status' => 'pending',
            ]);
            
            $this->info('✅ Test order created with ID: ' . $order->id);
            
            // Get the controller
            $controller = app(\App\Http\Controllers\Frontend\PrintingOrderController::class);
            
            // Use reflection to call the protected method
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('sendOrderConfirmationEmail');
            $method->setAccessible(true);
            
            $this->info('📤 Sending order confirmation email...');
            $method->invoke($controller, $order);
            
            $this->info('✅ Test printing order email sent successfully!');
            $this->info('📬 Please check the inbox for: ' . $email);
            $this->info('📁 Also check spam/junk folder if not in inbox');
            $this->info('');
            $this->info('Order Details:');
            $this->info('  Order Number: ' . $order->order_number);
            $this->info('  Viewer Type: ' . $order->viewer_type);
            $this->info('  Total Files: ' . $order->total_files);
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
