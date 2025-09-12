<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Constants for API credentials
$client_id = 'api-key-440c70cc-6ef3-4768-abf8-c31092d156bcf93e64f8-50b1-708a-f965-c629f72c8455@action1.com';
$client_secret = '689095c4a812f096a12705c7bd60d1a3';
$api_url = 'https://app.au.action1.com/api/3.0';

//$_SESSION['ACTION1_ACCESS_TOKEN'] = "eyJraWQiOiJMU2hRRkJENkNWS1phaHBHU05tYStBaks0MHBvVEl2ZDdlc3AydkpscHFVPSIsImFsZyI6IlJTMjU2In0.eyJjdXN0b206Y3JlYXRlZF9hdCI6IjE3NDA2MzI0MTUiLCJzdWIiOiJhOTNlMDRlOC0yMGMxLTcwZDYtNThlMC0wYTY1YWZlZGZlOTUiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5hcC1zb3V0aGVhc3QtMi5hbWF6b25hd3MuY29tXC9hcC1zb3V0aGVhc3QtMl9kV0Y5TUQyOFUiLCJwaG9uZV9udW1iZXJfdmVyaWZpZWQiOmZhbHNlLCJjb2duaXRvOnVzZXJuYW1lIjoiYTkzZTA0ZTgtMjBjMS03MGQ2LTU4ZTAtMGE2NWFmZWRmZTk1IiwiZ2l2ZW5fbmFtZSI6IklKTl9HTFBJX0FQSSIsImF1ZCI6IjJvM3V1YWZvZTM0a2oyZ2xxMmlnbDVubjEzIiwiZXZlbnRfaWQiOiJmMDQxNjdlYi0zY2RjLTQ3ZWItYjUyZi1jNTZmMDcyNjA2MmMiLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTc0MDYzMjQ1MCwibmFtZSI6IjQ0MGM3MGNjLTZlZjMtNDc2OC1hYmY4LWMzMTA5MmQxNTZiYyIsInBob25lX251bWJlciI6IisxMzQ2NDQ0ODUzMCIsImV4cCI6MTc0MDYzNjA1MCwiaWF0IjoxNzQwNjMyNDUwLCJmYW1pbHlfbmFtZSI6IkFQSSIsImVtYWlsIjoiYXBpLWtleS00NDBjNzBjYy02ZWYzLTQ3NjgtYWJmOC1jMzEwOTJkMTU2YmNhOTNlMDRlOC0yMGMxLTcwZDYtNThlMC0wYTY1YWZlZGZlOTVAYWN0aW9uMS5jb20ifQ.TpA-Zni-WKfHq4yxi_A5rPwXaoOBWob4ZxwhCqYnisE556nYXTU9naEA5-SpuJH2jy5q8TQjbavDo0w_Jb8vYbjxsZ2lbm_sigKAB2OS9Un04DQ1POZUfxZjZToivMu73C1T5ECg14VGzVPfWaREdrhzdbVwCepSXdOhUkWoL3sw7KRc0pjWyJEu3xf9pZE2QRg_GxEKRWdDqGAr0DCmCcI5bBDVUk4Jl1ZYzDDJjl9LQx_rbZ6m1rjNn6lnotdQCKZxjsvztIRUXOEv1LJSpyTwki0v8qP2OjD5aG8gnVusBXkQeVsK9ORxZei2x1m-v2yLpQTMcIDONRuvF0zp5Q";
$_SESSION['ACTION1_TOKEN_EXPIRY'] = 3600;

// Function to get the OAuth2 token
function getAccessToken($client_id, $client_secret, $api_url) {
    $token_url = $api_url . '/oauth2/token';

    // Prepare the POST data
    $post_fields = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'client_credentials'
    ];

    // Use cURL to send the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Execute the request and capture the response
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for success and decode the response
    if ($http_code == 200) {
        $json = json_decode($response, true);
        return [
            'access_token' => $json['access_token'] ?? null,
            'expires_in' => $json['expires_in'] ?? 3600  // Get token expiration time
        ];
    } else {
        return null;
    }
}

// Function to refresh and store the token in session
function refreshTokenIfNeeded() {
    global $client_id, $client_secret, $api_url;

    // Check if the session token is near expiry or missing
    if (!isset($_SESSION['ACTION1_ACCESS_TOKEN']) || (isset($_SESSION['ACTION1_TOKEN_EXPIRY']) && time() >= $_SESSION['ACTION1_TOKEN_EXPIRY'] - 60)) {
        // Regenerate a new token
        $tokenData = getAccessToken($client_id, $client_secret, $api_url);
        if ($tokenData) {
            $_SESSION['ACTION1_ACCESS_TOKEN'] = $tokenData['access_token'];
            $_SESSION['ACTION1_TOKEN_EXPIRY'] = time() + $tokenData['expires_in'];
        } else {
            // Handle error if token generation failed
            echo "Error: Unable to refresh the access token.";
            session_destroy(); // Destroy the session if we can't get a new token
            exit();
        }
    }
}

// Function to kill the session after the token expires
function killSessionAfter59Minutes() {
    // Check if the session has started and if it's older than 59 minutes
    if (isset($_SESSION['ACTION1_TOKEN_EXPIRY']) && time() >= $_SESSION['ACTION1_TOKEN_EXPIRY']) {
        session_destroy(); // Kill the session
        header("Location: login.php"); // Redirect to login page or another appropriate action
        exit();
    }
}

// Ensure session and token are valid on every page load
refreshTokenIfNeeded();
killSessionAfter59Minutes();

// Function to fetch organization data (using the valid token from session)
function fetchOrganizationData() {
    global $api_url;

    // Check if access token is available
    if (isset($_SESSION['ACTION1_ACCESS_TOKEN'])) {
        $access_token = $_SESSION['ACTION1_ACCESS_TOKEN'];
        $org_url = $api_url . '/organizations?admin=Yes';

        // Use cURL to send the request with Bearer token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $org_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token
        ]);

        // Execute the request and capture the response
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check for success and decode the response
        if ($http_code == 200) {
            return json_decode($response, true);  // Return the JSON response as an associative array
        } else {
            return null;  // Return null if the request failed
        }
    } else {
        echo "Error: Access token is missing.";
        return null;
    }
}
?>
