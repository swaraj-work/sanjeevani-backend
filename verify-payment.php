<?php
/**
 * Payment Verification API for Razorpay Integration
 * 
 * This endpoint verifies payment signatures and processes successful payments
 */

require_once __DIR__ . '/config.php';

// Add CORS headers explicitly here too
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400'); // 24 hours

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

// Read request body
$request_body = file_get_contents('php://input');
$request_data = json_decode($request_body, true);

// Log incoming request (remove sensitive data)
$log_data = $request_data;
if (isset($log_data['razorpay_signature'])) {
    $log_data['razorpay_signature'] = substr($log_data['razorpay_signature'], 0, 10) . '...';
}
logMessage("Payment Verification Request: " . json_encode($log_data));

// Validate request data
if (!isset($request_data['razorpay_payment_id']) || 
    !isset($request_data['razorpay_order_id']) || 
    !isset($request_data['razorpay_signature'])) {
    sendJsonResponse(['success' => false, 'error' => 'Missing required payment details'], 400);
}

$razorpay_payment_id = $request_data['razorpay_payment_id'];
$razorpay_order_id = $request_data['razorpay_order_id'];
$razorpay_signature = $request_data['razorpay_signature'];
$form_data = isset($request_data['formData']) ? $request_data['formData'] : [];

// Verify signature
try {
    // Generate signature hash
    $text = $razorpay_order_id . '|' . $razorpay_payment_id;
    $expected_signature = hash_hmac('sha256', $text, RAZORPAY_KEY_SECRET);
    
    // Compare signatures
    if (hash_equals($expected_signature, $razorpay_signature)) {
        // Signature verified - payment is successful
        // Fetch payment details from Razorpay API for additional verification
        $api_url = 'https://api.razorpay.com/v1/payments/' . $razorpay_payment_id;
        $auth = base64_encode(RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $auth,
                'Content-Type: application/json'
            ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        logMessage("Payment API Response Code: " . $status_code);
        logMessage("Payment API Response: " . $response);
        
        // Process payment based on signature validation
        $payment_data = [
            'payment_id' => $razorpay_payment_id,
            'order_id' => $razorpay_order_id,
            'name' => $form_data['name'] ?? '',
            'email' => $form_data['email'] ?? '',
            'phone' => $form_data['phone'] ?? '',
            'accommodation' => $form_data['accommodation'] ?? 'no',
            'amount' => 100, // ₹1 in paise for testing
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'completed'
        ];
        
        // Log successful registration
        logMessage("Registration Successful: " . json_encode($payment_data));
        
        // Send successful response
        sendJsonResponse([
            'success' => true,
            'message' => 'Payment verified successfully',
            'order_id' => $razorpay_order_id,
            'payment_id' => $razorpay_payment_id,
            'data' => $payment_data
        ]);
    } else {
        // Signature verification failed
        logMessage("Signature verification failed for payment: " . $razorpay_payment_id, "ERROR");
        throw new Exception("Invalid payment signature");
    }
} catch (Exception $e) {
    logMessage("Payment Verification Error: " . $e->getMessage(), "ERROR");
    sendJsonResponse([
        'success' => false,
        'error' => 'Payment verification failed: ' . $e->getMessage()
    ], 400);
}
