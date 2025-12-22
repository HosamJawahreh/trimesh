#!/bin/bash

# Mesh Repair Service Startup Script
# This script starts the Python mesh repair service

echo "🚀 Starting Mesh Repair Service..."
echo "=================================="

# Navigate to service directory
cd "$(dirname "$0")/python-mesh-service" || exit 1

# Check Python version
echo "📋 Checking Python..."
python3 --version || { echo "❌ Python3 not found!"; exit 1; }

# Check if pip is available
if ! python3 -m pip --version &> /dev/null; then
    echo "⚠️  pip not found. Installing pip..."
    echo "   Please run: sudo apt-get install python3-pip python3-venv"
    exit 1
fi

# Create virtual environment if it doesn't exist
if [ ! -d "venv" ]; then
    echo "📦 Creating virtual environment..."
    python3 -m venv venv || { echo "❌ Failed to create venv"; exit 1; }
fi

# Activate virtual environment
echo "🔧 Activating virtual environment..."
source venv/bin/activate

# Install dependencies
echo "📥 Installing dependencies..."
pip install --quiet fastapi uvicorn pymeshfix trimesh numpy || {
    echo "❌ Failed to install dependencies"
    echo "   Try: sudo apt-get install build-essential python3-dev"
    exit 1
}

# Check if port 8001 is in use
if lsof -Pi :8001 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Port 8001 is already in use!"
    echo "   Killing existing process..."
    sudo kill -9 $(lsof -t -i:8001) 2>/dev/null
    sleep 2
fi

# Start the service
echo "🌟 Starting service on http://localhost:8001..."
echo "   Logs will be written to mesh-service.log"
echo "   Press Ctrl+C to stop, or close this terminal to run in background"
echo ""

# Run the service
python3 main.py 2>&1 | tee mesh-service.log

# If we get here, service stopped
echo ""
echo "❌ Service stopped"
