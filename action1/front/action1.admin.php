<?php
include('../../../inc/includes.php');

// Check permissions
Session::checkRight("config", READ);

// Fetch API data using your client ID and client secret
$config = Config::getConfigurationValues('action1');

// Simulate API call or fetch real API data using the client ID and secret
$clientID = $config['client_id'];
$clientSecret = $config['client_secret'];
$apiData = yourpluginname_get_api_data('https://api.example.com/data', $clientID, $clientSecret);

// Display data in tabs
$tabs = ['Tab 1', 'Tab 2', 'Tab 3'];  // Add tab names here
$tabContents = ['Content for Tab 1', 'Content for Tab 2', 'Content for Tab 3']; // Corresponding content

// GLPI's tabbed interface
$tabs = new PluginAction1SubTabs();
$tabs->displayTabs($tabs, $_GET['id'], $_SERVER['PHP_SELF']);

// Example content for the tabs
echo '<div class="center">';
echo '<table class="tab_cadre_fixe">';
foreach ($tabs as $index => $tab) {
    echo '<tr><th>' . $tab . '</th></tr>';
    echo '<tr><td>' . $tabContents[$index] . '</td></tr>';
}
echo '</table>';
echo '</div>';