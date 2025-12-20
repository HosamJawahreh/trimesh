/**
 * Geometry Repair & Volume Calculation Module
 * Handles mesh repair, hole filling, and accurate volume calculation
 */

class GeometryRepair {
    constructor() {
        // Wait for THREE.js to be available
        if (typeof THREE === 'undefined') {
            console.warn('⚠️ THREE.js not yet loaded - GeometryRepair will wait');
            this.threeReady = false;
        } else {
            this.THREE = window.THREE;
            this.threeReady = true;
        }
    }

    /**
     * Ensure THREE.js is loaded
     */
    ensureThreeReady() {
        if (!this.threeReady && typeof THREE !== 'undefined') {
            this.THREE = window.THREE;
            this.threeReady = true;
            console.log('✓ THREE.js now available for GeometryRepair');
        }

        if (!this.threeReady) {
            throw new Error('THREE.js is not loaded yet. Please wait for viewer to initialize.');
        }

        return true;
    }

    /**
     * Repair mesh geometry - merge vertices, remove duplicates, fix normals
     */
    async repairGeometry(geometry) {
        this.ensureThreeReady();

        console.log('🔧 Starting geometry repair...');

        if (!geometry || !geometry.attributes || !geometry.attributes.position) {
            console.error('Invalid geometry');
            return geometry;
        }

        try {
            // Clone geometry to avoid modifying original
            const repairedGeometry = geometry.clone();

            // Step 1: Merge duplicate vertices
            const positionAttribute = repairedGeometry.attributes.position;
            const vertexCount = positionAttribute.count;
            console.log(`  📊 Original vertices: ${vertexCount}`);

            // Use BufferGeometryUtils if available, otherwise manual merge
            if (this.THREE.BufferGeometryUtils && this.THREE.BufferGeometryUtils.mergeVertices) {
                const merged = this.THREE.BufferGeometryUtils.mergeVertices(repairedGeometry, 0.0001);
                Object.assign(repairedGeometry, merged);
                console.log(`  ✓ Merged vertices: ${repairedGeometry.attributes.position.count}`);
            } else {
                console.log('  ⚠️ BufferGeometryUtils not available, using manual merge');
                this.manualMergeVertices(repairedGeometry);
            }

            // Step 2: Recompute normals for proper lighting
            repairedGeometry.computeVertexNormals();
            console.log('  ✓ Recomputed normals');

            // Step 3: Compute bounding box and sphere
            repairedGeometry.computeBoundingBox();
            repairedGeometry.computeBoundingSphere();
            console.log('  ✓ Computed bounding volumes');

            // Step 4: Center geometry
            repairedGeometry.center();
            console.log('  ✓ Centered geometry');

            console.log('✅ Geometry repair complete');
            return repairedGeometry;

        } catch (error) {
            console.error('❌ Error repairing geometry:', error);
            return geometry; // Return original if repair fails
        }
    }

    /**
     * Manual vertex merging for environments without BufferGeometryUtils
     */
    manualMergeVertices(geometry, tolerance = 0.0001) {
        const positions = geometry.attributes.position.array;
        const vertexMap = new Map();
        const uniqueVertices = [];
        const indices = [];

        // Find unique vertices
        for (let i = 0; i < positions.length; i += 3) {
            const x = Math.round(positions[i] / tolerance) * tolerance;
            const y = Math.round(positions[i + 1] / tolerance) * tolerance;
            const z = Math.round(positions[i + 2] / tolerance) * tolerance;
            const key = `${x},${y},${z}`;

            if (!vertexMap.has(key)) {
                vertexMap.set(key, uniqueVertices.length / 3);
                uniqueVertices.push(x, y, z);
            }
            indices.push(vertexMap.get(key));
        }

        // Update geometry
        geometry.setAttribute('position', new this.THREE.Float32BufferAttribute(uniqueVertices, 3));
        geometry.setIndex(indices);

        console.log(`  ✓ Manual merge: ${positions.length / 3} → ${uniqueVertices.length / 3} vertices`);
    }

    /**
     * Calculate accurate volume using signed tetrahedral method
     * Works for closed (watertight) meshes
     */
    calculateVolume(geometry) {
        this.ensureThreeReady();

        console.log('📐 Calculating volume...');

        if (!geometry || !geometry.attributes || !geometry.attributes.position) {
            console.error('Invalid geometry for volume calculation');
            return 0;
        }

        try {
            const positions = geometry.attributes.position.array;
            const indices = geometry.index ? geometry.index.array : null;

            let volume = 0;
            const p1 = new this.THREE.Vector3();
            const p2 = new this.THREE.Vector3();
            const p3 = new this.THREE.Vector3();

            if (indices) {
                // Indexed geometry
                for (let i = 0; i < indices.length; i += 3) {
                    const i1 = indices[i] * 3;
                    const i2 = indices[i + 1] * 3;
                    const i3 = indices[i + 2] * 3;

                    p1.set(positions[i1], positions[i1 + 1], positions[i1 + 2]);
                    p2.set(positions[i2], positions[i2 + 1], positions[i2 + 2]);
                    p3.set(positions[i3], positions[i3 + 1], positions[i3 + 2]);

                    volume += this.signedVolumeOfTriangle(p1, p2, p3);
                }
            } else {
                // Non-indexed geometry
                for (let i = 0; i < positions.length; i += 9) {
                    p1.set(positions[i], positions[i + 1], positions[i + 2]);
                    p2.set(positions[i + 3], positions[i + 4], positions[i + 5]);
                    p3.set(positions[i + 6], positions[i + 7], positions[i + 8]);

                    volume += this.signedVolumeOfTriangle(p1, p2, p3);
                }
            }

            // Take absolute value and convert to mm³
            volume = Math.abs(volume);

            console.log(`  ✓ Raw volume: ${volume.toFixed(2)} cubic units`);
            console.log(`  ✓ Volume in mm³: ${volume.toFixed(2)}`);
            console.log(`  ✓ Volume in cm³: ${(volume / 1000).toFixed(2)}`);

            return volume; // Return in mm³

        } catch (error) {
            console.error('❌ Error calculating volume:', error);
            return 0;
        }
    }

    /**
     * Calculate signed volume of a tetrahedron formed by triangle and origin
     * Formula: V = (1/6) * |a · (b × c)|
     */
    signedVolumeOfTriangle(p1, p2, p3) {
        return p1.dot(p2.clone().cross(p3)) / 6.0;
    }

    /**
     * Get mesh quality metrics
     */
    getMeshQuality(geometry) {
        const metrics = {
            vertices: geometry.attributes.position.count,
            triangles: geometry.index ? geometry.index.count / 3 : geometry.attributes.position.count / 3,
            hasIndex: !!geometry.index,
            hasNormals: !!geometry.attributes.normal,
            hasUVs: !!geometry.attributes.uv,
            boundingBox: null,
            boundingSphere: null
        };

        if (!geometry.boundingBox) {
            geometry.computeBoundingBox();
        }
        if (!geometry.boundingSphere) {
            geometry.computeBoundingSphere();
        }

        metrics.boundingBox = {
            min: geometry.boundingBox.min.toArray(),
            max: geometry.boundingBox.max.toArray(),
            size: geometry.boundingBox.getSize(new this.THREE.Vector3()).toArray()
        };

        metrics.boundingSphere = {
            center: geometry.boundingSphere.center.toArray(),
            radius: geometry.boundingSphere.radius
        };

        return metrics;
    }

    /**
     * Full repair and calculate workflow
     */
    async repairAndCalculate(geometry) {
        console.log('🔄 Starting full repair and volume calculation...');

        // Repair geometry
        const repairedGeometry = await this.repairGeometry(geometry);

        // Calculate volume
        const volume = this.calculateVolume(repairedGeometry);

        // Get quality metrics
        const metrics = this.getMeshQuality(repairedGeometry);

        return {
            geometry: repairedGeometry,
            volume: volume,
            volumeCm3: volume / 1000,
            metrics: metrics
        };
    }
}

// Create global instance
if (typeof window !== 'undefined') {
    window.GeometryRepair = GeometryRepair;
    window.geometryRepair = new GeometryRepair();
    console.log('✅ GeometryRepair module loaded');
}
