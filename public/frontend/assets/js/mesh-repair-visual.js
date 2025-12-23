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

        // Safety check: If too many holes detected, mesh is likely badly broken
        if (analysis.holes > 100) {
            console.error(`❌ Mesh appears to be severely damaged (${analysis.holes} estimated holes)`);
            console.error('   This mesh may need manual repair in a 3D modeling tool');
            console.error(`   Open edges detected: ${analysis.openEdges}`);
            return {
                repaired: false,
                holesFound: analysis.holes,
                holesFilled: 0,
                error: 'Mesh too damaged for automatic repair'
            };
        }

        // Step 2: Find hole boundaries
        const holeBoundaries = this.findHoleBoundaries(geometry, analysis);
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
            // Calculate volume BEFORE repair for comparison
            const originalVolumeMm3 = this.calculateGeometryVolume(geometry);
            console.log(`📊 Volume BEFORE repair: ${(originalVolumeMm3 / 1000).toFixed(2)} cm³`);

            const mergedGeometry = this.addRepairVisualization(viewer, repairGeometries, mesh);

            // CRITICAL: Update fileData.geometry so volume calculation uses repaired mesh
            if (mergedGeometry) {
                fileData.geometry = mergedGeometry;
                fileData.mesh.geometry = mergedGeometry;
                fileData.hasRepairVisualization = true; // Mark that repairs are visible
                console.log('✅ Updated fileData.geometry and mesh.geometry to repaired version');
                console.log(`   Original geometry: ${geometry.attributes.position.count} vertices`);
                console.log(`   New geometry: ${mergedGeometry.attributes.position.count} vertices`);

                // Calculate volume AFTER repair for comparison
                const repairedVolumeMm3 = this.calculateGeometryVolume(mergedGeometry);
                const repairedVolumeCm3 = repairedVolumeMm3 / 1000;
                console.log(`📊 Volume AFTER repair: ${repairedVolumeCm3.toFixed(2)} cm³`);
                console.log(`📊 Volume DIFFERENCE: ${((repairedVolumeMm3 - originalVolumeMm3) / 1000).toFixed(2)} cm³`);
                
                // CRITICAL: Store repaired volume in fileData for immediate use
                fileData.repairedVolume = { cm3: repairedVolumeCm3, mm3: repairedVolumeMm3 };
                console.log(`✅ Stored repaired volume in fileData:`, fileData.repairedVolume);
            } else {
                console.warn('⚠️ Failed to get merged geometry');
            }
        } else {
            console.log('⚠️ No repair geometries created');
        }

        // Step 5: Return results

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
            console.log('⚠️ No position attribute found in geometry');
            return { holes: 0, openEdges: 0, manifold: false };
        }

        const vertices = position.array;
        const indices = geometry.index ? geometry.index.array : null;

        console.log(`🔍 Analyzing geometry: ${vertices.length / 3} vertices, ${indices ? 'indexed' : 'non-indexed'}`);

        // For non-indexed geometry, we NEED to build an edge map to detect holes
        // Many STL files (especially medical scans) are non-indexed but may have holes
        if (!indices) {
            console.log('   ℹ️ Non-indexed geometry - building edge map from triangles');

            const edgeMap = new Map();
            const vertexMap = new Map(); // Map to merge duplicate vertices
            let triangleCount = 0;

            // Build vertex map to identify unique vertices (tolerance for floating point errors)
            const tolerance = 0.0001;
            const getVertexKey = (x, y, z) => {
                const kx = Math.round(x / tolerance);
                const ky = Math.round(y / tolerance);
                const kz = Math.round(z / tolerance);
                return `${kx},${ky},${kz}`;
            };

            // Map each vertex position to an index
            const vertexIndices = [];
            let nextIndex = 0;

            for (let i = 0; i < vertices.length; i += 3) {
                const key = getVertexKey(vertices[i], vertices[i + 1], vertices[i + 2]);
                if (!vertexMap.has(key)) {
                    vertexMap.set(key, nextIndex++);
                }
                vertexIndices.push(vertexMap.get(key));
            }

            console.log(`   Mapped ${vertices.length / 3} vertices to ${vertexMap.size} unique positions`);

            // Build edge map from triangles
            for (let i = 0; i < vertexIndices.length; i += 3) {
                const v0 = vertexIndices[i];
                const v1 = vertexIndices[i + 1];
                const v2 = vertexIndices[i + 2];

                // Add three edges of triangle
                this.addEdge(edgeMap, v0, v1);
                this.addEdge(edgeMap, v1, v2);
                this.addEdge(edgeMap, v2, v0);

                triangleCount++;
            }

            console.log(`   Processed ${triangleCount} non-indexed triangles`);
            console.log(`   Built edge map with ${edgeMap.size} unique edges`);

            // Count open edges
            let openEdges = 0;
            for (const [key, count] of edgeMap.entries()) {
                if (count === 1) {
                    openEdges++;
                }
            }

            console.log(`   Found ${openEdges} open edges in non-indexed geometry`);

            // Estimate holes
            let estimatedHoles = 0;
            if (openEdges > 0) {
                if (openEdges < 20) {
                    estimatedHoles = 1;
                } else if (openEdges < 100) {
                    estimatedHoles = Math.ceil(openEdges / 15);
                } else if (openEdges < 500) {
                    estimatedHoles = Math.ceil(openEdges / 30);
                } else {
                    estimatedHoles = Math.min(Math.ceil(openEdges / 50), 50);
                    console.warn(`   ⚠️ Very high open edge count (${openEdges}) - mesh may be severely damaged`);
                }
            }

            return {
                triangles: triangleCount,
                openEdges: openEdges,
                holes: estimatedHoles,
                manifold: openEdges === 0,
                watertight: openEdges === 0,
                nonIndexed: true,
                vertexMap: vertexMap,
                vertexIndices: vertexIndices
            };
        }

        // Build edge map to find open edges (for indexed geometry)
        const edgeMap = new Map();
        let triangleCount = 0;

        console.log(`   Processing ${indices.length / 3} indexed triangles...`);
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

        console.log(`   Built edge map with ${edgeMap.size} unique edges`);

        // Count open edges (edges that appear only once)
        let openEdges = 0;
        const openEdgesList = [];

        for (const [key, count] of edgeMap.entries()) {
            if (count === 1) {
                openEdges++;
                if (openEdges <= 20) {
                    openEdgesList.push(key);
                }
            }
        }

        console.log(`   Found ${openEdges} open edges (boundary edges)`);
        if (openEdges > 0 && openEdges <= 20) {
            console.log(`   Sample open edges: ${openEdgesList.join(', ')}`);
        }

        // Estimate number of holes MORE CONSERVATIVELY
        // Most models have 0-10 holes. If we detect thousands of open edges,
        // it's likely the mesh is actually broken or the format is wrong
        let estimatedHoles = 0;
        if (openEdges > 0) {
            if (openEdges < 20) {
                estimatedHoles = 1; // Small hole
            } else if (openEdges < 100) {
                estimatedHoles = Math.ceil(openEdges / 15); // 2-7 holes
            } else if (openEdges < 500) {
                estimatedHoles = Math.ceil(openEdges / 30); // 4-17 holes
            } else {
                // If >500 open edges, likely a badly broken mesh or non-manifold
                // Cap at a reasonable number and warn
                estimatedHoles = Math.min(Math.ceil(openEdges / 50), 50);
                console.warn(`   ⚠️ Very high open edge count (${openEdges}) - mesh may be severely damaged`);
            }
        }

        console.log(`   Estimated ${estimatedHoles} holes from ${openEdges} open edges`);

        const result = {
            triangles: triangleCount,
            openEdges: openEdges,
            holes: estimatedHoles,
            manifold: openEdges === 0,
            watertight: openEdges === 0
        };

        console.log('📊 Analysis result:', result);

        return result;
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
    findHoleBoundaries(geometry, analysis = null) {
        const position = geometry.attributes.position;
        const vertices = position.array;
        const indices = geometry.index ? geometry.index.array : null;

        console.log('🔍 Finding hole boundaries...');

        // Build edge map
        const edgeMap = new Map();
        const openEdges = [];

        if (!indices) {
            // Non-indexed geometry - use vertex mapping from analysis if available
            console.log('   Processing non-indexed geometry for boundary detection');

            if (analysis && analysis.vertexIndices) {
                console.log('   Using pre-computed vertex mapping');
                const vertexIndices = analysis.vertexIndices;

                // Build edge map from mapped vertices
                for (let i = 0; i < vertexIndices.length; i += 3) {
                    const v0 = vertexIndices[i];
                    const v1 = vertexIndices[i + 1];
                    const v2 = vertexIndices[i + 2];

                    const edges = [
                        [v0, v1],
                        [v1, v2],
                        [v2, v0]
                    ];

                    for (const [va, vb] of edges) {
                        const key = va < vb ? `${va}-${vb}` : `${vb}-${va}`;
                        edgeMap.set(key, (edgeMap.get(key) || 0) + 1);
                    }
                }

                // Find open edges and get their actual positions
                for (const [key, count] of edgeMap.entries()) {
                    if (count === 1) {
                        const [va, vb] = key.split('-').map(Number);

                        // Find the actual vertex positions (first occurrence)
                        let v1Pos = null, v2Pos = null;
                        for (let i = 0; i < vertexIndices.length; i++) {
                            if (vertexIndices[i] === va && !v1Pos) {
                                v1Pos = [vertices[i * 3], vertices[i * 3 + 1], vertices[i * 3 + 2]];
                            }
                            if (vertexIndices[i] === vb && !v2Pos) {
                                v2Pos = [vertices[i * 3], vertices[i * 3 + 1], vertices[i * 3 + 2]];
                            }
                            if (v1Pos && v2Pos) break;
                        }

                        if (v1Pos && v2Pos) {
                            openEdges.push({
                                indices: [va, vb],
                                positions: [v1Pos, v2Pos]
                            });
                        }
                    }
                }

                console.log(`   Found ${openEdges.length} open edges from non-indexed geometry`);
            } else {
                console.warn('   ⚠️ No vertex mapping available for non-indexed geometry');
                return [];
            }
        } else {
            // Indexed geometry
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

            console.log(`   Built edge map from ${indices.length / 3} triangles`);

            // Find open edges
            for (const [key, count] of edgeMap.entries()) {
                if (count === 1) {
                    const [v1, v2] = key.split('-').map(Number);

                    // Get actual vertex positions
                    const v1x = vertices[v1 * 3];
                    const v1y = vertices[v1 * 3 + 1];
                    const v1z = vertices[v1 * 3 + 2];
                    const v2x = vertices[v2 * 3];
                    const v2y = vertices[v2 * 3 + 1];
                    const v2z = vertices[v2 * 3 + 2];

                    openEdges.push({
                        indices: [v1, v2],
                        positions: [[v1x, v1y, v1z], [v2x, v2y, v2z]]
                    });
                }
            }

            console.log(`   Found ${openEdges.length} open edges (boundary edges)`);
        }

        // Group connected open edges into boundaries
        const boundaries = this.groupOpenEdges(openEdges);

        console.log(`   Grouped into ${boundaries.length} hole boundaries`);
        boundaries.forEach((boundary, idx) => {
            console.log(`   Boundary ${idx + 1}: ${boundary.length} edges`);
        });

        return boundaries;
    },    /**
     * Group connected open edges into hole boundaries
     * AGGRESSIVE MODE: Repairs all boundaries including single/double edges for medical STL files
     */
    groupOpenEdges(openEdges) {
        if (openEdges.length === 0) {
            console.log('   No open edges to group');
            return [];
        }

        console.log(`   Grouping ${openEdges.length} open edges (AGGRESSIVE MEDICAL MODE)...`);

        const boundaries = [];
        const used = new Set();
        let singleEdgeCount = 0;
        let smallBoundaryCount = 0;

        for (let i = 0; i < openEdges.length; i++) {
            if (used.has(i)) continue;

            const boundary = [];
            const queue = [openEdges[i]];
            used.add(i);

            while (queue.length > 0 && boundary.length < 1000) {
                const edge = queue.shift();
                boundary.push(edge);

                // Find connected edges
                const lastVertex = edge.indices[1]; // Get the second vertex index

                for (let j = 0; j < openEdges.length; j++) {
                    if (used.has(j)) continue;

                    // Check if this edge connects to our current edge
                    if (openEdges[j].indices[0] === lastVertex) {
                        queue.push(openEdges[j]);
                        used.add(j);
                        break; // Found the next edge in the boundary
                    }
                }
            }

            // ACCEPT ALL BOUNDARIES - even single edges for medical STL repair
            boundaries.push(boundary);
            if (boundary.length >= 3) {
                // Don't log each one to avoid console spam
            } else if (boundary.length === 2) {
                smallBoundaryCount++;
            } else {
                singleEdgeCount++;
            }
        }

        console.log(`   📊 Grouped into ${boundaries.length} boundaries:`);
        console.log(`      ✓ ${boundaries.filter(b => b.length >= 3).length} standard boundaries (≥3 edges)`);
        console.log(`      ✓ ${smallBoundaryCount} small gaps (2 edges) - WILL REPAIR`);
        console.log(`      ✓ ${singleEdgeCount} scattered edges (1 edge) - WILL REPAIR`);

        return boundaries;
    },

    /**
     * Fill a hole with triangles - AGGRESSIVE MODE for medical STL files
     * Handles boundaries of ANY size including single edges
     */
    fillHole(boundary) {
        if (!boundary || boundary.length === 0) {
            return null;
        }

        const positions = [];

        if (boundary.length === 1) {
            // Single edge - create a VOLUME-ADDING triangle by offsetting the third point
            const edge = boundary[0];
            const p1 = edge.positions[0];
            const p2 = edge.positions[1];

            // Calculate edge vector
            const edgeX = p2[0] - p1[0];
            const edgeY = p2[1] - p1[1];
            const edgeZ = p2[2] - p1[2];
            const edgeLength = Math.sqrt(edgeX*edgeX + edgeY*edgeY + edgeZ*edgeZ);

            // Calculate midpoint
            const midX = (p1[0] + p2[0]) / 2;
            const midY = (p1[1] + p2[1]) / 2;
            const midZ = (p1[2] + p2[2]) / 2;

            // Create a perpendicular offset (inward) - use a fraction of edge length
            const offsetDist = edgeLength * 0.1; // 10% of edge length for volume

            // Simple offset: move along average normal (approximate)
            // Use a vector perpendicular to edge by rotating 90° in XY plane
            const perpX = -edgeY;
            const perpY = edgeX;
            const perpZ = 0;
            const perpLength = Math.sqrt(perpX*perpX + perpY*perpY + perpZ*perpZ);

            // Normalize and scale
            let offsetX, offsetY, offsetZ;
            if (perpLength > 0.001) {
                offsetX = (perpX / perpLength) * offsetDist;
                offsetY = (perpY / perpLength) * offsetDist;
                offsetZ = (perpZ / perpLength) * offsetDist;
            } else {
                // Edge is vertical, offset in XY plane
                offsetX = offsetDist;
                offsetY = 0;
                offsetZ = 0;
            }

            // Create a triangle with OFFSET third point for volume
            positions.push(
                p1[0], p1[1], p1[2],
                p2[0], p2[1], p2[2],
                midX + offsetX, midY + offsetY, midZ + offsetZ
            );

        } else if (boundary.length === 2) {
            // Two edges - create triangles to bridge the gap
            const edge1 = boundary[0];
            const edge2 = boundary[1];

            // Create two triangles to close the gap
            positions.push(
                edge1.positions[0][0], edge1.positions[0][1], edge1.positions[0][2],
                edge1.positions[1][0], edge1.positions[1][1], edge1.positions[1][2],
                edge2.positions[0][0], edge2.positions[0][1], edge2.positions[0][2]
            );

            positions.push(
                edge1.positions[1][0], edge1.positions[1][1], edge1.positions[1][2],
                edge2.positions[1][0], edge2.positions[1][1], edge2.positions[1][2],
                edge2.positions[0][0], edge2.positions[0][1], edge2.positions[0][2]
            );

        } else {
            // Standard fan triangulation for 3+ edges
            const firstPos = boundary[0].positions[0];

            for (let i = 1; i < boundary.length - 1; i++) {
                const p1 = boundary[i].positions[0];
                const p2 = boundary[i + 1].positions[0];

                positions.push(
                    firstPos[0], firstPos[1], firstPos[2],
                    p1[0], p1[1], p1[2],
                    p2[0], p2[1], p2[2]
                );
            }
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
            console.log('⚠️ No repair geometries to visualize');
            return null;
        }

        console.log(`🎨 Adding repair visualization for ${repairGeometries.length} repaired areas`);

        // Create material for repaired areas (GRAY - to show what changed)
        const repairMaterial = new THREE.MeshPhongMaterial({
            color: 0x808080,  // Medium gray
            emissive: 0x404040,  // Subtle darker gray glow
            shininess: 30,
            side: THREE.DoubleSide,
            transparent: false,
            opacity: 1.0,
            wireframe: false
        });

        console.log('   Repair areas will be shown in GRAY color for clear visibility');

        // Merge all repair geometries
        const mergedRepairGeometry = new THREE.BufferGeometry();
        const allPositions = [];

        for (const repairGeo of repairGeometries) {
            const pos = repairGeo.attributes.position.array;
            for (let i = 0; i < pos.length; i++) {
                allPositions.push(pos[i]);
            }
        }

        mergedRepairGeometry.setAttribute('position', new THREE.Float32BufferAttribute(allPositions, 3));
        mergedRepairGeometry.computeVertexNormals();

        console.log(`   Merged ${repairGeometries.length} repair geometries into ${allPositions.length / 9} triangles`);

        // Create mesh for repaired areas (visual indicator)
        const repairMesh = new THREE.Mesh(mergedRepairGeometry, repairMaterial);
        repairMesh.userData.isRepairVisualization = true;
        repairMesh.userData.originalMesh = originalMesh;
        repairMesh.userData.persistent = true; // Mark as persistent - don't remove
        repairMesh.name = 'RepairVisualization_' + (originalMesh.name || 'mesh');

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

        // CRITICAL: Store reference to repair mesh so it persists
        if (!viewer.repairMeshes) {
            viewer.repairMeshes = [];
        }
        viewer.repairMeshes.push(repairMesh);
        console.log('   ✅ Added visual repair mesh to scene (bright cyan-green)');
        console.log('   ✅ Repair mesh stored in viewer.repairMeshes (persistent)');

        // CRITICAL: Merge the original and repair geometries for volume calculation
        let mergedGeometry = null;
        try {
            const originalGeometry = originalMesh.geometry;
            mergedGeometry = this.mergeGeometries(originalGeometry, mergedRepairGeometry);

            if (mergedGeometry) {
                console.log('   ✅ Merged original + repair geometries for accurate volume calculation');
                console.log(`   Original: ${originalGeometry.attributes.position.count} vertices`);
                console.log(`   Repair: ${mergedRepairGeometry.attributes.position.count} vertices`);
                console.log(`   Merged: ${mergedGeometry.attributes.position.count} vertices`);
            }
        } catch (mergeError) {
            console.error('❌ Could not merge geometries:', mergeError);
        }

        // Render
        if (viewer.render) {
            viewer.render();
        }

        console.log('✅ Repair visualization complete');

        // Show notification
        if (window.showToolbarNotification) {
            showToolbarNotification(
                `Repaired areas shown in bright green/cyan color`,
                'success',
                3000
            );
        }

        // Return the merged geometry so caller can update fileData
        return mergedGeometry;
    },

    /**
     * Calculate volume from geometry (for comparison)
     */
    calculateGeometryVolume(geometry) {
        if (!geometry || !geometry.attributes || !geometry.attributes.position) {
            return 0;
        }

        const position = geometry.attributes.position;
        const vertices = position.array;
        let volume = 0;

        // Calculate volume using signed volume of triangles
        for (let i = 0; i < vertices.length; i += 9) {
            const v1 = [vertices[i], vertices[i + 1], vertices[i + 2]];
            const v2 = [vertices[i + 3], vertices[i + 4], vertices[i + 5]];
            const v3 = [vertices[i + 6], vertices[i + 7], vertices[i + 8]];

            // Signed volume of tetrahedron formed by origin and triangle
            const signedVol = (v1[0] * v2[1] * v3[2] + v2[0] * v3[1] * v1[2] + v3[0] * v1[1] * v2[2] -
                              v1[0] * v3[1] * v2[2] - v2[0] * v1[1] * v3[2] - v3[0] * v2[1] * v1[2]) / 6.0;
            volume += signedVol;
        }

        return Math.abs(volume); // Return in mm³
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
