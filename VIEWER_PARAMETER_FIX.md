# Viewer Parameter Preservation Fix

## Problem
When clicking the "Save & Calculate" button on the dental viewer (`/quote?viewer=dental`), the `viewer` parameter was being removed from the URL, causing the system to default to "General" viewer type when clicking "Request Quote".

### Symptoms
1. User visits `/quote?viewer=dental`
2. User uploads file and clicks "Save & Calculate"
3. URL changes to `/quote?files=xxx` (viewer parameter lost)
4. Clicking "Request Quote" defaults to General instead of Dental
5. Review page shows "General" instead of "Dental"

## Root Cause
The issue was in `/public/frontend/assets/js/enhanced-save-calculate.js` at line 1077. After saving the quote to the database, the code was updating the browser URL to show the file IDs, but it was **only including the `files` parameter and omitting the `viewer` parameter**.

### Original Code (Line 1077)
```javascript
// Update URL without reload to show file IDs
const newUrl = `${window.location.pathname}?files=${filesParam}`;
window.history.pushState({}, '', newUrl);
```

This created a URL like `/quote?files=xxx` which **overwrote** the entire query string, removing `?viewer=dental`.

## Solution
Modified the URL update logic to preserve the `viewer` parameter from the current URL before updating.

### Fixed Code
```javascript
// IMPORTANT: Preserve the viewer parameter from current URL
const currentUrl = new URL(window.location.href);
const viewerParam = currentUrl.searchParams.get('viewer');

// Update URL without reload to show file IDs + preserve viewer
let newUrl = `${window.location.pathname}?files=${filesParam}`;
if (viewerParam) {
    newUrl += `&viewer=${viewerParam}`;
    console.log('✅ Viewer parameter preserved:', viewerParam);
}

window.history.pushState({}, '', newUrl);
```

Now the URL becomes `/quote?files=xxx&viewer=dental` which preserves the viewer type throughout the entire workflow.

## Files Modified
- `/public/frontend/assets/js/enhanced-save-calculate.js` (lines 1071-1079)

## Testing
To verify the fix:

1. **Navigate to Dental Viewer**
   ```
   Visit: /quote?viewer=dental
   ```

2. **Upload a File**
   - Upload a .stl or .obj file
   - Wait for it to load

3. **Click "Save & Calculate"**
   - After calculation completes, check browser URL
   - Should show: `/quote?files=xxx&viewer=dental`
   - Viewer parameter should still be present ✅

4. **Click "Request Quote"**
   - Review page should show "Dental" not "General" ✅
   - Modal should show dental-specific settings ✅

5. **Verify Console Logs**
   ```javascript
   // Should see in console:
   ✅ Viewer parameter preserved: dental
   ✅ Updated browser URL to match viewer link: /quote?files=xxx&viewer=dental
   ```

## Related Systems
This fix complements the existing viewer preservation mechanisms:

1. **GLOBAL_VIEWER_TYPE constant** (quote-viewer.blade.php line 128-134)
   - Captures viewer type at page load
   - Used throughout the session

2. **FileStorageManager.updateURL()** (file-storage-manager.js line 510-555)
   - Already preserves viewer parameter when updating file IDs
   - Works correctly

3. **URL Cleanup on File Load** (quote-viewer.blade.php line 8667-8675)
   - Already preserves viewer parameter when removing files parameter
   - Works correctly

4. **File Remove Handler** (3d-file-manager.js line 177-204)
   - Already preserves viewer parameter when removing files
   - Works correctly

## Impact
- ✅ Dental viewer now maintains viewer type through entire workflow
- ✅ Request Quote button correctly identifies dental vs general
- ✅ Review page shows correct viewer type
- ✅ Email notifications will have correct viewer type
- ✅ Order records will have correct viewer type
- ✅ No page reloads required - URL updates seamlessly
- ✅ Share links will include viewer parameter

## Browser Compatibility
The fix uses standard Web APIs supported by all modern browsers:
- `URL()` constructor
- `URLSearchParams.get()`
- `window.history.pushState()`

## Notes
- The fix is non-breaking - if no viewer parameter exists, it works as before
- Console logs added for debugging and verification
- Follows the same pattern used in other parts of the codebase
- Minimal code change - only added 7 lines

## Date Fixed
December 27, 2025

## Author
GitHub Copilot
