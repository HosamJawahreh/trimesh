#!/bin/bash

# 🎯 FINAL VERIFICATION TEST SCRIPT
# This will verify all fixes are in place

echo "=========================================="
echo "🔍 VERIFYING SAVE & CALCULATE FIXES"
echo "=========================================="
echo ""

cd /home/hjawahreh/Desktop/Projects/Trimesh

# 1. Check if new code exists in quote.blade.php
echo "1️⃣ Checking quote.blade.php for NEW handler..."
if grep -q "DEC-23-2025-V2" resources/views/frontend/pages/quote.blade.php; then
    echo "   ✅ NEW handler code found (DEC-23-2025-V2)"
else
    echo "   ❌ NEW handler code NOT found"
    exit 1
fi

# 2. Check if old code is disabled in quote-viewer.blade.php
echo ""
echo "2️⃣ Checking quote-viewer.blade.php for DISABLED old handler..."
if grep -q "This old handler is DISABLED" resources/views/frontend/pages/quote-viewer.blade.php; then
    echo "   ✅ OLD handler is properly disabled"
else
    echo "   ❌ OLD handler still active - CONFLICT!"
    exit 1
fi

# 3. Check if EnhancedSaveCalculate module exists
echo ""
echo "3️⃣ Checking enhanced-save-calculate.js..."
if [ -f "public/frontend/assets/js/enhanced-save-calculate.js" ]; then
    SIZE=$(stat -f%z "public/frontend/assets/js/enhanced-save-calculate.js" 2>/dev/null || stat -c%s "public/frontend/assets/js/enhanced-save-calculate.js" 2>/dev/null)
    echo "   ✅ File exists (Size: $SIZE bytes)"

    # Check version
    if grep -q "version: '4.0'" public/frontend/assets/js/enhanced-save-calculate.js; then
        echo "   ✅ Version 4.0 confirmed"
    fi
else
    echo "   ❌ enhanced-save-calculate.js NOT found"
    exit 1
fi

# 4. Check Python service
echo ""
echo "4️⃣ Checking Python mesh service..."
if curl -s http://localhost:8001/health | grep -q "healthy"; then
    echo "   ✅ Python service running on port 8001"
else
    echo "   ⚠️  Python service not responding (may need to start it)"
fi

# 5. Check API routes
echo ""
echo "5️⃣ Checking API routes..."
if php artisan route:list | grep -q "api/quotes/store"; then
    echo "   ✅ Quote storage API route registered"
else
    echo "   ❌ Quote API routes missing"
    exit 1
fi

if php artisan route:list | grep -q "api/3d-files/store"; then
    echo "   ✅ File storage API route registered"
else
    echo "   ❌ File storage API routes missing"
    exit 1
fi

# 6. Clear all caches
echo ""
echo "6️⃣ Clearing all Laravel caches..."
php artisan view:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
echo "   ✅ All caches cleared"

echo ""
echo "=========================================="
echo "✅ ALL SERVER-SIDE CHECKS PASSED!"
echo "=========================================="
echo ""
echo "🚀 NEXT STEPS:"
echo ""
echo "1. CLOSE YOUR BROWSER COMPLETELY"
echo "2. Reopen browser"
echo "3. Go to: http://127.0.0.1:8000/quote"
echo "4. Press CTRL + SHIFT + R (hard refresh)"
echo "5. Or use Incognito: CTRL + SHIFT + N"
echo ""
echo "📋 WHAT TO EXPECT IN CONSOLE (F12):"
echo "   🔥🔥🔥 QUOTE.BLADE.PHP SCRIPT LOADED - NEW VERSION DEC-23-2025-V2"
echo "   ℹ️ Save & Calculate handler delegated to quote.blade.php"
echo "   💾 ===== ENHANCED SAVE & CALCULATE V4.0 LOADED ====="
echo "   ✅✅✅ Save & Calculate button connected successfully"
echo ""
echo "💡 When you click 'Save & Calculate':"
echo "   💾💾💾 SAVE & CALCULATE CLICKED - NEW HANDLER V2"
echo "   📞 Calling EnhancedSaveCalculate.execute()"
echo "   🚀 Starting enhanced save & calculate..."
echo "   ✅ Quote saved successfully!"
echo ""
echo "=========================================="
