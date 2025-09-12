<?php

// Include GLPI's common header
include('../../../inc/includes.php');

// Check if the user has super admin rights
Session::checkRight("config", UPDATE); // Check if user has update rights

// To be available when the plugin is not activated
Plugin::load('action1');

Html::header(__('Action1 Configuration', 'action1'), $_SERVER['PHP_SELF'], "config", "action1");

// Render the header
H//tml::header(__('Action1 Configuration', 'action1'), $_SERVER['PHP_SELF'], 'tools', 'action1');

// Form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!Html::checkCsrfToken($_POST['csrf_token'])) {
        echo "<div class='error'>" . __('CSRF token validation failed.', 'action1') . "</div>";
        exit; // Stop further processing
    }

    // Validate and sanitize input
    $client_id = isset($_POST['client_id']) ? trim($_POST['client_id']) : '';
    $client_secret = isset($_POST['client_secret']) ? trim($_POST['client_secret']) : '';

    // Save configuration values
    if (!empty($client_id) && !empty($client_secret)) {
        try {
            // Prepare the queries to update configuration in the database
            $query1 = "INSERT INTO `glpi_plugin_action1_option` (`option_name`, `option_value`) 
                        VALUES ('client_id', ?) 
                        ON DUPLICATE KEY UPDATE `option_value` = VALUES(`option_value`);";
            $query2 = "INSERT INTO `glpi_plugin_action1_option` (`option_name`, `option_value`) 
                        VALUES ('client_secret', ?) 
                        ON DUPLICATE KEY UPDATE `option_value` = VALUES(`option_value`);";

            // Execute the queries
            $DB->prepare($query1)->execute([$client_id]);
            $DB->prepare($query2)->execute([$client_secret]);

            echo "<div class='success'>" . __('Configuration saved successfully.', 'action1') . "</div>";
        } catch (Exception $e) {
            // Log the error
            error_log('Failed to save configuration: ' . $e->getMessage());
            echo "<div class='error'>" . __('Failed to save configuration. Please try again.', 'action1') . "</div>";
        }
    } else {
        echo "<div class='error'>" . __('Please fill in all required fields.', 'action1') . "</div>";
    }
}

// Form content
echo "<h1>" . __('Action1 Plugin Configuration', 'action1') . "</h1>";
echo "<form action='' method='post'>";
echo "<input type='hidden' name='csrf_token' value='" . Html::csrf_token() . "'>"; // CSRF token
echo "<label for='client_id'>" . __('Client ID:', 'action1') . "</label>";
echo "<input type='text' name='client_id' id='client_id' value=''>";
echo "<br><br>";
echo "<label for='client_secret'>" . __('Client Secret:', 'action1') . "</label>";
echo "<input type='password' name='client_secret' id='client_secret' value=''>";
echo "<br><br>";
echo "<input type='submit' value='" . __('Save', 'action1') . "'>";
echo "</form>";

// Include GLPI's common footer
Html::footer();
