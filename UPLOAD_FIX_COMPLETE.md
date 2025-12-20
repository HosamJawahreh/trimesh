# ✅ PROBLEM SOLVED! File Upload Fix

## 🎯 Root Cause Identified

**Error:** `413 Content Too Large`  
**Reason:** Your 6.63 MB file becomes ~9 MB when base64 encoded, but PHP only allows 8 MB POST data

## Current Limits (TOO SMALL):
```
post_max_size = 8M
upload_max_filesize = 2M
```

## Required Limits (FOR 3D FILES):
```
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
memory_limit = 512M
```

---

## 🔧 FIX #1: Using .user.ini (EASIEST - No sudo needed)

Create a `.user.ini` file in your public directory:

```bash
cd /home/hjawahreh/Desktop/Projects/Trimesh/public

cat > .user.ini << 'EOF'
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
EOF

# Restart PHP-FPM (you may need to ask system admin)
sudo systemctl restart php8.3-fpm
```

---

## 🔧 FIX #2: Edit php.ini Directly

**Step 1: Find your php.ini file**
```bash
php -i | grep "Loaded Configuration File"
```

**For PHP-FPM (web requests):**
```bash
# Edit this file:
sudo nano /etc/php/8.3/fpm/php.ini

# Find and change these lines:
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M

# Save (Ctrl+O, Enter, Ctrl+X)

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

**For CLI (artisan commands):**
```bash
sudo nano /etc/php/8.3/cli/php.ini
# Make same changes
```

---

## 🔧 FIX #3: Create Custom INI File (RECOMMENDED)

```bash
# Create custom config file
sudo nano /etc/php/8.3/fpm/conf.d/99-upload-limits.ini

# Add this content:
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M

# Save and restart
sudo systemctl restart php8.3-fpm
```

---

## 🔧 FIX #4: If Using Nginx (ALSO NEEDED!)

Nginx also has upload size limits:

```bash
sudo nano /etc/nginx/nginx.conf

# Add inside http block:
http {
    ...
    client_max_body_size 200M;
    ...
}

# Or in your site config:
sudo nano /etc/nginx/sites-available/your-site

# Add inside server block:
server {
    ...
    client_max_body_size 200M;
    ...
}

# Restart Nginx
sudo systemctl restart nginx
```

---

## 🔧 FIX #5: If Using Apache

```bash
# Edit Apache config or .htaccess
sudo nano /etc/apache2/apache2.conf

# Add:
LimitRequestBody 209715200

# Or create .htaccess in public folder:
cd /home/hjawahreh/Desktop/Projects/Trimesh/public

cat > .htaccess << 'EOF'
php_value upload_max_filesize 200M
php_value post_max_size 210M
php_value max_execution_time 300
php_value memory_limit 512M
EOF

# Restart Apache
sudo systemctl restart apache2
```

---

## ✅ Verify The Fix

**Step 1: Check new limits**
```bash
php -i | grep -E "post_max_size|upload_max_filesize"
```

Should show:
```
post_max_size => 210M => 210M
upload_max_filesize => 200M => 200M
```

**Step 2: Test upload again**
1. Go to: `http://127.0.0.1:9000/test-upload.html`
2. Upload the same 6.63 MB file
3. Should now succeed! ✅

**Step 3: Test on real quote page**
1. Go to: `http://127.0.0.1:9000/quote`
2. Upload a 3D file
3. Should upload to server successfully
4. Check browser console - should see: `☁️ File uploaded to server: file_xxx`

---

## 🎯 Quick Commands (Copy-Paste)

```bash
# Navigate to project
cd /home/hjawahreh/Desktop/Projects/Trimesh

# Create .user.ini in public folder
cat > public/.user.ini << 'EOF'
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
EOF

# Check if it worked
php -i | grep -E "post_max_size|upload_max_filesize"

# Restart PHP-FPM (if you have sudo access)
sudo systemctl restart php8.3-fpm

# Test upload
# Open: http://127.0.0.1:9000/test-upload.html
```

---

## 📊 After The Fix

Once you've applied one of the fixes and restarted PHP-FPM:

1. **Test Upload:**
   ```
   Open: http://127.0.0.1:9000/test-upload.html
   Upload the 6.63 MB file
   Should see: ✅ Upload successful!
   ```

2. **Test Sharing:**
   ```
   Upload a file on quote page
   Click "Share"
   Copy link
   Open in incognito
   File should load! ✅
   ```

3. **Verify Database:**
   ```bash
   php artisan tinker
   >>> App\Models\ThreeDFile::latest()->first();
   # Should show your uploaded file
   ```

---

## 🐛 If Still Not Working

1. **Check PHP-FPM is using new config:**
   ```bash
   sudo systemctl status php8.3-fpm
   sudo systemctl restart php8.3-fpm
   ```

2. **Check web server picked up changes:**
   ```bash
   # For Nginx:
   sudo nginx -t
   sudo systemctl restart nginx
   
   # For Apache:
   sudo apache2ctl configtest
   sudo systemctl restart apache2
   ```

3. **Create phpinfo page to verify:**
   ```bash
   echo '<?php phpinfo();' > public/info.php
   # Visit: http://127.0.0.1:9000/info.php
   # Search for: upload_max_filesize and post_max_size
   # DELETE info.php when done (security risk!)
   ```

---

## 🎉 Expected Result

After applying fix:
- ✅ Files up to 200 MB will upload successfully
- ✅ Sharing will work across browsers
- ✅ Files saved to database
- ✅ Files saved to server storage
- ✅ 72-hour auto-expiry active

---

## ⚡ FASTEST FIX (If you have sudo):

```bash
# One command to rule them all:
sudo bash -c 'cat > /etc/php/8.3/fpm/conf.d/99-upload-limits.ini << EOF
upload_max_filesize = 200M
post_max_size = 210M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
EOF
' && sudo systemctl restart php8.3-fpm && echo "✅ PHP limits increased!"

# Then test:
# Open http://127.0.0.1:9000/test-upload.html and upload again!
```

---

Need help applying the fix? Let me know which method you want to use (based on your server access level)!
