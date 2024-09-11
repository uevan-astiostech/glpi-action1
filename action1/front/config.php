<?php

include ("../../../inc/includes.php");

//Session::checkRight("config", UPDATE);

// To be available when plugin in not activated
Plugin::load('action1');

Html::header("TITLE", $_SERVER['PHP_SELF'], "config", "plugins");
echo __("This is the plugin config page", 'action1');
Html::footer();