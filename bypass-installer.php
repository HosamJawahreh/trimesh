<?php

/**
 * BYPASS INSTALLER SCRIPT
 * Run this ONCE on your live server to mark the app as installed
 *
 * Usage:
 * 1. Upload this file to your live server root directory
 * 2. Visit: https://trimesh.brand-makers.net/bypass-installer.php
 * 3. Delete this file after running
 */

// Include Laravel bootstrap
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    // Boot Laravel
    $kernel->bootstrap();

    // Check if configurations table exists
    if (!Schema::hasTable('configurations')) {
        echo "❌ ERROR: configurations table doesn't exist!\n";
        echo "Run: php artisan migrate --force\n";
        exit(1);
    }

    // Mark app as installed
    DB::table('configurations')->updateOrInsert(
        ['key' => 'is_installed'],
        [
            'value' => '1',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );

    echo "✅ SUCCESS! App marked as installed.\n";
    echo "You can now visit: " . config('app.url') . "\n";
    echo "\n";
    echo "⚠️ IMPORTANT: Delete this file (bypass-installer.php) for security!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
