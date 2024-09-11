<?php
// front/action1setup.form.php

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access this file directly.");
}

Session::checkRight("config", UPDATE);

// Check if form is submitted
if (isset($_POST["save"])) {
    $clientID = $_POST["client_id"];
    $clientSecret = $_POST["client_secret"];

    // Save the settings in GLPI (as plugin preferences or custom DB tables)
    // Example for saving in the plugin preferences
    Config::saveConfigurationValues('action1', ['client_id' => $clientID, 'client_secret' => $clientSecret]);

    Html::back();
}

// Retrieve saved values (if they exist)
$clientID = Config::getConfigurationValues('action1', ['client_id' => '']);
$clientSecret = Config::getConfigurationValues('action1', ['client_secret' => '']);

// Display the form
echo "<form method='post' action=''>";
echo "<table class='tab_cadre_fixe'>";
echo "<tr><th colspan='2'>Action1 Setup</th></tr>";

echo "<tr><td>Client ID:</td>";
echo "<td><input type='text' name='client_id' value='" . Html::entities($clientID['client_id']) . "'></td></tr>";

echo "<tr><td>Client Secret:</td>";
echo "<td><input type='text' name='client_secret' value='" . Html::entities($clientSecret['client_secret']) . "'></td></tr>";

echo "<tr><td colspan='2' class='center'><input type='submit' name='save' value='Save' class='submit'></td></tr>";
echo "</table>";
echo "</form>";