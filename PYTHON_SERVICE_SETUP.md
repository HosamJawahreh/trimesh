# Python Mesh Repair Service - Manual Setup

## Issue
The Python mesh repair service is showing as "Offline" in the admin dashboard because it's not currently running.

## Quick Start (Without Docker)

### Option 1: Install Docker (Recommended)
```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Start the service
cd /home/hjawahreh/Desktop/Projects/Trimesh
./quick-start.sh
```

### Option 2: Run Python Service Directly

#### Step 1: Install Python Dependencies
```bash
cd /home/hjawahreh/Desktop/Projects/Trimesh/python-mesh-service

# Install pip if not already installed
sudo apt-get update
sudo apt-get install -y python3-pip python3-venv

# Create virtual environment
python3 -m venv venv
source venv/bin/activate

# Install dependencies
pip install fastapi uvicorn pymeshfix trimesh numpy
```

#### Step 2: Start the Service
```bash
# From python-mesh-service directory with venv activated
python3 main.py
```

Or run in background:
```bash
nohup python3 main.py > mesh-service.log 2>&1 &
```

#### Step 3: Verify Service is Running
```bash
# Check if service is responding
curl http://localhost:8001/health

# Expected response:
# {"status":"healthy","service":"mesh-repair","version":"1.0.0"}
```

### Option 3: Quick System Service Setup

Create a systemd service for automatic startup:

```bash
# Create service file
sudo nano /etc/systemd/system/mesh-repair.service
```

Add this content:
```ini
[Unit]
Description=Mesh Repair Python Service
After=network.target

[Service]
Type=simple
User=hjawahreh
WorkingDirectory=/home/hjawahreh/Desktop/Projects/Trimesh/python-mesh-service
Environment="PATH=/home/hjawahreh/Desktop/Projects/Trimesh/python-mesh-service/venv/bin"
ExecStart=/home/hjawahreh/Desktop/Projects/Trimesh/python-mesh-service/venv/bin/python3 main.py
Restart=always

[Install]
WantedBy=multi-user.target
```

Then:
```bash
# Reload systemd
sudo systemctl daemon-reload

# Start service
sudo systemctl start mesh-repair

# Enable on boot
sudo systemctl enable mesh-repair

# Check status
sudo systemctl status mesh-repair
```

## Admin Menu Added ✅

The "3D Quote" section has been added to the admin sidebar with:
- 📊 **Dashboard** - Statistics and monitoring
- 📋 **Repair Logs** - History and filtering
- ⚙️ **Settings** - Service configuration

Access at: `http://127.0.0.1:8000/admin/mesh-repair/dashboard`

## Troubleshooting

### Service Not Starting
```bash
# Check if port 8001 is already in use
sudo lsof -i :8001

# Kill process if needed
sudo kill -9 <PID>
```

### Dependencies Installation Failed
```bash
# Install build tools
sudo apt-get install -y build-essential python3-dev

# Try installing dependencies one by one
pip install fastapi
pip install uvicorn
pip install pymeshfix
pip install trimesh
pip install numpy
```

### Check Service Logs
```bash
# If running in background
tail -f mesh-service.log

# If using systemd
sudo journalctl -u mesh-repair -f
```

## Current Status

✅ Database migrations completed
✅ Admin routes configured
✅ Admin views created with correct layout
✅ Admin menu added with 3D Quote section
⚠️ Python service needs to be started (choose one option above)

## Next Steps

1. Start the Python service using one of the options above
2. Refresh the admin dashboard
3. The service status should show "Online" with a green indicator
4. Test mesh repair functionality

## Service Configuration

Default settings in Laravel (can be changed in Settings page):
- Service URL: `http://localhost:8001`
- Timeout: 300 seconds
- Max File Size: 100 MB

## Testing

Once the service is running:
```bash
# Test health endpoint
curl http://localhost:8001/health

# Test with a sample file (from Laravel)
# Go to: http://127.0.0.1:8000/admin/mesh-repair/dashboard
# The service status should show as "Online"
```
