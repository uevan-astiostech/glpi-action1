<?php
echo "<h1>" . __('Endpoints', 'action1') . "</h1>";

// Include session manager to ensure token and session are properly handled
include 'session_manager.php'; 

// Retrieve Action1 access token and organization ID from the session
$access_token = isset($_SESSION['ACTION1_ACCESS_TOKEN']) ? $_SESSION['ACTION1_ACCESS_TOKEN'] : null;
$org_id = isset($_SESSION['ACTION1_ORG_ID']) ? $_SESSION['ACTION1_ORG_ID'] : null;

// Retrieve filters from the form submission
$search = isset($_POST['search']) ? htmlspecialchars($_POST['search']) : '';
$status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : '';
$online_status = isset($_POST['online_status']) ? htmlspecialchars($_POST['online_status']) : '';
$update_status = isset($_POST['update_status']) ? htmlspecialchars($_POST['update_status']) : '';
$vulnerability_status = isset($_POST['vulnerability_status']) ? htmlspecialchars($_POST['vulnerability_status']) : '';
$reboot_required = isset($_POST['reboot_required']) ? htmlspecialchars($_POST['reboot_required']) : '';
$page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;

// Set the 'from' parameter based on the current page
$from = ($page) * 100 + 1;

// API query parameters based on filter inputs
$query_params = [
    'limit' => 100,
    'from' => $from,
    'fields' => '*',
    'search' => $search,
    'status' => $status,
    'online_status' => $online_status,
    'update_status' => $update_status,
    'vulnerability_status' => $vulnerability_status,
    'reboot_required' => $reboot_required
];
$api_query = http_build_query($query_params);

// Check if both values are available
if ($access_token && $org_id) {
    // Define the API URL with the query parameters
    $api_url = 'https://app.au.action1.com/api/3.0/endpoints/managed/' . htmlspecialchars($org_id) . '?' . $api_query;

    //echo $api_url;

    // Initialize cURL to make the API call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token
    ]);

    // Execute the API request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if the response was successful
    if ($http_code == 200) {
        // Parse the JSON response
        $data = json_decode($response, true);
        $endpoints = isset($data['items']) ? $data['items'] : [];

        // Display the filter form
        echo "<form method='post' action=''>
                <div class='filter-container'>
                    <label for='search'>Search:</label>
                    <input type='text' id='search' name='search' value='" . htmlspecialchars($search) . "'>
                    <br />
                    <label for='status'>Status:</label>
                    <select id='status' name='status'>
                        <option value=''>Any</option>
                        <option value='Connected' " . ($status == 'Connected' ? 'selected' : '') . ">Connected</option>
                        <option value='Disconnected' " . ($status == 'Disconnected' ? 'selected' : '') . ">Disconnected</option>
                        <option value='Pending Uninstall' " . ($status == 'Pending Uninstall' ? 'selected' : '') . ">Pending Uninstall</option>
                    </select>
                    <br />
                    <label for='online_status'>Online Status:</label>
                    <select id='online_status' name='online_status'>
                        <option value=''>Any</option>
                        <option value='SUCCESS' " . ($online_status == 'SUCCESS' ? 'selected' : '') . ">SUCCESS</option>
                        <option value='WARNING' " . ($online_status == 'WARNING' ? 'selected' : '') . ">WARNING</option>
                        <option value='ERROR' " . ($online_status == 'ERROR' ? 'selected' : '') . ">ERROR</option>
                    </select>
                    <br />
                    <label for='update_status'>Update Status:</label>
                    <select id='update_status' name='update_status'>
                        <option value=''>Any</option>
                        <option value='SUCCESS' " . ($update_status == 'SUCCESS' ? 'selected' : '') . ">SUCCESS</option>
                        <option value='WARNING' " . ($update_status == 'WARNING' ? 'selected' : '') . ">WARNING</option>
                        <option value='ERROR' " . ($update_status == 'ERROR' ? 'selected' : '') . ">ERROR</option>
                    </select>
                    <br />
                    <label for='vulnerability_status'>Vulnerability Status:</label>
                    <select id='vulnerability_status' name='vulnerability_status'>
                        <option value=''>Any</option>
                        <option value='SUCCESS' " . ($vulnerability_status == 'SUCCESS' ? 'selected' : '') . ">SUCCESS</option>
                        <option value='WARNING' " . ($vulnerability_status == 'WARNING' ? 'selected' : '') . ">WARNING</option>
                        <option value='ERROR' " . ($vulnerability_status == 'ERROR' ? 'selected' : '') . ">ERROR</option>
                    </select>
                    <br />
                    <label for='reboot_required'>Reboot Required:</label>
                    <select id='reboot_required' name='reboot_required'>
                        <option value=''>Any</option>
                        <option value='Yes' " . ($reboot_required == 'Yes' ? 'selected' : '') . ">Yes</option>
                        <option value='No' " . ($reboot_required == 'No' ? 'selected' : '') . ">No</option>
                    </select>
                    <br />
                    <button type='submit' class='search-button'>Search</button>
                </div>
              </form>";

        // Display the pagination links
        echo "<div class='pagination'>";
        echo "<a href='?page=endpoints'>1</a> ";
        echo "<a href='?page=endpoints&pg=2'>2</a> ";
        echo "<a href='?page=endpoints&pg=3'>3</a>";
        echo "</div><br />";

        // Display the data in a table
        echo "<form method='post' action='process_endpoints.php'>"; // Form added for checkbox submission
        echo "<table class='customTable'>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Name</th>
                        <th>Comment</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Reboot Required</th>
                        <th>OS</th>
                        <th>Missing Updates</th>
                        <th>Vulnerabilities</th>
                    </tr>
                </thead>
                <tbody>";

        // Loop through each endpoint and display the details in a table
        foreach ($endpoints as $endpoint) {
            $missing_updates = isset($endpoint['missing_updates']) ? 
                'Critical: ' . htmlspecialchars($endpoint['missing_updates']['critical']) . ' Others: ' . htmlspecialchars($endpoint['missing_updates']['other']) : 'N/A';

            $vulnerabilities = isset($endpoint['vulnerabilities']) ? 
                'Critical: ' . htmlspecialchars($endpoint['vulnerabilities']['critical']) . ' Others: ' . htmlspecialchars($endpoint['vulnerabilities']['other']) : 'N/A';

            echo "<tr class='clickable-row'>
                    <td><input type='checkbox' name='selected_endpoints[]' value='" . htmlspecialchars($endpoint['id']) . "'></td>
                    <td><a href='?page=endpoint_details&endpoint_id=" . htmlspecialchars($endpoint['id']) . "'>" . htmlspecialchars($endpoint['name']) . "</a></td>
                    <td>" . htmlspecialchars($endpoint['comment']) . "</td>
                    <td>" . htmlspecialchars($endpoint['user']) . "</td>
                    <td>" . htmlspecialchars($endpoint['status']) . "</td>
                    <td>" . htmlspecialchars($endpoint['reboot_required']) . "</td>
                    <td>" . htmlspecialchars($endpoint['OS']) . "</td>
                    <td>" . $missing_updates . "</td>
                    <td>" . $vulnerabilities . "</td>
                  </tr>";
        }

        echo "</tbody></table>";
        // echo "<input type='submit' value='Submit Selected Endpoints'>";
        echo "</form>";

        // Display pagination again after the table
        echo "<br /><div class='pagination'>";
        echo "<a href='?page=endpoints'>1</a> ";
        echo "<a href='?page=endpoints&pg=2'>2</a> ";
        echo "<a href='?page=endpoints&pg=3'>3</a>";
        echo "</div>";

    } else {
        echo "Error: Failed to retrieve data from API. HTTP Status: $http_code";
    }

} else {
    echo "Error: Access token or Organization ID not found in the session.";
}
?>

<!-- Add CSS for the custom table, filter, and buttons -->
<style>
.pagination a {
    margin: 0 5px;
    padding: 5px 10px;
    text-decoration: none;
    border: 1px solid #ccc;
    color: #333;
}

.pagination a:hover {
    background-color: #f4f4f4;
}

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
  padding: 10px;
}

table.customTable thead {
  background-color: #7EA8F8;
}

.filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.filter-container label {
  margin-right: 5px;
}

.filter-container input, .filter-container select, .filter-container button {
  padding: 5px;
}

.search-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  cursor: pointer;
}

.search-button:hover {
  background-color: #45a049;
}

.action-buttons {
  margin-bottom: 20px;
}

.action-buttons button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  margin-right: 10px;
  cursor: pointer;
}

.action-buttons button:hover {
  background-color: #0056b3;
}

.action-buttons button:disabled {
  background-color: #D3D3D3;
}

</style>
