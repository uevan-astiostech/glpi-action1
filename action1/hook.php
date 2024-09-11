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

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_action1_install()
{
    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_action1_uninstall()
{
    Config::deleteConfigurationValues('plugin:action1');
    return true;
}

// Add the sub-menu to the Tools section
function plugin_action1_add_menus() {
    global $PLUGIN_HOOKS;

    // Add the submenu under Tools
    $PLUGIN_HOOKS['menu_toadd']['tools'] = ['action1' => 'PluginAction1Menu'];
}

function PluginAction1Menu() {
    return [
        'title'  => __('Action1 Setup', 'action1'),
        'page'   => '/plugins/action1/front/setup.form.php',
        'icon'   => 'fas fa-cogs'
    ];
}