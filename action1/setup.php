<?php

use Glpi\Plugin\Hooks;
use GlpiPlugin\Action1\Config;
use GlpiPlugin\Action1\Action1;
use GlpiPlugin\Action1\ItemForm;
use GlpiPlugin\Action1\Showtabitem;

define('PLUGIN_ACTION1_VERSION', '0.0.5');

// Minimal GLPI version, inclusive
define("PLUGIN_ACTION1_MIN_GLPI_VERSION", "10.0.0");
// Maximum GLPI version, exclusive
define("PLUGIN_ACTION1_MAX_GLPI_VERSION", "10.0.99");

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_action1()
{
    global $PLUGIN_HOOKS, $CFG_GLPI;

    $PLUGIN_HOOKS['csrf_compliant']['action1'] = true;

    // Register menu hook
    // $PLUGIN_HOOKS['config_page']['action1'] = 'front/setup.form.php';
    Plugin::registerClass(Config::class, ['addtabon' => 'Config']);

    //$PLUGIN_HOOKS['menu_toadd']['tools'] = ['action1' => 'PluginAction1Menu'];
    
    $PLUGIN_HOOKS['menu_toadd']['action1'] = [
        'plugins' => Action1::class,
        'tools' => Action1::class
    ];
    $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY]['action1'] = true;
    $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY_ICON]['action1'] = 'fas fa-puzzle-piece';

    $PLUGIN_HOOKS['config_page']['action1'] = 'front/config.php';

    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['action1'] = 'action1.js';
    $PLUGIN_HOOKS[Hooks::ADD_CSS]['action1'] = 'action1.css';

    $PLUGIN_HOOKS['status']['action1'] = 'plugin_action1_Status';

    $PLUGIN_HOOKS[Hooks::CSRF_COMPLIANT]['action1'] = true;
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_action1()
{
    return [
        'name' => 'Action1',
        'version' => PLUGIN_ACTION1_VERSION,
        'author' => '<a href="https://www.astiostech.com/">AstiosTech Sdn Bhd</a>',
        'license' => 'GPLv2+',
        'homepage' => 'https://www.astiostech.com/',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_ACTION1_MIN_GLPI_VERSION,
                'max' => PLUGIN_ACTION1_MAX_GLPI_VERSION,
            ]
        ]
    ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_action1_check_prerequisites()
{
    if (false) {
        return false;
    }
    return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_action1_check_config($verbose = false)
{
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'action1');
    }
    return false;
}

/*
function PluginAction1Menu() {
    return [
        'title'  => __('Action1 Setup', 'action1'),
        'page'   => '/plugins/action1/front/setup.form.php',
        'icon'   => 'fas fa-cogs'
    ];
}*/