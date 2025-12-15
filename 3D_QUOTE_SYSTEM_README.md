# 🎨 Professional 3D Quote System

## Overview

A complete, production-ready 3D printing instant quote system built with Laravel + Bootstrap 5 + Three.js. This system provides real-time 3D model visualization, automatic geometry analysis, and dynamic pricing calculations.

## 🌟 Key Features

### For Customers
- **Multi-File Upload** - Upload up to 10 files per quote
- **Real-Time 3D Viewer** - Interactive Three.js viewer with STL/OBJ/PLY support
- **Instant Analysis** - Automatic volume and dimension calculation
- **Live Pricing** - Dynamic price updates based on material and quantity
- **Material Selection** - Choose from 8+ materials per file
- **Quantity Management** - Set quantities with automatic discount calculation
- **Responsive Design** - Works on desktop, tablet, and mobile

### For Administrators
- **Pricing Management** - Full control over material pricing
- **Quote Tracking** - View and manage all submitted quotes
- **Status Management** - Update quote status and add notes
- **Material Control** - Add, edit, enable/disable materials
- **Flexible Pricing** - Configure per-cm³, surface area, and setup fees

## 📦 What's Included

### Backend Components
- Quote management system
- File upload and storage
- Geometry analysis API
- Dynamic pricing engine
- Admin dashboard controllers
- Comprehensive validation

### Frontend Components
- Beautiful quote form page
- Interactive 3D model viewer
- Drag & drop file upload
- Real-time price calculator
- Material and quantity selectors
- Customer information form
- Success confirmation modal

### Database Schema
- Quotes tracking with status
- File storage with metadata
- Admin-configurable pricing rules
- Automatic quote number generation

## 🚀 Quick Start

### 1. Installation Complete ✅
All migrations run and pricing rules seeded automatically.

### 2. Access Points

#### Customer Facing
- **Homepage Section**: Scroll to "Get Your 3D Printing Quote in Seconds"
- **Quote Page**: Visit `/quote/new`

#### Admin Panel
- **Pricing Management**: `/admin/3d-pricing`
- **Quote Management**: `/admin/3d-quotes`

### 3. Test Upload
1. Visit `/quote/new`
2. Drag & drop an STL/OBJ/PLY file
3. Watch the 3D model load
4. See instant price calculation
5. Submit quote

## 💡 Technical Highlights

### Advanced Pricing Formula
```
Base = (Volume × Price/cm³) + (Surface × Price/mm²)
With Machine Cost = Volume / Speed × Hour Rate
Apply Multiplier + Setup Fee
Enforce Minimum Price
Apply Quantity Discounts (5%, 7%, 10%, 15%)
```

### 3D Viewer Features
- Orbit controls (rotate, zoom, pan)
- Wireframe mode toggle
- Auto-rotate animation
- Smart camera positioning
- Professional lighting
- Shadow mapping
- Anti-aliasing

### Geometry Analysis
- Accurate volume calculation using signed tetrahedron method
- Bounding box dimensions
- Surface area computation
- Real-time display

## 📊 Default Materials

| Material | Technology | Base Price | Min Price | Description |
|----------|-----------|------------|-----------|-------------|
| PLA | FDM | $0.50/cm³ | $5.00 | Standard prototyping |
| ABS | FDM | $0.60/cm³ | $6.00 | Durable plastic |
| PETG | FDM | $0.70/cm³ | $7.00 | Strong & chemical resistant |
| Nylon | FDM | $1.20/cm³ | $10.00 | Industrial strength |
| TPU | FDM | $1.50/cm³ | $12.00 | Flexible rubber-like |
| Resin Standard | SLA | $2.00/cm³ | $15.00 | High detail |
| Resin Tough | SLA | $2.50/cm³ | $18.00 | Impact resistant |
| Stainless Steel | SLM | $5.00/cm³ | $50.00 | Metal printing |

## 🎯 Use Cases

### Prototyping Services
- Rapid quote generation
- Multiple material options
- Quantity pricing
- Professional presentation

### 3D Printing Bureaus
- Automated quote processing
- Material inventory management
- Customer self-service
- Order tracking

### Manufacturing
- Custom part pricing
- Material comparison
- Volume discounts
- Production quotes

## 🛠️ Customization

### Add New Materials
```php
use App\Models\PricingRule;

PricingRule::create([
    'material' => 'carbon_fiber',
    'display_name' => 'Carbon Fiber',
    'technology' => 'FDM',
    'price_per_cm3' => 2.00,
    'minimum_price' => 20.00,
    'is_active' => true,
]);
```

### Adjust Pricing
```sql
UPDATE pricing_rules 
SET price_per_cm3 = 0.80, 
    minimum_price = 7.50,
    multiplier = 1.15
WHERE material = 'pla';
```

### Modify UI Colors
Edit `resources/views/frontend/home/home_main/sections/3d-quote.blade.php`:
```css
.tp-btn-primary {
    background: #your-color;
}
```

## 📁 File Structure

```
app/
├── Http/Controllers/
│   ├── QuoteController.php                 # Main quote API
│   └── Admin/
│       ├── PricingRuleController.php       # Pricing management
│       └── QuoteManagementController.php   # Quote admin
├── Models/
│   ├── Quote.php                           # Quote model
│   ├── QuoteFile.php                       # File model
│   └── PricingRule.php                     # Pricing model
└── Services/
    └── PricingService.php                  # Pricing logic

database/
├── migrations/
│   ├── create_quotes_table.php
│   ├── create_quote_files_table.php
│   └── create_pricing_rules_table.php
└── seeders/
    └── PricingRuleSeeder.php

resources/views/
└── frontend/
    ├── quote/
    │   └── new.blade.php                   # Quote form page
    └── home/home_main/sections/
        └── 3d-quote.blade.php              # Homepage section

public/frontend/assets/js/
├── model-viewer-3d.js                      # 3D viewer class
├── quote-manager.js                        # Quote management
└── quote-app.js                            # Main application

routes/
├── api.php                                 # API endpoints
├── web.php                                 # Frontend routes
└── quote-admin.php                         # Admin routes
```

## 🔌 API Documentation

### POST /api/quote/upload
Upload a 3D model file.

**Request:**
```json
{
  "file": "(binary)",
  "quote_id": 123 // optional
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "quote_id": 123,
    "file_id": 456,
    "file_name": "model.stl",
    "file_url": "/storage/models/model_12345.stl"
  }
}
```

### POST /api/quote/analyze
Analyze geometry and calculate price.

**Request:**
```json
{
  "file_id": 456,
  "volume_mm3": 10000,
  "width_mm": 50,
  "height_mm": 30,
  "depth_mm": 20,
  "surface_area_mm2": 5000,
  "material": "pla",
  "quantity": 2
}
```

**Response:**
```json
{
  "success": true,
  "unit_price": 8.50,
  "total_price": 17.00,
  "breakdown": {
    "material_cost": 5.00,
    "surface_cost": 0.50,
    "machine_cost": 1.00,
    "setup_fee": 2.00
  },
  "production": {
    "print_time_hours": 1.5,
    "material_name": "PLA (Standard)"
  }
}
```

### GET /api/quote/materials
Get available materials.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "material": "pla",
      "display_name": "PLA (Standard)",
      "technology": "FDM",
      "price_per_cm3": 0.50,
      "minimum_price": 5.00
    }
  ]
}
```

## 🎓 Advanced Usage

### Quantity Discounts
Automatically applied:
- 5-9 items: 5% off
- 10-19 items: 7% off
- 20-49 items: 10% off
- 50+ items: 15% off

### Print Time Estimation
```
Time = Volume (mm³) / Print Speed (mm³/hour)
Cost = Time × Machine Hour Rate
```

### Custom Multipliers
Use multipliers for:
- Complexity factors
- Rush orders
- Special finishes
- Premium services

## 🔒 Security

- ✅ CSRF protection on all forms
- ✅ File type validation (STL/OBJ/PLY only)
- ✅ File size limits (50MB default)
- ✅ Admin authentication required
- ✅ SQL injection protection
- ✅ XSS prevention

## 📈 Performance

- Fast geometry analysis (< 2s for typical models)
- Optimized 3D rendering (60 FPS)
- Efficient database queries
- CDN-ready static assets
- Lazy loading for large files

## 🌐 Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers (iOS/Android)

## 📚 Resources

### Documentation Files
- `IMPLEMENTATION_COMPLETE.md` - Full implementation details
- `QUOTE_SYSTEM_SETUP.md` - Installation and setup guide
- `TESTING_GUIDE.md` - Testing instructions
- This `README.md` - System overview

### External Resources
- [Three.js Documentation](https://threejs.org/docs/)
- [Laravel Documentation](https://laravel.com/docs)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)

## 🤝 Support

For issues or questions:
1. Check the TESTING_GUIDE.md
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check browser console for JavaScript errors
4. Verify database migrations completed

## 🎉 Success!

Your professional 3D quote system is ready to use!

**Quick Links:**
- 📝 New Quote: `/quote/new`
- 🏠 Homepage: `/` (see new section)
- 🔧 Admin: `/admin/3d-pricing`
- 📊 Quotes: `/admin/3d-quotes`

Built with ❤️ for the Trimesh platform.
