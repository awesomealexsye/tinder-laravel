#!/bin/bash

# Ngrok Setup Script for Laravel API
# This script will start ngrok tunnel for your Laravel application

echo "🚀 Starting Ngrok Tunnel for Laravel API..."
echo ""

# Check if Laravel server is running
if ! lsof -ti:8000 > /dev/null 2>&1; then
    echo "⚠️  Laravel server is not running on port 8000"
    echo "Starting Laravel server in background..."
    php artisan serve --port=8000 > /dev/null 2>&1 &
    sleep 2
    echo "✅ Laravel server started"
else
    echo "✅ Laravel server is already running on port 8000"
fi

# Check if ngrok is configured
if ! ngrok config check > /dev/null 2>&1; then
    echo ""
    echo "❌ Ngrok authtoken not configured!"
    echo ""
    echo "Please follow these steps:"
    echo "1. Sign up for free at: https://dashboard.ngrok.com/signup"
    echo "2. Get your authtoken from: https://dashboard.ngrok.com/get-started/your-authtoken"
    echo "3. Run this command:"
    echo "   ngrok config add-authtoken YOUR_TOKEN_HERE"
    echo ""
    exit 1
fi

# Kill any existing ngrok processes
pkill -f "ngrok http 8000" 2>/dev/null

echo "🌐 Starting ngrok tunnel..."
echo ""

# Start ngrok with browser warning bypass header
# Note: This requires ngrok paid plan. For free plan, users need to click "Visit Site"
ngrok http 8000 --request-header-add "ngrok-skip-browser-warning:true" > /tmp/ngrok-output.log 2>&1 &
NGROK_PID=$!
echo $NGROK_PID > /tmp/ngrok.pid

# Wait for ngrok to start
sleep 3

# Get the public URL from ngrok API
PUBLIC_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)

if [ -z "$PUBLIC_URL" ]; then
    echo "⚠️  Could not get ngrok URL. Check ngrok dashboard at: http://localhost:4040"
    echo "Ngrok is running (PID: $NGROK_PID)"
    echo "Visit http://localhost:4040 to see your public URL"
else
    echo "✅ Ngrok tunnel is active!"
    echo ""
    echo "📱 Public URL: $PUBLIC_URL"
    echo ""
    echo "🔄 Updating Swagger documentation for ngrok..."
    php fix-ngrok-swagger.php "$PUBLIC_URL" 2>/dev/null
    echo ""
    echo "📍 API Endpoints:"
    echo "   - API Base: $PUBLIC_URL/api"
    echo "   - Swagger Docs: $PUBLIC_URL/api/documentation"
    echo ""
    echo "💡 Tip: In Swagger UI, select 'Ngrok Public Server' from the server dropdown"
    echo ""
    echo "🔐 To stop ngrok, run: kill $NGROK_PID"
    echo "📊 Ngrok Dashboard: http://localhost:4040"
fi
