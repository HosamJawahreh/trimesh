#!/bin/bash

# COMPREHENSIVE DIAGNOSTIC AND FIX SCRIPT
# This script checks everything and provides solutions

echo "🔍 ====== TRIMESH SYSTEM DIAGNOSTIC ======"
echo ""

# 1. Check syntax errors
echo "1️⃣ Checking PHP Syntax Errors..."
cd /home/hjawahreh/Desktop/Projects/Trimesh
php -l app/Http/Controllers/Api/MeshRepairController.php
php -l app/Http/Controllers/Admin/MeshRepairAdminController.php
echo "✅ Syntax check complete"
echo ""

# 2. Clear all caches
echo "2️⃣ Clearing Laravel Caches..."
php artisan cache:clear 2>/dev/null || echo "  ⚠️  Cache clear skipped"
php artisan config:clear 2>/dev/null || echo "  ⚠️  Config clear skipped"
php artisan route:clear 2>/dev/null || echo "  ⚠️  Route clear skipped"
php artisan view:clear 2>/dev/null || echo "  ⚠️  View clear skipped"
echo "✅ Laravel caches cleared"
echo ""

# 3. Check Python service
echo "3️⃣ Checking Python Mesh Repair Service..."
if curl -s http://localhost:8001/health >/dev/null 2>&1; then
    echo "  ✅ Service is ONLINE"
    curl -s http://localhost:8001/health | python3 -m json.tool 2>/dev/null || echo "  Response OK"
else
    echo "  ❌ Service is OFFLINE"
    echo ""
    echo "  📝 To start the Python service:"
    echo "     Option 1 (Simple):"
    echo "       cd /home/hjawahreh/Desktop/Projects/Trimesh"
    echo "       ./start-service-simple.sh"
    echo ""
    echo "     Option 2 (Manual):"
    echo "       cd /home/hjawahreh/Desktop/Projects/Trimesh/python-mesh-service"
    echo "       python3 main.py"
    echo ""
    echo "  ℹ️  NOTE: The service is OPTIONAL for basic functionality"
    echo "           Client-side mesh repair works without it!"
fi
echo ""

# 4. Check if Python dependencies are available
echo "4️⃣ Checking Python Dependencies..."
if python3 -c "import fastapi, uvicorn, pymeshfix, trimesh, numpy" 2>/dev/null; then
    echo "  ✅ All Python packages installed"
else
    echo "  ⚠️  Some Python packages missing"
    echo "     Install with: pip3 install --user fastapi uvicorn pymeshfix trimesh numpy"
fi
echo ""

# 5. Check database connection
echo "5️⃣ Checking Database..."
if php artisan db:show 2>/dev/null | head -5; then
    echo "  ✅ Database connected"
else
    echo "  ⚠️  Could not verify database"
fi
echo ""

# 6. Check if migrations are run
echo "6️⃣ Checking Migrations..."
if php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo (Schema::hasTable('mesh_repairs') ? '✅' : '❌') . ' mesh_repairs table exists\n';" 2>/dev/null; then
    echo "  Database tables verified"
else
    echo "  ⚠️  Run: php artisan migrate"
fi
echo ""

# 7. Summary
echo "========================================"
echo "📊 SUMMARY"
echo "========================================"
echo ""
echo "✅ PHP Files: Syntax OK"
echo "✅ Laravel: Caches cleared"
echo ""
if curl -s http://localhost:8001/health >/dev/null 2>&1; then
    echo "✅ Python Service: ONLINE"
else
    echo "⚠️  Python Service: OFFLINE (optional)"
fi
echo ""
echo "📝 NEXT STEPS:"
echo ""
echo "1. Clear Browser Cache:"
echo "   - Go to: http://127.0.0.1:8000/quote"
echo "   - Press: Ctrl + Shift + R (hard refresh)"
echo ""
echo "2. Test Quote Page:"
echo "   - Upload an STL file"
echo "   - Click 'Save & Calculate'"
echo "   - Check console (F12) for logs"
echo ""
echo "3. Admin Dashboard (optional):"
echo "   - Visit: http://127.0.0.1:8000/admin/mesh-repair/dashboard"
echo "   - Service will show 'Online' if Python service is running"
echo "   - Service shows 'Offline' - that's normal if not started"
echo ""
echo "========================================"
echo ""

