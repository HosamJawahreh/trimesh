// 🔬 PASTE THIS IN BROWSER CONSOLE TO DIAGNOSE TOOLBAR ISSUES
// Copy everything below and paste in Console (F12)

console.clear();
console.log('%c🔬 TOOLBAR DIAGNOSTIC SCRIPT', 'font-size:20px; font-weight:bold; color:#00ff00; background:#000; padding:10px;');
console.log('Running comprehensive diagnostics...\n');

// Test 1: Check if viewers exist
console.log('%c1️⃣ Checking Global Viewer Objects', 'font-weight:bold; color:#00aaff;');
console.log('   window.viewerGeneral:', !!window.viewerGeneral ? '✅ EXISTS' : '❌ MISSING');
console.log('   window.viewerMedical:', !!window.viewerMedical ? '✅ EXISTS' : '❌ MISSING');
console.log('   window.viewer:', !!window.viewer ? '✅ EXISTS' : '❌ MISSING');

if (!window.viewerGeneral && !window.viewer) {
    console.error('%c❌ CRITICAL ERROR: No viewer found!', 'color:red; font-weight:bold;');
    console.error('The 3d-viewer-pro.js did not initialize properly.');
    console.error('Solution: Hard refresh (Ctrl+Shift+R) and wait 3 seconds before testing.');
} else {
    console.log('✅ Viewer objects found!\n');
}

// Test 2: Check viewer initialization
console.log('%c2️⃣ Checking Viewer Initialization', 'font-weight:bold; color:#00aaff;');
const viewer = window.viewerGeneral || window.viewer;
if (viewer) {
    console.log('   Initialized:', viewer.initialized ? '✅ YES' : '❌ NO');
    console.log('   Scene:', viewer.scene ? '✅ EXISTS' : '❌ MISSING');
    console.log('   Camera:', viewer.camera ? '✅ EXISTS' : '❌ MISSING');
    console.log('   Renderer:', viewer.renderer ? '✅ EXISTS' : '❌ MISSING');
    console.log('   Controls:', viewer.controls ? '✅ EXISTS' : '❌ MISSING');
    
    if (viewer.renderer && viewer.renderer.domElement) {
        const canvas = viewer.renderer.domElement;
        const width = canvas.width;
        const height = canvas.height;
        console.log('   Canvas size:', width + 'x' + height, 
            (width > 0 && height > 0) ? '✅ VALID' : '❌ INVALID (0x0)');
        
        if (width === 0 || height === 0) {
            console.error('%c⚠️ WARNING: Canvas has 0 dimensions!', 'color:orange; font-weight:bold;');
            console.error('This will cause screenshot errors.');
            console.error('Solution: Make sure viewer container is visible, try resizing window.');
        }
    }
    
    if (viewer.scene) {
        console.log('   Scene children count:', viewer.scene.children.length);
        
        // Check for grid
        const grid = viewer.scene.children.find(c => 
            c.type === 'GridHelper' || 
            c.name === 'grid' || 
            c.userData?.isGridHelper
        );
        
        if (grid) {
            console.log('   Grid helper:', '✅ FOUND (visible: ' + grid.visible + ')');
        } else {
            console.log('   Grid helper:', '❌ NOT FOUND');
        }
    }
} else {
    console.error('❌ Cannot check initialization - no viewer!');
}
console.log('');

// Test 3: Check toolbar handler
console.log('%c3️⃣ Checking Toolbar Handler', 'font-weight:bold; color:#00aaff;');
console.log('   window.toolbarHandler:', !!window.toolbarHandler ? '✅ EXISTS' : '❌ MISSING');

if (window.toolbarHandler) {
    const methods = Object.keys(window.toolbarHandler);
    console.log('   Methods count:', methods.length);
    console.log('   Key methods:');
    
    const keyMethods = [
        'toggleGridMain',
        'toggleBoundingBox',
        'toggleAxis',
        'takeScreenshot',
        'toggleMeasurement',
        'shareModel',
        'saveAndCalculate'
    ];
    
    keyMethods.forEach(method => {
        const exists = typeof window.toolbarHandler[method] === 'function';
        console.log('      ' + method + ':', exists ? '✅' : '❌');
    });
} else {
    console.error('%c❌ CRITICAL ERROR: toolbarHandler missing!', 'color:red; font-weight:bold;');
    console.error('The inline script did not execute.');
}
console.log('');

// Test 4: Check viewer container visibility
console.log('%c4️⃣ Checking Viewer Container', 'font-weight:bold; color:#00aaff;');
const generalDiv = document.getElementById('viewer3dGeneral');
const medicalDiv = document.getElementById('viewer3dMedical');

if (generalDiv) {
    const width = generalDiv.offsetWidth;
    const height = generalDiv.offsetHeight;
    const visible = generalDiv.style.display !== 'none';
    console.log('   General container:');
    console.log('      Size:', width + 'x' + height, (width > 0) ? '✅' : '❌');
    console.log('      Visible:', visible ? '✅' : '❌');
} else {
    console.log('   General container: ❌ NOT FOUND');
}

if (medicalDiv) {
    const width = medicalDiv.offsetWidth;
    const height = medicalDiv.offsetHeight;
    const visible = medicalDiv.style.display !== 'none';
    console.log('   Medical container:');
    console.log('      Size:', width + 'x' + height, (width > 0) ? '✅' : '❌');
    console.log('      Visible:', visible ? '✅' : '❌');
} else {
    console.log('   Medical container: ❌ NOT FOUND');
}
console.log('');

// Test 5: Try to toggle grid manually
console.log('%c5️⃣ Testing Grid Toggle Function', 'font-weight:bold; color:#00aaff;');
if (window.toolbarHandler && window.toolbarHandler.toggleGridMain) {
    try {
        console.log('   Attempting to toggle grid...');
        window.toolbarHandler.toggleGridMain('General');
        console.log('   ✅ Function executed (check console for logs)');
    } catch (error) {
        console.error('   ❌ Function failed:', error.message);
    }
} else {
    console.error('   ❌ Cannot test - toggleGridMain not found');
}
console.log('');

// Summary
console.log('%c📋 DIAGNOSTIC SUMMARY', 'font-size:16px; font-weight:bold; color:#ffaa00; background:#000; padding:10px;');

let issues = [];
if (!window.viewerGeneral && !window.viewer) issues.push('Viewer not initialized');
if (!window.toolbarHandler) issues.push('Toolbar handler missing');
if (viewer && viewer.renderer && viewer.renderer.domElement.width === 0) issues.push('Canvas has 0 width');

if (issues.length === 0) {
    console.log('%c✅ NO CRITICAL ISSUES FOUND!', 'color:green; font-weight:bold; font-size:14px;');
    console.log('Toolbar buttons should be working. If not:');
    console.log('1. Try uploading a 3D file first');
    console.log('2. Click buttons and check console for their logs');
    console.log('3. Make sure you\'re on the visible viewer (General or Dental)');
} else {
    console.log('%c❌ ISSUES FOUND:', 'color:red; font-weight:bold; font-size:14px;');
    issues.forEach((issue, i) => {
        console.log(`   ${i + 1}. ${issue}`);
    });
    console.log('\n🔧 RECOMMENDED FIXES:');
    if (issues.includes('Viewer not initialized')) {
        console.log('   • Hard refresh browser (Ctrl+Shift+R)');
        console.log('   • Wait 3 seconds after page load');
        console.log('   • Check Network tab for 3d-viewer-pro.js (should be 200 OK)');
    }
    if (issues.includes('Toolbar handler missing')) {
        console.log('   • Check for JavaScript errors in Console');
        console.log('   • Make sure inline scripts executed');
    }
    if (issues.includes('Canvas has 0 width')) {
        console.log('   • Make sure viewer container is visible');
        console.log('   • Try resizing browser window');
        console.log('   • Upload a 3D file to trigger initialization');
    }
}

console.log('\n%c💡 NEXT STEPS:', 'color:#00ffff; font-weight:bold;');
console.log('1. Hard refresh: Ctrl+Shift+R');
console.log('2. Wait 3 seconds for initialization');
console.log('3. Upload a 3D file');
console.log('4. Click toolbar buttons');
console.log('5. Check console for button logs\n');

console.log('%c🔬 Diagnostic complete!', 'font-size:16px; color:#00ff00;');
