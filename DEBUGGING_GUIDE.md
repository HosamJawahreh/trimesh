# 🐛 DEBUGGING: File Upload & Measurement Issues

## Issue 1: Files Not Uploading to Server

### Symptoms
- Share link shows "File not found"
- Files requested but don't exist in database
- No upload logs in Laravel log

### Root Cause Analysis
The JavaScript `saveToServer()` function may be:
1. Failing silently (catch block hiding errors)
2. File too large (exceeds PHP/server limits)
3. Base64 encoding issue
4. Network/CORS issue

### Debug Steps

1. **Check Browser Console for Upload Errors:**
   - Open browser DevTools (F12)
   - Go to Console tab
   - Upload a file
   - Look for "❌ Server upload failed" or "❌ Failed to save to server"

2. **Check Network Tab:**
   - Open DevTools (F12) → Network tab
   - Upload a file
   - Look for POST to `/api/3d-files/store`
   - Check status code (should be 200, not 404/419/500)
   - Check request payload size

3. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep "3D File"
   ```
   Should show:
   ```
   === 3D FILE UPLOAD START ===
   Request Has file: YES
   === 3D FILE UPLOAD SUCCESS ===
   ```

### Quick Fixes

#### Fix 1: Increase PHP Upload Limits
```bash
# Check current limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Edit php.ini:
upload_max_filesize = 200M
post_max_size = 200M
max_execution_time = 300
memory_limit = 512M

# Restart PHP/Apache/Nginx
```

#### Fix 2: Clear Browser Cache
```
Hard refresh: Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)
```

#### Fix 3: Test Upload API Directly
```bash
# Create test base64 data
echo "test data" | base64

# Test API
curl -X POST http://127.0.0.1:8000/api/3d-files/store \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"file":"dGVzdCBkYXRhCg==","fileName":"test.stl"}'
```

Should return:
```json
{"success":true,"fileId":"file_xxx","message":"File uploaded successfully"}
```

## Issue 2: Measurement Tool Not Working

### Symptoms
- Clicking on model doesn't create measurement points
- No markers appear

### Root Cause Analysis
The measurement click handler may:
1. Not be initialized properly
2. Canvas not receiving click events  
3. Raycaster not finding meshes
4. THREE.js not loaded

### Debug Steps

1. **Check Console for Measurement Logs:**
   - Click "Measure" button
   - Should see: "✅ Measurement mode activated"
   - Click on model
   - Should see: "🖱️ Canvas clicked in measurement mode"
   - Should see: "🎯 Click raycasting against X meshes"

2. **Check if THREE.js is Loaded:**
   ```javascript
   console.log(window.THREE); // Should show THREE.js object
   console.log(window.viewerGeneral); // Should show viewer object
   ```

3. **Check if Canvas Exists:**
   ```javascript
   const viewer = window.viewerGeneral || window.viewerMedical;
   console.log(viewer.renderer.domElement); // Should show <canvas> element
   ```

### Quick Fixes

#### Fix 1: Ensure Measurement Button Triggers Mode
The button needs to call `toggleMeasurementMode()`. Check if button exists:
```javascript
document.querySelector('[data-action="measure"]')
```

#### Fix 2: Verify Click Handler is Set Up
After upload completes, should see:
```
✅ Measurement click handler setup complete on canvas
```

## Complete Test Procedure

### Step 1: Test File Upload
```bash
# In terminal 1: Monitor logs
cd /home/hjawahreh/Desktop/Projects/Trimesh
tail -f storage/logs/laravel.log | grep "3D File"

# In browser:
1. Open http://127.0.0.1:9000/quote
2. Open DevTools (F12) → Console
3. Upload a small STL file
4. Watch both console and terminal
```

**Expected Output (Browser Console):**
```
📤 Uploading file to server...
   File name: test.stl
   File size: 2.34 MB
   Base64 length: 3145728
   Making POST request to: /api/3d-files/store
   Response status: 200 OK
☁️ File uploaded to server: file_xxx
```

**Expected Output (Laravel Log):**
```
=== 3D FILE UPLOAD START ===
Request Has file: YES
3D File Upload - Generated ID: file_xxx
3D File Upload - Decoded size: 2458624 bytes
3D File Upload - File stored: shared-3d-files/2025-12-15/file_xxx.dat
3D File Upload - Database record created: ID 1
=== 3D FILE UPLOAD SUCCESS ===
```

### Step 2: Test File Sharing
```bash
# After upload, click "Share" button
# Copy the share link
# Open in NEW incognito window
# File should load
```

### Step 3: Test Measurement
```bash
# 1. Upload a file
# 2. Wait for upload to complete
# 3. Click "Measure" button (ruler icon)
# 4. Click TWO points on the model
# 5. Distance should appear
```

**Expected Console Output:**
```
✅ Measurement mode activated
🖱️ Canvas clicked in measurement mode
🎯 Click raycasting against 1 meshes
📍 Point 1: (x, y, z)
🖱️ Canvas clicked in measurement mode
🎯 Click raycasting against 1 meshes
📍 Point 2: (x, y, z)
📏 Distance: 25.45 mm
```

## If Still Not Working

### Collect Debug Info:

1. **Browser Console Output:**
   ```
   Right-click → Save as... → console.log
   ```

2. **Laravel Logs:**
   ```bash
   tail -100 storage/logs/laravel.log > debug-laravel.log
   ```

3. **Network Request:**
   - DevTools → Network → Find `/api/3d-files/store`
   - Right-click → Copy → Copy as cURL

4. **System Info:**
   ```bash
   php -v
   php -i | grep upload_max_filesize
   ls -lh storage/app/public/shared-3d-files/
   php artisan route:list | grep 3d-files
   ```

Share all these outputs for further debugging!
