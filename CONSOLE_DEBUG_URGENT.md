# 🚨 URGENT DEBUG - Browser Console Check

## What to do RIGHT NOW:

1. Open browser console (F12)
2. Paste this code and press Enter:

```javascript
// === DIAGNOSTIC CHECK ===
console.log('='.repeat(60));
console.log('🔍 DIAGNOSTIC CHECK - SAVE & CALCULATE');
console.log('='.repeat(60));

// Check if EnhancedSaveCalculate exists
console.log('\n1. EnhancedSaveCalculate Module:');
console.log('   Exists:', !!window.EnhancedSaveCalculate);
console.log('   Version:', window.EnhancedSaveCalculate?.version);

// Check button
const btn = document.getElementById('saveCalculationsBtnMain');
console.log('\n2. Save Button:');
console.log('   Found:', !!btn);
console.log('   Listeners:', btn?._events || 'Cannot inspect');

// Check what happens when clicked
if (btn) {
    console.log('\n3. Adding TEST listener...');
    btn.addEventListener('click', function testHandler() {
        console.log('🧪 TEST HANDLER FIRED - This is a NEW listener');
    }, { once: true });
    console.log('   ✅ Test listener added');
    console.log('   👉 Now click "Save & Calculate" button');
}

// Check for old saveCalculations function
console.log('\n4. Old Functions Check:');
console.log('   saveCalculations exists:', typeof saveCalculations !== 'undefined');

// Check viewer
console.log('\n5. Viewer Status:');
console.log('   viewerGeneral:', !!window.viewerGeneral);
console.log('   uploadedFiles:', window.viewerGeneral?.uploadedFiles?.length || 0);

console.log('\n' + '='.repeat(60));
console.log('NOW CLICK "Save & Calculate" and watch console output');
console.log('='.repeat(60));
```

## What this will tell us:

1. **If EnhancedSaveCalculate is loaded**
2. **What version is running**
3. **If the button exists**
4. **What happens when you click it**

---

## PASTE THIS IN CONSOLE AND SEND ME THE OUTPUT!

Then click "Save & Calculate" and send me ALL the console messages.

This will tell me EXACTLY what's loading and what's not.
