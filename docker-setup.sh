#!/bin/bash

# LawnStarter Exercise - Docker Setup Script
# This script sets up and starts the entire application using Laravel Sail

set -e  # Exit on any error

echo "🚀 Starting LawnStarter Exercise Docker Setup..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running. Please start Docker Desktop and try again."
    exit 1
fi

echo "✅ Docker is running"

# Start the containers
echo "📦 Starting Docker containers..."
./vendor/bin/sail up -d

# Wait for containers to be ready
echo "⏳ Waiting for containers to be ready..."
sleep 3

# Check if containers are running
if ! ./vendor/bin/sail ps | grep -q "Up"; then
    echo "❌ Error: Containers failed to start properly"
    ./vendor/bin/sail logs
    exit 1
fi

echo "✅ Containers are running"

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
./vendor/bin/sail npm install

# Build frontend assets
echo "🏗️  Building frontend assets..."
./vendor/bin/sail npm run build

# Run database migrations
echo "🗄️  Running database migrations..."
./vendor/bin/sail artisan migrate --force

# Start queue worker in background
echo "🔄 Starting queue worker..."
./vendor/bin/sail artisan queue:work --daemon > /dev/null 2>&1 &

# Start scheduler in background (for statistics computation)
echo "⏰ Starting scheduler..."
./vendor/bin/sail artisan schedule:work > /dev/null 2>&1 &

echo ""
echo "🎉 Setup complete! Your application is ready."
echo ""
echo "📍 Access your application:"
echo "   Frontend: http://localhost:8080"
echo "   API: http://localhost:8080/api"
echo ""
echo "🔧 Useful commands:"
echo "   View logs: ./vendor/bin/sail logs"
echo "   Stop containers: ./vendor/bin/sail down"
echo "   Run tests: ./vendor/bin/sail test"
echo ""
echo "🧪 Test the API:"
echo "   curl \"http://localhost:8080/api/v1/starwars/search/people?query=luke\""
echo "   curl \"http://localhost:8080/api/v1/statistics\""
echo "" 