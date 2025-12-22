"""
Production-Grade Mesh Repair Microservice
Provides industrial-quality mesh repair using pymeshfix and trimesh
"""
import os
import tempfile
import logging
from typing import Optional, Dict, Any
from pathlib import Path

import trimesh
import pymeshfix
import numpy as np
from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from fastapi.responses import FileResponse, JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="TriMesh Professional Mesh Repair Service",
    description="Industrial-grade mesh repair and volume calculation API",
    version="1.0.0"
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
        
        # Create new trimesh object
        repaired_mesh = trimesh.Trimesh(
            vertices=repaired_vertices,
            faces=repaired_faces,
            process=True  # Clean up mesh
        )
        
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


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001, log_level="info")
