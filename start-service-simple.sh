#!/bin/bash

# Simple Python Service Starter
# This runs the mesh repair service without Docker

echo "🚀 Starting Mesh Repair Service (Simple Mode)"
echo "=============================================="
echo ""

cd "$(dirname "$0")/python-mesh-service" || exit 1

# Check if Python 3 is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 not found!"
    echo "   Install it: sudo apt-get install python3 python3-pip"
    exit 1
fi

echo "✅ Python 3 found: $(python3 --version)"

# Check if required packages are installed
echo ""
echo "📦 Checking dependencies..."

# Try to import required modules
if python3 -c "import fastapi, uvicorn, pymeshfix, trimesh, numpy" 2>/dev/null; then
    echo "✅ All dependencies installed"
else
    echo "⚠️  Some dependencies missing"
    echo "   Installing now (this may take a few minutes)..."
    echo ""

    # Install pip if needed
    if ! command -v pip3 &> /dev/null; then
        echo "Installing pip..."
        sudo apt-get update
        sudo apt-get install -y python3-pip python3-venv build-essential python3-dev
    fi

    # Install dependencies
    echo "Installing Python packages..."
    pip3 install --user fastapi uvicorn pymeshfix trimesh numpy

    echo ""
    echo "✅ Dependencies installed"
fi

# Check if port 8001 is already in use
if lsof -Pi :8001 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo ""
    echo "⚠️  Port 8001 is already in use"
    echo "   Attempting to stop existing process..."
    sudo kill -9 $(lsof -t -i:8001) 2>/dev/null
    sleep 2
fi

echo ""
echo "🌟 Starting service on http://localhost:8001"
echo "   Press Ctrl+C to stop"
echo "   Logs will appear below:"
echo "=============================================="
echo ""

# Run the service
python3 main.py

