/**
 * ========================================
 * DEBUG CALCULATOR - Troubleshooting Tool
 * ========================================
 */

console.log('🐛 Loading Debug Calculator...');

window.DebugCalculator = {
    
    /**
     * Run comprehensive diagnostics
     */
    runDiagnostics() {
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('🐛 RUNNING DIAGNOSTICS');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Check 1: Viewer exists
        console.log('\n1️⃣ CHECKING VIEWER:');
        if (window.viewerGeneral) {
            console.log('   ✅ viewerGeneral exists');
            console.log(`   Files uploaded: ${window.viewerGeneral.uploadedFiles?.length || 0}`);
            
            if (window.viewerGeneral.uploadedFiles && window.viewerGeneral.uploadedFiles.length > 0) {
                const file = window.viewerGeneral.uploadedFiles[0];
                console.log(`   File name: ${file.file?.name || 'unknown'}`);
                console.log(`   Has mesh: ${!!file.mesh}`);
                console.log(`   Has geometry: ${!!file.geometry}`);
                
                const geometry = file.geometry || (file.mesh && file.mesh.geometry);
                if (geometry) {
                    console.log(`   Geometry type: ${geometry.type}`);
                    console.log(`   Vertices: ${geometry.attributes.position?.count || 0}`);
                    console.log(`   Has index: ${!!geometry.index}`);
                    console.log(`   Triangles: ${geometry.index ? geometry.index.count / 3 : geometry.attributes.position.count / 3}`);
                }
            }
        } else {
            console.error('   ❌ viewerGeneral NOT found');
        }

        // Check 2: Calculator modules
        console.log('\n2️⃣ CHECKING MODULES:');
        console.log(`   VolumeCalculator: ${!!window.VolumeCalculator ? '✅' : '❌'}`);
        console.log(`   PricingCalculator: ${!!window.PricingCalculator ? '✅' : '❌'}`);
        console.log(`   SimpleSaveCalculate: ${!!window.SimpleSaveCalculate ? '✅' : '❌'}`);

        // Check 3: UI Elements
        console.log('\n3️⃣ CHECKING UI ELEMENTS:');
        const volumeElements = document.querySelectorAll('#quoteTotalVolumeGeneral');
        const priceElements = document.querySelectorAll('#quoteTotalPriceGeneral');
        const techSelect = document.getElementById('technologySelectGeneral');
        const matSelect = document.getElementById('materialSelectGeneral');
        const saveBtn = document.getElementById('saveCalculationsBtn');

        console.log(`   Volume displays: ${volumeElements.length}`);
        console.log(`   Price displays: ${priceElements.length}`);
        console.log(`   Technology select: ${techSelect ? '✅' : '❌'} (value: ${techSelect?.value || 'N/A'})`);
        console.log(`   Material select: ${matSelect ? '✅' : '❌'} (value: ${matSelect?.value || 'N/A'})`);
        console.log(`   Save button: ${saveBtn ? '✅' : '❌'}`);

        // Check 4: Test volume calculation
        if (window.viewerGeneral && window.viewerGeneral.uploadedFiles && window.viewerGeneral.uploadedFiles.length > 0) {
            console.log('\n4️⃣ TESTING VOLUME CALCULATION:');
            const file = window.viewerGeneral.uploadedFiles[0];
            const geometry = file.geometry || (file.mesh && file.mesh.geometry);
            
            if (geometry && window.VolumeCalculator) {
                try {
                    const result = window.VolumeCalculator.calculateVolume(geometry);
                    console.log(`   ✅ Volume calculation: ${result.cm3.toFixed(2)} cm³`);
                    console.log(`   Volume in mm³: ${result.mm3.toFixed(2)}`);
                } catch (error) {
                    console.error(`   ❌ Volume calculation failed:`, error);
                }
            }
        }

        // Check 5: Test pricing calculation
        console.log('\n5️⃣ TESTING PRICING CALCULATION:');
        if (window.PricingCalculator) {
            const testVolume = 4.58; // Test with a known volume
            const tech = document.getElementById('technologySelectGeneral')?.value || 'fdm';
            const mat = document.getElementById('materialSelectGeneral')?.value || 'pla';
            
            try {
                const result = window.PricingCalculator.calculatePrice(testVolume, tech, mat);
                console.log(`   ✅ Test pricing (${testVolume} cm³, ${tech}/${mat}):`);
                console.log(`      Price per cm³: $${result.pricePerCm3.toFixed(2)}`);
                console.log(`      Total price: $${result.totalPrice.toFixed(2)}`);
            } catch (error) {
                console.error(`   ❌ Pricing calculation failed:`, error);
            }
        }

        console.log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('🐛 DIAGNOSTICS COMPLETE');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n');
    },

    /**
     * Manual volume calculation test
     */
    testVolumeCalculation() {
        if (!window.viewerGeneral || !window.viewerGeneral.uploadedFiles || window.viewerGeneral.uploadedFiles.length === 0) {
            console.error('❌ No model loaded');
            return;
        }

        const file = window.viewerGeneral.uploadedFiles[0];
        const geometry = file.geometry || (file.mesh && file.mesh.geometry);
        
        if (!geometry) {
            console.error('❌ No geometry found');
            return;
        }

        console.log('📐 Manual Volume Test:');
        console.log(`   File: ${file.file?.name}`);
        console.log(`   Vertices: ${geometry.attributes.position.count}`);
        console.log(`   Indexed: ${!!geometry.index}`);
        
        const result = window.VolumeCalculator.calculateVolume(geometry);
        console.log(`   Result: ${result.cm3.toFixed(2)} cm³ (${result.mm3.toFixed(2)} mm³)`);
        
        return result;
    },

    /**
     * Manual pricing test
     */
    testPricing(volume, tech, material) {
        volume = volume || 4.58;
        tech = tech || 'fdm';
        material = material || 'pla';

        console.log(`💰 Manual Pricing Test:`);
        console.log(`   Volume: ${volume} cm³`);
        console.log(`   Technology: ${tech}`);
        console.log(`   Material: ${material}`);

        const result = window.PricingCalculator.calculatePrice(volume, tech, material);
        console.log(`   Price per cm³: $${result.pricePerCm3.toFixed(2)}`);
        console.log(`   Total: $${result.totalPrice.toFixed(2)}`);

        return result;
    },

    /**
     * Test UI updates
     */
    testUIUpdate() {
        console.log('🎨 Testing UI Update:');
        
        const testData = {
            volume: 4.58,
            price: 2.29,
            printTime: '2.3h'
        };

        window.SimpleSaveCalculate.updateUI('General', testData);
        console.log('✅ UI update test complete - check sidebar');
    }
};

// Auto-run diagnostics on load
setTimeout(() => {
    console.log('\n🔍 Auto-running diagnostics in 2 seconds...');
    setTimeout(() => window.DebugCalculator.runDiagnostics(), 2000);
}, 100);

console.log('✅ Debug Calculator loaded');
console.log('💡 Type window.DebugCalculator.runDiagnostics() to run diagnostics');
console.log('💡 Type window.DebugCalculator.testVolumeCalculation() to test volume');
console.log('💡 Type window.DebugCalculator.testPricing() to test pricing');
console.log('💡 Type window.DebugCalculator.testUIUpdate() to test UI update');
