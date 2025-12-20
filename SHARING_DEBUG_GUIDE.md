# 3D File Sharing - Debug Guide

## Issue
When sharing a 3D file link and opening it in another browser, you see "File not found or expired" error.

## Root Cause
Files are being saved to **IndexedDB (local browser storage)** but **NOT being uploaded to the server**. This means:
- ✅ Files work in the same browser (IndexedDB)
- ❌ Files DON'T work when shared to other browsers (no server copy)

## How to Debug

### Step 1: Check Browser Console
1. Upload a 3D file
2. Open browser Developer Tools (F12)
3. Look at the Console tab for these messages:

**If upload is WORKING, you'll see:**
```
📤 Uploading file to server...
   File name: model.stl
   File size: 2.34 MB
   Base64 length: 3123456
   CSRF token found: abcd1234...
   Making POST request to: /api/3d-files/store
   Response status: 200 OK
✅ File successfully uploaded to server for global sharing
   Server File ID: file_1234567890_abc123xyz
   🔗 This file can now be shared across browsers and devices!
```

**If upload is FAILING, you'll see:**
```
📤 Uploading file to server...
   ... (some info) ...
   Response status: 419 Unknown Status (or 500, 422, etc.)
❌ Server upload failed: Error: Server error: 419
   Error message: Server error: 419 - Page Expired
⚠️ Sharing will be local only (IndexedDB)
⚠️ The file is saved locally but CANNOT be shared with other browsers
```

### Step 2: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

Look for:
```
[2025-12-15 15:44:10] production.INFO: 3D File Upload - Request received
[2025-12-15 15:44:10] production.INFO: 3D File Upload - Generated ID: file_1734275050_abc123xyz
[2025-12-15 15:44:10] production.INFO: 3D File Upload - Decoded size: 2456789 bytes
[2025-12-15 15:44:10] production.INFO: 3D File Upload - File stored: shared-3d-files/2025-12-15/file_1734275050_abc123xyz.dat
[2025-12-15 15:44:10] production.INFO: 3D File Upload - Metadata stored: shared-3d-files/2025-12-15/file_1734275050_abc123xyz.json
```

**If you DON'T see these logs**, the request isn't reaching the server.

### Step 3: Check Network Tab
1. Open Developer Tools (F12)
2. Go to Network tab
3. Upload a file
4. Look for a POST request to `/api/3d-files/store`
5. Click on it and check:
   - **Status Code**: Should be `200`
   - **Response**: Should show `{"success":true,"fileId":"..."}` 
   - **Request Headers**: Should have `X-CSRF-TOKEN`

### Step 4: Verify Files on Server
```bash
# Check if files are being created
ls -la storage/app/public/shared-3d-files/$(date +%Y-%m-%d)/

# Should show files like:
# file_1734275050_abc123xyz.dat (the 3D file data)
# file_1734275050_abc123xyz.json (the metadata)
```

## Common Issues & Solutions

### Issue 1: CSRF Token Missing (419 Error)
**Symptom**: Console shows "CSRF token not found in page!"

**Solution**: Verify the page has CSRF token:
```html
<meta name="csrf-token" content="...">
```

### Issue 2: File Size Too Large (413 Error)
**Symptom**: Response status 413 or upload times out

**Solution**: Increase PHP upload limits in `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
```

### Issue 3: Storage Permission Error (500 Error)
**Symptom**: Laravel log shows "Permission denied"

**Solution**: Fix directory permissions:
```bash
chmod -R 775 storage/app/public/shared-3d-files
chown -R www-data:www-data storage/app/public/shared-3d-files
```

### Issue 4: Wrong API Endpoint (404 Error)
**Symptom**: Network tab shows 404 for `/api/3d-files/store`

**Solution**: Verify route exists:
```bash
php artisan route:list | grep "3d-files"
```

Should show:
```
POST   api/3d-files/store ........................ 3d-files.store
GET    api/3d-files/{fileId} ..................... 3d-files.show
```

## Testing Shared Links

### Test 1: Same Browser (Should Always Work)
1. Upload file in Chrome
2. Copy the URL (should have `?file=file_123...`)
3. Open new Chrome tab/window
4. Paste URL
5. ✅ File should load (from IndexedDB)

### Test 2: Different Browser (Requires Server Upload)
1. Upload file in Chrome
2. Copy the URL
3. Open Firefox (or different device)
4. Paste URL
5. ✅ File should load ONLY if server upload succeeded

### Test 3: Incognito/Private Mode (Requires Server Upload)
1. Upload file in normal Chrome
2. Copy the URL
3. Open Chrome Incognito window
4. Paste URL
5. ✅ File should load ONLY if server upload succeeded

## Enhanced Logging (Latest Changes)

The following improvements have been made:

1. **Detailed Console Logging**: Shows every step of the upload process
2. **User Notification**: Shows warning if server upload fails
3. **CSRF Token Verification**: Explicitly checks for CSRF token
4. **Response Status Logging**: Shows HTTP status codes
5. **Error Messages**: More descriptive error messages

## Quick Test Command

Run this to see if uploads are working:
```bash
# Watch logs and file creation simultaneously
watch -n 1 'echo "=== Laravel Logs ===" && tail -5 storage/logs/laravel.log && echo "" && echo "=== Files ===" && ls -lah storage/app/public/shared-3d-files/$(date +%Y-%m-%d)/ 2>/dev/null || echo "No files today"'
```

Then upload a file and watch the output update in real-time.

## Next Steps

1. **Upload a test file** and check the console for detailed logs
2. **Share the exact error message** you see (screenshot preferred)
3. **Check Laravel logs** for any server-side errors
4. **Verify files are created** in the storage directory

The enhanced logging will now tell you EXACTLY where the process is failing.
