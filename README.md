# MiniCal - Modern Appointment Scheduling Platform

![MiniCal](https://img.shields.com/badge/PHP-8.3%2B-indigo)
![TailwindCSS](https://img.shields.com/badge/TailwindCSS-3.x-blue)
![License](https://img.shields.com/badge/License-MIT-green)

MiniCal is a lightweight, modern, open-source appointment scheduling platform inspired by Cal.com. Built with Core PHP (MVC Architecture), MySQL, Tailwind CSS, Vanilla JavaScript, and FullCalendar.

---

## ✨ Features

- 📅 **Smart Weekly Availability**: Set working hours for each day of the week. Auto-detect & prevent double bookings.
- 🎨 **Cal.com Light UI Aesthetic**: Crisp typography, high-contrast black primary buttons, light cards with subtle borders.
- 🌐 **Bilingual Support**: Instant client-side English (🇬🇧) and Bengali (🇧🇩 বাংলা) language switching.
- 📝 **Custom Booking Form Builder**: Dynamically add custom fields (Text, Textarea, Select, Radio, Checkbox, Phone, Date) for attendees.
- 💳 **Free & Paid Appointments**: Support for free consultations or upfront payments (Stripe & Razorpay integrations).
- 🔗 **Public Booking Engine**: Host booking page at `/u/{username}` with date/slot picker.
- 👑 **Dedicated Super Admin Dashboard**: Platform overview, user account management (enable/disable/delete), global appointment logs, dynamic pricing plan manager, and SMTP settings.
- 📱 **REST API (v1)**: Bearer Token authentication for Android/iOS mobile applications.

---

## 🛠️ Technology Stack

- **Backend**: Core PHP 8.3+ (MVC Architecture, Composer, PDO, PHPMailer)
- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript (Fetch API), FullCalendar
- **Database**: MySQL / MariaDB
- **Web Server**: Apache (`.htaccess` URL rewrite routing)

---

## 🚀 Quick Setup Guide

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/DibyenduCode/minical.git
   ```

2. **Move to XAMPP htdocs**:
   Place the project folder inside `C:/xampp/htdocs/cal`.

3. **Import Database Schema**:
   Import `database/schema.sql` into MySQL / phpMyAdmin:
   ```bash
   mysql -u root -e "source database/schema.sql"
   ```

4. **Access Application**:
   - **Landing Page**: `http://localhost/cal/`
   - **Login**: `http://localhost/cal/login`
   - **Super Admin Credentials**:
     - Email: `admin@minical.local`
     - Password: `admin123`
   - **Public Booking Page**: `http://localhost/cal/u/admin`

---

## 📡 REST API (v1) Endpoints

All API requests require `Authorization: Bearer <token>` header (except `/login`).

- `POST /api/v1/login` - Authenticate and retrieve Bearer token
- `GET /api/v1/dashboard` - Get host stats & metrics
- `GET /api/v1/profile` - Get user profile details
- `GET /api/v1/availability` - Get weekly schedule
- `GET /api/v1/bookings` - Get list of bookings
- `GET /api/v1/form-fields` - Get custom form fields

---

## 📄 License

Distributed under the MIT License.
