<?php

use App\Core\Auth;

// Home/Landing Page
$router->get('', function() {
    $controller = new App\Controllers\HomeController();
    $controller->index();
});

$router->get('home', function() {
    $controller = new App\Controllers\HomeController();
    $controller->index();
});

// ==================== Authentication Routes ====================
$router->get('register', 'AuthController@showRegister');
$router->post('register', 'AuthController@register');

$router->get('login', 'AuthController@showLogin');
$router->post('login', 'AuthController@login');

$router->get('logout', 'AuthController@logout');

// ==================== Doctor Routes ====================
// Public routes - anyone can view
$router->get('doctors', 'DoctorController@index');
$router->get('doctors/search', 'DoctorController@search');
$router->get('doctors/{id}', 'DoctorController@show');

// Doctor-only routes
$router->get('doctor/dashboard', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->dashboard();
});

$router->get('doctor/profile', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->profile();
});

$router->post('doctor/profile', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->updateProfile();
});

$router->get('doctor/availability', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->availability();
});

$router->post('doctor/availability', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->createSlots();
});

$router->post('doctor/unavailability', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->setUnavailability();
});

$router->get('doctor/appointments', function() {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\DoctorController();
    $controller->appointments();
});

// ==================== Appointment Routes ====================
// Patient routes
$router->get('appointments', function() {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->index();
});

$router->get('appointments/create/{doctor_id}', function($doctor_id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->create($doctor_id);
});

$router->post('appointments', function() {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->store();
});

$router->get('appointments/{id}', function($id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->show($id);
});

$router->post('appointments/{id}/cancel', function($id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->cancel($id);
});

$router->post('appointments/{id}/confirm', function($id) {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\AppointmentController();
    $controller->confirm($id);
});

$router->post('appointments/{id}/complete', function($id) {
    Auth::requireRole('doctor');
    $controller = new App\Controllers\AppointmentController();
    $controller->complete($id);
});

// Payment routes
$router->get('appointments/{id}/payment', function($id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->payment($id);
});

$router->post('appointments/{id}/payment', function($id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->processPayment($id);
});

// Review routes
$router->get('appointments/{id}/review', function($id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->reviewForm($id);
});

$router->post('appointments/{id}/review', function($id) {
    Auth::requireAuth();
    $controller = new App\Controllers\AppointmentController();
    $controller->submitReview($id);
});

// ==================== Admin Routes ====================
$router->get('admin/dashboard', function() {
    Auth::requireRole('admin');
    $controller = new App\Controllers\AdminController();
    $controller->dashboard();
});

$router->get('admin/users', function() {
    Auth::requireRole('admin');
    $controller = new App\Controllers\AdminController();
    $controller->users();
});

$router->get('admin/doctors', function() {
    Auth::requireRole('admin');
    $controller = new App\Controllers\AdminController();
    $controller->doctors();
});

$router->get('admin/appointments', function() {
    Auth::requireRole('admin');
    $controller = new App\Controllers\AdminController();
    $controller->appointments();
});

// ==================== 404 Handler ====================
$router->setNotFound(function() {
    http_response_code(404);
    echo "404 - Page Not Found";
});

?>