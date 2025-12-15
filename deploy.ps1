# Laravel to Vercel Deployment Script (PowerShell)
# Simple helper script for deploying Laravel to Vercel

Write-Host "🚀 Laravel to Vercel Deployment Helper" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host ""

# Check if Vercel CLI is installed
Write-Host "Checking for Vercel CLI..." -ForegroundColor Yellow
try {
    $vercelVersion = vercel --version 2>$null
    Write-Host "✅ Vercel CLI is installed (version: $vercelVersion)" -ForegroundColor Green
} catch {
    Write-Host "❌ Vercel CLI is not installed!" -ForegroundColor Red
    Write-Host "📦 Installing Vercel CLI..." -ForegroundColor Yellow
    npm install -g vercel
    Write-Host "✅ Vercel CLI installed!" -ForegroundColor Green
}

Write-Host ""

# Check if user is logged in
Write-Host "🔐 Checking Vercel login status..." -ForegroundColor Yellow
try {
    $whoami = vercel whoami 2>$null
    Write-Host "✅ Already logged in to Vercel as: $whoami" -ForegroundColor Green
} catch {
    Write-Host "❌ Not logged in to Vercel" -ForegroundColor Red
    Write-Host "🔑 Please login..." -ForegroundColor Yellow
    vercel login
}

Write-Host ""
Write-Host "📋 Pre-deployment Checklist:" -ForegroundColor Cyan
Write-Host ""

# Check for required files
Write-Host "Checking required files..." -ForegroundColor Yellow
$requiredFiles = @("vercel.json", "api\index.php", ".env")
$allExist = $true

foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "  ✅ $file exists" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $file is missing!" -ForegroundColor Red
        $allExist = $false
    }
}

Write-Host ""

# Check if APP_KEY is set
if (Test-Path .env) {
    $envContent = Get-Content .env -Raw
    if ($envContent -match 'APP_KEY=\s*$' -or $envContent -match 'APP_KEY=base64:\s*$') {
        Write-Host "⚠️  Warning: APP_KEY is empty in .env file!" -ForegroundColor Yellow
        Write-Host "   Run: php artisan key:generate" -ForegroundColor Yellow
        Write-Host ""
    }
}

# Deployment options
Write-Host "Select deployment type:" -ForegroundColor Cyan
Write-Host "1) Preview Deployment (for testing)" -ForegroundColor White
Write-Host "2) Production Deployment" -ForegroundColor White
Write-Host "3) Cancel" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Enter your choice (1-3)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "🚀 Deploying to Preview..." -ForegroundColor Cyan
        vercel
    }
    "2" {
        Write-Host ""
        Write-Host "🚀 Deploying to Production..." -ForegroundColor Cyan
        vercel --prod
    }
    "3" {
        Write-Host "❌ Deployment cancelled" -ForegroundColor Red
        exit 0
    }
    default {
        Write-Host "❌ Invalid choice. Deployment cancelled." -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "✅ Deployment complete!" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Next steps:" -ForegroundColor Cyan
Write-Host "1. Set up environment variables in Vercel Dashboard" -ForegroundColor White
Write-Host "2. Configure your database connection" -ForegroundColor White
Write-Host "3. Test your API endpoints" -ForegroundColor White
Write-Host ""
Write-Host "📚 See DEPLOYMENT_GUIDE.md for detailed instructions" -ForegroundColor Yellow
