# 🚀 Panduan Deploy Laravel To-Do List ke Vercel

## 📋 Daftar Isi
1. [Persiapan](#persiapan)
2. [Konfigurasi Database](#konfigurasi-database)
3. [Deploy ke Vercel](#deploy-ke-vercel)
4. [Setup Environment Variables](#setup-environment-variables)
5. [Testing API](#testing-api)
6. [Troubleshooting](#troubleshooting)

---

## 1️⃣ Persiapan

### Install Vercel CLI
Buka terminal dan jalankan:

```bash
npm install -g vercel
```

### Login ke Vercel
```bash
vercel login
```

Pilih metode login (GitHub, GitLab, Bitbucket, atau Email).

---

## 2️⃣ Konfigurasi Database

### Opsi A: Menggunakan MySQL (Direkomendasikan untuk Production)

Anda bisa menggunakan database gratis dari:
- **PlanetScale** (https://planetscale.com) - MySQL Serverless (Recommended)
- **Railway** (https://railway.app) - MySQL/PostgreSQL
- **Supabase** (https://supabase.com) - PostgreSQL

#### Contoh Setup PlanetScale:

1. Daftar di https://planetscale.com
2. Buat database baru
3. Dapatkan connection string
4. Update `.env` atau environment variables di Vercel dengan:
   ```
   DB_CONNECTION=mysql
   DB_HOST=<your-planetscale-host>
   DB_PORT=3306
   DB_DATABASE=<your-database-name>
   DB_USERNAME=<your-username>
   DB_PASSWORD=<your-password>
   ```

### Opsi B: Menggunakan SQLite (Untuk Testing)

**⚠️ PENTING:** SQLite tidak ideal untuk serverless karena filesystem tidak persisten.

Jika tetap ingin menggunakan SQLite, pastikan:
1. File `database/database.sqlite` ada
2. Update file `config/database.php` untuk menggunakan `/tmp` di production

---

## 3️⃣ Deploy ke Vercel

### Step-by-Step Deployment:

#### 1. Pastikan di directory project
```bash
cd d:\Laravel\TugasLaravel
```

#### 2. Jalankan deploy (First Time)
```bash
vercel
```

Akan ada beberapa pertanyaan:
- **"Set up and deploy..."** → Tekan `Y`
- **"Which scope..."** → Pilih account Anda
- **"Link to existing project?"** → Tekan `N` (untuk project baru)
- **"What's your project's name?"** → `todolist-laravel` (atau nama lain)
- **"In which directory is your code located?"** → Tekan Enter (root directory)
- **"Want to override the settings?"** → Tekan `N`

#### 3. Deploy ke Production
Setelah berhasil deploy pertama kali, untuk deploy berikutnya:
```bash
vercel --prod
```

---

## 4️⃣ Setup Environment Variables

### Via Dashboard Vercel:

1. Buka https://vercel.com/dashboard
2. Pilih project Anda
3. Klik **Settings** → **Environment Variables**
4. Tambahkan variable berikut:

#### Required Variables:
```
APP_NAME=TodoList
APP_KEY=<your-laravel-app-key>
APP_ENV=production
APP_DEBUG=false
APP_URL=<your-vercel-url>

DB_CONNECTION=mysql
DB_HOST=<your-database-host>
DB_PORT=3306
DB_DATABASE=<your-database-name>
DB_USERNAME=<your-database-username>
DB_PASSWORD=<your-database-password>

CACHE_DRIVER=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr

SANCTUM_STATEFUL_DOMAINS=<your-vercel-domain>
SESSION_DOMAIN=<your-vercel-domain>
```

### Generate APP_KEY:

Jika belum punya APP_KEY, generate dengan:
```bash
php artisan key:generate --show
```

Copy output dan paste ke environment variable `APP_KEY`.

### Via CLI (Alternative):

```bash
vercel env add APP_KEY
# Paste your APP_KEY when prompted

vercel env add DB_CONNECTION
# Type: mysql

vercel env add DB_HOST
# Your database host

# ... dan seterusnya untuk variable lainnya
```

---

## 5️⃣ Testing API

### Mendapatkan URL Deployment
Setelah deploy, Vercel akan memberikan URL seperti:
```
https://todolist-laravel.vercel.app
https://todolist-laravel-<random>.vercel.app (preview)
```

### Test Endpoints:

#### 1. Register User
```bash
curl -X POST https://todolist-laravel.vercel.app/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### 2. Login
```bash
curl -X POST https://todolist-laravel.vercel.app/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

Save token dari response untuk digunakan di request berikutnya.

#### 3. Get Tasks (Protected Route)
```bash
curl -X GET https://todolist-laravel.vercel.app/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### 4. Create Task
```bash
curl -X POST https://todolist-laravel.vercel.app/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Task",
    "description": "Task description",
    "status": "pending"
  }'
```

---

## 6️⃣ Menjalankan Migration di Database

### Jika menggunakan PlanetScale:

1. Install MySQL client atau gunakan phpMyAdmin
2. Connect ke database
3. Export SQL dari local:
   ```bash
   php artisan schema:dump
   ```
4. Import ke PlanetScale via CLI atau web interface

### Jika punya akses SSH ke server sementara:

```bash
# Di local, export schema
php artisan migrate:refresh --seed

# Atau export langsung SQL
mysqldump -u root -p your_database > migration.sql

# Upload SQL ke database production
```

### Alternative: Menggunakan Vercel CLI untuk run migration

**⚠️ Catatan:** Laravel di Vercel bersifat serverless, jadi migration harus dilakukan manual ke database.

---

## 7️⃣ Troubleshooting

### Issue: "500 Internal Server Error"

**Solusi:**
1. Check logs di Vercel Dashboard → Your Project → Deployments → [Select Deployment] → Runtime Logs
2. Pastikan `APP_KEY` sudah di-set di environment variables
3. Pastikan database connection string benar

### Issue: "SQLSTATE[HY000] [2002] Connection refused"

**Solusi:**
1. Verifikasi environment variables database (`DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`)
2. Pastikan database service online dan bisa diakses dari public internet
3. Whitelist IP Vercel jika database memerlukan IP whitelisting

### Issue: "Route not found"

**Solusi:**
1. Pastikan `vercel.json` sudah correct
2. Clear cache: `vercel env pull` lalu re-deploy
3. Check routing configuration di `vercel.json`

### Issue: "Storage not writable"

**Solusi:**
Laravel di Vercel harus menggunakan `/tmp` untuk writable storage. Update:

```php
// config/view.php
'compiled' => env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views'))),

// Di .env atau Vercel env vars:
VIEW_COMPILED_PATH=/tmp/views
```

### Issue: "Session/Cache not working"

**Solusi:**
Serverless tidak mendukung file-based session/cache. Update di environment variables:
```
CACHE_DRIVER=array
SESSION_DRIVER=cookie
```

---

## 8️⃣ Best Practices untuk Production

### 1. Gunakan Database Eksternal
- ✅ PlanetScale (MySQL Serverless)
- ✅ Supabase (PostgreSQL)
- ✅ Railway
- ❌ SQLite (tidak direkomendasikan untuk serverless)

### 2. Set APP_DEBUG=false
Jangan pernah leave debug mode ON di production!

### 3. Setup CORS untuk Frontend
Tambahkan di `config/cors.php`:
```php
'allowed_origins' => [
    'https://your-frontend-domain.vercel.app',
],
```

### 4. Rate Limiting
Aktifkan rate limiting untuk API di `app/Http/Kernel.php`

### 5. Monitoring
- Setup error tracking (Sentry, Bugsnag)
- Monitor Vercel logs regularly

---

## 9️⃣ Connecting Frontend

Jika Anda punya frontend (React, Vue, Next.js), update base URL API:

```javascript
// Frontend .env
VITE_API_URL=https://todolist-laravel.vercel.app/api
# atau
NEXT_PUBLIC_API_URL=https://todolist-laravel.vercel.app/api
```

```javascript
// API configuration
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

// Example fetch
const login = async (email, password) => {
    const response = await fetch(`${API_BASE_URL}/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
    });
    return response.json();
};
```

---

## 🎉 Selesai!

Laravel API Anda sekarang sudah live di Vercel! 

**URL Produksi:** https://[your-project-name].vercel.app

**Untuk update deployment:**
```bash
# Commit changes to git
git add .
git commit -m "Update"

# If linked to Git, Vercel auto-deploys
# Or manually:
vercel --prod
```

---

## 📚 Resources

- [Vercel Documentation](https://vercel.com/docs)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Vercel PHP Runtime](https://github.com/vercel-community/php)
- [PlanetScale Laravel](https://planetscale.com/docs/tutorials/connect-laravel-app)

---

**Need help?** Check Vercel deployment logs or Laravel logs for detailed error messages.
