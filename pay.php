<?php
// Start a PHP session to access session variables
session_start();

// Include the configuration file that contains database credentials and Paystack secret key
require 'config.php';

/**
 * STEP 1: REQUEST VALIDATION
 * Ensure this script only processes POST requests and user is logged in
 */
// Check if the request method is not POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request - This endpoint only accepts POST requests");
}

// Verify that the user is logged in by checking for user_id in session
if (!isset($_SESSION['user_id'])) {
    die("User not logged in - Please login to make a payment");
}

/**
 * STEP 2: AMOUNT PROCESSING
 * Get and validate the payment amount from form submission
 */
// Get amount from POST data and convert to integer for security
$amount = (int)$_POST['amount'];

// Validate that amount meets minimum requirement (50 Naira)
if ($amount < 50) {
    die("Minimum funding amount is ₦50 - Please enter a higher amount");
}

// Convert Naira amount to kobo (Paystack uses kobo as base unit: 1 Naira = 100 kobo)
$amount_kobo = $amount * 100;

/**
 * STEP 3: CALLBACK URL SETUP
 * Prepare the URL where Paystack will send payment verification
 */
// Construct callback URL dynamically based on current server details
// Format: http://yourdomain.com/path/to/verify.php
$callback_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php";

/**
 * STEP 4: PAYSTACK REQUEST DATA PREPARATION
 * Prepare the data payload for Paystack API
 */
$fields = [
    // NOTE: Currently using hardcoded email - should be replaced with user's actual email
    'email' => 'altrafi@gmail.com', 
    
    // Amount in kobo (must be integer)
    'amount' => $amount_kobo,
    
    // URL Paystack will call to verify payment
    'callback_url' => $callback_url,
    
    // Additional data to identify this transaction
    // Here we store the user_id for reference when Paystack calls us back
    'metadata' => json_encode(['user_id' => $_SESSION['user_id']])
];

// Convert the array to URL-encoded query string for the API request
$fields_string = http_build_query($fields);

/**
 * STEP 5: INITIALIZE CURL REQUEST TO PAYSTACK
 * Set up and configure the API request
 */
// Initialize cURL session with Paystack's transaction initialization endpoint
$ch = curl_init('https://api.paystack.co/transaction/initialize');

// Set request headers:
// - Authorization with your Paystack secret key from config.php
// - Cache control to prevent caching
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
    "Cache-Control: no-cache"
]);

// Specify this is a POST request
curl_setopt($ch, CURLOPT_POST, true);

// Attach the form data (payment details)
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

// Tell cURL to return the response as a string instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

/**
 * STEP 6: EXECUTE REQUEST AND HANDLE RESPONSE
 */
// Execute the cURL request and store the result
$result = curl_exec($ch);

// Check for cURL errors
if ($result === false) {
    die('Curl error: ' . curl_error($ch));
}

// Close the cURL session to free resources
curl_close($ch);

// Decode the JSON response from Paystack into an associative array
$response = json_decode($result, true);

// Check if Paystack returned an error status
if (!$response['status']) {
    die('API error: ' . $response['message']);
}

/**
 * STEP 7: REDIRECT TO PAYMENT PAGE
 * On success, send user to Paystack's payment page
 */
// The response contains an authorization URL - redirect user there
header('Location: ' . $response['data']['authorization_url']);

// Ensure no further code is executed after redirect
exit();