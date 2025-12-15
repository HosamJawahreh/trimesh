# 🧪 Quick Testing Guide - Global Sharing

## ✅ Test Checklist

### 1️⃣ **Upload and Local Save**
- [ ] Upload a 3D file (STL/OBJ/PLY)
- [ ] Check browser console for: `💾 File saved to IndexedDB`
- [ ] Check browser console for: `☁️ File uploaded to server`
- [ ] Verify auto-rotate is disabled until upload completes
- [ ] Verify auto-rotate enables after model loads

### 2️⃣ **Mode Switching**
- [ ] Click "General" tab
  - [ ] Verify blue gradient background appears
  - [ ] Verify "General" button turns blue (#4a90e2)
  - [ ] Verify general form fields show
  
- [ ] Click "Medical" tab
  - [ ] Verify gray gradient background appears
  - [ ] Verify "Medical" button turns blue (#4a90e2)
  - [ ] Verify medical form fields show
  - [ ] Verify all medical dropdowns update the form

### 3️⃣ **Camera State & Share**
- [ ] Upload file and rotate camera
- [ ] Click "Share" button
- [ ] Verify toast shows success message
- [ ] Verify link is copied to clipboard
- [ ] Check console for: `☁️ Camera state synced to server`

### 4️⃣ **Global Sharing - Same Device**
- [ ] Copy the shared link
- [ ] Open link in **incognito/private window**
- [ ] Check console for: `📡 File not found locally, trying server...`
- [ ] Check console for: `✅ File loaded from server`
- [ ] Verify model renders with same camera position
- [ ] Verify quote form loads correctly

### 5️⃣ **Global Sharing - Different Device** (Ultimate Test)
- [ ] Upload file on Device 1 (e.g., Desktop Chrome)
- [ ] Rotate camera and click Share
- [ ] Send link to Device 2 (e.g., Phone Safari)
- [ ] Open link on Device 2
- [ ] Verify model loads and renders
- [ ] Verify camera shows same angle as Device 1

### 6️⃣ **Error Handling**
- [ ] Try sharing before uploading file
  - [ ] Should show error: "Invalid file ID: null"
  
- [ ] Try accessing non-existent file ID
  - [ ] URL: `/quote-viewer?file=invalid123`
  - [ ] Should gracefully handle with "File not found"

### 7️⃣ **Button Styling**
- [ ] Verify active button has blue color (#4a90e2)
- [ ] Verify inactive buttons are gray
- [ ] Hover over inactive button → Should show hover effect
- [ ] No inline styles overriding CSS

---

## 📱 Browser Console Commands (for debugging)

### Check IndexedDB
```javascript
// Open browser DevTools → Application → IndexedDB → ThreeDFiles → files
// Should see your uploaded files
```

### Check Server Storage
```bash
# On server terminal
ls -la storage/app/public/shared-3d-files/$(date +%Y-%m-%d)/
# Should show .dat and .json files
```

### Force Server Load
```javascript
// In browser console, clear IndexedDB:
indexedDB.deleteDatabase('ThreeDFiles');
// Reload page → Should load from server
```

### Check CSRF Token
```javascript
// In browser console:
document.querySelector('meta[name="csrf-token"]').content
// Should show token value
```

---

## 🐛 Common Issues & Solutions

### Issue: "CSRF token mismatch"
**Solution**: Refresh page to get new token

### Issue: "File not found on server"
**Check**: 
1. Storage symlink: `php artisan storage:link`
2. File permissions: `chmod -R 775 storage/`
3. File actually uploaded (check console logs)

### Issue: "Network error" when uploading
**Check**:
1. Server is running (`php artisan serve`)
2. `/api/3d-files/store` route exists
3. File size not exceeding server limits

### Issue: "Model not rendering after share"
**Check**:
1. File ID in URL is correct
2. File exists on server (not expired)
3. Browser console for errors

### Issue: "Buttons not switching forms"
**Check**:
1. No JavaScript errors in console
2. Inline styles removed from buttons
3. CSS loaded properly

---

## ✨ Success Indicators

### Upload Success
```
💾 File saved to IndexedDB: abc123
☁️ File uploaded to server: abc123
✓ Auto-rotate enabled on model load
```

### Share Success
```
📋 Share link copied to clipboard
☁️ Camera state synced to server
```

### Load from Server Success
```
📡 File not found locally, trying server...
✅ File loaded from server
💾 File cached locally from server
```

### Mode Switch Success
```
✓ Viewer background changed to general mode
✓ General viewer resized
✓ General quote updated
```

---

## 🎯 Expected Results

| Test | Expected Outcome |
|------|-----------------|
| Upload file | ✅ Saves locally + server |
| Share link | ✅ Copy link + sync camera |
| Open shared link (same device) | ✅ Loads from server |
| Open shared link (different device) | ✅ Loads from server globally |
| Switch General/Medical | ✅ Forms update, backgrounds change |
| Active button style | ✅ Blue color (#4a90e2) |
| Camera position | ✅ Preserved across shares |
| Auto-rotate | ✅ Disabled until file upload |

---

## 🚨 Critical Tests

**MUST PASS** before considering feature complete:

1. ✅ File uploads to server successfully
2. ✅ Shared link works in incognito mode
3. ✅ Camera position preserved on load
4. ✅ Mode switching updates form correctly
5. ✅ No JavaScript console errors

---

**Testing Status**: Ready for manual testing ✅
