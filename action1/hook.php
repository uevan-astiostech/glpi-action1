<?php

use Glpi\Plugin\Hooks; // Import Hooks class

function plugin_change_profile_action1()
{
    if (Session::haveSuperAdminAccess()) {
        $_SESSION["glpi_plugin_action1_profile"] = ['action1' => 'w'];
    } else {
        unset($_SESSION["glpi_plugin_action1_profile"]);
    }
}

/**
 * Define the Action1 menu under the Tools section
 */
function plugin_action1_menu() {
    global $PLUGIN_HOOKS;

    // Ensure the menu entry links to the config form correctly
    // $PLUGIN_HOOKS['menu_toadd']['action1'] = [
    //     'tools' => 'front/action1.php'  // Link to the configuration page
    // ];
}
