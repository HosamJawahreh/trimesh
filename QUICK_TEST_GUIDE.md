# 🧪 Quick Testing Guide - Mesh Repair & Pricing

## 🚀 Quick Start (30 seconds)

### 1. Hard Refresh Browser
```
Windows/Linux: Ctrl + Shift + R
Mac: Cmd + Shift + R
```

### 2. Open Browser Console
```
Windows/Linux: F12 or Ctrl + Shift + I
Mac: Cmd + Option + I
```

### 3. Click "Save & Calculate"

### 4. Watch Console Output

---

## 📊 What to Look For

### ✅ SUCCESS - Console shows:

```
🔍 Analyzing geometry: 15000 vertices, indexed
   Processing 5000 indexed triangles...
   Built edge map with 7500 unique edges
   Found 48 open edges (boundary edges)
   Estimated 4 holes from 48 open edges

🔍 Finding hole boundaries...
   Found 48 open edges (boundary edges)
   Grouped into 4 hole boundaries
   Boundary 1: 12 edges
   Boundary 2: 15 edges
   Boundary 3: 10 edges
   Boundary 4: 11 edges

   Filling hole with 12 boundary edges...
   ✅ Created repair geometry with 10 triangles
   [repeat for each hole]

🎨 Adding repair visualization for 4 repaired areas
   Merged 4 repair geometries into 40 triangles
   ✅ Merged original + repair geometries
   Original: 15000 vertices
   Repair: 120 vertices
   Merged: 15120 vertices

📐 Calculating volume for: model.stl
   Geometry has 15120 vertices
   ✅ Volume: 4.58 cm³ (4580.00 mm³)

💰 Pricing calculation:
   Technology: fdm
   Material: pla
   Volume: 4.58 cm³
   Price per cm³: $0.50
   Total price: $2.29
```

### ✅ VISUAL - You should see:
- 🟢 **Green/cyan patches** on the 3D model (filled holes)
- 📊 **Updated volume** in sidebar (higher than before)
- 💰 **Calculated price** based on new volume
- 🔔 **Green notification**: "Repaired X holes across Y files"

---

## ❌ TROUBLESHOOTING

### Problem: "Repaired 0 holes"

**Check 1: Open Edges**
Look in console for: `Found X open edges`
- If X = 0: Mesh is watertight (no holes to fix) ✅
- If X > 0 but 0 holes repaired: Continue to Check 2

**Check 2: Boundaries**
Look for: `Grouped into X hole boundaries`
- If X = 0: Edge grouping failed
- If X > 0: Boundaries found but not filled

**Check 3: Fill Result**
Look for: `Created repair geometry with X triangles`
- If missing: Hole filling failed
- Check for error messages

**Solution**: The mesh might actually be watertight! Try a different model or check console logs for actual errors.

---

### Problem: Volume doesn't change

**Check 1: Merged Geometry**
Look for:
```
✅ Merged original + repair geometries
Original: 15000 vertices
Merged: 15120 vertices    <-- Should be HIGHER
```

**Check 2: Volume Calculation**
Look for:
```
📐 Calculating volume for: model.stl
   Geometry has 15120 vertices    <-- Should match merged count
```

**Solution**: If merged vertices = original vertices, no geometry was added (no holes filled).

---

### Problem: Price seems wrong

**Check 1: Technology & Material**
Look for:
```
💰 Pricing calculation:
   Technology: fdm    <-- Should match your selection
   Material: pla      <-- Should match your selection
```

**Check 2: Price Calculation**
```
   Volume: 4.58 cm³
   Price per cm³: $0.50
   Total price: $2.29    <-- Should be Volume × Price per cm³
```

**Solution**: Verify dropdown selections and pricing matrix in code.

---

## 🎯 Test Scenarios

### Scenario 1: Mesh WITH Holes
**Expected**:
- ✅ Open edges > 0
- ✅ Holes found > 0
- ✅ Green patches visible
- ✅ Volume increases
- ✅ Price based on new volume

### Scenario 2: Watertight Mesh
**Expected**:
- ✅ Open edges = 0
- ✅ Holes found = 0
- ✅ No green patches
- ✅ Volume = original
- ✅ Message: "All meshes are watertight"

### Scenario 3: Different Materials
**Test**:
1. Select FDM + PLA → Check price
2. Select FDM + ABS → Price should increase
3. Select SLA + Resin → Price should be higher
4. Select DMLS + Titanium → Price should be much higher

**Pricing Reference**:
- FDM/PLA: $0.50/cm³
- FDM/ABS: $0.60/cm³
- SLA/Resin: $2.50/cm³
- DMLS/Titanium: $15.00/cm³

---

## 🔍 Debug Commands

### Check uploaded files:
```javascript
viewer = window.viewerGeneral;
console.log('Files:', viewer.uploadedFiles.length);
console.log('First file:', viewer.uploadedFiles[0].file.name);
```

### Check geometry:
```javascript
fileData = viewer.uploadedFiles[0];
console.log('Vertices:', fileData.geometry.attributes.position.count);
console.log('Has index:', !!fileData.geometry.index);
```

### Check repair meshes:
```javascript
repairs = viewer.scene.children.filter(c => c.userData.isRepairVisualization);
console.log('Repair meshes:', repairs.length);
```

### Check selections:
```javascript
tech = document.getElementById('technologySelectGeneral');
mat = document.getElementById('materialSelectGeneral');
console.log('Technology:', tech.value);
console.log('Material:', mat.value);
```

---

## 📋 Quick Checklist

Before reporting issues, verify:
- [ ] Hard refresh completed (Ctrl+Shift+R)
- [ ] Console is open and visible
- [ ] Model uploaded successfully
- [ ] Clicked "Save & Calculate"
- [ ] Checked console for error messages
- [ ] Verified technology/material selections
- [ ] Looked for green patches on model
- [ ] Checked volume display in sidebar
- [ ] Verified price calculation

---

## 🎉 Success Indicators

### Console Logs ✅
- Geometry analyzed
- Holes detected (if present)
- Boundaries found and grouped
- Repair geometry created
- Geometries merged
- Volume calculated from merged geometry
- Pricing calculated

### Visual Feedback ✅
- Green/cyan patches on model (if holes filled)
- Volume displayed in sidebar
- Price displayed in sidebar
- Print time estimated
- Green success notification

### No Errors ❌
- No red error messages in console
- No "undefined" or "null" errors
- No "Cannot read property" errors

---

## 🆘 Still Having Issues?

### Share these details:
1. **Console output** (full logs)
2. **Model file type** (STL, OBJ, PLY?)
3. **Model size** (file size, vertex count)
4. **Selected technology** and **material**
5. **Expected behavior** vs **actual behavior**
6. **Screenshots** of console and 3D viewer

### Common Issues:
- **Cache not cleared**: Try Ctrl+F5 or clear cache in browser settings
- **Console filtered**: Make sure console shows "All" messages, not just errors
- **Wrong viewer**: Make sure using "General" tab, not "Medical"
- **File not loaded**: Check if model is visible before clicking Save

---

**Happy Testing! 🚀**

If everything works: You should see detailed logs, green patches, updated volume, and correct pricing! ✨
