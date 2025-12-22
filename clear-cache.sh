#!/bin/bash

# Quick Fix Script for Quote Page
# Run this to force browser cache refresh

echo "🔄 Clearing Laravel cache and optimizing..."

# Clear all Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Cache cleared!"
echo ""
echo "📝 Now do this in your browser:"
echo "   1. Open quote page: http://127.0.0.1:8000/quote"
echo "   2. Press: Ctrl + Shift + R (hard refresh)"
echo "   3. Or: Ctrl + F5"
echo "   4. Open Console: F12"
echo "   5. Look for: '✅ All systems ready!'"
echo ""
