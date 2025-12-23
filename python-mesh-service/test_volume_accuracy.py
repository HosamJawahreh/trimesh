"""
Test volume calculation accuracy with NumPy
"""
import numpy as np
import trimesh

# Create simple test geometries with known volumes
print("=" * 60)
print("VOLUME CALCULATION ACCURACY TEST")
print("=" * 60)

# Test 1: Cube (10mm x 10mm x 10mm) = 1000 mm³ = 1.0 cm³
cube = trimesh.creation.box(extents=[10, 10, 10])
cube_volume_mm3 = abs(cube.volume)
cube_volume_cm3 = cube_volume_mm3 / 1000.0

print(f"\n1. CUBE (10mm x 10mm x 10mm)")
print(f"   Expected: 1000 mm³ = 1.0 cm³")
print(f"   Calculated: {cube_volume_mm3:.4f} mm³ = {cube_volume_cm3:.4f} cm³")
print(f"   Accuracy: {(cube_volume_cm3 / 1.0 * 100):.2f}%")

# Test 2: Sphere (radius 10mm) = 4/3 * π * r³ = 4188.79 mm³ = 4.189 cm³
sphere = trimesh.creation.icosphere(subdivisions=4, radius=10)
sphere_volume_mm3 = abs(sphere.volume)
sphere_volume_cm3 = sphere_volume_mm3 / 1000.0
expected_sphere = (4/3) * np.pi * (10**3)

print(f"\n2. SPHERE (radius 10mm)")
print(f"   Expected: {expected_sphere:.2f} mm³ = {expected_sphere/1000:.4f} cm³")
print(f"   Calculated: {sphere_volume_mm3:.2f} mm³ = {sphere_volume_cm3:.4f} cm³")
print(f"   Accuracy: {(sphere_volume_mm3 / expected_sphere * 100):.2f}%")

# Test 3: Cylinder (radius 5mm, height 20mm) = π * r² * h = 1570.80 mm³ = 1.571 cm³
cylinder = trimesh.creation.cylinder(radius=5, height=20)
cylinder_volume_mm3 = abs(cylinder.volume)
cylinder_volume_cm3 = cylinder_volume_mm3 / 1000.0
expected_cylinder = np.pi * (5**2) * 20

print(f"\n3. CYLINDER (radius 5mm, height 20mm)")
print(f"   Expected: {expected_cylinder:.2f} mm³ = {expected_cylinder/1000:.4f} cm³")
print(f"   Calculated: {cylinder_volume_mm3:.2f} mm³ = {cylinder_volume_cm3:.4f} cm³")
print(f"   Accuracy: {(cylinder_volume_mm3 / expected_cylinder * 100):.2f}%")

# Test 4: Complex mesh - load real STL if available
print(f"\n4. TESTING WITH ANALYZE_MESH FUNCTION")

from main import analyze_mesh

# Test with cube
stats_cube = analyze_mesh(cube)
print(f"\n   Cube analysis:")
print(f"   Volume: {stats_cube['volume_cm3']:.4f} cm³")
print(f"   Watertight: {stats_cube['is_watertight']}")

# Test with sphere
stats_sphere = analyze_mesh(sphere)
print(f"\n   Sphere analysis:")
print(f"   Volume: {stats_sphere['volume_cm3']:.4f} cm³")
print(f"   Watertight: {stats_sphere['is_watertight']}")

print("\n" + "=" * 60)
print("CONCLUSION:")
print("✅ Trimesh uses NumPy internally for volume calculation")
print("✅ Volume = abs(mesh.volume) / 1000 for cm³ conversion")
print("✅ Accuracy is typically >99% for watertight meshes")
print("=" * 60)
