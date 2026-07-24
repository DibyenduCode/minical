# 🗓️ DayCal - Modern Open-Source Appointment Scheduling Platform

![DayCal](https://img.shields.com/badge/DayCal-v1.0-indigo?style=for-the-badge&logo=calendar)
![PHP](https://img.shields.com/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.com/badge/MySQL-8.0%2B-orange?style=for-the-badge&logo=mysql)
![TailwindCSS](https://img.shields.com/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss)
![React Native Ready](https://img.shields.com/badge/Mobile-React%20Native%20Ready-61DAFB?style=for-the-badge&logo=react)
![License](https://img.shields.com/badge/License-MIT-green?style=for-the-badge)

**DayCal** is a high-performance, open-source appointment scheduling & calendar management platform built with modern **Core PHP (MVC Architecture)**, **MySQL**, **Tailwind CSS**, **Vanilla JavaScript**, and a complete **REST API (v1)** designed for **React Native companion mobile apps**.

Inspired by Cal.com, DayCal empowers freelancers, consultants, educators, and enterprise teams to host custom booking pages, accept paid consultations, automate weekly availability, build custom attendee forms, apply promo codes, and connect custom branded domains.

---

## 🌟 Key Features

### 📅 Smart Availability & Scheduling Engine
- **Weekly Schedule Customization**: Set working hours per day of the week with active day toggling.
- **Break Period Support**: Define lunch or break intervals (e.g. 13:00 to 14:00) during working days.
- **Buffer Times & Booking Windows**: Configure pre/post meeting buffer times (e.g. 15 mins) and advance booking windows.
- **Automated Double-Booking Prevention**: Real-time slot conflict checking against existing appointments.

### 🔗 Public Booking Engine (`/u/{username}`)
- **Personalized Booking Pages**: Clean, high-converting booking link hosted at `http://localhost/daycal/u/{username}`.
- **Interactive Calendar & Slot Selector**: Real-time available slot generation dynamically computed based on timezones and availability rules.
- **Free & Paid Appointments**: Support for free consultations or paid appointments with UPI, QR Code, or payment confirmation workflows.
- **Google Meet & Google Calendar Integration**: Auto-generate Google Meet links and sync calendar events upon booking.

### 📝 Custom Booking Form Builder
- **Dynamic Attendee Questions**: Create custom form fields per user or per specific event type.
- **Supported Field Types**: Single-line text, multi-line textarea, dropdown select, checkboxes, radio buttons, phone number, and date pickers.
- **Response Tracking**: Form responses stored alongside booking entries and visible in user and admin dashboards.

### 🏷️ Promo Codes & Discount System
- **Flexible Discounts**: Create percentage-based (e.g., 20% OFF) or fixed-amount (e.g., $15 OFF) promo codes.
- **Usage Caps & Expiry Control**: Set maximum usage limits and expiration dates for promotional campaigns.
- **Public Checkout Application**: Attendees can apply promo codes directly during appointment scheduling.

### 📱 REST API (v1) for React Native Companion App
- **Mobile Authentication**: Bearer Token generation via `POST /api/v1/signup` and `POST /api/v1/login`.
- **CORS & Preflight Ready**: Pre-configured `Access-Control-Allow-Origin: *` and `OPTIONS` preflight handling.
- **Full Mobile Feature Parity**: Mobile endpoints for Dashboard, Profile, Event Types CRUD, Weekly Availability, Bookings management, Custom Form Fields, and Promo Codes.

### 👑 Super Admin Dashboard (`/admin`)
- **System Overview & Analytics**: Platform-wide metrics for total users, bookings, revenue, and active subscriptions.
- **SaaS Subscription Plans**: Dynamic creation, editing, and deletion of subscription tiers (`Free`, `Pro`, `Enterprise`).
- **User Account Control**: Enable/disable user accounts, force password changes, update subscription plans, or delete accounts.
- **System Settings & SMTP Configuration**: Manage global site settings, SMTP mail server credentials, payment gateways, and developer API keys.

### 🌐 White-Label Custom Domains
- **Branded Domain Mapping**: Map custom user domains (e.g. `booking.yourbrand.com`) to automatically serve branded public booking pages.

---

## 🛠️ Technology Stack

| Layer | Technology |
| :--- | :--- |
| **Backend Framework** | Core PHP 8.2+ (MVC Architecture, PSR-4 Autoloading, PDO) |
| **Database** | MySQL 8.0+ / MariaDB (InnoDB, Prepared Statements) |
| **Frontend UI** | HTML5, Tailwind CSS 3.x, Vanilla JavaScript (Fetch API), Google Fonts (*Plus Jakarta Sans*) |
| **Routing & Middleware** | Custom Front Controller (`index.php`), Apache `.htaccess` Rewrite Engine |
| **Mobile Integration** | RESTful JSON API (v1) with Bearer Token Authentication for React Native |
| **Mailing System** | Native SMTP Socket Client & PHP Mail fallback |

---

## 📂 Project Architecture

```
daycal/
├── app/
│   ├── controllers/            # Controller layer (Routing Callbacks)
│   │   ├── AdminController.php
│   │   ├── ApiController.php             # REST API Controller for React Native
│   │   ├── AuthController.php            # Web Auth (Login, Signup, Reset Password)
│   │   ├── AvailabilityController.php    # Weekly Schedule Settings
│   │   ├── DashboardController.php       # Host Analytics & Bookings
│   │   ├── EventController.php           # Event Types Management
│   │   ├── FormBuilderController.php     # Custom Attendees Form Builder
│   │   ├── IntegrationController.php     # Google OAuth & Developer API Keys
│   │   ├── ProfileController.php         # User Profile & Plan Upgrade
│   │   └── PublicBookingController.php   # Public Booking Engine (/u/{username})
│   ├── core/                   # Framework Core
│   │   ├── App.php                       # Front Controller Router & CORS Manager
│   │   ├── Controller.php                # Base Controller Class
│   │   ├── Database.php                  # PDO Database Connection Singleton
│   │   ├── Model.php                     # Base Model Class
│   │   ├── Request.php                   # Request Parser & Bearer Token Extractor
│   │   ├── Response.php                  # JSON & HTTP Response Utility
│   │   └── Session.php                   # Session & CSRF Protection
│   ├── models/                 # Database Models (Active Record / Data Access)
│   │   ├── Availability.php
│   │   ├── Booking.php
│   │   ├── Event.php
│   │   ├── FormField.php
│   │   ├── Profile.php
│   │   └── User.php
│   └── services/               # Background Services & Integrations
│       ├── EmailService.php              # SMTP Mailer for Confirmations & Resets
│       ├── GoogleAuthService.php
│       └── GoogleCalendarService.php
├── config/                     # Application & Database Configuration
│   ├── config.php
│   └── database.php
├── database/                   # SQL Schemas & Database Dumps
│   └── daycal_daycal.sql
├── public/                     # Static Public Assets & File Uploads
│   └── uploads/                # Logos, Avatars, and Payment QR Codes
├── routes/
│   └── web.php                 # Central Route Definitions
├── scratch/                    # Master Automated Test Suites
│   ├── master_a_to_z_test.php
│   └── test_all_apis.php
├── templates/                  # View Templates (PHP Layouts)
│   ├── admin/
│   ├── auth/
│   ├── availability/
│   ├── booking/
│   ├── dashboard/
│   ├── event/
│   ├── form_builder/
│   ├── home/
│   ├── integrations/
│   ├── layout/
│   └── profile/
├── .htaccess                   # Apache URL Rewrite Rules
├── index.php                   # Entry Point
└── README.md                   # Project Documentation
```

---

## 🗄️ Database Schema Summary (`17 Tables`)

DayCal database (`daycal_daycal.sql`) includes 17 relational tables:

1. **`users`**: User accounts, passwords (Bcrypt), roles (`admin`/`user`), and status.
2. **`profiles`**: User bio, timezone, phone, company, custom domain, avatar, UPI ID, and QR code.
3. **`events`**: Event types (duration, buffer, location, price, currency, slug).
4. **`availability`**: Weekly schedules (day of week, start/end time, active state, break times).
5. **`bookings`**: Customer appointments, dates, slots, promo codes, prices, and statuses (`confirmed`, `pending`, `completed`, `cancelled`).
6. **`booking_form_fields`**: Custom attendee form field configurations.
7. **`booking_form_responses`**: Attendee answers submitted during booking.
8. **`booking_logs`**: Audit logs for appointment state changes.
9. **`promo_codes`**: Promotional discount codes, types, caps, and expiry dates.
10. **`plans`**: SaaS pricing plan tiers.
11. **`settings`**: Global system parameters (site name, SMTP credentials, payment details).
12. **`api_tokens`**: Bearer API tokens generated for React Native companion apps.
13. **`google_accounts`**: Connected Google OAuth credentials.
14. **`calendar_events`**: Synced Google Calendar event mappings.
15. **`password_resets`**: Password reset token requests and expiration timestamps.
16. **`payments`**: Payment transaction history.
17. **`payment_accounts`**: Payment gateway credentials.

---

## 🚀 Quick Start & Installation Guide

### Prerequisites
- PHP 8.2 or higher
- MySQL / MariaDB Server 8.0+
- XAMPP / WAMP / Laragon or Apache Web Server with `mod_rewrite` enabled

### 1. Clone Project
Clone or copy the project into your local server root (e.g. `C:/xampp/htdocs/daycal`):
```bash
git clone https://github.com/DibyenduCode/minical.git C:/xampp/htdocs/daycal
```

### 2. Import Database
Import the SQL dump into phpMyAdmin or MySQL CLI:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS daycal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p daycal < C:/xampp/htdocs/daycal/daycal_daycal.sql
```

### 3. Verify Database Config (`config/database.php`)
Ensure your database credentials match:
```php
return [
    'host' => 'localhost',
    'port' => 3306,
    'db'   => 'daycal',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4'
];
```

### 4. Access DayCal Web Application
- **Landing Page**: `http://localhost/daycal/`
- **Login**: `http://localhost/daycal/login`
- **Super Admin Credentials**:
  - **Email**: `admin@minical.local`
  - **Password**: `admin123`
- **Public Booking Engine**: `http://localhost/daycal/u/admin`

---

## 📱 REST API (v1) Reference for React Native

All authenticated endpoints require an `Authorization: Bearer <token>` header.

### 🔑 Authentication API
| Method | Endpoint | Description | Payload Sample |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/signup` | Register new mobile user & return API token | `{"name":"John", "username":"john", "email":"john@test.com", "password":"password123"}` |
| `POST` | `/api/v1/login` | Authenticate mobile user & return API token | `{"email":"john@test.com", "password":"password123"}` |
| `GET` | `/api/check-username` | Check if a username is available | Query string: `?username=john` |

### 📊 Dashboard & Profile API
| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/dashboard` | Get host booking metrics & status totals | Yes |
| `GET` | `/api/v1/profile` | Get host user profile details | Yes |
| `POST` | `/api/v1/profile` | Update profile fields & timezone | Yes |

### 📅 Events & Availability API
| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/events` | List host's event types | Yes |
| `POST` | `/api/v1/events` | Create new event type | Yes |
| `POST` | `/api/v1/events/update/{id}` | Update existing event type | Yes |
| `POST` | `/api/v1/events/delete/{id}` | Delete event type | Yes |
| `GET` | `/api/v1/availability` | Get weekly availability schedule | Yes |
| `POST` | `/api/v1/availability` | Update weekly availability schedule | Yes |

### 📌 Bookings API
| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/bookings` | Fetch host bookings list | Yes |
| `POST` | `/api/v1/bookings/confirm-payment` | Approve booking payment | Yes |
| `POST` | `/api/v1/bookings/cancel` | Cancel booking with reason | Yes |
| `POST` | `/api/v1/bookings/complete` | Mark booking completed | Yes |
| `GET` | `/u/{username}/slots` | Get public available slots for a date | No |

---

## 🧪 Quality Assurance & Status

DayCal codebase and REST API v1 endpoints have passed 100% automated A-to-Z functional and health status verification.

---

## 📄 License

Distributed under the **MIT License**. Free for commercial and personal use.
