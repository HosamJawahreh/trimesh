#!/bin/bash

# Quick Start Script for Server-Side Mesh Repair System
# Usage: ./quick-start.sh

set -e

echo "=========================================="
echo "TriMesh Server-Side Mesh Repair Setup"
echo "=========================================="
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker not found. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose not found. Please install Docker Compose first."
    exit 1
fi

echo "✅ Docker and Docker Compose found"
echo ""

# Check for .env file
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        cat > .env << EOF
APP_NAME="TriMesh"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=trimesh
DB_USERNAME=trimesh_user
DB_PASSWORD=secret

MESH_REPAIR_SERVICE_URL=http://mesh-repair:8001
MESH_REPAIR_TIMEOUT=120
MESH_REPAIR_MAX_FILE_SIZE=104857600
EOF
    fi
    echo "✅ .env file created"
    echo "   Please review and update credentials if needed"
    echo ""
fi

# Build services
echo "🔨 Building Docker containers..."
docker-compose build

echo ""
echo "✅ Build complete"
echo ""

# Start services
echo "🚀 Starting services..."
docker-compose up -d

echo ""
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check service health
echo ""
echo "🔍 Checking service health..."

# Check MySQL
if docker-compose exec -T mysql mysqladmin ping -h localhost --silent; then
    echo "✅ MySQL: Running"
else
    echo "⚠️  MySQL: Not ready yet"
fi

# Check mesh repair service
if curl -s http://localhost:8001/health > /dev/null 2>&1; then
    echo "✅ Mesh Repair Service: Running"
else
    echo "⚠️  Mesh Repair Service: Not ready yet (may take a minute)"
fi

# Run migrations
echo ""
echo "📊 Running database migrations..."
docker-compose exec -T laravel php artisan migrate --force

echo ""
echo "=========================================="
echo "✅ Setup Complete!"
echo "=========================================="
echo ""
echo "Services running:"
echo "  - Laravel:      http://localhost"
echo "  - Mesh Repair:  http://localhost:8001"
echo "  - MySQL:        localhost:3306"
echo ""
echo "Useful commands:"
echo "  View logs:      docker-compose logs -f"
echo "  Stop services:  docker-compose down"
echo "  Restart:        docker-compose restart"
echo ""
echo "Test the mesh repair service:"
echo "  cd python-mesh-service"
echo "  pip install requests"
echo "  python test_service.py /path/to/model.stl"
echo ""
echo "API endpoints:"
echo "  curl http://localhost/api/mesh/status"
echo "  curl http://localhost/api/mesh/stats"
echo ""
echo "View documentation:"
echo "  - MESH_REPAIR_DEPLOYMENT_GUIDE.md"
echo "  - SERVER_SIDE_MESH_REPAIR_COMPLETE.md"
echo "  - python-mesh-service/README.md"
echo ""
echo "=========================================="
