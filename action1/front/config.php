<?php

include ("../../../inc/includes.php");

//Session::checkRight("config", UPDATE);

// To be available when plugin in not activated
Plugin::load('action1');

//Html::header("TITLE", $_SERVER['PHP_SELF'], "config", "plugins");
Html::header(__('Action1 Setup', 'action1'), $_SERVER['PHP_SELF'], "admin", "config");
echo __("This is the plugin config page", 'action1');

echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
echo "<table class='tab_cadre_fixe'>";
echo "<tr><th colspan='2'>" . __('Action1 API Setup', 'action1') . "</th></tr>";

// Client ID field
echo "<tr class='tab_bg_1'>";
echo "<td>" . __('Client ID', 'action1') . "</td>";
echo "<td><input type='text' name='client_id' value='' size='50'></td>";
echo "</tr>";

// Client Secret field
echo "<tr class='tab_bg_1'>";
echo "<td>" . __('Client Secret', 'action1') . "</td>";
echo "<td><input type='password' name='client_secret' value='' size='50'></td>";
echo "</tr>";

// Submit button
echo "<tr class='tab_bg_2'>";
echo "<td colspan='2' class='center'><input type='submit' name='save' value='" . __('Save', 'action1') . "' class='submit'></td>";
echo "</tr>";

echo "</table>";
echo "</form>";
Html::footer();