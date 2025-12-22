#!/bin/bash
# Test script to debug the mesh analyze endpoint

echo "🔍 Testing Mesh Analyze Endpoint"
echo "================================"
echo ""

# Get the test STL file
TEST_FILE="/home/hjawahreh/Desktop/Projects/Trimesh/public/samples/test.stl"

if [ ! -f "$TEST_FILE" ]; then
    echo "⚠️  Test file not found at: $TEST_FILE"
    echo "Using any available STL file..."
    TEST_FILE=$(find /home/hjawahreh/Desktop/Projects/Trimesh -name "*.stl" -type f | head -1)
fi

if [ -z "$TEST_FILE" ]; then
    echo "❌ No STL files found. Please upload an STL file first."
    exit 1
fi

echo "📁 Using file: $TEST_FILE"
echo "📊 File size: $(du -h "$TEST_FILE" | cut -f1)"
echo ""

echo "🌐 Sending request to Laravel API..."
echo ""

RESPONSE=$(curl -s -w "\nHTTP_CODE:%{http_code}" -X POST \
  http://127.0.0.1:8000/api/mesh/analyze \
  -F "file=@${TEST_FILE}" \
  -H "Accept: application/json")

HTTP_CODE=$(echo "$RESPONSE" | grep "HTTP_CODE:" | cut -d: -f2)
BODY=$(echo "$RESPONSE" | sed '/HTTP_CODE:/d')

echo "📥 Response:"
echo "Status Code: $HTTP_CODE"
echo ""
echo "Body:"
echo "$BODY" | jq . 2>/dev/null || echo "$BODY"
echo ""

if [ "$HTTP_CODE" = "200" ]; then
    echo "✅ SUCCESS!"
else
    echo "❌ FAILED with status $HTTP_CODE"
    echo ""
    echo "🔍 Checking Laravel logs..."
    tail -20 /home/hjawahreh/Desktop/Projects/Trimesh/storage/logs/laravel.log | grep -A 5 "Mesh analyze"
fi
