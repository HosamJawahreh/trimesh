"""
Simplified Mesh Repair Microservice (Without pymeshfix)
Provides basic mesh repair using trimesh only
"""
import os
import tempfile
import logging
from typing import Optional, Dict, Any
from pathlib import Path

import trimesh
import numpy as np
from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from fastapi.responses import FileResponse, JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from stl import mesh as stl_mesh

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="TriMesh Mesh Repair Service (Simplified)",
    description="Basic mesh repair and volume calculation API",
    version="1.0.0-simple"
)

# CORS configuration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
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


class MeshRepairResponse(BaseModel):
    """Response model for mesh repair"""
    success: bool
    original_stats: Dict[str, Any]
    repaired_stats: Dict[str, Any]
    repair_summary: Dict[str, Any]
    repair_time_seconds: float
    volume_change_cm3: float
    volume_change_percent: float


def analyze_mesh(mesh: trimesh.Trimesh) -> Dict[str, Any]:
    """Analyze mesh and return statistics"""
    try:
        stats = {
            "vertices": len(mesh.vertices),
            "faces": len(mesh.faces),
            "edges": len(mesh.edges),
        }

        # Volume calculation
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

        # Hole detection (approximate)
        edges = mesh.edges_unique
        edge_count = len(edges)
        stats["holes_count"] = max(0, edge_count - (3 * len(mesh.faces) // 2))

        # Connected components
        components = mesh.split(only_watertight=False)
        stats["connected_components"] = len(components)

        return stats

    except Exception as e:
        logger.error(f"Mesh analysis error: {str(e)}")
        raise


def repair_mesh_basic(mesh: trimesh.Trimesh, aggressive: bool = True) -> trimesh.Trimesh:
    """
    Basic mesh repair using trimesh utilities
    (Without pymeshfix - less robust but functional)
    """
    try:
        # 1. Remove duplicate vertices
        mesh.merge_vertices()

        # 2. Remove degenerate faces
        mesh.remove_degenerate_faces()

        # 3. Remove duplicate faces
        mesh.remove_duplicate_faces()

        # 4. Fix normals
        mesh.fix_normals()

        # 5. Fill holes (basic - just closes small gaps)
        if aggressive:
            mesh.fill_holes()

        # 6. Remove unreferenced vertices
        mesh.remove_unreferenced_vertices()

        return mesh

    except Exception as e:
        logger.error(f"Basic repair error: {str(e)}")
        return mesh  # Return original if repair fails


@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "service": "TriMesh Mesh Repair Service (Simplified)",
        "version": "1.0.0-simple",
        "status": "running",
        "note": "Using trimesh only (pymeshfix not available)"
    }


@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "service": "mesh-repair",
        "version": "1.0.0-simple",
        "repair_engine": "trimesh"
    }


@app.post("/analyze")
async def analyze_mesh_file(
    file: UploadFile = File(...),
):
    """
    Analyze uploaded mesh file
    POST /analyze
    """
    try:
        # Validate file
        if not file.filename.lower().endswith(('.stl', '.obj', '.ply')):
            raise HTTPException(400, "Only STL, OBJ, and PLY files are supported")

        # Save uploaded file
        temp_path = UPLOAD_DIR / file.filename
        with open(temp_path, "wb") as f:
            content = await file.read()
            if len(content) > MAX_FILE_SIZE:
                raise HTTPException(413, "File too large (max 100MB)")
            f.write(content)

        # Load mesh
        mesh = trimesh.load(str(temp_path))

        # Analyze
        analysis = analyze_mesh(mesh)
        analysis["filename"] = file.filename

        # Cleanup
        temp_path.unlink(missing_ok=True)

        # Recommendations
        recommendations = []
        if not analysis["is_watertight"]:
            recommendations.append("Mesh is not watertight - repair recommended")
        if not analysis["is_manifold"]:
            recommendations.append("Mesh has non-manifold edges - repair recommended")
        if analysis["holes_count"] > 0:
            recommendations.append(f"Mesh has approximately {analysis['holes_count']} holes")

        return {
            "success": True,
            "analysis": analysis,
            "recommendations": recommendations
        }

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Analysis failed: {str(e)}")
        raise HTTPException(500, f"Analysis failed: {str(e)}")


@app.post("/repair")
async def repair_mesh_file(
    file: UploadFile = File(...),
    aggressive: bool = Form(True),
    save_result: bool = Form(True)
):
    """
    Repair uploaded mesh file
    POST /repair
    """
    import time
    start_time = time.time()

    try:
        # Validate file
        if not file.filename.lower().endswith(('.stl', '.obj', '.ply')):
            raise HTTPException(400, "Only STL, OBJ, and PLY files are supported")

        # Save uploaded file
        temp_path = UPLOAD_DIR / file.filename
        with open(temp_path, "wb") as f:
            content = await file.read()
            f.write(content)

        # Load original mesh
        mesh_original = trimesh.load(str(temp_path))
        original_stats = analyze_mesh(mesh_original)

        # Repair mesh
        mesh_repaired = repair_mesh_basic(mesh_original.copy(), aggressive=aggressive)
        repaired_stats = analyze_mesh(mesh_repaired)

        # Calculate changes
        volume_change_cm3 = repaired_stats["volume_cm3"] - original_stats["volume_cm3"]
        volume_change_percent = (volume_change_cm3 / original_stats["volume_cm3"]) * 100 if original_stats["volume_cm3"] > 0 else 0

        # Repair summary
        repair_summary = {
            "vertices_removed": original_stats["vertices"] - repaired_stats["vertices"],
            "faces_removed": original_stats["faces"] - repaired_stats["faces"],
            "holes_filled": max(0, original_stats["holes_count"] - repaired_stats["holes_count"]),
            "method": "trimesh_basic",
            "aggressive_mode": aggressive
        }

        repair_time = time.time() - start_time

        # Calculate quality score (0-100)
        quality_score = 50  # Base score
        if repaired_stats["is_watertight"]:
            quality_score += 30
        if repaired_stats["is_manifold"]:
            quality_score += 20

        response = {
            "success": True,
            "original_stats": original_stats,
            "repaired_stats": repaired_stats,
            "repair_summary": repair_summary,
            "repair_time_seconds": round(repair_time, 2),
            "volume_change_cm3": round(volume_change_cm3, 4),
            "volume_change_percent": round(volume_change_percent, 2),
            "quality_score": quality_score
        }

        # Cleanup
        temp_path.unlink(missing_ok=True)

        return response

    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Repair failed: {str(e)}")
        raise HTTPException(500, f"Repair failed: {str(e)}")


if __name__ == "__main__":
    import uvicorn
    logger.info("Starting TriMesh Mesh Repair Service (Simplified)...")
    logger.info("Running on http://127.0.0.1:8001")
    uvicorn.run(app, host="127.0.0.1", port=8001, log_level="info")
