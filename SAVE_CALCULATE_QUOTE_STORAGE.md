# 💾 Save & Calculate → Quote Storage System

**Date:** December 23, 2025  
**Status:** ✅ IMPLEMENTED & READY FOR TESTING

---

## 🎯 Overview

The enhanced "Save & Calculate" system now automatically:
1. ✅ Saves uploaded files to server storage
2. ✅ Creates database records in `quotes` table  
3. ✅ Generates shareable viewer links
4. ✅ Logs all data for admin dashboard access

---

## 🏗️ Architecture

### Flow Diagram
```
User Clicks "Save & Calculate"
          ↓
1. Repair meshes (if needed)
          ↓
2. Calculate volumes & pricing
          ↓
3. Upload files to server (if not already uploaded)
          ↓
4. Create quote record in database
          ↓
5. Display success with viewer link
          ↓
6. Admin can view in logs/dashboard
```

---

## 📊 Database Schema

### `quotes` Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `quote_number` | string | Unique identifier (QT-XXXXX) |
| `customer_name` | string | Customer name (optional) |
| `customer_email` | string | Customer email (optional) |
| `customer_phone` | string | Customer phone (optional) |
| `file_ids` | json | Array of file IDs from `three_d_files` |
| `file_count` | integer | Number of files in quote |
| `total_volume_cm3` | decimal | Total volume in cm³ |
| `total_price` | decimal | Total calculated price |
| `material` | string | Selected material (PLA, ABS, etc.) |
| `color` | string | Selected color |
| `quality` | string | Print quality |
| `quantity` | integer | Number of copies |
| `pricing_breakdown` | json | Per-file pricing details |
| `notes` | text | Customer notes |
| `admin_notes` | text | Admin notes |
| `status` | enum | pending, reviewed, quoted, accepted, rejected, completed |
| `form_type` | enum | general or medical |
| `ip_address` | string | User IP |
| `user_agent` | string | Browser info |
| `viewed_at` | timestamp | When admin viewed |
| `responded_at` | timestamp | When admin responded |
| `created_at` | timestamp | Quote creation time |
| `updated_at` | timestamp | Last update time |

---

## 🔌 API Endpoints

### 1. Save Quote
**Endpoint:** `POST /api/quotes/store`

**Request Body:**
```json
{
  "file_ids": ["file_1234567890_abc123", "file_1234567891_def456"],
  "total_volume_cm3": 125.50,
  "total_price": 42.75,
  "material": "PLA",
  "color": "White",
  "quality": "High",
  "quantity": 2,
  "pricing_breakdown": [
    {
      "file_id": "file_1234567890_abc123",
      "file_name": "model1.stl",
      "volume_cm3": 75.25,
      "price": 25.50
    },
    {
      "file_id": "file_1234567891_def456",
      "file_name": "model2.stl",
      "volume_cm3": 50.25,
      "price": 17.25
    }
  ],
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+1234567890",
  "form_type": "general",
  "notes": "Rush order please"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Quote saved successfully",
  "data": {
    "quote_id": 1,
    "quote_number": "QT-ABC12345",
    "viewer_link": "http://127.0.0.1:8000/quote?files=file_1234567890_abc123,file_1234567891_def456",
    "single_file_link": "http://127.0.0.1:8000/quote?file=file_1234567890_abc123",
    "file_count": 2,
    "total_price": 42.75,
    "status": "pending",
    "created_at": "2025-12-23T13:45:00.000000Z"
  }
}
```

### 2. Get All Quotes
**Endpoint:** `GET /api/quotes`

**Query Parameters:**
- `status` - Filter by status
- `form_type` - Filter by form type
- `from_date` - Filter by start date
- `to_date` - Filter by end date
- `per_page` - Results per page (default: 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "quote_number": "QT-ABC12345",
        "customer_name": "John Doe",
        "customer_email": "john@example.com",
        "file_count": 2,
        "total_volume_cm3": 125.50,
        "total_price": 42.75,
        "status": "pending",
        "created_at": "2025-12-23T13:45:00.000000Z"
      }
    ],
    "per_page": 20,
    "total": 1
  }
}
```

### 3. Get Single Quote
**Endpoint:** `GET /api/quotes/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "quote": {
      "id": 1,
      "quote_number": "QT-ABC12345",
      // ... all quote fields
    },
    "files": [
      {
        "id": 1,
        "file_id": "file_1234567890_abc123",
        "file_name": "model1.stl",
        "file_size": 1024000,
        // ... file details
      }
    ],
    "viewer_link": "http://127.0.0.1:8000/quote?files=..."
  }
}
```

### 4. Update Quote
**Endpoint:** `PUT /api/quotes/{id}`

**Request Body:**
```json
{
  "status": "reviewed",
  "admin_notes": "Quote reviewed and approved",
  "total_price": 45.00
}
```

### 5. Delete Quote
**Endpoint:** `DELETE /api/quotes/{id}`

---

## 💻 Frontend Integration

### JavaScript (enhanced-save-calculate.js)

The `saveQuoteToDatabase()` method handles quote creation:

```javascript
async saveQuoteToDatabase(viewer, viewerId, totalVolume, totalPrice) {
    // 1. Collect file IDs from uploaded files
    // 2. Build pricing breakdown
    // 3. Get customer info from form
    // 4. Send POST request to /api/quotes/store
    // 5. Return result with viewer link
}
```

**Called automatically after pricing calculation:**
```javascript
// Step 6: Save Quote to Database
await this.updateProgress('Saving quote...', 95);
const quoteData = await this.saveQuoteToDatabase(viewer, viewerId, totalVolume, totalPrice);

if (quoteData && quoteData.success) {
    console.log('✅ Quote saved:', quoteData.data.quote_number);
    console.log('🔗 Viewer Link:', quoteData.data.viewer_link);
    
    this.showNotification(
        `Quote ${quoteData.data.quote_number} saved! View in <a href="${quoteData.data.viewer_link}">viewer</a>`,
        'success'
    );
}
```

---

## 📋 Quote Model Features

### Automatic Quote Number Generation
```php
$quoteNumber = Quote::generateQuoteNumber();
// Result: "QT-ABC12345" (8 random uppercase chars)
```

### Get Viewer Links
```php
$quote = Quote::find(1);

// Multi-file link
echo $quote->viewer_link;
// http://127.0.0.1:8000/quote?files=file1,file2,file3

// Single file link (first file only)
echo $quote->single_file_link;
// http://127.0.0.1:8000/quote?file=file1
```

### Get Related Files
```php
$quote = Quote::find(1);
$files = $quote->threeDFiles();
// Returns collection of ThreeDFile models
```

### Query Scopes
```php
// Get pending quotes
$pending = Quote::pending()->get();

// Get recent quotes (last 7 days)
$recent = Quote::recent()->get();

// Get quotes from last 30 days
$monthly = Quote::recent(30)->get();
```

### Status Badge
```php
$quote = Quote::find(1);
echo $quote->status_badge; // 'warning', 'success', 'danger', etc.
```

---

## 🧪 Testing Guide

### 1. Test Quote Creation

1. Open http://127.0.0.1:8000/quote
2. Upload one or more STL files
3. Adjust settings (material, color, quality)
4. Click **"Save & Calculate"**
5. Wait for processing...

**Expected Console Output:**
```
💾 Saving quote to database...
📋 File IDs for quote: ['file_1234567890_abc123']
📤 Sending quote data to server: {...}
✅ Quote API response: {success: true, data: {...}}
✅ Quote saved successfully: QT-ABC12345
🔗 Viewer Link: http://127.0.0.1:8000/quote?file=file_1234567890_abc123
📋 Quote Number: QT-ABC12345
```

**Expected UI:**
- ✅ Success notification with quote number
- ✅ Clickable viewer link
- ✅ Total volume and price displayed

### 2. Verify Database Record

```bash
php artisan tinker
```

```php
// Check latest quote
$quote = \App\Models\Quote::latest()->first();
echo $quote->quote_number;
echo $quote->viewer_link;
dd($quote->toArray());
```

### 3. Test API Directly

```bash
# Get all quotes
curl -X GET http://127.0.0.1:8000/api/quotes \
  -H "Accept: application/json"

# Get specific quote
curl -X GET http://127.0.0.1:8000/api/quotes/1 \
  -H "Accept: application/json"
```

### 4. Test Viewer Link

1. Copy the `viewer_link` from console or API response
2. Open in new browser tab/incognito window
3. Model should load automatically with all files
4. Camera position preserved (if available)

---

## 🎨 Admin Dashboard Integration

### Create Admin Route

Add to `routes/web.php`:
```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/quotes', [AdminQuoteController::class, 'index'])->name('admin.quotes');
    Route::get('/admin/quotes/{id}', [AdminQuoteController::class, 'show'])->name('admin.quotes.show');
    Route::put('/admin/quotes/{id}/status', [AdminQuoteController::class, 'updateStatus'])->name('admin.quotes.status');
});
```

### Admin Controller

```php
namespace App\Http\Controllers\Admin;

class AdminQuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('threeDFiles')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return view('admin.quotes.index', compact('quotes'));
    }
    
    public function show($id)
    {
        $quote = Quote::with('threeDFiles')->findOrFail($id);
        
        if (!$quote->viewed_at) {
            $quote->viewed_at = now();
            $quote->save();
        }
        
        return view('admin.quotes.show', compact('quote'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);
        $quote->status = $request->status;
        $quote->admin_notes = $request->admin_notes;
        $quote->responded_at = now();
        $quote->save();
        
        return redirect()->back()->with('success', 'Quote updated');
    }
}
```

### Admin View (resources/views/admin/quotes/index.blade.php)

```blade
<div class="card">
    <div class="card-header">
        <h3>Quote Logs</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Customer</th>
                    <th>Files</th>
                    <th>Volume</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotes as $quote)
                <tr>
                    <td>{{ $quote->quote_number }}</td>
                    <td>
                        {{ $quote->customer_name }}<br>
                        <small>{{ $quote->customer_email }}</small>
                    </td>
                    <td>{{ $quote->file_count }} file(s)</td>
                    <td>{{ number_format($quote->total_volume_cm3, 2) }} cm³</td>
                    <td>${{ number_format($quote->total_price, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $quote->status_badge }}">
                            {{ ucfirst($quote->status) }}
                        </span>
                    </td>
                    <td>{{ $quote->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.quotes.show', $quote->id) }}" class="btn btn-sm btn-info">
                            View
                        </a>
                        <a href="{{ $quote->viewer_link }}" target="_blank" class="btn btn-sm btn-primary">
                            3D View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $quotes->links() }}
    </div>
</div>
```

---

## 🔍 Troubleshooting

### Issue: "Files must be saved to storage before creating quote"

**Cause:** Files not uploaded to server yet

**Solution:**
1. Ensure files are uploaded through `/api/3d-files/store` first
2. Check `fileData.storageId` or `fileData.id` exists and starts with `file_`
3. Verify `window.fileStorageManager.currentFileId` is set

### Issue: "Some files do not exist in the database"

**Cause:** File IDs in request don't match database records

**Solution:**
1. Check `three_d_files` table has matching records
2. Verify file IDs are correct format (`file_TIMESTAMP_RANDOM`)
3. Ensure files haven't expired (72-hour limit)

### Issue: Quote saved but no viewer link displayed

**Cause:** JavaScript notification issue or link generation error

**Solution:**
1. Check browser console for errors
2. Verify `result.data.viewer_link` exists in API response
3. Check Quote model `getViewerLinkAttribute()` method

### Issue: Admin can't see quotes

**Cause:** Route or permission issue

**Solution:**
1. Verify admin routes are registered
2. Check middleware is applied correctly
3. Ensure user has admin privileges

---

## ✅ Success Checklist

After implementation, verify:

- [ ] Files upload to server storage
- [ ] Database records created in `quotes` table
- [ ] Quote number generated (QT-XXXXXXXX format)
- [ ] Viewer link works in new browser
- [ ] Multi-file support functional
- [ ] Pricing breakdown saved correctly
- [ ] Customer info captured (if provided)
- [ ] Status tracking works
- [ ] Admin can view logs
- [ ] API endpoints respond correctly

---

## 📈 Next Steps

1. **Admin Dashboard** - Create comprehensive admin interface
2. **Email Notifications** - Send quote confirmations to customers
3. **Quote Responses** - Allow admin to respond with official quotes
4. **Export Options** - Generate PDF quotes
5. **Analytics** - Track conversion rates and popular materials
6. **Customer Portal** - Let customers track their quotes

---

## 🎉 Summary

The Save & Calculate system now provides:

✅ **Automatic file storage** - All files saved to server  
✅ **Database logging** - Complete quote records  
✅ **Shareable links** - Direct viewer access  
✅ **Admin visibility** - View all quotes in dashboard  
✅ **Customer tracking** - Optional contact info capture  
✅ **Pricing details** - Per-file and total pricing  
✅ **Status management** - Track quote lifecycle  

**Status: READY FOR TESTING** 🚀

Test now: Upload a file, click "Save & Calculate", and watch the magic happen!
