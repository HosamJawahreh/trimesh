# 🌐 Global File Sharing Feature

## Overview
The 3D Quote System now supports **global file sharing** - files can be shared across devices, browsers, and users. When you share a link, anyone with that link can view your 3D model with the exact camera position you saved.

## 🎯 Key Features

### 1. **Hybrid Storage Architecture**
- **Local Storage**: Files saved to IndexedDB for instant access
- **Server Storage**: Files also saved to server for global accessibility
- **Smart Loading**: Checks local cache first, then falls back to server

### 2. **Automatic Synchronization**
- Files uploaded → Saved locally + uploaded to server
- Camera moved → Saved locally + synced to server
- Link shared → Recipients load from server seamlessly

### 3. **72-Hour Expiry**
- Both local and server files expire after 72 hours
- Automatic cleanup prevents storage bloat
- Expired files are gracefully removed

---

## 📁 Technical Implementation

### Backend (Laravel)

#### **Controller**: `app/Http/Controllers/ThreeDFileController.php`

```php
// Store 3D file on server
POST /api/3d-files/store
Request: { fileName, fileData (base64), metadata }
Response: { success, fileId, message }

// Retrieve 3D file from server
GET /api/3d-files/{fileId}
Response: { success, fileId, fileName, fileData, cameraState, ... }

// Update camera state
POST /api/3d-files/{fileId}/camera
Request: { cameraState: { position, rotation, zoom } }
Response: { success, message }

// Cleanup expired files (scheduled)
POST /api/3d-files/cleanup-expired
Response: { success, deletedCount }
```

#### **Storage Location**
```
storage/app/public/shared-3d-files/
├── 2024-01-15/
│   ├── abc123def456.dat  (Binary file data)
│   ├── abc123def456.json (Metadata + camera state)
│   ├── xyz789uvw123.dat
│   └── xyz789uvw123.json
└── 2024-01-16/
    └── ...
```

#### **Routes**: `routes/web.php`
```php
Route::prefix('api/3d-files')->group(function () {
    Route::post('/store', [ThreeDFileController::class, 'store']);
    Route::get('/{fileId}', [ThreeDFileController::class, 'show']);
    Route::post('/{fileId}/camera', [ThreeDFileController::class, 'updateCamera']);
    Route::post('/cleanup-expired', [ThreeDFileController::class, 'cleanupExpired']);
});
```

---

### Frontend (JavaScript)

#### **File Manager**: `public/frontend/assets/js/file-storage-manager.js`

##### **Key Methods**

**1. Save File (Hybrid)**
```javascript
async saveFile(file, metadata)
├── Save to IndexedDB (local)
└── saveToServer(fileId, fileData, fileName, metadata)
    └── POST /api/3d-files/store
```

**2. Load File (Smart Fallback)**
```javascript
async loadFile(fileId)
├── Try IndexedDB first (fast local cache)
└── If not found → loadFromServer(fileId)
    ├── GET /api/3d-files/{fileId}
    └── Cache locally for future use
```

**3. Sync Camera State**
```javascript
async updateEdits(fileId, 'camera', cameraState)
├── Update IndexedDB (local)
└── syncCameraToServer(fileId, cameraState)
    └── POST /api/3d-files/{fileId}/camera
```

##### **Data Flow**

```
┌──────────────────────────────────────────────────────┐
│                    USER UPLOADS FILE                  │
└──────────────────┬───────────────────────────────────┘
                   │
                   ├─> IndexedDB (Browser Storage)
                   │   ✓ Instant local access
                   │   ✓ 72-hour expiry
                   │
                   └─> Server Storage (Laravel)
                       ✓ Global accessibility
                       ✓ 72-hour expiry
                       ✓ /shared-3d-files/{date}/{id}.dat+.json

┌──────────────────────────────────────────────────────┐
│                  USER SHARES LINK                     │
└──────────────────┬───────────────────────────────────┘
                   │
                   ├─> Copy: /quote-viewer?file={fileId}
                   │
                   └─> Recipient Opens Link
                       │
                       ├─> Check IndexedDB
                       │   └─> Not found (different device)
                       │
                       └─> Fetch from Server
                           └─> GET /api/3d-files/{fileId}
                               └─> Load model + camera state
```

---

## 🔧 Configuration

### **CSRF Token**
All POST requests require CSRF token (automatically included):
```html
<!-- In master layout -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### **Storage Symlink**
Ensure storage is linked for public access:
```bash
php artisan storage:link
```

### **Scheduled Cleanup** (Optional)
Add to `app/Console/Kernel.php` for automatic cleanup:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        app(\App\Http\Controllers\ThreeDFileController::class)->cleanupExpired();
    })->daily();
}
```

---

## 🧪 Testing Global Sharing

### **Test Scenario 1: Same Device, Different Browser**
1. Upload file in Chrome → Share link
2. Open link in Firefox → File loads from server
3. ✅ Model renders with saved camera position

### **Test Scenario 2: Different Device**
1. Upload file on Desktop → Share link
2. Open link on Mobile → File loads from server
3. ✅ Model renders with saved camera position

### **Test Scenario 3: Camera State Sync**
1. Upload file → Rotate camera → Share link
2. Recipient opens link
3. ✅ Camera shows exact rotation from step 1

### **Test Scenario 4: Expiry Handling**
1. Upload file
2. Wait 72+ hours (or manually delete server files)
3. Try loading → Gracefully shows "File not found"

---

## 🎨 UI/UX Features

### **Active Button Styling**
- Active mode button: **Primary Blue (#4a90e2)**
- Inactive buttons: Gray with hover effect
- CSS-based (no inline style conflicts)

### **Mode Switching**
- **General Mode**: Blue gradient background
- **Medical Mode**: Gray gradient background
- Smooth transitions between modes
- Form content updates dynamically

### **Auto-Rotate Behavior**
- Disabled by default (no spinning until file uploaded)
- Enabled automatically when model loads
- Can be toggled with auto-rotate button

---

## 📊 Browser Console Logs

When sharing works correctly, you'll see:
```
💾 File saved to IndexedDB: abc123def456
☁️ File uploaded to server: abc123def456
📡 File not found locally, trying server...
✅ File loaded from server
💾 File cached locally from server
☁️ Camera state synced to server
```

---

## 🚀 Benefits

1. **Universal Access**: Share 3D models with anyone, anywhere
2. **No Account Required**: Recipients don't need to sign up
3. **Persistent Camera**: Exact view saved and shared
4. **Performance**: Local cache prevents re-downloading
5. **Privacy**: 72-hour auto-expiry protects user data
6. **Reliability**: Fallback system ensures files always load

---

## 🔐 Security Considerations

- **CSRF Protection**: All POST requests validated
- **File ID Validation**: Server checks file existence and expiry
- **No User Data**: Files stored with random IDs, no personal info
- **Auto Cleanup**: Expired files deleted automatically
- **Public Storage**: Files are publicly accessible (by design for sharing)

---

## 🛠️ Maintenance

### **Manual Cleanup**
```bash
php artisan tinker
>>> app(\App\Http\Controllers\ThreeDFileController::class)->cleanupExpired();
```

### **Check Storage Usage**
```bash
du -sh storage/app/public/shared-3d-files/
```

### **Clear All Shared Files** (if needed)
```bash
rm -rf storage/app/public/shared-3d-files/*
```

---

## 📝 Changelog

### **v1.0** - Global Sharing Implementation
- ✅ Server-side file storage with 72-hour expiry
- ✅ Hybrid storage (IndexedDB + Server)
- ✅ Camera state synchronization
- ✅ Smart fallback loading (local → server)
- ✅ Active button primary blue styling
- ✅ Mode switching fixes (removed inline styles)
- ✅ Auto-rotate disabled until file upload

---

## 🤝 Related Files

- `app/Http/Controllers/ThreeDFileController.php` - Server-side storage logic
- `routes/web.php` - API endpoints
- `public/frontend/assets/js/file-storage-manager.js` - Hybrid storage manager
- `resources/views/frontend/pages/quote.blade.php` - 3D viewer interface
- `resources/views/frontend/pages/quote-viewer.blade.php` - Quote form with tabs

---

**Feature Status**: ✅ **Complete and Ready for Testing**
