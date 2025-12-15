# 🎯 Quick Start Guide - Testing Your 3D Quote System

## ✅ System Status
- ✅ Database migrated successfully
- ✅ 8 materials seeded (PLA, ABS, PETG, Nylon, TPU, Resin x2, Steel)
- ✅ All routes registered (13 routes active)
- ✅ API endpoints operational
- ✅ Frontend components installed
- ✅ 3D viewer ready

## 🚀 Test Your System Now

### 1. View Homepage Section
```
URL: http://your-domain.com/
```
- Scroll down to see the new "Get Your 3D Printing Quote in Seconds" section
- It's positioned **above the services section**
- Click "Start Your Quote" button

### 2. Access Quote Page
```
URL: http://your-domain.com/quote/new
```

**What to expect:**
- Left column: File upload area + files list
- Right column: 3D viewer + pricing summary
- Beautiful Bootstrap 5 interface
- Drag & drop functionality

### 3. Test File Upload

**Get Sample Files:**
You can test with any STL, OBJ, or PLY file. Popular sources:
- Thingiverse.com
- Printables.com
- Download sample STL files

**Upload Process:**
1. Drag & drop a file or click to browse
2. Watch the upload progress
3. See the model load in 3D viewer automatically
4. View geometry analysis (volume, dimensions)
5. See real-time price calculation

### 4. Test Features

**3D Viewer Controls:**
- Click and drag to rotate
- Scroll to zoom
- Right-click and drag to pan
- Click "Reset View" to center
- Click "Toggle Wireframe" to see edges
- Click "Auto Rotate" for animation

**File Management:**
- Upload multiple files (up to 10)
- Each file appears in the list
- Select different materials per file
- Change quantity per file
- Remove files with trash icon
- Watch total price update live

**Material Selection:**
- Default material dropdown at top
- Per-file material override
- Prices update instantly
- 8 materials available

### 5. Submit a Test Quote

**Fill in (optional):**
- Customer name
- Email
- Phone
- Notes

**Click "Submit Quote Request"**
- Success modal appears
- Quote number generated (format: Q-YYYYMMDD-####)
- Data saved to database

## 🔍 Verify in Database

```sql
-- Check quotes table
SELECT * FROM quotes ORDER BY id DESC LIMIT 5;

-- Check uploaded files
SELECT * FROM quote_files ORDER BY id DESC LIMIT 10;

-- Check pricing rules
SELECT material, display_name, price_per_cm3, minimum_price, is_active 
FROM pricing_rules 
ORDER BY sort_order;
```

## 📊 Admin Testing

### View Pricing Rules
```
URL: http://your-domain.com/admin/3d-pricing
```
(Requires admin authentication)

**Features:**
- View all materials
- Edit pricing formulas
- Enable/disable materials
- Add new materials
- Reorder display

### View Quotes
```
URL: http://your-domain.com/admin/3d-quotes
```
(Requires admin authentication)

**Features:**
- See all submitted quotes
- View quote details
- Update status
- Add admin notes
- Preview 3D files

## 🧪 API Testing (Optional)

### Test with cURL or Postman

**1. Get Materials:**
```bash
curl http://your-domain.com/api/quote/materials
```

**2. Upload File:**
```bash
curl -X POST http://your-domain.com/api/quote/upload \
  -H "X-CSRF-TOKEN: your-token" \
  -F "file=@/path/to/model.stl"
```

**3. Calculate Price:**
```bash
curl -X POST http://your-domain.com/api/quote/calculate \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "volume_mm3": 10000,
    "material": "pla",
    "quantity": 1
  }'
```

## 🎨 Customization Quick Tips

### Change Default Material
Edit `public/frontend/assets/js/quote-app.js`:
```javascript
quoteManager.setDefaultMaterial('pla'); // Change to 'abs', 'petg', etc.
```

### Adjust Pricing
```sql
UPDATE pricing_rules 
SET price_per_cm3 = 0.75, minimum_price = 8.00 
WHERE material = 'pla';
```

### Modify Colors
```sql
UPDATE pricing_rules 
SET color_hex = '#FF5733' 
WHERE material = 'pla';
```

### Change Upload Limits
Edit `.env` or `php.ini`:
```
upload_max_filesize = 100M
post_max_size = 110M
```

## 🐛 Troubleshooting

### Files Not Uploading?
```bash
# Check permissions
chmod -R 775 storage/app/public
chmod -R 775 public/frontend/assets/js

# Create storage link if needed
php artisan storage:link
```

### 3D Viewer Not Showing?
- Check browser console for errors
- Verify Three.js CDN is loading
- Clear browser cache
- Check if JavaScript files exist

### Pricing Not Calculating?
```bash
# Verify pricing rules exist
php artisan tinker
>>> \App\Models\PricingRule::count();

# Re-seed if needed
php artisan db:seed --class=PricingRuleSeeder
```

### Routes Not Working?
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Verify routes
php artisan route:list --path=quote
```

## 📱 Mobile Testing

The system is fully responsive! Test on:
- Desktop (optimal experience)
- Tablet (good layout)
- Mobile (functional, vertical layout)

## 🎯 Success Indicators

✅ Homepage section displays properly
✅ Quote page loads without errors
✅ Files upload successfully
✅ 3D models render in viewer
✅ Dimensions and volume calculated
✅ Prices display correctly
✅ Materials dropdown populates
✅ Quote submission works
✅ Success modal appears

## 📞 Need Help?

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Browser Console
Press F12 to open developer tools and check:
- Console tab for JavaScript errors
- Network tab for failed requests
- Application tab for storage issues

### Database Check
```bash
php artisan tinker
>>> \App\Models\Quote::count();
>>> \App\Models\QuoteFile::count();
>>> \App\Models\PricingRule::active()->count();
```

## 🎉 You're Ready!

Your instant 3D quote system is fully operational and ready for production use!

**Key URLs:**
- 🏠 Homepage: `/`
- 📝 New Quote: `/quote/new`
- 🔧 Admin Pricing: `/admin/3d-pricing`
- 📊 Admin Quotes: `/admin/3d-quotes`

**Features Working:**
- ✅ Multi-file upload
- ✅ Real-time 3D preview
- ✅ Automatic geometry analysis
- ✅ Dynamic pricing calculation
- ✅ Material selection
- ✅ Quantity management
- ✅ Quote submission
- ✅ Admin management

Enjoy your professional 3D quote system! 🚀🎨✨
