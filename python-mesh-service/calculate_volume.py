"""
Volume Calculation API Endpoint
Calculates accurate volume using NumPy and trimesh
"""
from fastapi import APIRouter, HTTPException, File, UploadFile
from fastapi.responses import JSONResponse
import trimesh
import numpy as np
import tempfile
import os
from typing import Optional

router = APIRouter()

@router.post("/calculate-volume")
async def calculate_volume(file: UploadFile = File(...)):
    """
    Calculate volume from STL file using trimesh + NumPy
    Returns volume in mm³ and cm³
    """
    try:
        # Save uploaded file temporarily
        with tempfile.NamedTemporaryFile(delete=False, suffix='.stl') as tmp_file:
            content = await file.read()
            tmp_file.write(content)
            tmp_path = tmp_file.name

        try:
            # Load mesh with trimesh
            mesh = trimesh.load(tmp_path)
            
            # Calculate volume using NumPy (production-grade accuracy)
            volume_mm3 = float(abs(mesh.volume))
            volume_cm3 = volume_mm3 / 1000.0
            
            # Get mesh statistics
            vertices_count = len(mesh.vertices)
            faces_count = len(mesh.faces)
            is_watertight = mesh.is_watertight
            is_volume_valid = mesh.is_volume

            return JSONResponse({
                "success": True,
                "volume_mm3": volume_mm3,
                "volume_cm3": volume_cm3,
                "mesh_stats": {
                    "vertices": vertices_count,
                    "faces": faces_count,
                    "is_watertight": is_watertight,
                    "is_volume_valid": is_volume_valid
                },
                "method": "trimesh_numpy"
            })

        finally:
            # Clean up temp file
            if os.path.exists(tmp_path):
                os.unlink(tmp_path)

    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Volume calculation failed: {str(e)}"
        )
