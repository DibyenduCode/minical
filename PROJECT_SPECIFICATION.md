# MiniCal - Complete Project Specification (MVP)

> **Project Name:** MiniCal
> **Version:** 1.0 (MVP)
> **Architecture:** Core PHP (MVC)
> **Frontend:** HTML5 + Tailwind CSS + Vanilla JavaScript + AJAX + FullCalendar
> **Database:** MySQL / MariaDB

---

# Project Goal

Build a lightweight, modern, responsive appointment scheduling platform inspired by Cal.com.

The application should be easy to host on cPanel/VPS, simple to maintain, mobile-friendly, and designed with clean, scalable architecture.

The application must support both **Free** and **Paid** appointments, Google Calendar synchronization, custom booking forms, and a REST API for future Android/iOS applications.

---

# Design Requirements

## UI Style

The entire application must have a modern SaaS design.

Design language:

* Clean
* Minimal
* Premium
* Professional
* Fast
* Accessible

Use plenty of whitespace.

Rounded corners.

Soft shadows.

Modern cards.

Smooth hover animations.

Subtle transitions.

No outdated UI components.

---

# Color System

Primary Color

* Indigo

Accent

* Blue

Success

* Green

Warning

* Orange

Danger

* Red

Background

* White
* Light Gray

Dark Mode

Design every component so Dark Mode can easily be added later.

---

# Typography

Use a modern Google Font.

Examples:

* Inter
* Manrope
* Plus Jakarta Sans

Headings

* Bold
* Clean
* Large spacing

Body

* Easy to read

---

# Responsive Design (Mandatory)

The entire application **MUST** be fully responsive.

Support:

* Mobile
* Tablet
* Laptop
* Desktop
* Large Desktop

Recommended breakpoints

* <640px
* 640px
* 768px
* 1024px
* 1280px
* 1536px

Requirements

* Mobile-first design
* Flexible layouts
* Responsive cards
* Responsive tables
* Responsive forms
* Responsive calendar
* Responsive navigation
* Responsive modals
* Responsive dropdowns
* Responsive buttons
* Responsive typography

No horizontal scrolling.

No broken layouts.

All pages must work perfectly on every device.

---

# Technology Stack

## Backend

* Core PHP 8.3+
* MVC Architecture
* Composer
* PDO
* PHPMailer

---

## Frontend

* HTML5
* Tailwind CSS
* Vanilla JavaScript
* Fetch API (AJAX)
* FullCalendar

---

## Database

* MySQL
* MariaDB

---

# Folder Structure

```text
project/

app/
    controllers/
    models/
    services/
    middleware/
    helpers/
    libraries/

config/

routes/

templates/

public/
    css/
    js/
    images/

storage/
    logs/
    cache/

uploads/

api/
    v1/

admin/

vendor/

index.php
```

---

# Authentication

Features

* Register
* Login
* Logout
* Forgot Password
* Remember Me
* Change Password

Security

* Password Hashing
* CSRF Protection
* Session Regeneration
* Input Validation
* Rate Limiting

---

# Dashboard

Dashboard cards

* Today's Bookings
* Upcoming Bookings
* Total Bookings
* Cancelled Bookings
* Revenue
* Pending Payments

Quick Actions

* Booking Link
* Availability
* Event Settings
* Google Calendar
* Payment Settings
* Booking Form Builder
* API Tokens
* Profile

---

# Profile

Fields

* Name
* Username
* Email
* Phone
* Timezone
* Profile Picture

---

# Availability

Weekly schedule

Monday

Tuesday

Wednesday

Thursday

Friday

Saturday

Sunday

Each day supports

* Enable / Disable
* Start Time
* End Time

---

# Event

Version 1 supports one event.

Fields

* Event Name
* Description
* Duration
* Slug
* Location
* Free or Paid
* Price
* Currency
* Active Status

Booking URL

```
https://domain.com/u/username
```

---

# Custom Booking Form

Every user can build their own appointment form.

Default fields

* Full Name
* Email

Optional fields

* Phone
* Company
* Website
* Notes

Custom fields

Supported types

* Text
* Textarea
* Email
* Phone
* Number
* Date
* Dropdown
* Radio
* Checkbox
* Yes / No
* URL

Each field supports

* Label
* Placeholder
* Help Text
* Required
* Optional
* Display Order

Users can

* Create Fields
* Edit Fields
* Delete Fields
* Reorder Fields
* Preview Form

---

# Public Booking Page

Visitor can

* View Event
* View Profile
* View Duration
* Select Date
* Select Time
* Complete Booking Form
* Pay (if required)
* Confirm Appointment

---

# Google Calendar Integration

Users can

* Connect Google Account
* Disconnect Account
* Select Calendar

Automatically

* Create Event
* Update Event
* Delete Event

Before showing time slots

* Read busy times
* Hide occupied slots
* Prevent double booking

---

# Payment System

Every event can be

* Free
* Paid

Supported gateways

* Stripe
* Razorpay

Booking flow

Free

Booking

↓

Confirmed

Paid

Booking

↓

Payment

↓

Success

↓

Booking Confirmed

If payment fails

Booking is NOT confirmed.

Dashboard

Payments

* Connected Gateway
* Revenue
* Transactions
* Pending Payments

---

# Booking Management

Statuses

* Pending
* Awaiting Payment
* Paid
* Confirmed
* Completed
* Cancelled
* Refunded

Users can

* View Booking
* Cancel Booking
* Complete Booking

Search

* Name
* Email
* Date

Filters

* Today
* Upcoming
* Completed
* Cancelled

---

# Email Notifications

Customer

* Booking Confirmation
* Cancellation

Host

* New Booking
* Cancellation
* Payment Confirmation

---

# REST API

Authentication

```
POST /api/v1/login
POST /api/v1/logout
```

Dashboard

```
GET /api/v1/dashboard
```

Profile

```
GET /api/v1/profile
POST /api/v1/profile/update
```

Availability

```
GET /api/v1/availability
POST /api/v1/availability/update
```

Bookings

```
GET /api/v1/bookings
GET /api/v1/bookings/{id}
POST /api/v1/bookings/{id}/cancel
```

Google Calendar

```
GET /api/v1/google/status
GET /api/v1/google/connect
POST /api/v1/google/disconnect
```

Payments

```
GET /api/v1/payments
POST /api/v1/payments/connect
```

Booking Form

```
GET /api/v1/form-fields
POST /api/v1/form-fields
PUT /api/v1/form-fields/{id}
DELETE /api/v1/form-fields/{id}
```

Authentication

Bearer Token

---

# Admin Panel

Dashboard

Users

Bookings

Payments

Reports

Settings

Admin can

* View Users
* Disable Users
* Delete Users
* View Bookings
* View Payments
* Configure SMTP
* Configure Site Settings

---

# Database Tables

Core

* users
* profiles
* availability
* events
* bookings
* booking_logs

Forms

* booking_form_fields
* booking_form_responses

Payments

* payment_accounts
* payments

Google

* google_accounts
* calendar_events

Authentication

* api_tokens
* password_resets

System

* settings

---

# UI Pages

Public

* Home
* Login
* Register
* Booking Page
* Booking Success
* Booking Cancelled

User Dashboard

* Dashboard
* Bookings
* Availability
* Event
* Booking Form Builder
* Google Calendar
* Payments
* Profile
* API Tokens
* Settings

Admin

* Dashboard
* Users
* Bookings
* Payments
* Reports
* Settings

---

# Performance Requirements

* Fast loading
* Lazy loading where appropriate
* Optimized assets
* Optimized database queries
* Minimal JavaScript bundle
* SEO-friendly public pages

---

# Security

* Prepared Statements (PDO)
* Password Hashing
* CSRF Protection
* XSS Protection
* SQL Injection Prevention
* Session Regeneration
* API Authentication
* Input Validation
* File Upload Validation
* Login Rate Limiting

---

# Coding Standards

* Follow MVC architecture
* Reusable components
* Clean code
* Consistent naming conventions
* Separation of concerns
* Modular structure
* Well-documented functions
* Avoid duplicate code

---

# UI/UX Instructions

The generated interface **must**:

* Feel like a modern SaaS product.
* Use reusable UI components.
* Include smooth hover and transition effects.
* Use consistent spacing and typography.
* Display loading indicators for AJAX actions.
* Show clear success and error messages.
* Provide empty states for tables and lists.
* Use confirmation dialogs before destructive actions.
* Include breadcrumbs on dashboard pages.
* Support keyboard navigation where practical.
* Follow accessibility best practices (WCAG-friendly colors, focus states, semantic HTML).

---

# Final Deliverable Requirements

The generated project should be:

* Fully responsive on all devices.
* Production-ready folder structure.
* Clean, maintainable, and modular code.
* Built with Core PHP (no Laravel or other PHP frameworks).
* Tailwind CSS for styling.
* Vanilla JavaScript with Fetch API for interactivity.
* FullCalendar for scheduling.
* REST API ready for mobile applications.
* Easy to deploy on Apache/cPanel without additional server dependencies.
* Designed so future features (multi-user teams, Zoom, Google Meet, Outlook, subscriptions, webhooks, white-label, and multi-language support) can be added without major architectural changes.
