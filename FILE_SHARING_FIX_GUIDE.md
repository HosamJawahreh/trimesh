# File Sharing Fix - Testing & Debugging Guide

## 🔧 What Was Fixed

### 1. **Moved API Routes to `routes/api.php`**
   - **Problem:** Routes were in `web.php` with CSRF protection
   - **Solution:** Moved to `api.php` for stateless API access
   - **Result:** No more CSRF 419 errors on file uploads

### 2. **Enhanced Controller Logging**
   - Added comprehensive logging for every step
   - Logs request details, validation, file sizes, paths
   - Shows full error traces with file/line numbers

### 3. **Better Error Handling**
   - Validates base64 format properly
   - Creates directories if missing
   - Verifies file storage success
   - Returns detailed error messages

## 🧪 Testing Steps

### Step 1: Clear Cache and Optimize Routes
```bash
cd /home/hjawahreh/Desktop/Projects/Trimesh

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
```

### Step 2: Verify Storage Permissions
```bash
# Make sure storage is writable
chmod -R 775 storage/app/public/shared-3d-files
chown -R $USER:www-data storage/app/public/shared-3d-files

# Check current permissions
ls -la storage/app/public/shared-3d-files/
```

### Step 3: Check Routes
```bash
# Verify API routes are registered
php artisan route:list | grep 3d-files
```

You should see:
```
POST   api/3d-files/store ............... 3d-files.store
GET    api/3d-files/{fileId} ............ 3d-files.show
POST   api/3d-files/{fileId}/camera ..... 3d-files.updateCamera
POST   api/3d-files/cleanup-expired ..... 3d-files.cleanup
```

### Step 4: Monitor Logs in Real-Time
Open a new terminal and run:
```bash
cd /home/hjawahreh/Desktop/Projects/Trimesh
tail -f storage/logs/laravel.log
```

Keep this terminal open to see logs as you upload files.

### Step 5: Upload a Test File

1. Open your application in browser
2. Go to the 3D viewer/quote page
3. Upload a 3D file (STL or OBJ)
4. Watch the browser console (F12) for logs
5. Watch the Laravel log terminal for server logs

### Step 6: Verify File was Saved

```bash
# Check if file exists on server
ls -lh storage/app/public/shared-3d-files/$(date +%Y-%m-%d)/

# Check database record
php artisan tinker
>>> $file = App\Models\ThreeDFile::latest()->first();
>>> echo "File ID: " . $file->file_id . "\n";
>>> echo "File Name: " . $file->file_name . "\n";
>>> echo "File Size: " . ($file->file_size / 1024 / 1024) . " MB\n";
>>> echo "Expires: " . $file->expiry_time . "\n";
>>> echo "File Path: " . $file->file_path . "\n";
>>> exit
```

### Step 7: Test File Sharing

1. After uploading, get the share link
2. Open in a NEW **incognito/private** browser window
3. Or test in a completely different browser
4. The file should load correctly

### Step 8: Verify File is Accessible
```bash
# Test the API endpoint directly
curl http://your-domain.test/api/3d-files/FILE_ID_HERE

# Should return JSON with file data
```

## 📊 Debugging Common Issues

### Issue 1: "File not found" when sharing
**Check:**
```bash
# Are files in the directory?
ls -R storage/app/public/shared-3d-files/

# Is storage linked?
ls -la public/storage
php artisan storage:link

# Check database
php artisan tinker
>>> App\Models\ThreeDFile::count();
```

### Issue 2: Upload fails silently
**Check Browser Console (F12):**
- Look for red errors
- Check Network tab for failed requests
- Note the HTTP status code (404, 419, 500, etc.)

**Check Laravel Logs:**
```bash
tail -50 storage/logs/laravel.log | grep "3D File Upload"
```

### Issue 3: CSRF Token errors (419)
**Solution:**
```bash
# Make sure routes are in api.php, not web.php
grep -n "3d-files" routes/api.php
grep -n "3d-files" routes/web.php  # Should return nothing

# Clear route cache
php artisan route:clear
php artisan route:cache
```

### Issue 4: Permission denied errors
**Solution:**
```bash
# Fix storage permissions
sudo chown -R $USER:www-data storage/
sudo chmod -R 775 storage/
```

### Issue 5: File size too large
**Check:**
```bash
# PHP upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Nginx limits (if using nginx)
grep client_max_body_size /etc/nginx/nginx.conf
```

**Increase limits in `php.ini`:**
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 512M
```

## 🔍 Detailed Log Analysis

### What to Look For in Logs

**SUCCESS Pattern:**
```
=== 3D FILE UPLOAD START ===
Request Method: POST
Request Has file: YES
Request Has fileName: YES
3D File Upload - Generated ID: file_1234567890_xxxxx
3D File Upload - Decoded size: 2458624 bytes (2.34 MB)
3D File Upload - File stored: shared-3d-files/2025-12-15/file_xxxxx.dat
3D File Upload - File exists: YES
3D File Upload - Metadata stored: ...
3D File Upload - Database record created: ID 1
=== 3D FILE UPLOAD SUCCESS ===
```

**FAILURE Patterns:**

1. **Validation Error:**
```
Validation failed: {"file":["The file field is required."]}
```
→ JavaScript not sending file data

2. **Invalid Base64:**
```
3D File Upload - Invalid base64 data
```
→ File encoding problem

3. **Storage Error:**
```
3D File Upload - Failed to store file!
```
→ Permission or disk space issue

4. **Database Error:**
```
SQLSTATE[42S02]: Base table or view not found
```
→ Run `php artisan migrate`

## 🎯 Quick Test Commands

### Test 1: Can PHP write to storage?
```bash
php -r "file_put_contents('storage/app/public/test.txt', 'test'); echo 'Success: ' . file_exists('storage/app/public/test.txt');"
```

### Test 2: Is database connected?
```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected!';"
```

### Test 3: Are routes working?
```bash
curl -X POST http://localhost:8000/api/3d-files/store \
  -H "Content-Type: application/json" \
  -d '{"file":"dGVzdA==","fileName":"test.stl"}'
```

### Test 4: Check table exists
```bash
php artisan tinker --execute="echo Schema::hasTable('three_d_files') ? 'YES' : 'NO';"
```

## 📝 Manual Test Upload

If frontend isn't working, test API directly:

```bash
# Create test base64 file
echo "test file content" | base64 > /tmp/test_base64.txt

# Send to API
curl -X POST http://localhost:8000/api/3d-files/store \
  -H "Content-Type: application/json" \
  -d "{\"file\":\"$(cat /tmp/test_base64.txt)\",\"fileName\":\"test.stl\"}"
```

## 🚀 Production Checklist

Before deploying to production:

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan storage:link`
- [ ] Set correct storage permissions (775)
- [ ] Enable scheduler in crontab
- [ ] Check PHP upload limits
- [ ] Test file upload
- [ ] Test file sharing across browsers
- [ ] Verify cleanup command works
- [ ] Monitor logs for errors

## 📞 Getting Help

If still not working, collect this info:

1. **Laravel Log:**
```bash
tail -100 storage/logs/laravel.log > debug_laravel.log
```

2. **Browser Console:**
   - Open DevTools (F12)
   - Copy all errors from Console tab
   - Copy failed request from Network tab

3. **System Info:**
```bash
php -v
php artisan --version
ls -la storage/app/public/shared-3d-files/
php artisan route:list | grep 3d-files
```

4. **Database Check:**
```bash
php artisan tinker
>>> App\Models\ThreeDFile::all();
>>> exit
```

Share all this information for faster debugging!

## ✅ Success Indicators

You'll know it's working when:

1. ✅ File appears in `storage/app/public/shared-3d-files/YYYY-MM-DD/`
2. ✅ Database record created (check with tinker)
3. ✅ Share link opens file in different browser
4. ✅ No errors in Laravel logs
5. ✅ Browser console shows "File uploaded to server"

## 🎉 Next Steps After Success

1. Test the cleanup command:
```bash
php artisan threed:cleanup-expired --dry-run
```

2. Enable automatic cleanup (add to crontab):
```bash
crontab -e
# Add:
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

3. Test file expiry:
```bash
php artisan tinker
>>> $file = App\Models\ThreeDFile::latest()->first();
>>> $file->update(['expiry_time' => now()->subHour()]);
>>> exit
php artisan threed:cleanup-expired
```

Good luck! 🚀
