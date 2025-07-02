<?php
/**
 * Configuration file for Razorpay integration
 * 
 * Handles environment variables, error reporting, and CORS headers
 */

// Error reporting disabled
error_reporting(0);
ini_set('display_errors', '0'); 
ini_set('log_errors', '0');
// No longer logging errors to file

// Load environment variables
$dotenv_path = __DIR__ . '/../.env';
if (file_exists($dotenv_path)) {
    $env_lines = file($dotenv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

// Razorpay API keys (use environment variables or set directly for testing)
define('RAZORPAY_KEY_ID', getenv('NEXT_PUBLIC_RAZORPAY_KEY_ID') ?: 'rzp_live_1j9gDh1eamAJx7');
define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: 'R8MBxfH9XWjvOLS4SZeAY2hV');

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400'); // 24 hours

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Common functions

/**
 * Send JSON response
 * 
 * @param array $data The data to send
 * @param int $status_code HTTP status code
 * @return void
 */
function sendJsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Log message function (disabled)
 * 
 * @param string $message Message to log (ignored)
 * @param string $level Log level (ignored)
 * @return void
 */
function logMessage($message, $level = 'INFO') {
    // Logging disabled
    return;
}

/**
 * Generate a unique order ID
 * 
 * @return string
 */
function generateOrderId() {
    $timestamp = time();
    $random = bin2hex(random_bytes(8));
    return "order_{$timestamp}{$random}";
} 