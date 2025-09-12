<?php
echo "<h1>" . __('Software Management', 'action1') . "</h1>";

// Include session manager to ensure token and session are properly handled
include 'session_manager.php';

// Retrieve Action1 access token and organization ID from the session
$access_token = isset($_SESSION['ACTION1_ACCESS_TOKEN']) ? $_SESSION['ACTION1_ACCESS_TOKEN'] : null;
$org_id = isset($_SESSION['ACTION1_ORG_ID']) ? $_SESSION['ACTION1_ORG_ID'] : null;

// Check if both values are available
if ($access_token && $org_id) {

    // Define the API URL (replace org_id in URL)
    $api_url = 'https://app.au.action1.com/api/3.0/installed-software/' . htmlspecialchars($org_id) . '/data?limit=200';

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

        // Extract the list of software from the 'items' array
        $software_list = isset($data['items']) ? $data['items'] : [];

        // Display the search form
        echo '<form id="searchForm">
                <input type="text" id="searchBox" placeholder="Search Software">
                <button type="button" id="searchButton">Search</button>
              </form>';

        // Deploy Update button
        echo '<button id="deployUpdateButton" disabled>Deploy Update</button>';

        // Display the data in a table
        echo "<h2>Installed Software</h2>";
        echo "<table class='customTable' cellspacing='0' cellpadding='10'>
                <thead>
                    <tr>
                        <th></th> <!-- Blank header for checkboxes -->
                        <th>Name</th>
                        <th>Vendor</th>
                        <th>Version</th>
                        <th>Newest Update</th>
                        <th>Install Type</th>
                        <th>Update Status</th>
                        <th>Platform</th>
                        <th>Endpoints</th>
                    </tr>
                </thead>
                <tbody>";

        // Loop through each software and display the details in a table
        foreach ($software_list as $software) {
            $fields = $software['fields'];

            // Extract the latest version details if available
            $newest_update = isset($fields['_Missing_Updates'][0]['version']) ? $fields['_Missing_Updates'][0]['version'] : 'N/A';

            echo "<tr class='clickable-row' data-software='" . htmlspecialchars(json_encode($software)) . "'>
                    <td><input type='checkbox'></td> <!-- Checkbox for each row -->
                    <td>" . htmlspecialchars($fields['Name']) . "</td>
                    <td>" . htmlspecialchars($fields['Vendor']) . "</td>
                    <td>" . htmlspecialchars($fields['Version']) . "</td>
                    <td>" . htmlspecialchars($newest_update) . "</td>
                    <td>" . htmlspecialchars($fields['Install Type']) . "</td>
                    <td>" . htmlspecialchars($fields['Update Status']) . "</td>
                    <td>" . htmlspecialchars($fields['Platform']) . "</td>
                    <td>" . htmlspecialchars($fields['Endpoints']) . "</td>
                  </tr>";
        }

        echo "</tbody></table>";

        // Create a modal to display detailed information when clicking a row
        echo '<div id="modal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; background-color:#fff; border:1px solid #ccc;">
                <h3>Software Details</h3>
                <div id="modal-content"></div>
                <button onclick="closeModal()">Close</button>
              </div>';

    } else {
        echo "Error: Failed to retrieve data from API. HTTP Status: $http_code";
    }

} else {
    echo "Error: Access token or Organization ID not found in the session.";
}
?>

<script>
// Function to display modal with detailed software information in text format
function showDetails(data) {
    var modal = document.getElementById('modal');
    var modalContent = document.getElementById('modal-content');

    // Format the software details in a user-friendly text format
    var details = 
        '<strong>Name:</strong> ' + data.fields.Name + '<br>' +
        '<strong>Vendor:</strong> ' + data.fields.Vendor + '<br>' +
        '<strong>Version:</strong> ' + data.fields.Version + '<br>' +
        '<strong>Newest Update:</strong> ' + (data.fields._Missing_Updates[0] ? data.fields._Missing_Updates[0].version : 'N/A') + '<br>' +
        '<strong>Install Type:</strong> ' + data.fields['Install Type'] + '<br>' +
        '<strong>Update Status:</strong> ' + data.fields['Update Status'] + '<br>' +
        '<strong>Platform:</strong> ' + data.fields['Platform'] + '<br>' +
        '<strong>Endpoints:</strong> ' + data.fields['Endpoints'];

    modalContent.innerHTML = details;  // Populate modal content with formatted details
    modal.style.display = 'block';  // Show the modal
}

// Function to close the modal
function closeModal() {
    var modal = document.getElementById('modal');
    modal.style.display = 'none';
}

// Attach click event to each row to show the details in a modal
document.querySelectorAll('.clickable-row').forEach(function(row) {
    row.addEventListener('click', function() {
        var softwareData = JSON.parse(this.getAttribute('data-software'));
        showDetails(softwareData);
    });
});
</script>


<!-- Add CSS for the custom table -->
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
  padding: 5px;
}

table.customTable thead {
  background-color: #7EA8F8;
}

button#deployUpdateButton {
  margin-top: 10px;
  background-color: #7EA8F8;
  color: white;
  border: none;
  padding: 8px 16px;
  cursor: pointer;
}

button#deployUpdateButton:disabled {
  background-color: #CCCCCC;
  cursor: not-allowed;
}
</style>
