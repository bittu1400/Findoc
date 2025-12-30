<?php

return [
    // Application Name
    'name' => $_ENV['APP_NAME'] ?? 'FINDOC',

    // Application Environment
    'env' => $_ENV['APP_ENV'] ?? 'production',

    // Debug Mode
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),

    // Application URL
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',

    // Timezone
    'timezone' => $_ENV['TIMEZONE'] ?? 'UTC',

    // Session Configuration
    'session' => [
        'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 120, // minutes
        'secure' => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'http_only' => true,
        'same_site' => 'Lax'
    ],

    // File Upload Configuration
    'upload' => [
        'max_size' => $_ENV['MAX_UPLOAD_SIZE'] ?? 5242880, // 5MB in bytes
        'allowed_images' => explode(',', $_ENV['ALLOWED_IMAGE_TYPES'] ?? 'jpg,jpeg,png,gif'),
        'upload_path' => __DIR__ . '/../storage/uploads/'
    ],

    // Pagination
    'pagination' => [
        'per_page' => 12,
        'doctors_per_page' => 12,
        'appointments_per_page' => 10
    ],

    // Date and Time Formats
    'date_format' => 'Y-m-d',
    'time_format' => 'H:i',
    'datetime_format' => 'Y-m-d H:i:s',
    'display_date_format' => 'M d, Y',
    'display_time_format' => 'h:i A',

    // Payment Gateway
    'payment' => [
        'stripe' => [
            'public_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? '',
            'secret_key' => $_ENV['STRIPE_SECRET_KEY'] ?? '',
            'webhook_secret' => $_ENV['STRIPE_WEBHOOK_SECRET'] ?? ''
        ],
        'currency' => 'USD',
        'currency_symbol' => '$'
    ],

    // Email Configuration
    'mail' => [
        'driver' => $_ENV['MAIL_DRIVER'] ?? 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.mailtrap.io',
        'port' => $_ENV['MAIL_PORT'] ?? 2525,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@findoc.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'FINDOC'
        ]
    ],

    // SMS Configuration
    'sms' => [
        'twilio' => [
            'sid' => $_ENV['TWILIO_SID'] ?? '',
            'token' => $_ENV['TWILIO_TOKEN'] ?? '',
            'from' => $_ENV['TWILIO_FROM'] ?? ''
        ]
    ],

    // Application Features
    'features' => [
        'registration_enabled' => true,
        'doctor_registration_enabled' => true,
        'patient_registration_enabled' => true,
        'reviews_enabled' => true,
        'messaging_enabled' => false, // Not yet implemented
        'notifications_enabled' => false // Not yet implemented
    ],

    // Booking Configuration
    'booking' => [
        'min_advance_hours' => 2, // Minimum hours in advance to book
        'max_advance_days' => 30, // Maximum days in advance to book
        'cancellation_hours' => 24, // Hours before appointment to allow cancellation
        'slot_duration' => 30 // Default slot duration in minutes
    ],

    // Default Images
    'defaults' => [
        'doctor_avatar' => '/assets/images/default-doctor.png',
        'patient_avatar' => '/assets/images/default-patient.png'
    ],

    // Roles
    'roles' => [
        'admin' => 'admin',
        'doctor' => 'doctor',
        'patient' => 'patient'
    ],

    // Appointment Statuses
    'appointment_statuses' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled'
    ],

    // Payment Statuses
    'payment_statuses' => [
        'initiated' => 'Initiated',
        'success' => 'Success',
        'failed' => 'Failed',
        'refunded' => 'Refunded'
    ],

    // Rating Stars
    'rating' => [
        'min' => 1,
        'max' => 5
    ]
];