# 📝 Laravel TodoList API

Backend API untuk aplikasi To-Do List menggunakan Laravel 11 dengan Laravel Sanctum untuk autentikasi.

## ✨ Features

- ✅ User Registration & Authentication (Laravel Sanctum)
- ✅ CRUD Operations untuk Tasks
- ✅ RESTful API Design
- ✅ Token-based Authentication
- ✅ Database Migration & Seeding
- ✅ Ready untuk deploy ke Vercel

## 🚀 Tech Stack

- **Framework:** Laravel 11
- **Authentication:** Laravel Sanctum
- **Database:** MySQL / PostgreSQL / SQLite
- **PHP Version:** 8.2+

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM (untuk Vercel deployment)
- MySQL/PostgreSQL (untuk production) atau SQLite (untuk development)

## 🛠️ Local Development Setup

### 1. Clone & Install Dependencies

```bash
# Clone repository
git clone <repository-url>
cd TugasLaravel

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy .env.example
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env sesuai kebutuhan
# Untuk development, SQLite sudah cukup
DB_CONNECTION=sqlite
```

### 3. Database Setup

```bash
# Create SQLite database (jika belum ada)
touch database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Run seeders
php artisan db:seed
```

### 4. Run Development Server

```bash
# Start Laravel development server
php artisan serve

# (Optional) Run Vite dev server untuk frontend assets
npm run dev
```

API akan berjalan di: `http://localhost:8000`

## 📡 API Endpoints

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

#### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Tasks (Protected Routes)

#### Get All Tasks
```http
GET /api/tasks
Authorization: Bearer {token}
```

#### Create Task
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "My Task",
  "description": "Task description",
  "status": "pending"
}
```

#### Update Task
```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Task",
  "description": "Updated description",
  "status": "completed"
}
```

#### Delete Task
```http
DELETE /api/tasks/{id}
Authorization: Bearer {token}
```

## 🌐 Deployment ke Vercel

### Quick Start
Ikuti panduan di **[QUICK_START.md](QUICK_START.md)** untuk deployment cepat (5 menit).

### Detailed Guide
Untuk panduan lengkap step-by-step, baca **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)**.

### Automated Deployment

**Windows (PowerShell):**
```powershell
.\deploy.ps1
```

**Linux/Mac:**
```bash
chmod +x deploy.sh
./deploy.sh
```

## 🔧 Configuration Files

- `vercel.json` - Konfigurasi Vercel deployment
- `api/index.php` - Entry point untuk Vercel serverless
- `.env.production.example` - Environment variables template untuk production
- `config/cors.php` - CORS configuration untuk frontend integration

## 📚 Documentation

- [Quick Start Guide](QUICK_START.md) - Panduan cepat deployment
- [Deployment Guide](DEPLOYMENT_GUIDE.md) - Panduan lengkap deployment ke Vercel
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

## 🧪 Testing

```bash
# Run PHP tests
php artisan test

# Or using PHPUnit
vendor/bin/phpunit
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

**Gregorius Oliver Rachmadi** </br>
**Darren Nathanael**</br>
**Kenny William**</br>

## 🙏 Acknowledgments

- Laravel Team untuk framework yang luar biasa
- Vercel untuk serverless hosting
- Laravel Sanctum untuk authentication system
