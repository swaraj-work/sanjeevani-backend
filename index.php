<?php

/**
 * Sanjeevani API
 * 
 * Main entry point for the API with documentation
 */

require_once __DIR__ . '/config.php';

// Return API documentation for GET requests
$api_version = '1.0.0';
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
$server_port = $_SERVER['SERVER_PORT'] ?? '8000';
$base_url = "http://$server_name:$server_port";

// Create API documentation
$docs = [
    'api' => 'Sanjeevani API',
    'version' => $api_version,
    'endpoints' => [
        [
            'path' => '/api/create-order.php',
            'method' => 'POST',
            'description' => 'Create a new Razorpay order',
            'request' => [
                'formData' => [
                    'name' => 'Full name of the participant',
                    'email' => 'Email address',
                    'phone' => 'Phone number',
                    'accommodation' => 'Accommodation preference (yes/no)'
                ]
            ],
            'response' => [
                'orderId' => 'Razorpay Order ID',
                'amount' => 'Amount in paise (e.g., 99900 for ₹999)',
                'currency' => 'Currency code (e.g., INR)',
                'receipt' => 'Receipt ID',
                'status' => 'Order status'
            ]
        ],
        [
            'path' => '/api/verify-payment.php',
            'method' => 'POST',
            'description' => 'Verify payment after completion',
            'request' => [
                'razorpay_payment_id' => 'Payment ID from Razorpay',
                'razorpay_order_id' => 'Order ID from Razorpay',
                'razorpay_signature' => 'Signature from Razorpay',
                'formData' => 'Optional form data from registration'
            ],
            'response' => [
                'success' => 'Boolean indicating success',
                'message' => 'Status message',
                'order_id' => 'Order ID',
                'payment_id' => 'Payment ID'
            ]
        ]
    ],
    'status' => 'API is operational'
];

// Return API documentation
header('Content-Type: application/json');
echo json_encode($docs, JSON_PRETTY_PRINT);
exit; 