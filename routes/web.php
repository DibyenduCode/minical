<?php

use App\Core\App;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\AvailabilityController;
use App\Controllers\EventController;
use App\Controllers\FormBuilderController;
use App\Controllers\IntegrationController;
use App\Controllers\GoogleAuthController;
use App\Controllers\AdminController;
use App\Controllers\PublicBookingController;
use App\Controllers\ApiController;

// Home & Landing Page Routes
App::get('/', [HomeController::class, 'index']);
App::get('/home', [HomeController::class, 'index']);

// Authentication Routes
App::get('/login', [AuthController::class, 'showLogin']);
App::post('/login', [AuthController::class, 'login']);
App::get('/register', [AuthController::class, 'showRegister']);
App::post('/register', [AuthController::class, 'register']);
App::get('/logout', [AuthController::class, 'logout']);

// User Dashboard & Settings Routes
App::get('/dashboard', [DashboardController::class, 'index']);
App::get('/bookings', [DashboardController::class, 'bookingsList']);
App::post('/dashboard/cancel', [DashboardController::class, 'cancelBooking']);
App::post('/dashboard/complete', [DashboardController::class, 'completeBooking']);
App::get('/profile', [ProfileController::class, 'index']);
App::post('/profile', [ProfileController::class, 'update']);

// Integrations & Apps Routes
App::get('/integrations', [IntegrationController::class, 'index']);
App::get('/integrations/google/connect', [GoogleAuthController::class, 'connect']);
App::get('/integrations/google/callback', [GoogleAuthController::class, 'callback']);
App::post('/integrations/google/disconnect', [GoogleAuthController::class, 'disconnect']);
App::post('/integrations/google/select-calendar', [GoogleAuthController::class, 'selectCalendar']);

// Availability & Event Configuration
App::get('/availability', [AvailabilityController::class, 'index']);
App::post('/availability', [AvailabilityController::class, 'update']);

App::get('/event', [EventController::class, 'index']);
App::post('/event', [EventController::class, 'create']);
App::post('/event/update/{id}', [EventController::class, 'update']);
App::post('/event/delete/{id}', [EventController::class, 'delete']);

// Custom Booking Form Builder
App::get('/form-builder', [FormBuilderController::class, 'index']);
App::post('/form-builder', [FormBuilderController::class, 'create']);
App::post('/form-builder/delete/{id}', [FormBuilderController::class, 'delete']);

// Dedicated Super Admin Dashboard Routes
App::get('/admin', [AdminController::class, 'index']);
App::get('/admin/domains', [AdminController::class, 'domains']);
App::get('/admin/plans', [AdminController::class, 'plans']);
App::post('/admin/plans', [AdminController::class, 'createPlan']);
App::post('/admin/plans/delete/{id}', [AdminController::class, 'deletePlan']);

App::get('/admin/users', [AdminController::class, 'users']);
App::get('/admin/bookings', [AdminController::class, 'bookings']);
App::get('/admin/settings', [AdminController::class, 'settings']);
App::post('/admin/users/{id}/toggle', [AdminController::class, 'toggleUserStatus']);
App::post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser']);
App::post('/admin/settings', [AdminController::class, 'saveSettings']);

// Public Booking Engine
App::get('/u/{username}', [PublicBookingController::class, 'showPublicBooking']);
App::get('/u/{username}/slots', [PublicBookingController::class, 'getAvailableSlots']);
App::post('/u/{username}/book', [PublicBookingController::class, 'submitBooking']);
App::get('/booking/confirmation/{id}', [PublicBookingController::class, 'showConfirmation']);

// REST API Endpoints (v1)
App::post('/api/v1/login', [ApiController::class, 'login']);
App::get('/api/v1/dashboard', [ApiController::class, 'getDashboard']);
App::get('/api/v1/profile', [ApiController::class, 'getProfile']);
App::get('/api/v1/availability', [ApiController::class, 'getAvailability']);
App::get('/api/v1/bookings', [ApiController::class, 'getBookings']);
App::get('/api/v1/form-fields', [ApiController::class, 'getFormFields']);
