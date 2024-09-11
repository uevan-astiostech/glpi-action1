<?php
include('../../../inc/includes.php');

// Security check
Session::checkRight("config", READ);

// Check if form is submitted
if (isset($_POST['client_id']) && isset($_POST['client_secret'])) {
    // Save the data in the GLPI configuration (plugin-specific)
    Config::saveConfigurationValues('plugin:action1', [
        'client_id' => $_POST['client_id'],
        'client_secret' => $_POST['client_secret'],
    ]);

    Html::back();
}

// Get saved values, if any
$config = Config::getConfigurationValues('plugin:action1');

Html::header(__('Action1 Setup', 'action1'), $_SERVER['PHP_SELF'], "admin", "setup", "plugin_action1");

// Display form
echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
echo '<table class="tab_cadre_fixe">';
echo '<tr class="tab_bg_1"><td>' . __('Client ID', 'action1') . ':</td><td><input type="text" name="client_id" value="' . Html::cleanPostForTextField($config['client_id'] ?? '') . '"></td></tr>';
echo '<tr class="tab_bg_1"><td>' . __('Client Secret', 'action1') . ':</td><td><input type="text" name="client_secret" value="' . Html::cleanPostForTextField($config['client_secret'] ?? '') . '"></td></tr>';
echo '<tr class="tab_bg_1"><td colspan="2" class="center"><input type="submit" class="submit" value="' . __('Save', 'action1') . '"></td></tr>';
echo '</table>';
echo '</form>';

Html::footer();