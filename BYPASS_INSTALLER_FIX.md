# BYPASS INSTALLER - Live Server Fix

## Problem
Your live server is redirecting to `/installer/welcome` because Laravel thinks the app is not installed.

## Quick Fix (Run these commands on your live server)

### Method 1: Via Terminal/SSH
```bash
# Navigate to your project directory
cd /path/to/your/project

# Run this artisan command to mark app as installed
php artisan tinker --execute="
\Modules\Installer\Models\Configuration::updateOrCreate(
    ['key' => 'is_installed'],
    ['value' => '1', 'created_at' => now(), 'updated_at' => now()]
);
echo 'Installation marked as complete!' . PHP_EOL;
"
```

### Method 2: Via Database (phpMyAdmin or MySQL)
```sql
-- Check if configurations table exists
SHOW TABLES LIKE 'configurations';

-- If exists, insert or update the is_installed flag
INSERT INTO configurations (id, key, value, created_at, updated_at)
VALUES (NULL, 'is_installed', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE value = '1', updated_at = NOW();
```

### Method 3: Create a Bypass Route (Temporary)
Add this to your `routes/web.php` file temporarily:

```php
// Temporary installer bypass - REMOVE AFTER FIXING
Route::get('/bypass-installer-fix', function() {
    try {
        \Modules\Installer\Models\Configuration::updateOrCreate(
            ['key' => 'is_installed'],
            ['value' => '1']
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Installation marked as complete!',
            'redirect' => url('/')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
```

Then visit: `https://trimesh.brand-makers.net/bypass-installer-fix`

---

## What This Does

The installer checks this database record:
- **Table:** `configurations`
- **Key:** `is_installed`
- **Value:** `1` (installed) or `0` (not installed)

When you deployed to live server, this record was missing or set to `0`.

---

## After Fix

1. Visit: `https://trimesh.brand-makers.net`
2. Should go to homepage, NOT installer
3. You can remove the bypass route after confirming it works

---

## If Configurations Table Doesn't Exist

Run migrations first:
```bash
php artisan migrate --force
```

Then run the fix above.

---

## Prevention

Before deploying to production:
1. Export your local database
2. Import to live server
3. Or run: `php artisan db:seed` to seed initial data
