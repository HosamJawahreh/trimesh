<?php
/**
 * EMERGENCY INSTALLER BYPASS
 * This file disables the installer check temporarily
 * 
 * INSTRUCTIONS:
 * 1. Upload this to: /app/Http/Middleware/EmergencyBypass.php
 * 2. Then edit /bootstrap/app.php to add this middleware
 * 3. OR just use the fix-installer.php script instead (recommended)
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmergencyBypass
{
    public function handle(Request $request, Closure $next)
    {
        // This middleware does nothing - it just passes through
        // Use this to bypass the SetupMiddleware temporarily
        return $next($request);
    }
}
