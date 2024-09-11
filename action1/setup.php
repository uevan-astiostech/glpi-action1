<?php

/**
 * -------------------------------------------------------------------------
 * Action1 plugin for GLPI
 * Copyright (C) 2024 by the AstiosTech Development Team.
 * -------------------------------------------------------------------------
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * --------------------------------------------------------------------------
 */

 use Glpi\Plugin\Hooks;
 use GlpiPlugin\Action1\Config;
 use GlpiPlugin\Action1\Action1;
 use GlpiPlugin\Action1\ItemForm;
 use GlpiPlugin\Action1\Showtabitem;



define('PLUGIN_ACTION1_VERSION', '0.0.3');

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
function plugin_init_action1(){
    global $PLUGIN_HOOKS,$CFG_GLPI;

    $PLUGIN_HOOKS['csrf_compliant']['action1'] = true;

    // Register menu hook
    // $PLUGIN_HOOKS['config_page']['action1'] = 'front/setup.form.php';
    Plugin::registerClass(Config::class, ['addtabon' => 'Config']);
    $PLUGIN_HOOKS['menu_toadd']['action1'] = ['plugins' => Action1::class];
    $PLUGIN_HOOKS['config_page']['action1'] = 'front/config.php';
    
    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['action1'] = 'action1.js';
    $PLUGIN_HOOKS[Hooks::ADD_CSS]['action1']        = 'action1.css';

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
        'name'           => 'Action1',
        'version'        => PLUGIN_ACTION1_VERSION,
        'author'         => '<a href="https://www.astiostech.com/">AstiosTech Sdn Bhd</a>',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://www.astiostech.com/',
        'requirements'   => [
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
