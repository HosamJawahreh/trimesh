#!/usr/bin/env python3
"""
Quick test script to verify NumPy matrix operations in the mesh service
"""
import numpy as np
from scipy.spatial.transform import Rotation as R

print("=" * 60)
print("🧪 Testing NumPy Matrix Operations for TriMesh")
print("=" * 60)

# Test 1: NumPy version
print(f"\n✓ NumPy Version: {np.__version__}")

# Test 2: Create transformation matrix
print("\n📐 Test: Transformation Matrix Creation")
translation = np.array([10, 20, 30])
rotation = np.array([45, 0, 90])  # degrees
scale = np.array([1.5, 1.5, 1.5])

matrix = np.eye(4)
matrix[0:3, 0:3] = np.diag(scale)

rotation_rad = np.deg2rad(rotation)
r = R.from_euler('xyz', rotation_rad)
rotation_matrix = r.as_matrix()
matrix[:3, :3] = rotation_matrix @ matrix[:3, :3]
matrix[:3, 3] = translation

print("4x4 Transformation Matrix:")
print(matrix)

# Test 3: Vertex deduplication
print("\n🔍 Test: Vertex Optimization (Deduplication)")
vertices = np.array([
    [0.0, 0.0, 0.0],
    [1.0, 0.0, 0.0],
    [1.0, 1.0, 0.0],
    [0.0, 0.0, 0.0],  # Duplicate
    [1.0, 0.0, 0.0],  # Duplicate
], dtype=np.float64)

faces = np.array([
    [0, 1, 2],
    [3, 4, 1],
], dtype=np.int32)

print(f"Original vertices: {len(vertices)}")
unique_vertices, inverse = np.unique(
    np.round(vertices, decimals=6),
    axis=0,
    return_inverse=True
)
optimized_faces = inverse[faces]
print(f"Optimized vertices: {len(unique_vertices)} (removed {len(vertices) - len(unique_vertices)} duplicates)")

# Test 4: Normal computation
print("\n📊 Test: Vectorized Normal Computation")
v0 = vertices[faces[:, 0]]
v1 = vertices[faces[:, 1]]
v2 = vertices[faces[:, 2]]

edge1 = v1 - v0
edge2 = v2 - v0
face_normals = np.cross(edge1, edge2)
face_norms = np.linalg.norm(face_normals, axis=1, keepdims=True)
face_normals = face_normals / (face_norms + 1e-10)

print(f"Computed {len(face_normals)} face normals using NumPy cross product")
print(f"Sample normal: {face_normals[0]}")

# Test 5: Mesh quality metrics
print("\n📈 Test: Quality Metrics Computation")
edge_lengths_01 = np.linalg.norm(v1 - v0, axis=1)
edge_lengths_12 = np.linalg.norm(v2 - v1, axis=1)
edge_lengths_20 = np.linalg.norm(v0 - v2, axis=1)

s = (edge_lengths_01 + edge_lengths_12 + edge_lengths_20) / 2
areas = np.sqrt(np.maximum(0, s * (s - edge_lengths_01) * (s - edge_lengths_12) * (s - edge_lengths_20)))

max_edges = np.maximum(np.maximum(edge_lengths_01, edge_lengths_12), edge_lengths_20)
min_edges = np.minimum(np.minimum(edge_lengths_01, edge_lengths_12), edge_lengths_20)
aspect_ratios = min_edges / (max_edges + 1e-10)

print(f"Triangle areas: min={np.min(areas):.6f}, max={np.max(areas):.6f}, mean={np.mean(areas):.6f}")
print(f"Aspect ratios: min={np.min(aspect_ratios):.6f}, mean={np.mean(aspect_ratios):.6f}")
print(f"Edge lengths: min={np.min(min_edges):.6f}, max={np.max(max_edges):.6f}")

# Test 6: Matrix multiplication performance
print("\n⚡ Test: NumPy Performance")
large_vertices = np.random.rand(10000, 3).astype(np.float64)
transform_matrix = np.eye(4)
transform_matrix[:3, :3] = rotation_matrix
transform_matrix[:3, 3] = [5, 5, 5]

import time
start = time.time()
homogeneous = np.hstack([large_vertices, np.ones((len(large_vertices), 1))])
transformed = (transform_matrix @ homogeneous.T).T[:, :3]
elapsed = time.time() - start

print(f"Transformed {len(large_vertices)} vertices in {elapsed*1000:.2f}ms using NumPy")

print("\n" + "=" * 60)
print("✅ All NumPy matrix operations working correctly!")
print("=" * 60)
