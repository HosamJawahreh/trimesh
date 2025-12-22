<?php
/**
 * BYPASS INSTALLER - RUN THIS ONCE ON LIVE SERVER
 * 
 * Instructions:
 * 1. Upload this file to your live server root: https://trimesh.brand-makers.net/fix-installer.php
 * 2. Visit: https://trimesh.brand-makers.net/fix-installer.php
 * 3. Delete this file after running
 */

// Load Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get database connection
    $db = DB::connection();
    
    echo "<h1>Fixing Installer Redirect Issue</h1>";
    echo "<pre>";
    
    // Check if configurations table exists
    echo "Checking configurations table...\n";
    $tableExists = $db->select("SHOW TABLES LIKE 'configurations'");
    
    if (empty($tableExists)) {
        echo "❌ ERROR: configurations table does not exist!\n";
        echo "Run: php artisan migrate\n";
        exit;
    }
    
    echo "✅ Table exists\n\n";
    
    // Check current installation status
    echo "Checking current installation status...\n";
    $current = $db->table('configurations')->where('key', 'is_installed')->first();
    
    if ($current) {
        echo "Current status: {$current->value}\n";
        if ($current->value == '1') {
            echo "✅ Already marked as installed!\n\n";
        } else {
            echo "Updating to installed...\n";
            $db->table('configurations')
                ->where('key', 'is_installed')
                ->update(['value' => '1']);
            echo "✅ Updated to installed\n\n";
        }
    } else {
        echo "No installation record found, creating...\n";
        $db->table('configurations')->insert([
            'key' => 'is_installed',
            'value' => '1',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Installation record created\n\n";
    }
    
    // Also set admin_email if not exists
    echo "Checking admin_email...\n";
    $adminEmail = $db->table('configurations')->where('key', 'admin_email')->first();
    
    if (!$adminEmail) {
        $db->table('configurations')->insert([
            'key' => 'admin_email',
            'value' => 'admin@trimesh.brand-makers.net',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Admin email set\n\n";
    } else {
        echo "✅ Admin email already exists: {$adminEmail->value}\n\n";
    }
    
    // Verify final status
    echo "Final verification...\n";
    $final = $db->table('configurations')->where('key', 'is_installed')->first();
    
    if ($final && $final->value == '1') {
        echo "✅✅✅ SUCCESS! ✅✅✅\n\n";
        echo "The app is now marked as INSTALLED.\n";
        echo "Clear your browser cache (Ctrl+Shift+R)\n";
        echo "Visit: https://trimesh.brand-makers.net/\n\n";
        echo "⚠️ IMPORTANT: Delete this file (fix-installer.php) for security!\n";
    } else {
        echo "❌ ERROR: Something went wrong!\n";
        echo "Check database connection and permissions.\n";
    }
    
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>ERROR</h1>";
    echo "<pre>";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
