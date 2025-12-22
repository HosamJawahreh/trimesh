"""
Test script for mesh repair service
Run this after starting the service to verify functionality
"""
import requests
import sys
from pathlib import Path

# Service URL
BASE_URL = "http://localhost:8001"

def test_health():
    """Test health endpoint"""
    print("Testing health endpoint...")
    try:
        response = requests.get(f"{BASE_URL}/health")
        if response.status_code == 200:
            print("✅ Health check passed")
            print(f"   Response: {response.json()}")
            return True
        else:
            print(f"❌ Health check failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Cannot connect to service: {e}")
        return False

def test_analyze(test_file):
    """Test mesh analysis"""
    print(f"\nTesting mesh analysis with {test_file}...")

    if not Path(test_file).exists():
        print(f"❌ Test file not found: {test_file}")
        return False

    try:
        with open(test_file, 'rb') as f:
            files = {'file': (Path(test_file).name, f, 'application/octet-stream')}
            response = requests.post(f"{BASE_URL}/api/analyze", files=files)

        if response.status_code == 200:
            data = response.json()
            print("✅ Analysis successful")
            print(f"   Vertices: {data['vertices']}")
            print(f"   Faces: {data['faces']}")
            print(f"   Volume: {data['volume_cm3']:.4f} cm³")
            print(f"   Watertight: {data['is_watertight']}")
            print(f"   Holes: {data['holes_count']}")
            print(f"   Components: {data['connected_components']}")
            return True
        else:
            print(f"❌ Analysis failed: {response.status_code}")
            print(f"   Error: {response.text}")
            return False
    except Exception as e:
        print(f"❌ Error during analysis: {e}")
        return False

def test_repair(test_file):
    """Test mesh repair"""
    print(f"\nTesting mesh repair with {test_file}...")

    if not Path(test_file).exists():
        print(f"❌ Test file not found: {test_file}")
        return False

    try:
        with open(test_file, 'rb') as f:
            files = {'file': (Path(test_file).name, f, 'application/octet-stream')}
            data = {'aggressive': 'true'}
            response = requests.post(f"{BASE_URL}/api/repair", files=files, data=data)

        if response.status_code == 200:
            result = response.json()
            print("✅ Repair successful")
            print("\n   Original mesh:")
            print(f"     Volume: {result['original_stats']['volume_cm3']:.4f} cm³")
            print(f"     Holes: {result['original_stats'].get('holes_count', 'N/A')}")
            print(f"     Watertight: {result['original_stats']['is_watertight']}")

            print("\n   Repaired mesh:")
            print(f"     Volume: {result['repaired_stats']['volume_cm3']:.4f} cm³")
            print(f"     Holes: {result['repaired_stats'].get('holes_count', 'N/A')}")
            print(f"     Watertight: {result['repaired_stats']['is_watertight']}")

            print("\n   Repair summary:")
            summary = result['repair_summary']
            print(f"     Holes filled: {summary['holes_filled']}")
            print(f"     Vertices added: {summary['vertices_added']}")
            print(f"     Faces added: {summary['faces_added']}")
            print(f"     Method: {summary['repair_method']}")

            print(f"\n   Volume change: {result['volume_change_cm3']:.4f} cm³ ({result['volume_change_percent']:.2f}%)")

            return True
        else:
            print(f"❌ Repair failed: {response.status_code}")
            print(f"   Error: {response.text}")
            return False
    except Exception as e:
        print(f"❌ Error during repair: {e}")
        return False

def main():
    """Run all tests"""
    print("=" * 60)
    print("TriMesh Repair Service Test Suite")
    print("=" * 60)

    # Test health
    if not test_health():
        print("\n❌ Service not running. Start it with:")
        print("   python main.py")
        print("   or")
        print("   docker run -p 8001:8001 trimesh-repair-service")
        sys.exit(1)

    # Get test file from command line or use default
    test_file = sys.argv[1] if len(sys.argv) > 1 else None

    if test_file:
        # Run analysis test
        test_analyze(test_file)

        # Run repair test
        test_repair(test_file)
    else:
        print("\n⚠️  No test file provided")
        print("   Usage: python test_service.py <path/to/model.stl>")
        print("\n   Service is running and healthy, but skipping mesh tests")

    print("\n" + "=" * 60)
    print("✅ Test suite completed")
    print("=" * 60)

if __name__ == "__main__":
    main()
