# 🚨 URGENT FIX: Installer Redirect on Live Server

## Your Issue
**URL:** https://trimesh.brand-makers.net/
**Problem:** Redirects to `/installer/welcome` even though app is uploaded correctly

---

## ⚡ FASTEST SOLUTION (5 Minutes)

### Option 1: Upload & Run Fix Script

1. **Upload `fix-installer.php` to your live server root**
   ```
   /public_html/fix-installer.php
   ```

2. **Visit in browser:**
   ```
   https://trimesh.brand-makers.net/fix-installer.php
   ```

3. **You should see:**
   ```
   ✅✅✅ SUCCESS! ✅✅✅
   The app is now marked as INSTALLED.
   ```

4. **Delete the fix file:**
   ```bash
   rm /public_html/fix-installer.php
   ```

5. **Clear browser cache and visit:**
   ```
   https://trimesh.brand-makers.net/
   ```

**DONE! ✅**

---

## 📋 ALTERNATIVE: Direct Database Fix (phpMyAdmin)

If fix script doesn't work, run this SQL:

```sql
-- Step 1: Check if configurations table exists
SHOW TABLES LIKE 'configurations';

-- Step 2: Check current status
SELECT * FROM configurations WHERE `key` = 'is_installed';

-- Step 3a: If record exists, update it
UPDATE configurations 
SET `value` = '1', `updated_at` = NOW() 
WHERE `key` = 'is_installed';

-- Step 3b: If record doesn't exist, insert it
INSERT INTO configurations (`key`, `value`, `created_at`, `updated_at`) 
VALUES ('is_installed', '1', NOW(), NOW());

-- Step 4: Verify
SELECT * FROM configurations WHERE `key` = 'is_installed';
-- Should show: is_installed = 1

-- Step 5: Clear cache (optional)
DELETE FROM cache WHERE `key` LIKE 'config:%';
```

---

## 🔧 PERMANENT FIX: Disable Installer Check

Edit this file on your live server:
```
/Modules/Installer/app/Http/Middleware/SetupMiddleware.php
```

Find line 28:
```php
if(!$isInstalled){
    return redirect()->route('installer.welcome');
}
```

Change to:
```php
if(false && !$isInstalled){  // DISABLED
    return redirect()->route('installer.welcome');
}
```

Or comment out:
```php
// if(!$isInstalled){
//     return redirect()->route('installer.welcome');
// }
```

---

## 🐛 TROUBLESHOOTING

### Error: "Table 'configurations' doesn't exist"

**Solution:** Run migrations
```bash
# SSH to server
php artisan migrate --force
```

Or manually create table in phpMyAdmin:
```sql
CREATE TABLE IF NOT EXISTS `configurations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configurations_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO configurations (`key`, `value`, `created_at`, `updated_at`) 
VALUES ('is_installed', '1', NOW(), NOW());
```

### Error: "Database connection failed"

**Check .env file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Make sure:
- ✅ Database exists
- ✅ User has permissions
- ✅ Host is correct (usually `localhost` or `127.0.0.1`)

### Still Redirecting?

**Clear all caches:**
```bash
# SSH to server
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Or delete cache files manually
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
```

**Check file permissions:**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
# OR (depending on server)
chown -R your_user:your_user storage bootstrap/cache
```

---

## 📦 DEPLOYMENT CHECKLIST

Make sure you've uploaded ALL these:

**Required Files:**
- ✅ `.env` (with correct database credentials)
- ✅ `/vendor/` folder (run `composer install` on server)
- ✅ `/app/` folder
- ✅ `/config/` folder
- ✅ `/public/` folder
- ✅ `/storage/` folder (with write permissions)
- ✅ `/bootstrap/cache/` (with write permissions)
- ✅ `/database/migrations/` folder

**After Upload:**
```bash
# SSH to your server
cd /path/to/trimesh

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ✅ EXPECTED RESULTS

After applying fix:

1. **Homepage loads:**
   ```
   https://trimesh.brand-makers.net/ ✅
   ```

2. **Quote page works:**
   ```
   https://trimesh.brand-makers.net/quote ✅
   ```

3. **Admin panel accessible:**
   ```
   https://trimesh.brand-makers.net/admin/login ✅
   ```

4. **NO installer redirect!** ✅

---

## 🎯 QUICK SUMMARY

**The Problem:**
Laravel checks `configurations` table for `is_installed = 1`. If not found, it redirects to installer.

**The Solution:**
Set `is_installed = 1` in the `configurations` table using:
- Option 1: `fix-installer.php` script ⚡ (Easiest)
- Option 2: Direct SQL in phpMyAdmin 🗄️
- Option 3: Disable middleware check 🔧

**Choose the easiest option for you!**

---

## 📞 STILL NEED HELP?

**Share these details:**
1. Error message from `fix-installer.php`
2. Result of: `SELECT * FROM configurations WHERE key = 'is_installed';`
3. Your `.env` database settings (hide password)
4. PHP version on server: `php -v`
5. Laravel version: `php artisan --version`

---

**Most likely solution: Just run fix-installer.php and it's done in 2 minutes! 🚀**
