# 🎮 3D Viewer Keyboard Controls - Quick Reference

## Mouse Controls

| Action | Control |
|--------|---------|
| **Rotate Model** | Left Mouse Button + Drag |
| **Pan/Move Model** | Right Mouse Button + Drag |
| **Zoom In/Out** | Mouse Wheel Scroll |

## Keyboard Shortcuts

### Essential Controls

| Key | Action | Description |
|-----|--------|-------------|
| <kbd>H</kbd> | **Toggle Help** | Show/hide this controls overlay |
| <kbd>F</kbd> | **Fit Camera** | Automatically fit camera to model bounds |
| <kbd>Space</kbd> | **Reset Camera** | Reset camera to default position |

### View Controls

| Key | Action | Description |
|-----|--------|-------------|
| <kbd>R</kbd> | **Toggle Auto-Rotate** | Enable/disable automatic model rotation |
| <kbd>+</kbd> or <kbd>=</kbd> | **Zoom In** | Move camera closer to model |
| <kbd>-</kbd> or <kbd>_</kbd> | **Zoom Out** | Move camera farther from model |

### Navigation

| Key | Action | Description |
|-----|--------|-------------|
| <kbd>←</kbd> | **Pan Left** | Move view to the left |
| <kbd>→</kbd> | **Pan Right** | Move view to the right |
| <kbd>↑</kbd> | **Pan Up** | Move view upward |
| <kbd>↓</kbd> | **Pan Down** | Move view downward |

## Tips & Tricks

### For Quick Model Review
1. Load your STL/OBJ file
2. Press <kbd>F</kbd> to fit the model in view
3. Press <kbd>R</kbd> to enable auto-rotation
4. Sit back and watch! 😎

### For Detailed Inspection
1. Use mouse to rotate and zoom manually
2. Press <kbd>Space</kbd> to reset if you get lost
3. Use <kbd>+</kbd>/<kbd>-</kbd> for precise zoom control
4. Right-click and drag to pan to specific areas

### For Presentations
1. Load your model
2. Press <kbd>F</kbd> to center it
3. Press <kbd>R</kbd> to enable auto-rotation
4. Press <kbd>H</kbd> to hide controls overlay for clean screen

## Technical Details

### Camera Settings
- **Field of View:** 50°
- **Min Distance:** 5 units (very close zoom)
- **Max Distance:** 2000 units (very far zoom)
- **Zoom Speed:** 1.2x (responsive)
- **Pan Speed:** 0.8x (smooth)
- **Damping:** Enabled for realistic motion

### Control Features
- **Smooth Damping:** Natural, physics-based movement
- **Screen Space Panning:** Intuitive 2D panning
- **Constrained Rotation:** Can't flip the model upside down
- **Auto-Rotation Speed:** 2.0 (when enabled)

## Troubleshooting

### Model Not Visible?
- Press <kbd>F</kbd> to fit camera to model
- Press <kbd>Space</kbd> to reset camera

### Controls Not Responding?
- Make sure you're not typing in a text field
- Click on the 3D viewer area first
- Refresh the page if needed

### Can't Find a Feature?
- Press <kbd>H</kbd> to show the help overlay
- All controls are listed there

---

## Advanced Features

### Coming Soon
- 🔄 Save/restore camera positions
- 📏 Measurement tools with NumPy calculations
- 🎨 Custom color themes
- 📱 Touch gesture support for mobile
- 📊 Performance metrics display

---

**Tip:** Keep the help overlay visible (<kbd>H</kbd>) while learning the shortcuts!
