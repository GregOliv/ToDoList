#!/usr/bin/env bash

# Laravel Vercel Deployment Script
# Simple helper script for deploying Laravel to Vercel

echo "🚀 Laravel to Vercel Deployment Helper"
echo "======================================="
echo ""

# Check if Vercel CLI is installed
if ! command -v vercel &> /dev/null
then
    echo "❌ Vercel CLI is not installed!"
    echo "📦 Installing Vercel CLI..."
    npm install -g vercel
    echo "✅ Vercel CLI installed!"
    echo ""
fi

# Check if user is logged in
echo "🔐 Checking Vercel login status..."
vercel whoami &> /dev/null
if [ $? -ne 0 ]; then
    echo "❌ Not logged in to Vercel"
    echo "🔑 Please login..."
    vercel login
else
    echo "✅ Already logged in to Vercel"
fi

echo ""
echo "📋 Pre-deployment Checklist:"
echo ""

# Check for required files
echo "Checking required files..."
files=("vercel.json" "api/index.php" ".env")
all_exist=true

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file exists"
    else
        echo "  ❌ $file is missing!"
        all_exist=false
    fi
done

echo ""

# Check if APP_KEY is set
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64:$" .env; then
    echo "⚠️  Warning: APP_KEY is empty in .env file!"
    echo "   Run: php artisan key:generate"
    echo ""
fi

# Deployment options
echo "Select deployment type:"
echo "1) Preview Deployment (for testing)"
echo "2) Production Deployment"
echo "3) Cancel"
echo ""
read -p "Enter your choice (1-3): " choice

case $choice in
    1)
        echo ""
        echo "🚀 Deploying to Preview..."
        vercel
        ;;
    2)
        echo ""
        echo "🚀 Deploying to Production..."
        vercel --prod
        ;;
    3)
        echo "❌ Deployment cancelled"
        exit 0
        ;;
    *)
        echo "❌ Invalid choice. Deployment cancelled."
        exit 1
        ;;
esac

echo ""
echo "✅ Deployment complete!"
echo ""
echo "📝 Next steps:"
echo "1. Set up environment variables in Vercel Dashboard"
echo "2. Configure your database connection"
echo "3. Test your API endpoints"
echo ""
echo "📚 See DEPLOYMENT_GUIDE.md for detailed instructions"
