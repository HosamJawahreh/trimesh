# 3D Quote System - Installation & Setup Guide

## Overview
This is a complete 3D printing quote system integrated into your Laravel application. It includes:
- Real-time 3D model viewer (Three.js)
- Automatic volume and dimension calculation
- Dynamic pricing engine (admin-configurable)
- Multi-file upload support
- Material selection
- Quote management dashboard

## Installation Steps

### 1. Run Database Migrations
```bash
php artisan migrate
```

This will create three new tables:
- `quotes` - Stores quote information
- `quote_files` - Stores individual 3D files and their analysis
- `pricing_rules` - Stores material pricing configurations

### 2. Seed Default Pricing Rules
```bash
php artisan db:seed --class=PricingRuleSeeder
```

This will populate the pricing_rules table with 8 default materials:
- PLA (Standard) - $0.50/cm³
- ABS (Durable) - $0.60/cm³
- PETG (Strong) - $0.70/cm³
- Nylon (Industrial) - $1.20/cm³
- TPU (Flexible) - $1.50/cm³
- Resin Standard - $2.00/cm³
- Resin Tough - $2.50/cm³
- Stainless Steel - $5.00/cm³

### 3. Create Storage Symlink (if not already done)
```bash
php artisan storage:link
```

### 4. Set File Upload Permissions
```bash
chmod -R 775 storage/app/public
chmod -R 775 public/frontend/assets/js
```

### 5. Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## File Structure

### Backend (Laravel)
```
app/
├── Http/Controllers/
│   ├── QuoteController.php              # Main API controller
│   └── Admin/
│       ├── PricingRuleController.php    # Admin pricing management
│       └── QuoteManagementController.php # Admin quote management
├── Models/
│   ├── Quote.php                        # Quote model
│   ├── QuoteFile.php                    # File model
│   └── PricingRule.php                  # Pricing rule model
└── Services/
    └── PricingService.php               # Pricing calculation logic

database/
├── migrations/
│   ├── 2024_01_01_000001_create_quotes_table.php
│   ├── 2024_01_01_000002_create_quote_files_table.php
│   └── 2024_01_01_000003_create_pricing_rules_table.php
└── seeders/
    └── PricingRuleSeeder.php

routes/
├── api.php                              # API routes
├── web.php                              # Frontend routes
└── quote-admin.php                      # Admin routes
```

### Frontend
```
resources/views/
└── frontend/
    ├── quote/
    │   └── new.blade.php                # Main quote page
    └── home/home_main/sections/
        └── 3d-quote.blade.php           # Homepage section

public/frontend/assets/js/
├── model-viewer-3d.js                   # Three.js viewer class
├── quote-manager.js                     # Quote management class
└── quote-app.js                         # Main application logic
```

## API Endpoints

### Public Endpoints
- `POST /api/quote/upload` - Upload 3D file
- `POST /api/quote/analyze` - Analyze file and calculate price
- `POST /api/quote/calculate` - Calculate price for given parameters
- `GET /api/quote/materials` - Get available materials
- `POST /api/quote/submit` - Submit complete quote
- `GET /api/quote/{quoteId}` - Get quote details
- `DELETE /api/quote/file/{fileId}` - Delete a file

### Admin Endpoints (require auth + checkAdmin middleware)
- `GET /api/admin/pricing` - Get all pricing rules
- `POST /api/admin/pricing` - Create pricing rule
- `PUT /api/admin/pricing/{id}` - Update pricing rule
- `DELETE /api/admin/pricing/{id}` - Delete pricing rule
- `POST /api/admin/pricing/{id}/toggle` - Toggle active status
- `POST /api/admin/pricing/update-order` - Update sort order

## Web Routes

### Frontend
- `/quote/new` - Create new quote page
- `/quote/{quoteId}` - View quote details

### Admin
- `/admin/3d-pricing` - Manage pricing rules
- `/admin/3d-quotes` - View all quotes
- `/admin/3d-quotes/{id}` - View quote details

## Usage

### For Customers

1. **Navigate to Quote Page**: Visit `/quote/new`

2. **Upload Files**:
   - Drag & drop or click to select STL/OBJ/PLY files
   - Maximum 10 files per quote
   - Maximum 50MB per file

3. **View 3D Model**:
   - Automatic 3D preview
   - Rotate, zoom, pan controls
   - Wireframe toggle
   - Auto-rotate option

4. **Configure Options**:
   - Select material for each file
   - Set quantity
   - Real-time price updates

5. **Submit Quote**:
   - Optional: Add contact information
   - Submit for review

### For Administrators

1. **Manage Pricing Rules**:
   - Navigate to `/admin/3d-pricing`
   - Add/edit/delete materials
   - Configure pricing formulas:
     - Price per cm³
     - Surface area pricing
     - Minimum price
     - Setup fees
     - Multipliers
     - Machine hour rates
   - Enable/disable materials
   - Reorder display

2. **View Quotes**:
   - Navigate to `/admin/3d-quotes`
   - View all submitted quotes
   - Update quote status
   - Add admin notes
   - View 3D files

## Pricing Formula

The system uses a comprehensive pricing formula:

```php
base_price = (volume_cm3 × price_per_cm3) + (surface_area_mm2 × price_per_mm2)
base_price = base_price × multiplier
base_price = base_price + setup_fee
final_price = max(base_price, minimum_price)
total_price = final_price × quantity

// Quantity discounts applied:
// 5+ items: 5% off
// 10+ items: 7% off
// 20+ items: 10% off
// 50+ items: 15% off
```

## Configuration

### Adjust Upload Limits

Edit `.env` or `php.ini`:
```
upload_max_filesize = 50M
post_max_size = 60M
```

### Customize Colors

Edit material colors in admin or directly in `pricing_rules` table:
```sql
UPDATE pricing_rules SET color_hex = '#FF5733' WHERE material = 'pla';
```

### Change Default Material

Edit in `quote-app.js`:
```javascript
quoteManager.setDefaultMaterial('pla'); // Change 'pla' to your preferred default
```

## Troubleshooting

### Issue: Files not uploading
**Solution**: Check permissions on `storage/app/public` and verify PHP upload limits

### Issue: 3D viewer not showing
**Solution**: Ensure Three.js CDN is loading. Check browser console for errors.

### Issue: Price not calculating
**Solution**: Verify pricing rules exist in database. Check API endpoint responses.

### Issue: Admin routes not working
**Solution**: Clear route cache: `php artisan route:clear`

## Security Notes

1. **File Validation**: Only STL, OBJ, PLY files are accepted
2. **Size Limits**: Enforced at 50MB per file
3. **CSRF Protection**: All forms include CSRF tokens
4. **Admin Authentication**: All admin routes require authentication and admin role
5. **File Storage**: Files stored in public storage, consider moving to private if needed

## Performance Optimization

1. **Enable Caching**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **Queue Processing** (optional):
Convert heavy analysis to queue jobs for large files

3. **CDN**: Host Three.js libraries on your own CDN for better performance

## Future Enhancements

- [ ] Email notifications for quote submissions
- [ ] PDF quote generation
- [ ] User authentication for quote history
- [ ] Advanced material properties (color, finish, infill)
- [ ] Real-time collaboration
- [ ] Integration with 3D printing services
- [ ] Automated quote approval workflow

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify database migrations completed successfully
4. Ensure all files are properly uploaded and routes are registered

## Credits

- Three.js for 3D rendering
- Laravel for backend framework
- Bootstrap 5 for UI components
