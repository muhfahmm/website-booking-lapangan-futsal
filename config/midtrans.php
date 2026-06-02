<?php
/**
 * Konfigurasi Midtrans Payment Gateway
 * 
 * Merchant ID: G617610329
 * Environment: Sandbox (untuk development)
 */

// Midtrans Credentials
define('MIDTRANS_MERCHANT_ID', 'G617610329');
define('MIDTRANS_SERVER_KEY', 'Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('MIDTRANS_CLIENT_KEY', 'Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('MIDTRANS_ENVIRONMENT', 'production'); // 'sandbox' or 'production'

// Callback URLs
define('MIDTRANS_SUCCESS_URL', 'http://localhost/project-client-php/website_booking_lapangan_futsal/payment/success.php');
define('MIDTRANS_ERROR_URL', 'http://localhost/project-client-php/website_booking_lapangan_futsal/payment/failed.php');
define('MIDTRANS_NOTIFICATION_URL', 'http://localhost/project-client-php/website_booking_lapangan_futsal/payment/notification.php');

// Company Info
define('COMPANY_NAME', 'FutsalBook');
define('COMPANY_PHONE', '0812-3456-7890');
define('COMPANY_EMAIL', 'admin@futsalbook.com');

// Payment Configuration
define('PAYMENT_CURRENCY', 'IDR');
define('PAYMENT_LANGUAGE', 'id'); // 'id' for Indonesian, 'en' for English

// Enable Debug Mode (false in production)
define('MIDTRANS_DEBUG', true);

?>
