# Production-Grade Mesh Repair System - Deployment Guide

## Overview

This system provides industrial-quality mesh repair using a Python microservice (pymeshfix + trimesh) integrated with Laravel backend. The architecture matches professional platforms like Xometry, Shapeways, and Trimesh.

## Architecture

```
Frontend (Three.js)
    ↓
Laravel API (/api/mesh/*)
    ↓
Python Microservice (FastAPI:8001)
    ↓
pymeshfix + trimesh
    ↓
MySQL Database (mesh_repairs table)
```

## Prerequisites

- Docker & Docker Compose (recommended)
- OR: PHP 8.2+, Python 3.11+, MySQL 8.0+, Node.js 18+

## Quick Start (Docker)

### 1. Environment Configuration

Create `.env` file in project root:

```bash
# Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=trimesh
DB_USERNAME=trimesh_user
DB_PASSWORD=your_secure_password

# Mesh Repair Service
MESH_REPAIR_SERVICE_URL=http://mesh-repair:8001
MESH_REPAIR_TIMEOUT=120
MESH_REPAIR_MAX_FILE_SIZE=104857600
```

### 2. Build and Start Services

```bash
# Build all containers
docker-compose build

# Start services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f mesh-repair
```

### 3. Initialize Database

```bash
# Run migrations
docker-compose exec laravel php artisan migrate

# Seed data (optional)
docker-compose exec laravel php artisan db:seed
```

### 4. Test Mesh Repair Service

```bash
# Install test dependencies
cd python-mesh-service
pip install requests

# Run test (requires test STL file)
python test_service.py /path/to/model.stl
```

Expected output:
```
✅ Health check passed
✅ Analysis successful
   Vertices: 70805
   Volume: 4.58 cm³
   Watertight: False
   Holes: 1071
   
✅ Repair successful
   Volume change: +0.29 cm³ (6.33%)
   Holes filled: 1071
```

## Manual Installation (Without Docker)

### 1. Python Mesh Repair Service

```bash
cd python-mesh-service

# Create virtual environment
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Start service
python main.py
```

Service will run on `http://localhost:8001`

### 2. Laravel Backend

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=trimesh
DB_USERNAME=your_user
DB_PASSWORD=your_password

MESH_REPAIR_SERVICE_URL=http://localhost:8001

# Run migrations
php artisan migrate

# Start Laravel
php artisan serve
```

### 3. Frontend Assets

```bash
# Install Node dependencies
npm install

# Build assets
npm run build

# For development
npm run dev
```

## API Usage

### Check Service Status

```bash
curl http://localhost/api/mesh/status
```

Response:
```json
{
  "available": true,
  "service_url": "http://mesh-repair:8001",
  "max_file_size_mb": 100
}
```

### Analyze Mesh

```bash
curl -X POST http://localhost/api/mesh/analyze \
  -F "file_id=123"
```

Response:
```json
{
  "success": true,
  "analysis": {
    "filename": "model.stl",
    "vertices": 70805,
    "faces": 141354,
    "volume_cm3": 4.58,
    "is_watertight": false,
    "holes_count": 1071,
    "euler_number": -2142
  },
  "recommendations": [
    {
      "severity": "high",
      "message": "Model is not watertight (1071 holes detected)",
      "action": "Use aggressive repair mode"
    }
  ]
}
```

### Repair Mesh

```bash
curl -X POST http://localhost/api/mesh/repair \
  -F "file_id=123" \
  -F "aggressive=true" \
  -F "save_result=true"
```

Response:
```json
{
  "success": true,
  "repair_result": {
    "original_stats": {
      "volume_cm3": 4.58,
      "holes_count": 1071,
      "is_watertight": false
    },
    "repaired_stats": {
      "volume_cm3": 4.87,
      "holes_count": 0,
      "is_watertight": true
    },
    "volume_change_cm3": 0.29,
    "volume_change_percent": 6.33
  },
  "quality_score": 95.5,
  "repair_id": 42
}
```

### Get Repair History

```bash
curl http://localhost/api/mesh/history/123
```

### Get Statistics

```bash
curl http://localhost/api/mesh/stats
```

Response:
```json
{
  "success": true,
  "stats": {
    "total_repairs": 1547,
    "successful_repairs": 1523,
    "average_quality_score": 87.3,
    "average_volume_change": 0.18,
    "total_holes_filled": 45231,
    "watertight_achieved": 1489,
    "today_repairs": 23,
    "this_week_repairs": 147
  }
}
```

## Frontend Integration

Update `enhanced-save-calculate.js` to use server-side repair:

```javascript
async function repairMeshServerSide(fileId) {
    try {
        // Check if service is available
        const statusResponse = await fetch('/api/mesh/status');
        const status = await statusResponse.json();
        
        if (!status.available) {
            console.warn('Server-side repair unavailable, using client-side');
            return repairMeshClientSide();
        }
        
        // Show progress
        showRepairProgress('Analyzing mesh...');
        
        // Analyze first
        const analyzeResponse = await fetch('/api/mesh/analyze', {
            method: 'POST',
            body: new FormData([['file_id', fileId]])
        });
        const analysis = await analyzeResponse.json();
        
        console.log('Analysis:', analysis);
        displayRecommendations(analysis.recommendations);
        
        // Repair
        showRepairProgress('Repairing mesh (this may take a while)...');
        
        const repairResponse = await fetch('/api/mesh/repair', {
            method: 'POST',
            body: new FormData([
                ['file_id', fileId],
                ['aggressive', 'true'],
                ['save_result', 'true']
            ])
        });
        const repair = await repairResponse.json();
        
        if (repair.success) {
            console.log('✅ Server-side repair complete');
            console.log(`Volume: ${repair.repair_result.original_stats.volume_cm3} → ${repair.repair_result.repaired_stats.volume_cm3} cm³`);
            console.log(`Quality Score: ${repair.quality_score}/100`);
            
            // Update pricing with new volume
            updatePricingWithRepairedVolume(
                repair.repair_result.repaired_stats.volume_cm3
            );
            
            hideRepairProgress();
            showSuccessMessage(`Repaired successfully! Quality: ${repair.quality_score.toFixed(1)}/100`);
            
            return repair;
        } else {
            throw new Error(repair.message);
        }
        
    } catch (error) {
        console.error('Server-side repair error:', error);
        hideRepairProgress();
        
        // Fallback to client-side
        return repairMeshClientSide();
    }
}
```

## Database Schema

### mesh_repairs table

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| file_id | bigint | Foreign key to files table |
| original_volume_cm3 | decimal(12,4) | Volume before repair |
| repaired_volume_cm3 | decimal(12,4) | Volume after repair |
| holes_filled | int | Number of holes repaired |
| quality_score | decimal(5,2) | Quality score 0-100 |
| repair_time_seconds | decimal(8,2) | Time taken |
| status | enum | pending, processing, completed, failed |
| aggressive_mode | boolean | Repair mode used |
| is_watertight | boolean | Result watertight status |
| is_manifold | boolean | Result manifold status |
| repaired_file_path | varchar | Path to repaired file |
| metadata | json | Full repair details |
| created_at | timestamp | - |
| updated_at | timestamp | - |

### files table (updated)

Added columns:
- `repair_status` enum: none, pending, repaired, failed
- `is_watertight` boolean: Current watertight status
- `last_repair_at` timestamp: Last repair time

## Performance Tuning

### Python Service Scaling

Scale mesh repair service:
```bash
docker-compose up -d --scale mesh-repair=3
```

Add load balancer (nginx):
```nginx
upstream mesh_repair {
    server mesh-repair-1:8001;
    server mesh-repair-2:8001;
    server mesh-repair-3:8001;
}
```

### Resource Limits

In `docker-compose.yml`:
```yaml
mesh-repair:
  deploy:
    resources:
      limits:
        memory: 4G
        cpus: '4'
      reservations:
        memory: 2G
        cpus: '2'
```

### Database Optimization

Add indexes:
```sql
CREATE INDEX idx_mesh_repairs_file_id ON mesh_repairs(file_id);
CREATE INDEX idx_mesh_repairs_created_at ON mesh_repairs(created_at);
CREATE INDEX idx_files_repair_status ON files(repair_status);
```

## Monitoring

### Health Checks

All services have health checks:
```bash
# Python service
curl http://localhost:8001/health

# Laravel
curl http://localhost/api/mesh/status

# Database
docker-compose exec mysql mysqladmin ping
```

### Logs

```bash
# All logs
docker-compose logs -f

# Specific service
docker-compose logs -f mesh-repair

# Laravel logs
tail -f storage/logs/laravel.log

# Python service logs
docker-compose exec mesh-repair tail -f /var/log/mesh-repair.log
```

### Metrics

View repair statistics:
```bash
curl http://localhost/api/mesh/stats | jq
```

## Troubleshooting

### Issue: Python service not responding

**Check:**
```bash
docker-compose ps mesh-repair
docker-compose logs mesh-repair
```

**Solution:**
```bash
docker-compose restart mesh-repair
```

### Issue: "Service unavailable" error

**Check connectivity:**
```bash
docker-compose exec laravel curl http://mesh-repair:8001/health
```

**Solution:** Verify `MESH_REPAIR_SERVICE_URL` in `.env`

### Issue: Large files timing out

**Solution:** Increase timeout in `.env`:
```bash
MESH_REPAIR_TIMEOUT=300
```

### Issue: Repair quality low

**Check analysis first:**
```bash
curl -X POST http://localhost/api/mesh/analyze -F "file_id=123"
```

**Try aggressive mode:**
```bash
curl -X POST http://localhost/api/mesh/repair \
  -F "file_id=123" \
  -F "aggressive=true"
```

## Backup and Restore

### Backup

```bash
# Database
docker-compose exec mysql mysqldump -u root -p trimesh > backup.sql

# Repaired files
docker-compose exec laravel tar czf /tmp/repaired.tar.gz storage/app/repaired/
docker cp trimesh_laravel:/tmp/repaired.tar.gz ./repaired_backup.tar.gz
```

### Restore

```bash
# Database
docker-compose exec -T mysql mysql -u root -p trimesh < backup.sql

# Repaired files
docker cp repaired_backup.tar.gz trimesh_laravel:/tmp/
docker-compose exec laravel tar xzf /tmp/repaired.tar.gz -C storage/app/
```

## Production Deployment

### Security Checklist

- [ ] Change all default passwords
- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS for all connections
- [ ] Restrict mesh-repair service to internal network
- [ ] Enable firewall rules
- [ ] Set up regular backups
- [ ] Configure log rotation
- [ ] Enable rate limiting on API endpoints

### SSL/TLS

Use reverse proxy (nginx/traefik) with Let's Encrypt:

```yaml
# docker-compose.yml
services:
  traefik:
    image: traefik:v2.10
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock
      - ./traefik.yml:/traefik.yml
      - ./acme.json:/acme.json
```

## Support

- **Mesh repair issues**: Check Python service logs
- **API errors**: Check Laravel logs (`storage/logs/`)
- **Performance**: Review resource usage with `docker stats`
- **Database**: Check `mesh_repairs` table for repair history

## License

Same as parent project (TriMesh)
