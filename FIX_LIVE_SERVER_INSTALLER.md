# FIX INSTALLER REDIRECT - LIVE SERVER

## Problem
Your live server at `https://trimesh.brand-makers.net/` redirects to `/installer/welcome` because Laravel thinks the app is not installed.

## Root Cause
The app checks the `configurations` table for `is_installed = 1`. If not found or set to 0, it redirects to installer.

---

## 🚀 QUICK FIX (2 Minutes)

### Step 1: Upload Fix Script
1. Download `fix-installer.php` from your local project
2. Upload to live server root: `/public_html/fix-installer.php`
3. Make sure `.env` file is also uploaded with correct database credentials

### Step 2: Run Fix Script
Visit in browser:
```
https://trimesh.brand-makers.net/fix-installer.php
```

You should see:
```
✅✅✅ SUCCESS! ✅✅✅
The app is now marked as INSTALLED.
```

### Step 3: Delete Fix Script
**IMPORTANT FOR SECURITY:**
Delete `fix-installer.php` from your server after running!

### Step 4: Clear Cache & Test
1. Clear browser cache (Ctrl + Shift + R)
2. Visit: `https://trimesh.brand-makers.net/`
3. Should load normally without installer redirect!

---

## 📋 ALTERNATIVE FIX: Direct Database

If the script doesn't work, run this SQL directly in phpMyAdmin:

```sql
-- Check if record exists
SELECT * FROM configurations WHERE `key` = 'is_installed';

-- If exists, update it
UPDATE configurations SET `value` = '1' WHERE `key` = 'is_installed';

-- If doesn't exist, insert it
INSERT INTO configurations (`key`, `value`, `created_at`, `updated_at`) 
VALUES ('is_installed', '1', NOW(), NOW());

-- Verify
SELECT * FROM configurations WHERE `key` = 'is_installed';
-- Should show: is_installed | 1
```

---

## 🔧 PERMANENT FIX: Disable Installer Middleware

Edit `/app/Http/Middleware/SetupMiddleware.php`:

Find this line (around line 19):
```php
if (!app_setup()) {
    return redirect()->route('LaravelInstaller::welcome');
}
```

Change to:
```php
if (false && !app_setup()) {  // Disabled installer check
    return redirect()->route('LaravelInstaller::welcome');
}
```

Or comment it out:
```php
// if (!app_setup()) {
//     return redirect()->route('LaravelInstaller::welcome');
// }
```

---

## ✅ VERIFY IT WORKED

After applying fix:

1. **Visit homepage:**
   ```
   https://trimesh.brand-makers.net/
   ```
   Should load normally (not redirect to installer)

2. **Check database:**
   ```sql
   SELECT * FROM configurations WHERE `key` = 'is_installed';
   ```
   Should return: `is_installed = 1`

3. **Check admin panel:**
   ```
   https://trimesh.brand-makers.net/admin/login
   ```
   Should load login page

---

## 🐛 TROUBLESHOOTING

### Still Redirecting?

**Check 1: .env file uploaded?**
```bash
# SSH to server
ls -la .env
# Should exist with correct DB credentials
```

**Check 2: Database connection working?**
Visit:
```
https://trimesh.brand-makers.net/fix-installer.php
```
Check error messages

**Check 3: Clear Laravel cache**
```bash
# SSH to server
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

**Check 4: File permissions**
```bash
# Storage and cache need write permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Fix Script Shows Error?

**Database connection error:**
- Check `.env` file has correct DB credentials
- Verify database exists
- Check DB user has permissions

**Table not found:**
```bash
# Run migrations
php artisan migrate
```

---

## 📦 FILES TO UPLOAD

Make sure these files are on live server:

**Critical Files:**
- ✅ `.env` (with correct database credentials)
- ✅ All PHP files in `/app/`
- ✅ All files in `/vendor/` (run `composer install` on server)
- ✅ All files in `/public/`
- ✅ Database migrations in `/database/migrations/`

**After upload, run:**
```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

---

## 🎯 QUICK COMMAND SUMMARY

```bash
# 1. Upload files
# 2. SSH to server, then:

cd /path/to/trimesh
composer install --no-dev
php artisan migrate --force
php artisan config:clear
php artisan cache:clear

# 3. Visit fix-installer.php in browser
# 4. Delete fix-installer.php
# 5. Test homepage
```

---

## ✨ EXPECTED RESULT

After fix:
- ✅ Homepage loads: `https://trimesh.brand-makers.net/`
- ✅ Quote page works: `https://trimesh.brand-makers.net/quote`
- ✅ Admin login: `https://trimesh.brand-makers.net/admin/login`
- ❌ NO installer redirect!

---

**Need help? Check the error message from fix-installer.php and share it!**
