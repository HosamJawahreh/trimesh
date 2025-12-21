/**
 * ========================================
 * ACCURATE VOLUME CALCULATOR
 * Uses proven signed volume algorithm
 * ========================================
 */

console.log('📐 Loading Accurate Volume Calculator...');

window.VolumeCalculator = {
    
    /**
     * Calculate accurate volume from THREE.js geometry
     * Uses signed tetrahedron volume method
     */
    calculateVolume(geometry) {
        if (!geometry || !geometry.attributes || !geometry.attributes.position) {
            console.error('❌ Invalid geometry for volume calculation');
            return { cm3: 0, mm3: 0 };
        }

        const positions = geometry.attributes.position.array;
        const indices = geometry.index ? geometry.index.array : null;

        let signedVolume = 0;
        let triangleCount = 0;

        console.log('📐 Volume calculation started:');
        console.log(`   Vertices: ${positions.length / 3}`);
        console.log(`   Indexed: ${!!indices}`);

        if (indices && indices.length > 0) {
            // Indexed geometry
            for (let i = 0; i < indices.length; i += 3) {
                const i0 = indices[i] * 3;
                const i1 = indices[i + 1] * 3;
                const i2 = indices[i + 2] * 3;

                // Get triangle vertices
                const v0x = positions[i0];
                const v0y = positions[i0 + 1];
                const v0z = positions[i0 + 2];

                const v1x = positions[i1];
                const v1y = positions[i1 + 1];
                const v1z = positions[i1 + 2];

                const v2x = positions[i2];
                const v2y = positions[i2 + 1];
                const v2z = positions[i2 + 2];

                // Calculate signed volume of tetrahedron formed by origin and triangle
                // Formula: V = (1/6) * (v0 · (v1 × v2))
                
                // Cross product: v1 × v2
                const crossX = v1y * v2z - v1z * v2y;
                const crossY = v1z * v2x - v1x * v2z;
                const crossZ = v1x * v2y - v1y * v2x;

                // Dot product: v0 · (v1 × v2)
                const dot = v0x * crossX + v0y * crossY + v0z * crossZ;

                signedVolume += dot;
                triangleCount++;
            }
        } else if (positions.length >= 9) {
            // Non-indexed geometry - vertices are in sequence (every 3 vertices = 1 triangle)
            for (let i = 0; i < positions.length; i += 9) {
                const v0x = positions[i];
                const v0y = positions[i + 1];
                const v0z = positions[i + 2];

                const v1x = positions[i + 3];
                const v1y = positions[i + 4];
                const v1z = positions[i + 5];

                const v2x = positions[i + 6];
                const v2y = positions[i + 7];
                const v2z = positions[i + 8];

                // Cross product: v1 × v2
                const crossX = v1y * v2z - v1z * v2y;
                const crossY = v1z * v2x - v1x * v2z;
                const crossZ = v1x * v2y - v1y * v2x;

                // Dot product: v0 · (v1 × v2)
                const dot = v0x * crossX + v0y * crossY + v0z * crossZ;

                signedVolume += dot;
                triangleCount++;
            }
        } else {
            console.error('❌ Not enough vertices for volume calculation');
            return { cm3: 0, mm3: 0 };
        }

        // Final volume calculation
        const volumeMM3 = Math.abs(signedVolume / 6.0);
        const volumeCM3 = volumeMM3 / 1000.0;

        console.log(`   Triangles processed: ${triangleCount}`);
        console.log(`   Signed volume: ${signedVolume.toFixed(2)}`);
        console.log(`   Volume: ${volumeCM3.toFixed(2)} cm³ (${volumeMM3.toFixed(2)} mm³)`);

        return {
            cm3: volumeCM3,
            mm3: volumeMM3
        };
    },

    /**
     * Calculate volume for all uploaded files
     */
    calculateTotalVolume(viewer) {
        if (!viewer || !viewer.uploadedFiles || viewer.uploadedFiles.length === 0) {
            console.warn('⚠️ No files uploaded');
            return { cm3: 0, mm3: 0, files: [] };
        }

        let totalCM3 = 0;
        let totalMM3 = 0;
        const fileVolumes = [];

        console.log(`📐 Calculating volume for ${viewer.uploadedFiles.length} files...`);

        for (const fileData of viewer.uploadedFiles) {
            const geometry = fileData.geometry || (fileData.mesh && fileData.mesh.geometry);
            
            if (!geometry) {
                console.warn(`⚠️ No geometry for ${fileData.file?.name}`);
                continue;
            }

            const volume = this.calculateVolume(geometry);
            
            totalCM3 += volume.cm3;
            totalMM3 += volume.mm3;

            fileVolumes.push({
                fileName: fileData.file?.name || 'Unknown',
                volume: volume
            });

            console.log(`   ✅ ${fileData.file?.name}: ${volume.cm3.toFixed(2)} cm³`);
        }

        console.log(`📊 Total volume: ${totalCM3.toFixed(2)} cm³`);

        return {
            cm3: totalCM3,
            mm3: totalMM3,
            files: fileVolumes
        };
    }
};

console.log('✅ Accurate Volume Calculator loaded');
