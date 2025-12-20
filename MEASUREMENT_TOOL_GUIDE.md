# 📏 Measurement Tool Guide

## Overview
The measurement tool allows users to measure distances between two points on the 3D model surface using an intuitive click-based interface.

## How It Works

### Activation
1. Click the **"Measure"** button in the control bar
2. Button turns purple to indicate measurement mode is active
3. Notification appears: "Click first point on the model"

### Measuring Process

#### **Click 1 - Start Point**
- Click anywhere on the 3D model surface
- A **green sphere marker** appears at the clicked location
- Notification: "Click second point"
- Console: "📍 Start point placed"

#### **Click 2 - End Point**
- Click a second location on the model surface
- A **red sphere marker** appears at the second location
- A **bright yellow line** connects the two points
- A **3D label** displays the distance in mm and cm
- Notification shows: "Distance: X.XX mm (X.XX cm)" for 7 seconds
- The measurement stays visible on the model

#### **Click 3 - New Measurement**
- Third click automatically clears the previous measurement
- Starts a new measurement with the clicked point as the new start
- Previous markers, line, and label are removed
- A new **green sphere marker** appears
- Process repeats from step 1

### Visual Elements

**Start Point Marker:**
- Green sphere (2mm diameter)
- 80% opacity
- Semi-transparent

**End Point Marker:**
- Red sphere (2mm diameter)
- 80% opacity
- Semi-transparent

**Measurement Line:**
- Bright yellow color (#ffff00)
- Connects start and end points
- Always visible

**Distance Label:**
- Black background with yellow border
- White text showing distance
- Positioned above the midpoint
- Always faces the camera
- Format: "XX.XX mm"
- Rendered on top of everything (depthTest disabled)

### Deactivation
- Click the **"Measure"** button again to exit measurement mode
- All markers, lines, and labels are removed
- Button returns to normal appearance

## Technical Details

### Raycasting
- Uses Three.js Raycaster for accurate 3D point detection
- Only detects clicks on the model surface (not empty space)
- Works with the model's modelGroup object

### Distance Calculation
- Uses Three.js Vector3.distanceTo() method
- Results in millimeters (model units)
- Displayed in both mm and cm

### Visual Rendering
- Markers: Three.js Mesh with SphereGeometry
- Line: Three.js Line with BufferGeometry
- Label: Three.js Mesh with PlaneGeometry + CanvasTexture
- All elements properly disposed on cleanup

### Compatibility
- Works with both General and Medical viewers
- Supports multiple file uploads
- Measurements persist across camera movements
- Label always faces camera for readability

## User Experience

### Success States
✅ Click on model → Point placed immediately  
✅ Two points → Distance calculated and displayed  
✅ Third click → Auto-reset for new measurement  

### Error Handling
❌ Click on empty space → Warning notification: "Click on the model surface"  
❌ No model loaded → Measurement button inactive  

### Visual Feedback
- Color-coded markers (green = start, red = end)
- Bright yellow line for high visibility
- Clear distance label with units
- Real-time notifications guide the user
- Button active state shows measurement mode

## Console Messages

```javascript
📏 Measurement mode ACTIVE - Click two points on the model
✅ Point picked: { x: 10.23, y: 15.67, z: 8.91 }
✓ Marker 1 created at: Vector3
📍 Start point placed
✅ Point picked: { x: 20.45, y: 25.89, z: 18.34 }
✓ Marker 2 created at: Vector3
✓ Line drawn between points
📏 Distance measured: 45.67 mm
✓ Label created
📍 End point placed - measurement complete
🔄 Starting new measurement
🧹 Measurement cleared
```

## Best Practices

1. **Activate measurement mode** before clicking on the model
2. **Click directly on the model surface** for accurate measurements
3. **Wait for markers to appear** before clicking the next point
4. **Read the notifications** for guidance through the process
5. **Deactivate measurement mode** when done to avoid accidental measurements

## Troubleshooting

**Problem:** Clicks not registering  
**Solution:** Ensure measurement mode is active (button is purple)

**Problem:** "No mesh hit" message  
**Solution:** Click directly on the model surface, not empty space

**Problem:** Measurements disappear  
**Solution:** Third click starts a new measurement automatically

**Problem:** Line not visible  
**Solution:** Line is bright yellow - ensure model isn't blocking view

**Problem:** Label facing wrong direction  
**Solution:** Label automatically rotates to face camera - rotate view if needed

## Features Summary

- ✅ Two-point distance measurement
- ✅ Click-to-measure interface
- ✅ Auto-reset on third click
- ✅ Visual markers (color-coded)
- ✅ Bright yellow connecting line
- ✅ 3D distance label (always faces camera)
- ✅ Real-time notifications
- ✅ Distance in mm and cm
- ✅ Proper cleanup and disposal
- ✅ Works with multiple files
- ✅ Compatible with both viewer modes

## Implementation
- **File:** `resources/views/frontend/pages/quote-viewer.blade.php`
- **Lines:** 2010-2355
- **Framework:** Three.js
- **Raycasting:** Three.js Raycaster
- **Version:** Implemented December 16, 2025
