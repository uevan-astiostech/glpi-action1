<?php
include 'session_manager.php';

$org_id = isset($_SESSION['ACTION1_ORG_ID']) ? $_SESSION['ACTION1_ORG_ID'] : null;
$access_token = isset($_SESSION['ACTION1_ACCESS_TOKEN']) ? $_SESSION['ACTION1_ACCESS_TOKEN'] : null;

// Get the raw POST data and decode it
$data = json_decode(file_get_contents('php://input'), true);

// Prepare the API URL for deployment
$deploy_api_url = 'https://app.au.action1.com/api/3.0/automations/schedules/' . $org_id;

// Initialize cURL to send the POST request to Action1 API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $deploy_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
]);

// Execute the request and get the response
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    echo json_encode(['success' => true, 'message' => 'Deployment scheduled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to schedule deployment. HTTP Status Code: ' . $http_code]);
}
?>
