# QuTick - Modern Ticketing & Issue Tracking System

QuTick is a high-fidelity, modern ticketing and issue tracking application designed for seamless communication between users and administrators. It features an eye-pleasing, soft-toned light theme inspired by premium dashboard layouts, robust user activity log auditing, and landscape PDF reporting.

---

## 🚀 Key Features

* **Premium Soft Light UI/UX**: Designed using Tailwind CSS for clean layout aesthetics, featuring soft rounded panels (`rounded-2xl`), responsive navigation grids, and elegant indicator badges.
* **Complete Ticketing Lifecycle**: Allows registered users to submit issues, view details, track progress, and communicate via ticket replies.
* **Role-Based Workflows**:
  * **Admin Panel**: Dashboard statistics with doughnut chart visualization, ticket priority and status updates, and full system audit logs.
  * **User Panel**: Streamlined dashboard to manage personal tickets, reply to issues, and track custom activity logs.
* **System Activity Auditing**: A database-backed transaction logging system that records user registrations, logins, logouts, ticket creation, edits, and status transitions, along with IP addresses and user agents.
* **On-Demand PDF Reports**: Admin-exclusive landscape (A4-L) report exports using `mPDF` with custom date-range filters.

---

## 🛠️ Tech Stack

### Backend (`laravel-api`)
* **Framework**: Laravel 12
* **Database**: MySQL / MariaDB
* **Authentication**: Laravel Sanctum (Token-based SPA Authentication)
* **Libraries**: `mpdf/mpdf` (PDF generation), `carbon` (Date operations)

### Frontend (`vue-frontend`)
* **Framework**: Vue 3 (Vite-powered Single Page App)
* **State Management**: Pinia
* **Routing**: Vue Router
* **Styling**: Tailwind CSS
* **Visualization**: Chart.js (Donut charts)
* **Icons**: Feather Icons

---

## ⚙️ Getting Started & Installation

### Prerequisites
* PHP >= 8.2
* Node.js >= 20.x
* Composer
* MySQL Database

### 1. Backend Setup (`laravel-api`)

```bash
# Navigate to backend directory
cd laravel-api

# Install Composer dependencies
composer install

# Copy environment configuration
copy .env.example .env

# Configure your DB connection inside .env (e.g., DB_DATABASE=qutick_api_new)

# Generate application security key
php artisan key:generate

# Run migrations and seed database
php artisan migrate --seed

# Start the Laravel development server
php artisan serve --port=8001
```

### 2. Frontend Setup (`vue-frontend`)

```bash
# Navigate to frontend directory
cd ../vue-frontend

# Install npm dependencies
npm install

# Run the development build
npm run dev
```

The frontend will run at `http://localhost:5173`. Access the API endpoints at `http://127.0.0.1:8001/api`.

---

## 📄 License
This project is open-sourced software licensed under the [MIT license](LICENSE).
