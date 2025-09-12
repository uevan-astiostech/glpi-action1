<?php
include 'session_manager.php';

$org_id = isset($_SESSION['ACTION1_ORG_ID']) ? $_SESSION['ACTION1_ORG_ID'] : null;
$access_token = isset($_SESSION['ACTION1_ACCESS_TOKEN']) ? $_SESSION['ACTION1_ACCESS_TOKEN'] : null;

// Get the endpoint ID and package IDs from the GET parameters
$endpoint_id = isset($_GET['endpoint_id']) ? htmlspecialchars($_GET['endpoint_id']) : null;
$package_ids = isset($_GET['package_ids']) ? explode(',', htmlspecialchars($_GET['package_ids'])) : [];
$deploy_now = isset($_GET['deploy_now']) ? $_GET['deploy_now'] : null;
$scheduled_time = isset($_GET['scheduled_time']) ? $_GET['scheduled_time'] : null;

echo "<h3>Update Deployment</h3>";

// Check if the necessary data is available
if (!$endpoint_id || empty($package_ids)) {
    die("Invalid request: Endpoint ID and package IDs are required.");
}

// If 'deploy_now' is set (submitted form)
if (isset($deploy_now)) {

    //echo "form submitted";
    // Fetch missing updates from Action1 API
    $updates_api_url = 'https://app.au.action1.com/api/3.0/endpoints/managed/' . $org_id . '/' . $endpoint_id . '/missing-updates';

    //echo $updates_api_url."<br/>";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $updates_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token
    ]);
    $updates_response = curl_exec($ch);
    $updates_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($updates_http_code != 200) {
        die("Failed to fetch missing updates. HTTP Status Code: " . $updates_http_code);
    }

    $missing_updates = json_decode($updates_response, true);
    $updates = isset($missing_updates['items']) ? $missing_updates['items'] : [];

    // Prepare package deployment details
    $packages_to_deploy = '';
    $software_name = '';
    foreach ($updates as $update) {
        if (in_array($update['id'], $package_ids)) {
            $version = isset($update['versions'][0]['version']) ? $update['versions'][0]['version'] : null;
            if ($version) {
                $packages_to_deploy = $version;
                $software_name = $update['name'];
            }
        }
    }

    if (empty($packages_to_deploy)) {
        die("No matching packages found for deployment.");
    }

    // Determine deployment time: now or scheduled
    if ($deploy_now === 'now') {
        //$utc_time = gmdate("H-i-s", strtotime(' -7 hours 58 minutes'));
        $utc_time = gmdate("H-i-s", strtotime($scheduled_time . ' +2 minutes'));
        //$utc_date = gmdate("Y-m-d", strtotime(' -7 hours 58 minutes'));
        $utc_date = gmdate("Y-m-d", strtotime($scheduled_time . ' +2 minutes'));
        $settings = 'ENABLED ONCE AT:' . $utc_time . ' DATE:' . $utc_date;
    } else {
        $utc_time = gmdate("H-i-s", strtotime($scheduled_time . ' -8 hours'));
        $utc_date = gmdate("Y-m-d", strtotime($scheduled_time . ' -8 hours'));
        //$settings = 'ENABLED ONCE AT:' . $utc_time . ' DATE:' . $utc_date;
        $settings = 'ENABLED ONCE AT:' . $utc_time . ' DATE:' . $utc_date;
        //$settings = 'ENABLED WEEKLY:Mon,Tue,Wed,Thu,Fri AT:' . $utc_time;
    }

    // echo $settings."<br/>";
    // echo $endpoint_id."<br/>";
    // echo $software_name."<br/>";
    // echo $package_ids[0]."<br/>";
    // echo $packages_to_deploy."<br/>";

    $deployment_json = '{
        "name": "Deploy Software: '. $software_name .' '.$packages_to_deploy . '",
        "endpoints": [
          {
            "id": "' . $endpoint_id .'",
            "type": "Endpoint"
          }
        ],
        "actions": [
          {
            "name": "Deploy Update",
            "template_id": "deploy_update",
            "params": {
              "display_summary": "' . $software_name . ' ('.$packages_to_deploy . ')",
              "packages": [
                {
                  "'.$package_ids[0].'": "'.$packages_to_deploy.'"
                }
              ],
              "reboot_options": {
                "auto_reboot": "no"
              }
            }
          }
        ],
        "settings": "' . $settings . '",
        "retry_minutes": "240"
      }';

    // Construct the deployment JSON
    // $deployment_json = json_encode([
    //     'name' => 'Deploy specified update: ' . $software_name,
    //     'endpoints' => [
    //         [
    //             'id' => $endpoint_id,
    //             'type' => 'Endpoint'
    //         ]
    //     ],
    //     'actions' => [
    //         [
    //             'name' => 'Deploy Update',
    //             'template_id' => 'deploy_update',
    //             'params' => [
    //                 'scope' => 'Specified',
    //                 'display_summary' => 'Specified: ' . $software_name . " (".$packages_to_deploy . ")",
    //                 'reboot_options' => [
    //                     'auto_reboot' => 'no'
    //                 ],
    //                 'packages' => [
    //                     $package_ids[0] => $packages_to_deploy
    //                 ]
    //             ]
    //         ]
    //     ],
    //     'settings' => $settings,
    //     'retry_minutes' => '10'
    // ]);

    //echo "Array";
    //var_dump($deployment_json);

    // API URL for scheduling the deployment
    $deploy_api_url = 'https://app.au.action1.com/api/3.0/automations/schedules/' . $org_id;

    // Send the deployment request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $deploy_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $deployment_json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ]);
    $deploy_response = curl_exec($ch);
    $deploy_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check if deployment is successful
    if ($deploy_http_code == 200) {
        //echo $deploy_response;
        echo "Deploy Successfull";
        header("Location: action1.php?page=endpoint_details&endpoint_id=" . $endpoint_id);
        exit;
    } else {
        echo "Failed to schedule the deployment. HTTP Status Code: " . $deploy_http_code;
    }
}
?>

<!-- Form for Deployment -->
<form id="deploymentForm" method="GET">
    <input type="hidden" name="page" value="deploy">
    <input type="hidden" name="endpoint_id" value="<?php echo $endpoint_id; ?>">
    <input type="hidden" name="package_ids" value="<?php echo implode(',', $package_ids); ?>">
    
    <table style="border: 0 none;">
        <tbody>
            <tr>
                <td colspan="2"><label for="deploy_now">Deploy Schedule:</label></td>
            </tr>
            <tr>
                <td><input type="radio" name="deploy_now" value="now" checked></td>
                <td> Now</td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <!-- Date-time selector (shown if 'Schedule for later' is selected) -->
                <td><input type="radio" name="deploy_now" value="scheduled"></td>
                <td> Schedule for later</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="scheduled_time_selector" style="display:none;">
                        <label for="scheduled_time">Select Date and Time:</label>
                        <input type="datetime-local" name="scheduled_time" id="scheduled_time">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    <button type="button" onclick="submitForm()">Deploy</button>
</form>

<script>
    // JavaScript to toggle date-time selector visibility
    document.querySelectorAll('input[name="deploy_now"]').forEach(function(elem) {
        elem.addEventListener('change', function() {
            if (this.value === 'scheduled') {
                document.getElementById('scheduled_time_selector').style.display = 'block';
            } else {
                document.getElementById('scheduled_time_selector').style.display = 'none';
            }
        });
    });

    // Handle form submission and validate
    function submitForm() {
        const deployNow = document.querySelector('input[name="deploy_now"]:checked').value;
        const scheduledTimeInput = document.getElementById('scheduled_time');
        
        if (deployNow === 'scheduled' && !scheduledTimeInput.value) {
            alert('Please select a date and time for the scheduled deployment.');
            return;
        }

        // If valid, submit the form using GET method
        document.getElementById('deploymentForm').submit();
    }
</script>
