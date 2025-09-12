<?php

use Glpi\Plugin\Hooks; // Import Hooks class

define('PLUGIN_ACTION1_VERSION', '0.0.7');
define("PLUGIN_ACTION1_MIN_GLPI_VERSION", "10.0.0");
define("PLUGIN_ACTION1_MAX_GLPI_VERSION", "10.0.99");

function plugin_init_action1()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['add_javascript']['action1'] = 'js/action1.js';
    $PLUGIN_HOOKS['add_css']['action1'] = 'css/action1.css';
    //$PLUGIN_HOOKS['add_css']['action1'] = Html::css('action1.css', 'plugins/action1/css/', ['force_css' => true]);
    $PLUGIN_HOOKS['csrf_compliant']['action1'] = true;

    $PLUGIN_HOOKS['menu_toadd']['action1'] = ['tools'  => 'PluginAction1Menu'];
    $PLUGIN_HOOKS['config_page']['action1'] = 'front/config.php'; // Configuration page
}

function plugin_version_action1()
{
    return [
        'name' => 'Action1',
        'version' => PLUGIN_ACTION1_VERSION,
        'author' => '<a href="https://www.astiostech.com/">AstiosTech Sdn Bhd</a>',
        'license' => 'GPLv2+',
        'homepage' => 'https://github.com/uevan-astiostech/glpi-action1',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_ACTION1_MIN_GLPI_VERSION,
                'max' => PLUGIN_ACTION1_MAX_GLPI_VERSION,
            ]
        ]
    ];
}

function plugin_action1_check_prerequisites()
{
    return true; // Assuming your prerequisites are met
}

function plugin_action1_check_config($verbose = false)
{
    return true; // Assuming your config is fine
}

function plugin_action1_install()
{
    global $DB;

    // Create the options table if it doesn't exist
    if (!$DB->tableExists("glpi_plugin_actionone_option")) {
        $query = "CREATE TABLE `glpi_plugin_actionone_option` (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `option_name` VARCHAR(255) DEFAULT NULL,
                    `option_value` TEXT,
                    PRIMARY KEY (`id`)
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $DB->query($query) or die("Error creating glpi_plugin_actionone_option: " . $DB->error());

        $query = "INSERT INTO `glpi_plugin_actionone_option`
                         (`id`, `option_name`, `option_value`)
                  VALUES (1, 'api_url', NULL),
                         (2, 'client_id', NULL),
                         (3, 'client_secret', NULL),
                         (4, 'access_token', NULL),
                         (5, 'refresh_token', NULL);";

        $DB->query($query) or die("Error populating glpi_plugin_actionone_option: " . $DB->error());
    }

    return true;
}

function plugin_action1_uninstall()
{
    global $DB;

    if ($DB->tableExists("glpi_plugin_actionone_option")) {
        $query = "DROP TABLE `glpi_plugin_actionone_option`;";
        $DB->query($query) or die("Error deleting glpi_plugin_actionone_option: " . $DB->error());
    }

    return true;
}
