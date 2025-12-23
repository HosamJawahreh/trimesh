# Add this to main.py after the /health endpoint


@app.post("/calculate-volume")
async def calculate_volume(file: UploadFile = File(...)):
    """
    Calculate accurate volume from STL file using trimesh + NumPy
    Returns volume in mm³ and cm³ with high precision
    
    This is the CRITICAL endpoint for accurate volume calculation.
    Frontend uses this after client-side repair for production-grade precision.
    """
    try:
        logger.info(f"📐 Volume calculation request for: {file.filename}")
        
        # Save uploaded file temporarily
        with tempfile.NamedTemporaryFile(delete=False, suffix='.stl') as tmp_file:
            content = await file.read()
            tmp_file.write(content)
            tmp_path = tmp_file.name

        try:
            # Load mesh with trimesh
            mesh = trimesh.load(tmp_path)
            
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
                "filename": file.filename
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
