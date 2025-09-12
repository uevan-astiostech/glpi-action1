<?php

include("../../../inc/includes.php");

// Check if the user has super admin rights
Session::checkRight("config", UPDATE); // Check if user has update rights

// To be available when the plugin is not activated
Plugin::load('action1');

Html::header(__('Action1 Configuration', 'action1'), $_SERVER['PHP_SELF'], "config", "action1");

function displayForm() {
    global $DB;

    // Retrieve the values from the database
    $api_url = getOption('api_url');
    $client_id = getOption('client_id');
    $client_secret = getOption('client_secret');

    // Display the form with existing values if they exist
    echo '<form method="post" action="">';
    echo '<label for="api_url">API URL:</label>';
    echo '<input type="text" name="api_url" value="' . htmlentities($api_url) . '" required><br>';
    
    echo '<label for="client_id">Client ID:</label>';
    echo '<input type="text" name="client_id" value="' . htmlentities($client_id) . '" required><br>';
    
    echo '<label for="client_secret">Client Secret:</label>';
    echo '<input type="password" name="client_secret" value="' . htmlentities($client_secret) . '" required><br>';
    
    echo '<input type="submit" name="submit" value="Save">';
    echo '</form>';
    
    // If form is submitted, process the values
    if (isset($_POST['submit'])) {
        $api_url = $_POST['api_url'];
        $client_id = $_POST['client_id'];
        $client_secret = $_POST['client_secret'];

        // Save the values into the database
        saveOption('api_url', $api_url);
        saveOption('client_id', $client_id);
        saveOption('client_secret', $client_secret);

        // Trigger the OAuth token retrieval
        $response = getOAuth2Token($api_url, $client_id, $client_secret);

        // Check the response and display the result
        if ($response && isset($response['access_token'])) {
            // Save tokens to the database
            saveOption('access_token', $response['access_token']);
            saveOption('refresh_token', $response['refresh_token']);

            echo '<p>Access Token: ' . htmlentities($response['access_token']) . '</p>';
            echo '<p>Refresh Token: ' . htmlentities($response['refresh_token']) . '</p>';
        } else {
            // Display error message
            echo '<p>Error: ' . htmlentities($response['error'] ?? 'Unknown error') . '</p>';
        }
    }
}

// function getOption($name) {
//     global $DB;

//     // Use the DB::query to fetch data
//     $query = "SELECT `option_value` FROM `glpi_plugin_actionone_option` WHERE `option_name` = '$name'";
//     $result = $DB->query($query);

//     // Fetch the result using fetch_assoc (not fetch_array)
//     if ($row = $DB->fetch_assoc($result)) {
//         return $row['option_value'];
//     }
//     return null;
// }

function getOption($name) {
    // Hard-code the client_id, client_secret, and api_url based on the requested values
    $options = [
        'client_id' => 'api-key-e940aedc-b054-4aaf-a45e-e1ec25a81006099e34d8-f061-70ba-9dec-3235af5b7719@action1.com',
        'client_secret' => 'f3245ff0d83bd37f03144a95c0f546ab',
        'api_url' => 'https://app.au.action1.com/api/3.0',
    ];

    // Return the hardcoded values based on the requested option
    if (array_key_exists($name, $options)) {
        return $options[$name];
    }

    // Return null if the option does not exist
    return null;
}

function saveOption($option_name, $option_value) {
    global $DB;
    // Check if the option already exists
    $result = $DB->query("SELECT * FROM glpi_plugin_actionone_option WHERE option_name = '$option_name'");
    if ($DB->numrows($result) > 0) {
        // Update existing option
        $DB->query("UPDATE glpi_plugin_actionone_option SET option_value = '$option_value' WHERE option_name = '$option_name'");
    } else {
        // Insert new option
        $DB->query("INSERT INTO glpi_plugin_actionone_option (option_name, option_value) VALUES ('$option_name', '$option_value')");
    }
}

function getOAuth2Token($api_url, $client_id, $client_secret) {
    $url = $api_url . '/oauth2/token';

    $data = json_encode([
        "client_id" => $client_id,
        "client_secret" => $client_secret
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    if ($response === false) {
        echo __('Curl error: ' . curl_error($ch), 'action1');
        return null;
    }

    curl_close($ch);
    return json_decode($response, true); // Return response as an associative array
}

// Call the function to display the form
displayForm();

echo "<p><a href='action1.php'>" . __('Go to Admin Page', 'action1') . "</a></p>";

Html::footer();
?>
