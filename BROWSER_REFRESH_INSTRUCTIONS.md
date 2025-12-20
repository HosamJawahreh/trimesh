# 🔄 Browser Hard Refresh Instructions

## The Pan Tool Icon is Already Added!

The Pan/Move tool button with the 4-directional arrow icon has been successfully added to your viewer. If you don't see it, you need to **hard refresh** your browser to clear the cache.

## How to Hard Refresh (Clear Cache):

### Windows / Linux:
- **Chrome / Edge / Firefox**: `Ctrl + Shift + R` or `Ctrl + F5`
- **Alternative**: `Shift + F5`

### Mac:
- **Chrome / Edge**: `Cmd + Shift + R`
- **Firefox**: `Cmd + Shift + R`
- **Safari**: `Cmd + Option + R`

## Alternative Method (If hard refresh doesn't work):

1. Open **Developer Tools** (F12)
2. **Right-click** on the refresh button in the browser
3. Select **"Empty Cache and Hard Reload"**

## What You Should See After Refresh:

### General Viewer Tools:
1. ☑️ Grid
2. 🔧 Repair
3. ⚪ Fill Holes
4. 🔄 Auto Rotate
5. 📏 **Measure** (ruler icon)
6. ✋ **Pan** (4-directional arrows) ← **NEW!**

### Medical Viewer Tools:
Same tools including the new Pan button!

## Pan Tool Icon:
```
    ↑
  ← + →
    ↓
```
Four arrows pointing in all directions (up, down, left, right)

## If Still Not Showing:

1. Check browser console (F12) for any errors
2. Verify the file path is correct: `/resources/views/frontend/pages/quote-viewer.blade.php`
3. Clear your browser cache completely:
   - Chrome: Settings → Privacy → Clear browsing data → Cached images and files
   - Firefox: Settings → Privacy → Clear Data → Cached Web Content
4. Try in an incognito/private window

## Button Location:
The Pan button appears:
- **After the Measure button**
- **Before the divider line**
- In the **Tools section** of the control bar
- At the **bottom of the viewer**

---

**Note**: The button is definitely in the code at:
- Line 235: General Viewer Pan Button (`id="panToolBtn"`)
- Line 510: Medical Viewer Pan Button (`id="panToolBtnMedical"`)

If you still don't see it after a hard refresh, there might be a caching issue with your Laravel app. Try:
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```
