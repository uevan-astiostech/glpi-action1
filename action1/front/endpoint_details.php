<?php
include 'session_manager.php'; 

// Check if the org_id is set in the session
$org_id = isset($_SESSION['ACTION1_ORG_ID']) ? $_SESSION['ACTION1_ORG_ID'] : null;
$endpoint_id = isset($_GET['endpoint_id']) ? $_GET['endpoint_id'] : null;

// Check if both org_id and endpoint_id are available
if (!$org_id || !$endpoint_id) {
    echo "Organization ID or Endpoint ID is missing.";
    exit;
}

// API access token (assuming it's stored in session)
$access_token = isset($_SESSION['ACTION1_ACCESS_TOKEN']) ? $_SESSION['ACTION1_ACCESS_TOKEN'] : null;

// Check if access token is available
if (!$access_token) {
    echo "Access token is missing. Please log in.";
    exit;
}

// API URL for endpoint details
$api_url = 'https://app.au.action1.com/api/3.0/endpoints/managed/' . htmlspecialchars($org_id) . '/' . htmlspecialchars($endpoint_id);

// Initialize cURL to make the API call for endpoint details
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token
]);

// Execute the API request for endpoint details
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check if the API call was successful
if ($http_code == 200) {
    $endpoint_details = json_decode($response, true);
    // Extract endpoint details
    $name = isset($endpoint_details['name']) ? $endpoint_details['name'] : 'N/A';
    $os = isset($endpoint_details['OS']) ? $endpoint_details['OS'] : 'N/A';
    $last_seen = isset($endpoint_details['last_seen']) ? $endpoint_details['last_seen'] : 'N/A';
    $cpu_name = isset($endpoint_details['CPU_name']) ? $endpoint_details['CPU_name'] : 'N/A';
    $ram = isset($endpoint_details['RAM']) ? $endpoint_details['RAM'] : 'N/A';
    $mac = isset($endpoint_details['MAC']) ? $endpoint_details['MAC'] : 'N/A';

    echo "<h3>Endpoint Details:</h3>";
    echo "<p>Name: $name</p>";
    echo "<p>Operating System: $os</p>";
    echo "<p>Last Seen: $last_seen</p>";
    echo "<p>CPU: $cpu_name</p>";
    echo "<p>RAM: $ram</p>";
    echo "<p>MAC Address: $mac</p>";
} else {
    echo "Failed to fetch endpoint details. HTTP Status Code: " . $http_code;
    exit;
}

echo '<div class="action-buttons">';
echo '<button id="remoteButton" class="button">Remote</button>';
echo '<button id="deployUpdateButton" class="button" disabled>Deploy Update</button>';
echo '<button class="button" disabled>Deploy Software</button>';
echo '<button class="button" disabled>Reboot</button>';
echo '</div>';

// Now, display the table for missing updates

// API URL for missing updates
$updates_api_url = 'https://app.au.action1.com/api/3.0/endpoints/managed/' . htmlspecialchars($org_id) . '/' . htmlspecialchars($endpoint_id) . '/missing-updates';

// Initialize cURL to make the API call for missing updates
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $updates_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token
]);

// Execute the API request for missing updates
$updates_response = curl_exec($ch);
$updates_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check if the API call for missing updates was successful
if ($updates_http_code == 200) {
    // Parse the response
    $missing_updates = json_decode($updates_response, true);
    $updates = isset($missing_updates['items']) ? $missing_updates['items'] : [];

    // Display missing updates in a table format
    echo "<h3>Missing Updates:</h3>";
    echo "<table class='customTable'>
            <thead>
                <tr>
                    <th></th> <!-- Empty header for checkbox -->
                    <th>Name</th>
                    <th>Installed Version</th>
                    <th>Latest Version</th>
                    <th>Release Date</th>
                    <th>Status</th>
                    <th>Update Type</th>
                    <th>Vulnerabilities</th>
                    <th>Security Severity</th>
                </tr>
            </thead>
            <tbody>";

    // Loop through the updates and display each one
    foreach ($updates as $update) {
        $package_id = isset($update['id']) ? $update['id'] : 'N/A';
        $name = isset($update['name']) ? $update['name'] : 'N/A';
        $latest_version = isset($update['versions'][0]['version']) ? $update['versions'][0]['version'] : 'N/A';
        $release_date = isset($update['versions'][0]['release_date']) ? $update['versions'][0]['release_date'] : 'N/A';
        $status = isset($update['versions'][0]['status']) ? $update['versions'][0]['status'] : 'N/A';
        $update_type = isset($update['versions'][0]['update_type']) ? $update['versions'][0]['update_type'] : 'N/A';
        $security_severity = isset($update['versions'][0]['security_severity']) ? $update['versions'][0]['security_severity'] : 'N/A';

        echo "<tr>
                <td><input type='checkbox' name='selected_updates[]' value='$package_id'></td>
                <td>$name</td>
                <td>N/A</td> <!-- Installed version not available in the response -->
                <td>$latest_version</td>
                <td>$release_date</td>
                <td>$status</td>
                <td>$update_type</td>
                <td>Unknown</td> <!-- Vulnerabilities not available in the response -->
                <td>$security_severity</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "Failed to fetch missing updates. HTTP Status Code: " . $updates_http_code;
}

?>

<div class="action-buttons">
    <button id="remoteButton" class="button">Remote</button>
    <button class="button" disabled id="deployUpdateBtn">Deploy Update</button>
    <button class="button" disabled>Reboot</button>
</div>

<script>
// Enable "Deploy Update" button if any checkbox is selected
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_updates[]"]');
    const deployButton = document.getElementById('deployUpdateButton');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const anyChecked = Array.from(checkboxes).some(chk => chk.checked);
            deployButton.disabled = !anyChecked; // Enable if any checkbox is checked
        });
    });

    // When the deploy button is clicked, get the selected checkboxes' values and navigate to another page using GET
    deployButton.addEventListener('click', function() {
        const selectedPackages = Array.from(checkboxes)
                                    .filter(chk => chk.checked)
                                    .map(chk => chk.value);
        const endpointId = "<?php echo htmlspecialchars($endpoint_id); ?>";
        const packageIds = selectedPackages.join(',');

        if (packageIds.length > 0) {
            window.location.href = `action1.php?page=deploy&endpoint_id=${endpointId}&package_ids=${packageIds}`;
        }
    });
});
</script>


<!-- Add CSS for the custom table and buttons -->
<style>
table.customTable {
  width: 100%;
  background-color: #FFFFFF;
  border-collapse: collapse;
  border-width: 2px;
  border-color: #7EA8F8;
  border-style: solid;
  color: #000000;
}

table.customTable td, table.customTable th {
  border-width: 2px;
  border-color: #7EA8F8;
  border-style: solid;
  padding: 5px; /* Changed padding as per your request */
}

table.customTable thead {
  background-color: #7EA8F8; /* Header background color */
}

.action-buttons {
  margin-top: 20px;
}

.action-buttons button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  margin-right: 10px;
  cursor: pointer;
}

.action-buttons button:disabled {
  background-color: #CCCCCC;
  cursor: not-allowed;
}
</style>
