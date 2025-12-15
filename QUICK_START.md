# 🚀 Quick Start - Deploy Laravel ke Vercel

## ⚡ Langkah Cepat (5 Menit)

### 1. Install Vercel CLI
```bash
npm install -g vercel
```

### 2. Login ke Vercel
```bash
vercel login
```
Pilih metode login (GitHub recommended).

### 3. Setup Database (PENTING!)

**Pilih salah satu:**

#### Option A: PlanetScale (Recommended - MySQL Gratis)
1. Daftar di https://planetscale.com
2. Buat database baru
3. Copy connection string
4. Simpan untuk step berikutnya

#### Option B: Supabase (PostgreSQL Gratis)
1. Daftar di https://supabase.com
2. Buat project baru
3. Di Settings → Database, copy connection string
4. Simpan untuk step berikutnya

### 4. Deploy!
```bash
cd d:\Laravel\TugasLaravel
vercel
```

Jawab pertanyaan:
- Set up and deploy? → **Y**
- Link to existing project? → **N**
- Project name? → **todolist-laravel** (atau nama lain)
- In which directory? → **Enter** (.)
- Want to override? → **N**

### 5. Setup Environment Variables

Buka dashboard: https://vercel.com/dashboard

1. Pilih project Anda
2. Klik **Settings** → **Environment Variables**
3. Tambahkan variable berikut satu per satu:

**Copy-paste value ini (GANTI yang di <...>):**

```
APP_NAME=TodoList
APP_ENV=production
APP_DEBUG=false
APP_KEY=<jalankan: php artisan key:generate --show>
APP_URL=<URL dari Vercel, contoh: https://todolist-laravel.vercel.app>

DB_CONNECTION=mysql
DB_HOST=<dari PlanetScale/Supabase>
DB_PORT=3306
DB_DATABASE=<nama database>
DB_USERNAME=<username database>
DB_PASSWORD=<password database>

CACHE_DRIVER=array
SESSION_DRIVER=cookie
LOG_CHANNEL=stderr

SANCTUM_STATEFUL_DOMAINS=<domain Vercel tanpa https://>
SESSION_DOMAIN=<domain Vercel tanpa https://>

VIEW_COMPILED_PATH=/tmp/views
APP_CONFIG_CACHE=/tmp/config.php
APP_ROUTES_CACHE=/tmp/routes.php
APP_SERVICES_CACHE=/tmp/services.php
```

### 6. Redeploy dengan Environment Variables
```bash
vercel --prod
```

### 7. Setup Database Schema

**Via PlanetScale/Supabase Dashboard:**
1. Connect ke database via web interface atau MySQL client
2. Export SQL dari local database:
   ```bash
   # Export schema
   php artisan migrate:fresh
   
   # Lalu manual copy table structure ke production database
   ```

**ATAU import manual:**
- Buka phpmyadmin/database tool
- Run migration SQL secara manual

### 8. Test API

Ganti `YOUR_DOMAIN` dengan domain Vercel Anda:

#### Register:
```bash
curl -X POST https://YOUR_DOMAIN/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Login:
```bash
curl -X POST https://YOUR_DOMAIN/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

Simpan `token` dari response!

#### Get Tasks:
```bash
curl -X GET https://YOUR_DOMAIN/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## ✅ Selesai!

API Laravel Anda sekarang live di: `https://[nama-project].vercel.app`

---

## 🐛 Troubleshooting Cepat

### Error 500?
1. Check Vercel logs: Dashboard → Deployments → [pilih deployment] → Runtime Logs
2. Pastikan `APP_KEY` sudah di-set
3. Cek koneksi database

### Database Connection Error?
1. Verifikasi `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`
2. Pastikan database online dan accessible dari internet
3. Test koneksi database dari MySQL client

### Route Not Found?
1. Clear cache: delete deployment dan redeploy
2. Pastikan `vercel.json` exists dan correct

---

## 📝 Update/Redeploy

Setiap kali ada perubahan code:

```bash
# Commit changes
git add .
git commit -m "Update code"

# Deploy
vercel --prod
```

Atau jika linked ke GitHub, Vercel akan auto-deploy setiap push!

---

## 🔗 Connect Frontend

Jika punya frontend app (React, Vue, Next.js):

```javascript
// .env di frontend
VITE_API_URL=https://your-laravel-app.vercel.app/api
```

```javascript
// API calls
const API_URL = import.meta.env.VITE_API_URL;

const login = async (email, password) => {
  const response = await fetch(`${API_URL}/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  const data = await response.json();
  return data;
};
```

---

**Butuh detail lebih lengkap?** Baca `DEPLOYMENT_GUIDE.md`

**Ada masalah?** Check runtime logs di Vercel dashboard!
