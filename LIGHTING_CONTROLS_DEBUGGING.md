# 🔧 Lighting Controls Debugging Guide

## Issue: Light and Shadow controls not working

## Quick Diagnostic Steps:

### Step 1: Open Browser Console
1. Go to the quote viewer page: `http://127.0.0.1:8000/quote/viewer=general`
2. Press `F12` or `Ctrl+Shift+I` to open Developer Tools
3. Click on "Console" tab

### Step 2: Check for These Messages

Look for these console messages in order:

```
🎨 Loading Enhanced Light & Shadow Controls...
🎨 Enhanced slider styles applied
💡 Setting up enhanced light and shadow controls...
💡 window.viewerGeneral exists: true
💡 Light slider found: true
💡 Light value display found: true
✅ Initializing light control...
💡 Initial light intensity: 0.9
💡 Updating light to 0.9 (45%)
💡 Main light intensity updated to 0.9
💡 Fill light intensity updated to 0.36
💡 Light intensity set to 0.9
🌑 Shadow slider found: true
🌑 Shadow value display found: true
✅ Initializing shadow control...
🌑 Initial shadow intensity: 1
🌑 Updating shadow to 1 (100%)
🌑 Shadow bias updated to -0.001
🌑 Shadow intensity set to 1
✅ Enhanced light and shadow controls ready!
```

### Step 3: Test the Sliders

1. **Upload a 3D model** first (STL, OBJ, or PLY file)
2. Look for the Light & Shadow controls in the toolbar (light blue background section)
3. Move the **Light slider** (sun icon) left and right
4. Move the **Shadow slider** (shadow icon) left and right
5. Check console for messages like:
   ```
   💡 Light slider input: 1.2
   💡 Updating light to 1.2 (60%)
   💡 Main light intensity updated to 1.2
   ```

### Step 4: Common Issues and Solutions

#### Issue 1: Sliders Not Visible
**Symptoms**: Can't find the light/shadow controls in toolbar
**Solution**:
- Hard refresh: `Ctrl+Shift+R` (Windows/Linux) or `Cmd+Shift+R` (Mac)
- Clear cache and reload
- Check if you're on the correct page (`/quote/viewer=general`)

#### Issue 2: Sliders Visible but Not Responding
**Symptoms**: Sliders move but nothing happens in the 3D viewer
**Console Shows**: `❌ window.viewerGeneral NOT found` or `❌ setLightIntensity method not found`
**Solution**:
1. Make sure a 3D model is uploaded
2. Wait 2-3 seconds after page load for viewer to initialize
3. Try uploading a model first, then use sliders

#### Issue 3: Console Errors
**Error**: `viewersReady event not fired`
**Solution**: The viewer initialization might have failed. Reload the page.

**Error**: `TypeError: Cannot read property 'intensity' of undefined`
**Solution**: The lights haven't been created yet. Upload a model first.

#### Issue 4: Changes Too Subtle
**Symptoms**: Slider moves but changes barely visible
**Solution**:
- For **light**: Try extreme values - move to 0% (very dark) or 150% (very bright)
- For **shadow**: Move to 0% (no shadows) to see clear difference
- Try different model colors - some colors show lighting changes better

### Step 5: Manual Testing

Open browser console and run these commands manually:

```javascript
// Check if viewer exists
console.log('Viewer exists:', !!window.viewerGeneral);

// Check light intensity
console.log('Current light:', window.viewerGeneral.getLightIntensity());

// Test setting light (should make it very bright)
window.viewerGeneral.setLightIntensity(2.0);

// Test setting light (should make it very dim)
window.viewerGeneral.setLightIntensity(0.2);

// Reset to normal
window.viewerGeneral.setLightIntensity(0.9);

// Test shadow (no shadows)
window.viewerGeneral.setShadowIntensity(0);

// Test shadow (full shadows)
window.viewerGeneral.setShadowIntensity(1.0);
```

## Test Page

Visit this standalone test page:
`http://127.0.0.1:8000/test-lighting-controls.html`

This page will:
- Check if viewer is available
- Show console logs visually
- Let you test controls without uploading a model

## Files to Check

### 1. HTML Controls Location
**File**: `resources/views/frontend/pages/quote-viewer.blade.php`
**Line**: ~840-880
**Look for**: `<div class="toolbar-group"` with light blue background containing sliders

### 2. JavaScript Controls
**File**: `public/frontend/assets/js/light-shadow-controls.js`
**Check**: File should be ~180 lines with debugging console.log statements

### 3. Viewer Methods
**File**: `public/frontend/assets/js/3d-viewer-pro.js`
**Lines**: ~1095-1180
**Methods**: `setLightIntensity()` and `setShadowIntensity()`

### 4. Script Inclusion
**File**: `resources/views/frontend/pages/quote-viewer.blade.php`
**Line**: ~5637
**Check for**: `<script src="{{ asset('frontend/assets/js/light-shadow-controls.js') }}?t={{ time() }}"></script>`

## Expected Behavior

### Light Intensity (0-200%)
- **0%**: Model appears completely dark/black
- **25%**: Very dim, hard to see details
- **50%**: Moderate lighting
- **90%** (default): Normal bright lighting
- **150%**: Very bright, washed out
- **200%**: Maximum brightness, may look flat

### Shadow Intensity (0-100%)
- **0%**: No shadows at all (model looks flat)
- **30%**: Subtle shadows
- **60%**: Moderate shadows
- **100%** (default): Full shadow darkness

## Still Not Working?

1. **Check Network Tab** (F12 → Network):
   - Look for `light-shadow-controls.js` - should return 200 OK
   - If 404, the file path is wrong

2. **Check for JavaScript Errors**:
   - Look for red errors in console
   - Check if any other scripts are breaking before controls load

3. **Verify Three.js Version**:
   - Run: `console.log(THREE.REVISION)`
   - Should be r128 or later for proper lighting support

4. **Check Browser Compatibility**:
   - Chrome/Edge: Full support ✅
   - Firefox: Full support ✅
   - Safari: Partial support (some CSS may differ)
   - IE: Not supported ❌

## Contact Information

If controls still don't work after trying all above steps, provide:
1. Browser console screenshot
2. Browser name and version
3. Any error messages shown
4. Screenshot of the toolbar area
