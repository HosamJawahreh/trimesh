"""
Production-Grade Mesh Repair Microservice
Provides industrial-quality mesh repair using pymeshfix and trimesh
"""
import os
import tempfile
import logging
from typing import Optional, Dict, Any
from pathlib import Path
from contextlib import asynccontextmanager

import trimesh
import pymeshfix
import numpy as np
from scipy.spatial.transform import Rotation as R
from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from fastapi.responses import FileResponse, JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# NumPy configuration for better performance
np.set_printoptions(precision=6, suppress=True)

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Modern lifespan event handler for startup and shutdown"""
    # Startup
    logger.info(f"🚀 TriMesh Mesh Repair Service Starting")
    logger.info(f"📦 NumPy version: {np.__version__}")
    logger.info(f"📦 Trimesh version: {trimesh.__version__}")
    logger.info(f"📦 SciPy version available for advanced matrix operations")
    logger.info(f"✓ Service ready with enhanced NumPy matrix operations")
    logger.info(f"✓ Volume calculation endpoint available")
    yield
    # Shutdown (if needed)
    logger.info(f"👋 TriMesh Mesh Repair Service Shutting Down")

# Initialize FastAPI app with lifespan
app = FastAPI(
    title="TriMesh Professional Mesh Repair Service",
    description="Industrial-grade mesh repair and volume calculation API",
    version="1.0.0",
    lifespan=lifespan
)

# CORS configuration - adjust origins for production
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Change to specific domains in production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configuration
MAX_FILE_SIZE = 100 * 1024 * 1024  # 100MB
UPLOAD_DIR = Path("uploads")
REPAIRED_DIR = Path("repaired")
UPLOAD_DIR.mkdir(exist_ok=True)
REPAIRED_DIR.mkdir(exist_ok=True)


# ============================================================================
# NumPy Matrix Operation Helpers for Enhanced Mesh Processing
# ============================================================================

def create_transformation_matrix(translation: np.ndarray = None,
                                rotation: np.ndarray = None,
                                scale: np.ndarray = None) -> np.ndarray:
    """
    Create a 4x4 transformation matrix using NumPy

    Args:
        translation: 3D translation vector [x, y, z]
        rotation: 3D rotation angles in degrees [rx, ry, rz]
        scale: 3D scale factors [sx, sy, sz]

    Returns:
        4x4 transformation matrix
    """
    matrix = np.eye(4)

    # Apply scale
    if scale is not None:
        scale = np.asarray(scale)
        matrix[0, 0] = scale[0]
        matrix[1, 1] = scale[1]
        matrix[2, 2] = scale[2]

    # Apply rotation
    if rotation is not None:
        rotation_rad = np.deg2rad(rotation)
        r = R.from_euler('xyz', rotation_rad)
        rotation_matrix = r.as_matrix()
        matrix[:3, :3] = rotation_matrix @ matrix[:3, :3]

    # Apply translation
    if translation is not None:
        translation = np.asarray(translation)
        matrix[:3, 3] = translation

    return matrix


def optimize_mesh_vertices(vertices: np.ndarray, faces: np.ndarray) -> tuple:
    """
    Optimize mesh using NumPy operations for better memory layout and cache efficiency

    Args:
        vertices: Nx3 array of vertex positions
        faces: Mx3 array of face indices

    Returns:
        Tuple of (optimized_vertices, optimized_faces)
    """
    # Ensure contiguous memory layout
    vertices = np.ascontiguousarray(vertices, dtype=np.float64)
    faces = np.ascontiguousarray(faces, dtype=np.int32)

    # Remove duplicate vertices using NumPy
    unique_vertices, inverse_indices = np.unique(
        np.round(vertices, decimals=6),
        axis=0,
        return_inverse=True
    )

    # Update face indices
    optimized_faces = inverse_indices[faces]

    logger.info(f"Optimized mesh: {len(vertices)} → {len(unique_vertices)} vertices")
    return unique_vertices, optimized_faces


def compute_vertex_normals_numpy(vertices: np.ndarray, faces: np.ndarray) -> np.ndarray:
    """
    Compute smooth vertex normals using NumPy vectorized operations

    Args:
        vertices: Nx3 array of vertex positions
        faces: Mx3 array of face indices

    Returns:
        Nx3 array of normalized vertex normals
    """
    # Initialize normals array
    normals = np.zeros_like(vertices)

    # Get triangle vertices
    v0 = vertices[faces[:, 0]]
    v1 = vertices[faces[:, 1]]
    v2 = vertices[faces[:, 2]]

    # Compute face normals using cross product
    edge1 = v1 - v0
    edge2 = v2 - v0
    face_normals = np.cross(edge1, edge2)

    # Normalize face normals
    face_norms = np.linalg.norm(face_normals, axis=1, keepdims=True)
    face_norms = np.where(face_norms == 0, 1, face_norms)  # Avoid division by zero
    face_normals = face_normals / face_norms

    # Accumulate face normals to vertices
    np.add.at(normals, faces[:, 0], face_normals)
    np.add.at(normals, faces[:, 1], face_normals)
    np.add.at(normals, faces[:, 2], face_normals)

    # Normalize vertex normals
    vertex_norms = np.linalg.norm(normals, axis=1, keepdims=True)
    vertex_norms = np.where(vertex_norms == 0, 1, vertex_norms)
    normals = normals / vertex_norms

    return normals


def compute_mesh_quality_metrics(vertices: np.ndarray, faces: np.ndarray) -> Dict[str, float]:
    """
    Compute mesh quality metrics using NumPy operations

    Args:
        vertices: Nx3 array of vertex positions
        faces: Mx3 array of face indices

    Returns:
        Dictionary of quality metrics
    """
    # Get triangle vertices
    v0 = vertices[faces[:, 0]]
    v1 = vertices[faces[:, 1]]
    v2 = vertices[faces[:, 2]]

    # Compute edge lengths
    edge_lengths_01 = np.linalg.norm(v1 - v0, axis=1)
    edge_lengths_12 = np.linalg.norm(v2 - v1, axis=1)
    edge_lengths_20 = np.linalg.norm(v0 - v2, axis=1)

    # Triangle areas using Heron's formula
    s = (edge_lengths_01 + edge_lengths_12 + edge_lengths_20) / 2
    areas = np.sqrt(np.maximum(0, s * (s - edge_lengths_01) * (s - edge_lengths_12) * (s - edge_lengths_20)))

    # Aspect ratio (min/max edge length per triangle)
    max_edges = np.maximum(np.maximum(edge_lengths_01, edge_lengths_12), edge_lengths_20)
    min_edges = np.minimum(np.minimum(edge_lengths_01, edge_lengths_12), edge_lengths_20)
    aspect_ratios = min_edges / (max_edges + 1e-10)

    return {
        "min_area": float(np.min(areas)),
        "max_area": float(np.max(areas)),
        "mean_area": float(np.mean(areas)),
        "min_aspect_ratio": float(np.min(aspect_ratios)),
        "mean_aspect_ratio": float(np.mean(aspect_ratios)),
        "min_edge_length": float(np.min(min_edges)),
        "max_edge_length": float(np.max(max_edges))
    }


# ============================================================================
# Pydantic Models
# ============================================================================
class MeshAnalysisResponse(BaseModel):
    """Response model for mesh analysis"""
    filename: str
    vertices: int
    faces: int
    edges: int
    volume_mm3: float
    volume_cm3: float
    surface_area_mm2: float
    bounding_box: Dict[str, list]
    is_watertight: bool
    is_manifold: bool
    holes_count: int
    connected_components: int
    euler_number: int
    genus: int


class MeshRepairResponse(BaseModel):
    """Response model for mesh repair"""
    success: bool
    original_stats: Dict[str, Any]
    repaired_stats: Dict[str, Any]
    repair_summary: Dict[str, Any]
    repaired_file_path: Optional[str] = None
    volume_change_cm3: float
    volume_change_percent: float


def analyze_mesh(mesh: trimesh.Trimesh) -> Dict[str, Any]:
    """
    Comprehensive mesh analysis

    Args:
        mesh: Input trimesh object

    Returns:
        Dictionary with detailed mesh statistics
    """
    try:
        # Basic geometry info
        stats = {
            "vertices": len(mesh.vertices),
            "faces": len(mesh.faces),
            "edges": len(mesh.edges),
        }

        # Volume calculation (absolute value for watertight check)
        volume_mm3 = float(abs(mesh.volume))
        stats["volume_mm3"] = volume_mm3
        stats["volume_cm3"] = volume_mm3 / 1000.0

        # Surface area
        stats["surface_area_mm2"] = float(mesh.area)

        # Bounding box
        bounds = mesh.bounds
        stats["bounding_box"] = {
            "min": bounds[0].tolist(),
            "max": bounds[1].tolist(),
            "size": (bounds[1] - bounds[0]).tolist()
        }

        # Topology checks
        stats["is_watertight"] = mesh.is_watertight
        stats["is_manifold"] = mesh.is_winding_consistent

        # Euler characteristic and genus
        stats["euler_number"] = mesh.euler_number
        # For closed surfaces: genus = (2 - euler) / 2
        stats["genus"] = (2 - mesh.euler_number) // 2 if mesh.is_watertight else -1

        # Connected components
        components = mesh.split(only_watertight=False)
        stats["connected_components"] = len(components)

        # Hole detection (edges that appear only once)
        edges_sorted = np.sort(mesh.edges_sorted, axis=1)
        unique_edges, counts = np.unique(edges_sorted, axis=0, return_counts=True)
        boundary_edges = unique_edges[counts == 1]
        stats["holes_count"] = len(boundary_edges)
        stats["boundary_edges"] = len(boundary_edges)

        # Add mesh quality metrics using NumPy operations
        try:
            quality_metrics = compute_mesh_quality_metrics(mesh.vertices, mesh.faces)
            stats["quality_metrics"] = quality_metrics
            logger.info(f"✓ Computed quality metrics: aspect_ratio={quality_metrics['mean_aspect_ratio']:.3f}")
        except Exception as e:
            logger.warning(f"Could not compute quality metrics: {str(e)}")
            stats["quality_metrics"] = {}

        return stats

    except Exception as e:
        logger.error(f"Error analyzing mesh: {str(e)}")
        return {
            "error": str(e),
            "vertices": 0,
            "faces": 0,
            "volume_cm3": 0.0
        }


def repair_mesh_pymeshfix(mesh: trimesh.Trimesh, aggressive: bool = True) -> trimesh.Trimesh:
    """
    Repair mesh using pymeshfix (MeshFix algorithm)

    Args:
        mesh: Input trimesh object
        aggressive: Use aggressive repair mode

    Returns:
        Repaired trimesh object
    """
    try:
        logger.info(f"Repairing mesh with {len(mesh.vertices)} vertices using pymeshfix...")

        # Create pymeshfix object
        meshfix = pymeshfix.MeshFix(mesh.vertices, mesh.faces)

        # Repair with different strategies based on mode
        if aggressive:
            # Aggressive repair: fill all holes, remove non-manifold edges
            meshfix.repair(verbose=False, joincomp=True, remove_smallest_components=False)
        else:
            # Conservative repair: minimal changes
            meshfix.repair(verbose=False, joincomp=False)

        # Get repaired mesh
        repaired_vertices = meshfix.v
        repaired_faces = meshfix.f

        # Preserve vertex colors if present
        vertex_colors = None
        if hasattr(mesh.visual, 'vertex_colors') and mesh.visual.vertex_colors is not None:
            original_colors = mesh.visual.vertex_colors
            logger.info(f"Original mesh has vertex colors: {original_colors.shape}")

            # Map old vertices to new vertices (pymeshfix may reorder/add vertices)
            # For vertices that exist in both, preserve colors
            # For new vertices, interpolate from nearby vertices
            if len(repaired_vertices) == len(mesh.vertices):
                # Same number of vertices - try to map colors
                vertex_colors = original_colors
                logger.info("Preserving vertex colors (same vertex count)")
            else:
                # Different number of vertices - interpolate colors for new vertices
                from scipy.spatial import cKDTree
                tree = cKDTree(mesh.vertices)
                distances, indices = tree.query(repaired_vertices, k=1)
                vertex_colors = original_colors[indices]
                logger.info(f"Interpolated colors for {len(repaired_vertices)} vertices")

        # Create new trimesh object with preserved colors
        repaired_mesh = trimesh.Trimesh(
            vertices=repaired_vertices,
            faces=repaired_faces,
            process=True  # Clean up mesh
        )

        # Apply vertex colors if we have them
        if vertex_colors is not None:
            repaired_mesh.visual.vertex_colors = vertex_colors
            logger.info("✓ Vertex colors preserved in repaired mesh")

        logger.info(f"Repair complete: {len(repaired_mesh.vertices)} vertices")
        return repaired_mesh

    except Exception as e:
        logger.error(f"Error in pymeshfix repair: {str(e)}")
        # Fallback to trimesh basic repair
        return repair_mesh_trimesh(mesh)


def repair_mesh_trimesh(mesh: trimesh.Trimesh) -> trimesh.Trimesh:
    """
    Repair mesh using trimesh built-in methods (fallback)

    Args:
        mesh: Input trimesh object

    Returns:
        Repaired trimesh object
    """
    try:
        logger.info("Using trimesh fallback repair...")

        # Remove duplicate vertices
        mesh.merge_vertices()

        # Remove degenerate faces
        mesh.remove_degenerate_faces()

        # Remove unreferenced vertices
        mesh.remove_unreferenced_vertices()

        # Fill holes (if possible)
        trimesh.repair.fill_holes(mesh)

        # Fix normals
        trimesh.repair.fix_normals(mesh)

        return mesh

    except Exception as e:
        logger.error(f"Error in trimesh repair: {str(e)}")
        return mesh  # Return original if repair fails


@app.get("/")
async def root():
    """Health check endpoint"""
    return {
        "service": "TriMesh Mesh Repair Service",
        "status": "running",
        "version": "1.0.0",
        "endpoints": {
            "analyze": "/api/analyze",
            "repair": "/api/repair",
            "repair_and_download": "/api/repair-download"
        }
    }


@app.get("/health")
async def health_check():
    """Health check for monitoring"""
    return {"status": "healthy", "service": "mesh-repair"}


@app.post("/calculate-volume")
async def calculate_volume(file: UploadFile = File(...)):
    """
    Calculate accurate volume from 3D file (STL/PLY/OBJ) using trimesh + NumPy
    Returns volume in mm³ and cm³ with high precision

    This is the CRITICAL endpoint for accurate volume calculation.
    Frontend uses this after client-side repair for production-grade precision.
    Supports: STL, PLY, OBJ, and other formats supported by trimesh.
    """
    try:
        logger.info(f"📐 Volume calculation request for: {file.filename}")

        # Detect file extension from filename
        file_ext = os.path.splitext(file.filename)[1] if file.filename else '.stl'
        if not file_ext:
            file_ext = '.stl'  # Default to STL

        logger.info(f"   Detected file extension: {file_ext}")

        # Save uploaded file temporarily with correct extension
        with tempfile.NamedTemporaryFile(delete=False, suffix=file_ext) as tmp_file:
            content = await file.read()
            tmp_file.write(content)
            tmp_path = tmp_file.name

        try:
            # Load mesh with trimesh (auto-detects format from extension)
            logger.info(f"   Loading mesh from: {tmp_path}")
            loaded = trimesh.load(tmp_path)

            # Handle Scene vs Mesh (PLY files often load as Scene with multiple meshes)
            if isinstance(loaded, trimesh.Scene):
                logger.info(f"   Loaded as Scene with {len(loaded.geometry)} geometries")
                # Merge all geometries in the scene into a single mesh
                meshes = [geom for geom in loaded.geometry.values() if isinstance(geom, trimesh.Trimesh)]
                if not meshes:
                    raise ValueError("Scene contains no valid mesh geometries")
                if len(meshes) == 1:
                    mesh = meshes[0]
                else:
                    # Concatenate multiple meshes
                    mesh = trimesh.util.concatenate(meshes)
                    logger.info(f"   Merged {len(meshes)} meshes into one")
            else:
                mesh = loaded
                logger.info(f"   Loaded as single Mesh")

            # Calculate volume using NumPy (production-grade accuracy)
            # trimesh uses NumPy internally for all mesh operations
            volume_mm3 = float(abs(mesh.volume))
            volume_cm3 = volume_mm3 / 1000.0

            # Get mesh statistics
            vertices_count = len(mesh.vertices)
            faces_count = len(mesh.faces)
            is_watertight = mesh.is_watertight
            is_volume_valid = mesh.is_volume

            logger.info(f"✅ Volume calculated: {volume_cm3:.4f} cm³ ({volume_mm3:.2f} mm³)")
            logger.info(f"   Mesh: {vertices_count} vertices, {faces_count} faces")
            logger.info(f"   Watertight: {is_watertight}, Volume valid: {is_volume_valid}")
            logger.info(f"   File format: {file_ext.upper()}")

            return JSONResponse({
                "success": True,
                "volume_mm3": round(volume_mm3, 4),
                "volume_cm3": round(volume_cm3, 4),
                "mesh_stats": {
                    "vertices": vertices_count,
                    "faces": faces_count,
                    "is_watertight": is_watertight,
                    "is_volume_valid": is_volume_valid
                },
                "method": "trimesh_numpy",
                "filename": file.filename,
                "file_format": file_ext.upper()
            })

        finally:
            # Clean up temp file
            if os.path.exists(tmp_path):
                os.unlink(tmp_path)

    except Exception as e:
        logger.error(f"❌ Volume calculation failed: {str(e)}")
        raise HTTPException(
            status_code=500,
            detail=f"Volume calculation failed: {str(e)}"
        )


@app.post("/api/analyze", response_model=MeshAnalysisResponse)
async def analyze_uploaded_mesh(file: UploadFile = File(...)):
    """
    Analyze uploaded 3D mesh file

    Args:
        file: Uploaded STL/OBJ/PLY file

    Returns:
        Comprehensive mesh analysis
    """
    try:
        # Validate file size
        content = await file.read()
        if len(content) > MAX_FILE_SIZE:
            raise HTTPException(status_code=400, detail=f"File too large (max {MAX_FILE_SIZE / 1024 / 1024}MB)")

        # Save to temporary file
        with tempfile.NamedTemporaryFile(delete=False, suffix=Path(file.filename).suffix) as tmp_file:
            tmp_file.write(content)
            tmp_path = tmp_file.name

        try:
            # Load mesh
            mesh = trimesh.load(tmp_path, force='mesh')

            # Analyze
            stats = analyze_mesh(mesh)

            return MeshAnalysisResponse(
                filename=file.filename,
                vertices=stats["vertices"],
                faces=stats["faces"],
                edges=stats["edges"],
                volume_mm3=stats["volume_mm3"],
                volume_cm3=stats["volume_cm3"],
                surface_area_mm2=stats["surface_area_mm2"],
                bounding_box=stats["bounding_box"],
                is_watertight=stats["is_watertight"],
                is_manifold=stats["is_manifold"],
                holes_count=stats.get("holes_count", 0),
                connected_components=stats["connected_components"],
                euler_number=stats["euler_number"],
                genus=stats.get("genus", -1)
            )

        finally:
            # Cleanup
            os.unlink(tmp_path)

    except Exception as e:
        logger.error(f"Error analyzing mesh: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error analyzing mesh: {str(e)}")


@app.post("/api/repair", response_model=MeshRepairResponse)
async def repair_uploaded_mesh(
    file: UploadFile = File(...),
    aggressive: bool = Form(True),
    return_file: bool = Form(False)
):
    """
    Repair uploaded 3D mesh file

    Args:
        file: Uploaded STL/OBJ/PLY file
        aggressive: Use aggressive repair mode
        return_file: Include repaired file in response

    Returns:
        Repair statistics and optionally repaired file
    """
    try:
        # Validate file
        content = await file.read()
        if len(content) > MAX_FILE_SIZE:
            raise HTTPException(status_code=400, detail=f"File too large")

        # Save original file
        original_path = UPLOAD_DIR / file.filename
        with open(original_path, "wb") as f:
            f.write(content)

        try:
            # Load original mesh
            logger.info(f"Loading mesh: {file.filename}")
            mesh_original = trimesh.load(str(original_path), force='mesh')

            # Analyze original
            original_stats = analyze_mesh(mesh_original)
            logger.info(f"Original: {original_stats['vertices']} vertices, "
                       f"{original_stats['volume_cm3']:.2f} cm³, "
                       f"watertight: {original_stats['is_watertight']}")

            # Repair mesh
            mesh_repaired = repair_mesh_pymeshfix(mesh_original, aggressive=aggressive)

            # Analyze repaired
            repaired_stats = analyze_mesh(mesh_repaired)
            logger.info(f"Repaired: {repaired_stats['vertices']} vertices, "
                       f"{repaired_stats['volume_cm3']:.2f} cm³, "
                       f"watertight: {repaired_stats['is_watertight']}")

            # Calculate changes
            volume_change = repaired_stats["volume_cm3"] - original_stats["volume_cm3"]
            volume_change_percent = (volume_change / original_stats["volume_cm3"] * 100) if original_stats["volume_cm3"] > 0 else 0

            # Save repaired mesh
            repaired_filename = f"repaired_{file.filename}"
            repaired_path = REPAIRED_DIR / repaired_filename
            mesh_repaired.export(str(repaired_path))

            # Repair summary
            repair_summary = {
                "holes_filled": original_stats.get("holes_count", 0) - repaired_stats.get("holes_count", 0),
                "vertices_added": repaired_stats["vertices"] - original_stats["vertices"],
                "faces_added": repaired_stats["faces"] - original_stats["faces"],
                "watertight_achieved": repaired_stats["is_watertight"],
                "manifold_achieved": repaired_stats["is_manifold"],
                "repair_method": "pymeshfix" if aggressive else "trimesh"
            }

            response_data = {
                "success": True,
                "original_stats": original_stats,
                "repaired_stats": repaired_stats,
                "repair_summary": repair_summary,
                "volume_change_cm3": round(volume_change, 4),
                "volume_change_percent": round(volume_change_percent, 2)
            }

            if return_file:
                response_data["repaired_file_path"] = str(repaired_path)

            return MeshRepairResponse(**response_data)

        finally:
            # Cleanup original file
            if original_path.exists():
                os.unlink(original_path)

    except Exception as e:
        logger.error(f"Error repairing mesh: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error repairing mesh: {str(e)}")


@app.post("/api/repair-download")
async def repair_and_download(
    file: UploadFile = File(...),
    aggressive: bool = Form(True)
):
    """
    Repair mesh and return repaired file for download

    Args:
        file: Uploaded mesh file
        aggressive: Use aggressive repair

    Returns:
        Repaired mesh file
    """
    try:
        # Perform repair
        repair_result = await repair_uploaded_mesh(file, aggressive, return_file=True)

        if not repair_result.repaired_file_path:
            raise HTTPException(status_code=500, detail="Repair completed but file not saved")

        # Return file
        repaired_path = Path(repair_result.repaired_file_path)

        return FileResponse(
            path=str(repaired_path),
            media_type="application/octet-stream",
            filename=f"repaired_{file.filename}",
            headers={
                "X-Volume-Original": str(repair_result.original_stats["volume_cm3"]),
                "X-Volume-Repaired": str(repair_result.repaired_stats["volume_cm3"]),
                "X-Volume-Change": str(repair_result.volume_change_cm3)
            }
        )

    except Exception as e:
        logger.error(f"Error in repair-download: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))


async def _repair_and_calculate_core(file: UploadFile, aggressive: bool = True):
    """Shared implementation for repair-and-calculate style endpoints."""
    try:
        if not isinstance(aggressive, bool):
            aggressive = str(aggressive).lower() not in {"false", "0", "no"}

        logger.info(f"🔧 Repair + Volume calculation for: {file.filename}")

        # Detect file extension
        file_ext = os.path.splitext(file.filename)[1] if file.filename else '.stl'
        if not file_ext:
            file_ext = '.stl'

        logger.info(f"   File extension: {file_ext}")

        # Read file content
        content = await file.read()
        if len(content) > MAX_FILE_SIZE:
            raise HTTPException(status_code=400, detail="File too large")

        # Save original file
        original_filename = f"original_{file.filename}"
        original_path_storage = UPLOAD_DIR / original_filename
        with open(original_path_storage, 'wb') as f:
            f.write(content)

        # Save to temporary file for processing
        with tempfile.NamedTemporaryFile(delete=False, suffix=file_ext) as tmp_file:
            tmp_file.write(content)
            original_path = tmp_file.name

        try:
            # Load original mesh
            logger.info(f"   Loading original mesh...")
            loaded = trimesh.load(original_path)

            # Handle Scene vs Mesh
            if isinstance(loaded, trimesh.Scene):
                logger.info(f"   Loaded as Scene with {len(loaded.geometry)} geometries")
                meshes = [geom for geom in loaded.geometry.values() if isinstance(geom, trimesh.Trimesh)]
                if not meshes:
                    raise ValueError("Scene contains no valid mesh geometries")
                if len(meshes) == 1:
                    mesh_original = meshes[0]
                else:
                    mesh_original = trimesh.util.concatenate(meshes)
                    logger.info(f"   Merged {len(meshes)} meshes")
            else:
                mesh_original = loaded

            # Analyze original mesh
            original_volume_mm3 = float(abs(mesh_original.volume))
            original_volume_cm3 = original_volume_mm3 / 1000.0
            original_watertight = mesh_original.is_watertight
            original_vertices = len(mesh_original.vertices)
            original_faces = len(mesh_original.faces)

            # Detect holes in original - Store boundary edges for visualization
            edges_sorted = np.sort(mesh_original.edges_sorted, axis=1)
            unique_edges, counts = np.unique(edges_sorted, axis=0, return_counts=True)
            boundary_edges_original = unique_edges[counts == 1]
            original_holes = len(boundary_edges_original)

            # Get vertices that are part of holes (for visualization)
            hole_vertices_indices = np.unique(boundary_edges_original.flatten())
            hole_vertices = mesh_original.vertices[hole_vertices_indices].tolist()

            logger.info(f"   Original: {original_vertices} verts, {original_faces} faces")
            logger.info(f"   Original volume: {original_volume_cm3:.4f} cm³")
            logger.info(f"   Original watertight: {original_watertight}, holes: {original_holes}")
            logger.info(f"   Hole boundary vertices: {len(hole_vertices_indices)}")

            # Repair mesh using pymeshfix
            logger.info(f"   Repairing mesh (aggressive={aggressive})...")
            mesh_repaired = repair_mesh_pymeshfix(mesh_original, aggressive=aggressive)

            # Analyze repaired mesh
            repaired_volume_mm3 = float(abs(mesh_repaired.volume))
            repaired_volume_cm3 = repaired_volume_mm3 / 1000.0
            repaired_watertight = mesh_repaired.is_watertight
            repaired_vertices = len(mesh_repaired.vertices)
            repaired_faces = len(mesh_repaired.faces)

            # Detect holes in repaired
            edges_sorted_repaired = np.sort(mesh_repaired.edges_sorted, axis=1)
            unique_edges_repaired, counts_repaired = np.unique(edges_sorted_repaired, axis=0, return_counts=True)
            boundary_edges_repaired = unique_edges_repaired[counts_repaired == 1]
            repaired_holes = len(boundary_edges_repaired)

            holes_filled = max(0, original_holes - repaired_holes)

            # Calculate mesh quality score (0-100)
            quality_score = 0.0
            try:
                quality_metrics = compute_mesh_quality_metrics(mesh_repaired.vertices, mesh_repaired.faces)
                # Quality score based on:
                # - Watertight: 40 points
                # - Mean aspect ratio (closer to 1.0 is better): 30 points
                # - No holes: 30 points
                watertight_score = 40 if repaired_watertight else 0
                aspect_score = min(30, quality_metrics.get('mean_aspect_ratio', 0) * 30)
                holes_score = 30 if repaired_holes == 0 else max(0, 30 - (repaired_holes * 2))
                quality_score = watertight_score + aspect_score + holes_score
                logger.info(f"   Quality score: {quality_score:.1f}/100 (watertight:{watertight_score}, aspect:{aspect_score:.1f}, holes:{holes_score})")
            except Exception as e:
                logger.warning(f"Could not compute quality score: {str(e)}")

            # Find NEW faces (repair areas) by comparing face counts
            new_faces_count = repaired_faces - original_faces
            repair_face_indices = list(range(original_faces, repaired_faces)) if new_faces_count > 0 else []

            # Get repair area vertices (for visualization)
            repair_vertices = []
            if len(repair_face_indices) > 0:
                repair_faces = mesh_repaired.faces[repair_face_indices]
                repair_vertices_indices = np.unique(repair_faces.flatten())
                repair_vertices = mesh_repaired.vertices[repair_vertices_indices].tolist()

            logger.info(f"   Repaired: {repaired_vertices} verts, {repaired_faces} faces")
            logger.info(f"   Repaired volume: {repaired_volume_cm3:.4f} cm³")
            logger.info(f"   Repaired watertight: {repaired_watertight}, holes: {repaired_holes}")
            logger.info(f"   ✅ Filled {holes_filled} holes")
            logger.info(f"   New faces added: {new_faces_count}, repair vertices: {len(repair_vertices)}")

            # Save repaired mesh to permanent storage
            repaired_filename = f"repaired_{file.filename}"
            repaired_path_storage = REPAIRED_DIR / repaired_filename
            mesh_repaired.export(str(repaired_path_storage))

            # Also save to temporary file for base64 encoding
            with tempfile.NamedTemporaryFile(delete=False, suffix=file_ext) as repaired_tmp:
                repaired_path = repaired_tmp.name

            mesh_repaired.export(repaired_path)
            logger.info(f"   Saved repaired mesh to: {repaired_path_storage}")

            # Read repaired file as bytes for response
            with open(repaired_path, 'rb') as f:
                repaired_bytes = f.read()

            # Encode as base64 for JSON response
            import base64
            repaired_base64 = base64.b64encode(repaired_bytes).decode('utf-8')

            # Calculate changes
            volume_change_cm3 = repaired_volume_cm3 - original_volume_cm3
            volume_change_percent = (volume_change_cm3 / original_volume_cm3 * 100) if original_volume_cm3 > 0 else 0

            response = {
                "success": True,
                "filename": file.filename,
                "repaired_filename": repaired_filename,
                "file_format": file_ext.upper(),

                # File paths for permanent storage
                "original_file_path": str(original_path_storage.relative_to(UPLOAD_DIR.parent)),
                "repaired_file_path": str(repaired_path_storage.relative_to(REPAIRED_DIR.parent)),

                # Original stats
                "original_volume_mm3": round(original_volume_mm3, 4),
                "original_volume_cm3": round(original_volume_cm3, 4),
                "original_vertices": original_vertices,
                "original_faces": original_faces,
                "original_watertight": original_watertight,
                "original_holes": original_holes,

                # Repaired stats
                "repaired_volume_mm3": round(repaired_volume_mm3, 4),
                "repaired_volume_cm3": round(repaired_volume_cm3, 4),
                "repaired_vertices": repaired_vertices,
                "repaired_faces": repaired_faces,
                "repaired_watertight": repaired_watertight,
                "repaired_holes": repaired_holes,

                # Changes
                "holes_filled": holes_filled,
                "vertices_added": repaired_vertices - original_vertices,
                "faces_added": repaired_faces - original_faces,
                "volume_change_cm3": round(volume_change_cm3, 4),
                "volume_change_percent": round(volume_change_percent, 2),

                # Visualization data for frontend
                "repair_visualization": {
                    "hole_vertices": hole_vertices[:1000],  # Limit to 1000 for JSON size
                    "repair_vertices": repair_vertices[:1000],
                    "repair_face_count": new_faces_count,
                    "boundary_edges_count": original_holes
                },

                # Quality assessment
                "quality_score": round(quality_score, 1),
                "quality_grade": "Excellent" if quality_score >= 90 else "Good" if quality_score >= 70 else "Fair" if quality_score >= 50 else "Poor",

                # Repaired file (base64 encoded)
                "repaired_file_base64": repaired_base64,

                "method": "pymeshfix + trimesh + numpy",
                "message": f"Repaired mesh: filled {holes_filled} holes, volume = {repaired_volume_cm3:.4f} cm³",
                "timestamp": os.path.getmtime(str(repaired_path_storage))
            }

            logger.info(f"✅ Complete: {repaired_volume_cm3:.4f} cm³, {holes_filled} holes filled")

            return JSONResponse(response)

        finally:
            # Cleanup temp files
            if os.path.exists(original_path):
                os.unlink(original_path)
            if 'repaired_path' in locals() and os.path.exists(repaired_path):
                os.unlink(repaired_path)

    except Exception as e:
        logger.error(f"❌ Repair and calculate failed: {str(e)}")
        import traceback
        logger.error(traceback.format_exc())
        raise HTTPException(
            status_code=500,
            detail=f"Repair and calculate failed: {str(e)}"
        )


@app.post("/repair-and-calculate")
async def repair_mesh_and_calculate_volume(file: UploadFile = File(...), aggressive: bool = Form(True)):
    """Repair mesh and calculate volume in a single request."""
    return await _repair_and_calculate_core(file, aggressive)


@app.post("/api/repair-and-calculate")
async def repair_mesh_and_calculate_volume_api(file: UploadFile = File(...), aggressive: bool = Form(True)):
    """API namespaced variant for backwards compatibility."""
    return await _repair_and_calculate_core(file, aggressive)


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001, log_level="info")
