/**
 * ========================================
 * MESH REPAIR WITH VISUAL FEEDBACK
 * Shows repaired areas in a special color
 * ========================================
 */

console.log('🔧 Loading Mesh Repair with Visual Feedback System...');

window.MeshRepairVisual = {
    
    /**
     * Repair mesh and highlight repaired areas
     */
    async repairMeshWithVisualization(viewer, fileData) {
        console.log(`🔧 Starting repair with visualization for: ${fileData.file.name}`);
        
        if (!fileData.mesh || !fileData.mesh.geometry) {
            console.error('❌ No mesh or geometry found');
            return { repaired: false, holesFound: 0, holesFilled: 0 };
        }
        
        const geometry = fileData.mesh.geometry;
        const mesh = fileData.mesh;
        
        // Step 1: Analyze geometry for holes
        const analysis = this.analyzeGeometry(geometry);
        console.log('📊 Geometry analysis:', analysis);
        
        if (analysis.holes === 0) {
            console.log('✅ No holes detected - mesh is watertight');
            return { repaired: true, holesFound: 0, holesFilled: 0, watertight: true };
        }
        
        // Step 2: Find hole boundaries
        const holeBoundaries = this.findHoleBoundaries(geometry);
        console.log(`🔍 Found ${holeBoundaries.length} hole boundaries`);
        
        if (holeBoundaries.length === 0) {
            console.log('⚠️ Could not detect hole boundaries');
            return { repaired: false, holesFound: analysis.holes, holesFilled: 0 };
        }
        
        // Step 3: Create repair geometry for each hole
        const repairGeometries = [];
        let holesFilled = 0;
        
        for (const boundary of holeBoundaries) {
            try {
                const repairGeo = this.fillHole(boundary);
                if (repairGeo) {
                    repairGeometries.push(repairGeo);
                    holesFilled++;
                }
            } catch (error) {
                console.warn('⚠️ Failed to fill hole:', error);
            }
        }
        
        console.log(`✅ Filled ${holesFilled} holes`);
        
        // Step 4: Add visual indicators for repaired areas
        if (repairGeometries.length > 0) {
            this.addRepairVisualization(viewer, repairGeometries, mesh);
            
            // CRITICAL: Update fileData.geometry so volume calculation uses repaired mesh
            fileData.geometry = mesh.geometry;
            console.log('✅ Updated fileData.geometry to repaired version');
        }
        
        // Step 5: Merge repair geometry with original (now done in addRepairVisualization)
        
        return {
            repaired: true,
            holesFound: analysis.holes,
            holesFilled: holesFilled,
            watertight: holesFilled >= analysis.holes
        };
    },
    
    /**
     * Analyze geometry for issues
     */
    analyzeGeometry(geometry) {
        const position = geometry.attributes.position;
        if (!position) {
            return { holes: 0, openEdges: 0, manifold: false };
        }
        
        const vertices = position.array;
        const indices = geometry.index ? geometry.index.array : null;
        
        // Build edge map to find open edges
        const edgeMap = new Map();
        let triangleCount = 0;
        
        if (indices) {
            for (let i = 0; i < indices.length; i += 3) {
                const i0 = indices[i];
                const i1 = indices[i + 1];
                const i2 = indices[i + 2];
                
                // Add three edges of triangle
                this.addEdge(edgeMap, i0, i1);
                this.addEdge(edgeMap, i1, i2);
                this.addEdge(edgeMap, i2, i0);
                
                triangleCount++;
            }
        } else {
            // Non-indexed geometry
            for (let i = 0; i < vertices.length; i += 9) {
                const i0 = i / 3;
                const i1 = i / 3 + 1;
                const i2 = i / 3 + 2;
                
                this.addEdge(edgeMap, i0, i1);
                this.addEdge(edgeMap, i1, i2);
                this.addEdge(edgeMap, i2, i0);
                
                triangleCount++;
            }
        }
        
        // Count open edges (edges that appear only once)
        let openEdges = 0;
        for (const [key, count] of edgeMap.entries()) {
            if (count === 1) {
                openEdges++;
            }
        }
        
        // Estimate number of holes (rough estimate)
        const estimatedHoles = Math.ceil(openEdges / 10);
        
        return {
            triangles: triangleCount,
            openEdges: openEdges,
            holes: estimatedHoles,
            manifold: openEdges === 0,
            watertight: openEdges === 0
        };
    },
    
    /**
     * Add edge to edge map
     */
    addEdge(edgeMap, v1, v2) {
        // Create consistent edge key (smaller index first)
        const key = v1 < v2 ? `${v1}-${v2}` : `${v2}-${v1}`;
        edgeMap.set(key, (edgeMap.get(key) || 0) + 1);
    },
    
    /**
     * Find hole boundaries
     */
    findHoleBoundaries(geometry) {
        const position = geometry.attributes.position;
        const vertices = position.array;
        const indices = geometry.index ? geometry.index.array : null;
        
        // Build edge map
        const edgeMap = new Map();
        const openEdges = [];
        
        if (indices) {
            for (let i = 0; i < indices.length; i += 3) {
                const i0 = indices[i];
                const i1 = indices[i + 1];
                const i2 = indices[i + 2];
                
                const edges = [
                    [i0, i1],
                    [i1, i2],
                    [i2, i0]
                ];
                
                for (const [v1, v2] of edges) {
                    const key = v1 < v2 ? `${v1}-${v2}` : `${v2}-${v1}`;
                    edgeMap.set(key, (edgeMap.get(key) || 0) + 1);
                }
            }
            
            // Find open edges
            for (const [key, count] of edgeMap.entries()) {
                if (count === 1) {
                    const [v1, v2] = key.split('-').map(Number);
                    openEdges.push([v1, v2]);
                }
            }
        }
        
        // Group connected open edges into boundaries
        const boundaries = this.groupOpenEdges(openEdges);
        
        return boundaries;
    },
    
    /**
     * Group connected open edges into hole boundaries
     */
    groupOpenEdges(openEdges) {
        if (openEdges.length === 0) return [];
        
        const boundaries = [];
        const used = new Set();
        
        for (let i = 0; i < openEdges.length; i++) {
            if (used.has(i)) continue;
            
            const boundary = [];
            const queue = [openEdges[i]];
            used.add(i);
            
            while (queue.length > 0 && boundary.length < 1000) {
                const edge = queue.shift();
                boundary.push(edge);
                
                // Find connected edges
                const lastVertex = edge[1];
                
                for (let j = 0; j < openEdges.length; j++) {
                    if (used.has(j)) continue;
                    
                    if (openEdges[j][0] === lastVertex) {
                        queue.push(openEdges[j]);
                        used.add(j);
                    }
                }
            }
            
            if (boundary.length >= 3) {
                boundaries.push(boundary);
            }
        }
        
        return boundaries;
    },
    
    /**
     * Fill a hole with triangles
     */
    fillHole(boundary) {
        if (!boundary || boundary.length < 3) {
            return null;
        }
        
        // Simple fan triangulation from first vertex
        const positions = [];
        const firstVertex = boundary[0][0];
        
        for (let i = 1; i < boundary.length - 1; i++) {
            positions.push(firstVertex, boundary[i][0], boundary[i + 1][0]);
        }
        
        if (positions.length === 0) {
            return null;
        }
        
        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
        geometry.computeVertexNormals();
        
        return geometry;
    },
    
    /**
     * Add visual indicators for repaired areas
     */
    addRepairVisualization(viewer, repairGeometries, originalMesh) {
        if (!repairGeometries || repairGeometries.length === 0) {
            return;
        }
        
        console.log(`🎨 Adding repair visualization for ${repairGeometries.length} repaired areas`);
        
        // Create special material for repaired areas (bright green/cyan)
        const repairMaterial = new THREE.MeshPhongMaterial({
            color: 0x00ff88,  // Bright cyan-green
            emissive: 0x00aa44,  // Glowing effect
            shininess: 100,
            side: THREE.DoubleSide,
            transparent: false,
            opacity: 1.0,
            wireframe: false
        });
        
        // Merge all repair geometries
        const mergedGeometry = new THREE.BufferGeometry();
        const allPositions = [];
        
        for (const repairGeo of repairGeometries) {
            const pos = repairGeo.attributes.position.array;
            for (let i = 0; i < pos.length; i++) {
                allPositions.push(pos[i]);
            }
        }
        
        mergedGeometry.setAttribute('position', new THREE.Float32BufferAttribute(allPositions, 3));
        mergedGeometry.computeVertexNormals();
        
        // Create mesh for repaired areas
        const repairMesh = new THREE.Mesh(mergedGeometry, repairMaterial);
        repairMesh.userData.isRepairVisualization = true;
        repairMesh.userData.originalMesh = originalMesh;
        
        // Position it at the same location as original mesh
        repairMesh.position.copy(originalMesh.position);
        repairMesh.rotation.copy(originalMesh.rotation);
        repairMesh.scale.copy(originalMesh.scale);
        
        // Add to scene
        if (viewer.modelGroup) {
            viewer.modelGroup.add(repairMesh);
        } else {
            viewer.scene.add(repairMesh);
        }
        
        // CRITICAL: Update the original mesh geometry to include repairs for volume calculation
        try {
            const originalGeometry = originalMesh.geometry;
            const newGeometry = this.mergeGeometries(originalGeometry, mergedGeometry);
            
            if (newGeometry) {
                // Update mesh geometry to include repairs
                originalMesh.geometry.dispose(); // Clean up old geometry
                originalMesh.geometry = newGeometry;
                originalMesh.geometry.computeBoundingBox();
                originalMesh.geometry.computeBoundingSphere();
                
                console.log('✅ Updated original mesh geometry to include repairs - NEW volume will be calculated');
            }
        } catch (mergeError) {
            console.warn('⚠️ Could not merge geometries:', mergeError);
        }
        
        // Render
        if (viewer.render) {
            viewer.render();
        }
        
        console.log('✅ Repair visualization added to scene');
        
        // Show notification
        if (window.showToolbarNotification) {
            showToolbarNotification(
                `Repaired areas shown in bright green/cyan color`,
                'success',
                3000
            );
        }
    },
    
    /**
     * Merge two geometries into one
     */
    mergeGeometries(geometry1, geometry2) {
        try {
            const positions1 = geometry1.attributes.position.array;
            const positions2 = geometry2.attributes.position.array;
            
            // Create new array with combined positions
            const mergedPositions = new Float32Array(positions1.length + positions2.length);
            mergedPositions.set(positions1, 0);
            mergedPositions.set(positions2, positions1.length);
            
            const mergedGeometry = new THREE.BufferGeometry();
            mergedGeometry.setAttribute('position', new THREE.BufferAttribute(mergedPositions, 3));
            mergedGeometry.computeVertexNormals();
            mergedGeometry.computeBoundingBox();
            mergedGeometry.computeBoundingSphere();
            
            console.log(`📐 Merged geometry: ${positions1.length/3} + ${positions2.length/3} = ${mergedPositions.length/3} vertices`);
            
            return mergedGeometry;
        } catch (error) {
            console.error('❌ Error merging geometries:', error);
            return null;
        }
    },
    
    /**
     * Remove repair visualization
     */
    removeRepairVisualization(viewer) {
        const toRemove = [];
        
        viewer.scene.traverse((obj) => {
            if (obj.userData.isRepairVisualization) {
                toRemove.push(obj);
            }
        });
        
        toRemove.forEach(obj => {
            if (obj.parent) {
                obj.parent.remove(obj);
            }
            if (obj.geometry) obj.geometry.dispose();
            if (obj.material) obj.material.dispose();
        });
        
        if (viewer.render) {
            viewer.render();
        }
        
        console.log(`✅ Removed ${toRemove.length} repair visualizations`);
    }
};

console.log('✅ Mesh Repair Visual System loaded');
