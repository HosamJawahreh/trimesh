<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\GlobalMail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('🔧 Testing email configuration...');
        $this->info('📧 Sending test email to: ' . $email);
        
        try {
            // Test data
            $subject = 'Test Email from Trimesh 3D';
            $message = '<h2>Email Configuration Test</h2>
                        <p>This is a test email to verify that your email configuration is working correctly.</p>
                        <p><strong>Sent from:</strong> ' . config('mail.from.address') . '</p>
                        <p><strong>Mailer:</strong> ' . config('mail.default') . '</p>
                        <p><strong>Host:</strong> ' . config('mail.mailers.smtp.host') . '</p>
                        <p><strong>Port:</strong> ' . config('mail.mailers.smtp.port') . '</p>
                        <p><strong>Encryption:</strong> ' . config('mail.mailers.smtp.encryption') . '</p>
                        <p><strong>Date/Time:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
                        <hr>
                        <p style="color: green;"><strong>✅ If you received this email, your email configuration is working correctly!</strong></p>';
            
            $link = [];
            
            Mail::to($email)->send(new GlobalMail($subject, $message, $link));
            
            $this->info('✅ Test email sent successfully!');
            $this->info('📬 Please check the inbox for: ' . $email);
            $this->info('📁 Also check spam/junk folder if not in inbox');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            
            return 1;
        }
    }
}
