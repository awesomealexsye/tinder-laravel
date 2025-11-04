#!/bin/bash

# Diagnostic script for ngrok Swagger issues

NGROK_URL="https://3e11f1fd13ec.ngrok-free.app"

echo "🔍 Testing Swagger via Ngrok..."
echo "URL: $NGROK_URL"
echo ""

echo "1️⃣  Testing main Swagger page..."
MAIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$NGROK_URL/api/documentation" 2>/dev/null)
echo "   Status: $MAIN_STATUS"
if [ "$MAIN_STATUS" = "200" ]; then
    echo "   ✅ Page accessible"
else
    echo "   ❌ Page not accessible (check ngrok is running)"
fi
echo ""

echo "2️⃣  Testing JSON endpoint..."
JSON_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$NGROK_URL/docs" 2>/dev/null)
echo "   Status: $JSON_STATUS"
if [ "$JSON_STATUS" = "200" ]; then
    echo "   ✅ JSON endpoint accessible"
    # Check if it's actually JSON
    CONTENT_TYPE=$(curl -s -o /dev/null -w "%{content_type}" "$NGROK_URL/docs" 2>/dev/null)
    echo "   Content-Type: $CONTENT_TYPE"
else
    echo "   ❌ JSON endpoint not accessible (run: php artisan l5-swagger:generate)"
fi
echo ""

echo "3️⃣  Testing CSS asset..."
CSS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$NGROK_URL/docs/asset/swagger-ui.css" 2>/dev/null)
echo "   Status: $CSS_STATUS"
if [ "$CSS_STATUS" = "200" ]; then
    echo "   ✅ CSS asset accessible"
else
    echo "   ❌ CSS asset not accessible"
fi
echo ""

echo "4️⃣  Testing JS asset..."
JS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$NGROK_URL/docs/asset/swagger-ui-bundle.js" 2>/dev/null)
echo "   Status: $JS_STATUS"
if [ "$JS_STATUS" = "200" ]; then
    echo "   ✅ JS asset accessible"
else
    echo "   ❌ JS asset not accessible"
fi
echo ""

echo "📊 Summary:"
if [ "$MAIN_STATUS" = "200" ] && [ "$JSON_STATUS" = "200" ] && [ "$CSS_STATUS" = "200" ] && [ "$JS_STATUS" = "200" ]; then
    echo "✅ All endpoints are accessible!"
    echo ""
    echo "💡 If Swagger UI is still not showing:"
    echo "   1. The ngrok browser warning page is likely blocking JavaScript"
    echo "   2. Open the URL in browser: $NGROK_URL/api/documentation"
    echo "   3. Click 'Visit Site' button if you see ngrok warning"
    echo "   4. Check browser console (F12) for JavaScript errors"
else
    echo "❌ Some endpoints are not accessible"
    echo "   - Check ngrok is running: curl http://localhost:4040/api/tunnels"
    echo "   - Check Laravel is running: curl http://localhost:8000"
    echo "   - Regenerate Swagger: php artisan l5-swagger:generate"
fi
