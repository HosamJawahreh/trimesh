# Undo/Redo System Fix

## Problem
The undo/redo buttons always show "nothing to undo" and "nothing to redo" because:
1. The state history is never initialized when the viewer loads
2. Most toolbar actions (like toggleBoundingBox, toggleAxis, toggleGrid, etc.) don't save state
3. Camera movements aren't being tracked

## Solution
1. Save initial state when viewer loads
2. Add `saveState()` calls to all actions that modify the viewer
3. Track camera position changes via controls
4. Update button states dynamically based on history availability

## Changes Made
- Added initial state save in viewer initialization
- Added saveState() calls to: toggleBoundingBox, toggleAxis, toggleGrid, toggleGridMain, toggleAutoRotate, changeModelColor, changeBackgroundColor
- Added camera change tracking via OrbitControls 'end' event
- Enhanced undo/redo button state management with enable/disable

## Files Modified
- resources/views/frontend/pages/quote-viewer.blade.php
