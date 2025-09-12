<?php
// Include session manager for handling token and session management
include('session_manager.php');

echo "<h1>" . __('Dashboard', 'action1') . "</h1>";

// Check for stored access token and validate expiry
if (!isset($_SESSION['ACTION1_ACCESS_TOKEN']) || time() >= $_SESSION['ACTION1_TOKEN_EXPIRY']) {
    $access_token = getAccessToken($client_id, $client_secret, $api_url);
    
    if ($access_token) {
        $_SESSION['ACTION1_ACCESS_TOKEN'] = $access_token;
        $_SESSION['ACTION1_TOKEN_EXPIRY'] = time() + (59 * 60);  // 59 minutes
    } else {
        echo "Error: Unable to retrieve access token.";
        exit;
    }
} else {
    $access_token = $_SESSION['ACTION1_ACCESS_TOKEN'];
}

// Fetch the organization data using the valid access token
$organization_data = fetchOrganizationData($access_token, $api_url);

if ($organization_data && isset($organization_data['items'][0])) {
    $organization = $organization_data['items'][0];
    $_SESSION['ACTION1_ORG_ID'] = $organization['id'];
    echo "<h2>" . htmlspecialchars($organization['name']) . "</h2>";
    echo "<h3>ID : " . htmlspecialchars($organization['id']) . "</h3>";
} else {
    echo "Error: Unable to retrieve organization data.";
}

// ==================== New Subscription API Call ====================

$subscription_api_url = 'https://app.au.action1.com/api/3.0/subscription/enterprise';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $subscription_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
$subscription_response = curl_exec($ch);
$subscription_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($subscription_http_code == 200) {
    $subscription_data = json_decode($subscription_response, true);
} else {
    echo "Error: Failed to retrieve subscription data. HTTP Status: $subscription_http_code";
}

// ==================== Vulnerabilities API Call ====================

$vulnerabilities_api_url = 'https://app.au.action1.com/api/3.0/vulnerabilities/'.htmlspecialchars($organization['id']).'?limit=2';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $vulnerabilities_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
$vulnerabilities_response = curl_exec($ch);
$vulnerabilities_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($vulnerabilities_http_code == 200) {
    $vulnerabilities_data = json_decode($vulnerabilities_response, true);
} else {
    echo "Error: Failed to retrieve vulnerabilities data. HTTP Status: $vulnerabilities_http_code";
}

// Display data in a 3-column grid
echo '<div class="dashboard-grid">';

if ($subscription_data) {
    echo '<div><span class="label">Type of Subscription:</span><span class="value">' . ucwords(htmlspecialchars($subscription_data['license_type'])) . '</span></div>';
    echo '<div><span class="label">License Status:</span><span class="value">' . ucwords(htmlspecialchars($subscription_data['license_status'])) . '</span></div>';
    echo '<div><span class="label">License Expiry:</span><span class="value">' . htmlspecialchars($subscription_data['license_expires']) . '</span></div>';
    echo '<div><span class="label">Licensed Endpoint Count:</span><span class="value">' . htmlspecialchars($subscription_data['licensed_endpoint_count']) . '</span></div>';
    echo '<div><span class="label">Actual Endpoint Count:</span><span class="value">' . htmlspecialchars($subscription_data['actual_endpoint_count']) . '</span></div>';
}

if ($vulnerabilities_data && isset($vulnerabilities_data['total_items'])) {
    echo '<div><span class="label">Total Vulnerabilities:</span><span class="value">' . htmlspecialchars($vulnerabilities_data['total_items']) . '</span></div>';
}

echo '</div>';
?>

<!-- Add CSS for grid layout -->
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }
    .dashboard-grid div {
        padding: 20px;
        background-color: #f9f9f9;
        -webkit-border-radius: 20px;
        -moz-border-radius: 20px;
        border-radius: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    .dashboard-grid .label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    .dashboard-grid .value {
        font-size: 16px;
        color: #333;
    }
</style>
