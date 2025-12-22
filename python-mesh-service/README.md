# TriMesh Professional Mesh Repair Microservice

Production-grade mesh repair service using **pymeshfix** and **trimesh** for industrial-quality 3D model processing.

## Features

- **Industrial-Grade Repair**: Uses MeshFix algorithm (same as Xometry, Shapeways)
- **Comprehensive Analysis**: Hole detection, volume calculation, topology checks
- **Multiple Formats**: Supports STL, OBJ, PLY files
- **RESTful API**: Easy integration with Laravel backend
- **Fast Processing**: Optimized for large models

## Quick Start

### Option 1: Using Docker (Recommended)

```bash
# Build the image
docker build -t trimesh-repair-service .

# Run the container
docker run -p 8001:8001 trimesh-repair-service
```

### Option 2: Using Python Directly

```bash
# Install dependencies
pip install -r requirements.txt

# Run the service
python main.py
```

The service will be available at `http://localhost:8001`

## API Endpoints

### 1. Health Check
```bash
GET /health
```

### 2. Analyze Mesh
```bash
POST /api/analyze
Content-Type: multipart/form-data

file: <3D model file>
```

**Response:**
```json
{
  "filename": "model.stl",
  "vertices": 70805,
  "faces": 141354,
  "volume_cm3": 4.58,
  "is_watertight": false,
  "holes_count": 1071,
  "euler_number": -2142,
  "genus": 1072
}
```

### 3. Repair Mesh
```bash
POST /api/repair
Content-Type: multipart/form-data

file: <3D model file>
aggressive: true (optional, default: true)
```

**Response:**
```json
{
  "success": true,
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
  "repair_summary": {
    "holes_filled": 1071,
    "vertices_added": 2145,
    "watertight_achieved": true,
    "repair_method": "pymeshfix"
  },
  "volume_change_cm3": 0.29,
  "volume_change_percent": 6.33
}
```

### 4. Repair and Download
```bash
POST /api/repair-download
Content-Type: multipart/form-data

file: <3D model file>
aggressive: true (optional)
```

Returns the repaired mesh file with volume information in headers.

## Testing with cURL

### Analyze a mesh
```bash
curl -X POST http://localhost:8001/api/analyze \
  -F "file=@/path/to/model.stl"
```

### Repair a mesh
```bash
curl -X POST http://localhost:8001/api/repair \
  -F "file=@/path/to/model.stl" \
  -F "aggressive=true"
```

### Download repaired mesh
```bash
curl -X POST http://localhost:8001/api/repair-download \
  -F "file=@/path/to/model.stl" \
  -F "aggressive=true" \
  --output repaired_model.stl
```

## Algorithm Details

### pymeshfix Repair Process
1. **Vertex Deduplication**: Merges duplicate vertices
2. **Edge Analysis**: Identifies non-manifold edges and boundaries
3. **Hole Filling**: Fills holes using advancing front mesh technique
4. **Manifold Repair**: Fixes non-manifold geometry
5. **Normal Consistency**: Ensures consistent face orientation

### Volume Calculation
- Uses **signed tetrahedron method** for accurate volume
- Converts mm³ to cm³ automatically
- Handles both watertight and non-watertight meshes

## Performance

- **Small models** (<10K faces): ~0.5 seconds
- **Medium models** (10K-100K faces): ~2-5 seconds
- **Large models** (>100K faces): ~10-30 seconds

## Integration with Laravel

Example Laravel HTTP client usage:

```php
use Illuminate\Support\Facades\Http;

$response = Http::attach(
    'file', file_get_contents($filePath), 'model.stl'
)->post('http://localhost:8001/api/repair');

$data = $response->json();
$repairedVolume = $data['repaired_stats']['volume_cm3'];
```

## Environment Variables

- `PORT`: Service port (default: 8001)
- `MAX_FILE_SIZE`: Max upload size in bytes (default: 100MB)
- `WORKERS`: Number of uvicorn workers (default: 2)

## Troubleshooting

### Issue: "File too large" error
**Solution**: Increase `MAX_FILE_SIZE` in `main.py`

### Issue: VTK errors
**Solution**: Ensure system dependencies are installed (see Dockerfile)

### Issue: Slow repairs
**Solution**: 
- Use `aggressive=false` for faster, conservative repairs
- Increase Docker container memory allocation
- Scale workers in production

## Production Deployment

### Using Docker Compose

See `../docker-compose.yml` for full stack deployment with Laravel.

### Kubernetes

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: mesh-repair-service
spec:
  replicas: 3
  template:
    spec:
      containers:
      - name: mesh-repair
        image: trimesh-repair-service:latest
        ports:
        - containerPort: 8001
        resources:
          limits:
            memory: "2Gi"
            cpu: "1000m"
```

## License

Same as parent project (TriMesh)

## Support

For issues related to:
- **Mesh repair algorithm**: Check pymeshfix documentation
- **API integration**: Contact backend team
- **Performance**: Review Docker resource allocation
