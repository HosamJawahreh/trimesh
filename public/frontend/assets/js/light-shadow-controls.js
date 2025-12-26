/**
 * Light and Shadow Control System for 3D Viewer
 * Adds interactive sliders with real-time percentage display
 */

console.log('🎨 Loading Enhanced Light & Shadow Controls...');

// Initialize controls when viewer is ready
window.addEventListener('viewersReady', () => {
    console.log('💡 Setting up enhanced light and shadow controls...');
    console.log('💡 window.viewerGeneral exists:', !!window.viewerGeneral);

    // Light Intensity Control
    const lightSlider = document.getElementById('lightIntensitySlider');
    const lightValueDisplay = document.getElementById('lightIntensityValue');

    console.log('💡 Light slider found:', !!lightSlider);
    console.log('💡 Light value display found:', !!lightValueDisplay);

    if (lightSlider && window.viewerGeneral) {
        console.log('✅ Initializing light control...');

        // Update function
        const updateLightIntensity = (intensity) => {
            const percentage = Math.round((intensity / 2) * 100);
            console.log(`💡 Updating light to ${intensity} (${percentage}%)`);

            // Update value display
            if (lightValueDisplay) {
                lightValueDisplay.textContent = `${percentage}%`;
                lightValueDisplay.style.color = intensity < 0.3 ? '#e67e22' : intensity > 1.5 ? '#f39c12' : '#f39c12';
            }

            // Update viewer
            if (window.viewerGeneral && typeof window.viewerGeneral.setLightIntensity === 'function') {
                window.viewerGeneral.setLightIntensity(intensity);
            } else {
                console.error('❌ setLightIntensity method not found!');
            }

            // Update slider background gradient
            const gradientPercentage = (intensity / 2) * 100;
            lightSlider.style.background = `linear-gradient(to right, #f39c12 0%, #f39c12 ${gradientPercentage}%, #e0e0e0 ${gradientPercentage}%, #e0e0e0 100%)`;
        };

        // Input event for real-time updates
        lightSlider.addEventListener('input', function() {
            const intensity = parseFloat(this.value);
            console.log(`💡 Light slider input: ${intensity}`);
            updateLightIntensity(intensity);
        });

        // Change event for final value
        lightSlider.addEventListener('change', function() {
            const intensity = parseFloat(this.value);
            console.log(`💡 Light intensity finalized at: ${Math.round((intensity/2)*100)}%`);
        });

        // Initialize
        const initialIntensity = window.viewerGeneral.getLightIntensity ? window.viewerGeneral.getLightIntensity() : 0.9;
        console.log(`💡 Initial light intensity: ${initialIntensity}`);
        lightSlider.value = initialIntensity;
        updateLightIntensity(initialIntensity);
    } else {
        console.error('❌ Light slider or viewer not found!');
    }

    // Shadow Intensity Control
    const shadowSlider = document.getElementById('shadowIntensitySlider');
    const shadowValueDisplay = document.getElementById('shadowIntensityValue');

    console.log('🌑 Shadow slider found:', !!shadowSlider);
    console.log('🌑 Shadow value display found:', !!shadowValueDisplay);

    if (shadowSlider && window.viewerGeneral) {
        console.log('✅ Initializing shadow control...');

        // Update function
        const updateShadowIntensity = (intensity) => {
            const percentage = Math.round(intensity * 100);
            console.log(`🌑 Updating shadow to ${intensity} (${percentage}%)`);

            // Update value display
            if (shadowValueDisplay) {
                shadowValueDisplay.textContent = `${percentage}%`;
                shadowValueDisplay.style.color = intensity < 0.3 ? '#95a5a6' : '#7f8c8d';
            }

            // Update viewer
            if (window.viewerGeneral && typeof window.viewerGeneral.setShadowIntensity === 'function') {
                window.viewerGeneral.setShadowIntensity(intensity);
            } else {
                console.error('❌ setShadowIntensity method not found!');
            }

            // Update slider background gradient
            const gradientPercentage = intensity * 100;
            shadowSlider.style.background = `linear-gradient(to right, #7f8c8d 0%, #7f8c8d ${gradientPercentage}%, #e0e0e0 ${gradientPercentage}%, #e0e0e0 100%)`;
        };

        // Input event for real-time updates
        shadowSlider.addEventListener('input', function() {
            const intensity = parseFloat(this.value);
            console.log(`🌑 Shadow slider input: ${intensity}`);
            updateShadowIntensity(intensity);
        });

        // Change event for final value
        shadowSlider.addEventListener('change', function() {
            const intensity = parseFloat(this.value);
            console.log(`🌑 Shadow intensity finalized at: ${Math.round(intensity*100)}%`);
        });

        // Initialize
        const initialIntensity = window.viewerGeneral.getShadowIntensity ? window.viewerGeneral.getShadowIntensity() : 1.0;
        console.log(`🌑 Initial shadow intensity: ${initialIntensity}`);
        shadowSlider.value = initialIntensity;
        updateShadowIntensity(initialIntensity);
    } else {
        console.error('❌ Shadow slider or viewer not found!');
    }

    console.log('✅ Enhanced light and shadow controls ready!');
});

// Add CSS for better slider styling
const style = document.createElement('style');
style.textContent = `
    /* Webkit browsers (Chrome, Safari, Edge) */
    .lighting-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        border: 2px solid #4a90e2;
        transition: all 0.2s ease;
    }

    .lighting-slider::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 3px 10px rgba(0,0,0,0.4);
    }

    .lighting-slider::-webkit-slider-thumb:active {
        transform: scale(0.95);
    }

    /* Firefox */
    .lighting-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: white;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        border: 2px solid #4a90e2;
        transition: all 0.2s ease;
    }

    .lighting-slider::-moz-range-thumb:hover {
        transform: scale(1.2);
        box-shadow: 0 3px 10px rgba(0,0,0,0.4);
    }

    .lighting-slider:hover {
        height: 8px;
        border-radius: 4px;
    }

    /* Add smooth transitions */
    .lighting-slider {
        transition: height 0.2s ease, border-radius 0.2s ease;
    }
`;
document.head.appendChild(style);

console.log('🎨 Enhanced slider styles applied');
