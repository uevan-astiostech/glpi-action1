<?php
include('../../../inc/includes.php');

// Check if user has the right permission
Session::checkRight("config", READ);

// HTML form for Client ID and Client Secret
if (isset($_POST['save'])) {
    $clientID = $_POST['client_id'];
    $clientSecret = $_POST['client_secret'];

    // Store values in GLPI configuration (or use your own mechanism)
    Config::saveConfigurationValues('action1', [
        'client_id' => $clientID,
        'client_secret' => $clientSecret
    ]);
}

$config = Config::getConfigurationValues('action1');

echo '<form method="post" action="">';
echo '<table class="tab_cadre_fixe">';
echo '<tr><th colspan="2">Action1 Setup</th></tr>';
echo '<tr><td>Client ID:</td><td><input type="text" name="client_id" value="' . $config['client_id'] . '" /></td></tr>';
echo '<tr><td>Client Secret:</td><td><input type="text" name="client_secret" value="' . $config['client_secret'] . '" /></td></tr>';
echo '<tr><td colspan="2" class="center"><input type="submit" name="save" value="Save" class="submit" /></td></tr>';
echo '</table>';
echo '</form>';