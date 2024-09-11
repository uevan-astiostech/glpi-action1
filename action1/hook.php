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

use GlpiPlugin\Action1\Action1;

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_action1_install()
{
    global $DB;

    $config = new Config();

    $config->setConfigurationValues('plugin:Action1', ['configuration' => false]);

    $default_charset = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign = DBConnection::getDefaultPrimaryKeySignOption();

    if (!$DB->tableExists("glpi_plugin_action1_option")) {
        $query = "CREATE TABLE `glpi_plugin_action1_option` (
                    `id` int {$default_key_sign} NOT NULL auto_increment,
                    `option_name` varchar(255) default NULL,
                    `option_value` text,
                  PRIMARY KEY (`id`)
                 ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->query($query) or die("error creating glpi_plugin_action1_option " . $DB->error());

        $query = "INSERT INTO `glpi_plugin_action1_option`
                         (`id`, `option_name`, `option_value`)
                  VALUES (1, 'client_id', NULL),
                         (2, 'client_secret', NULL)";
        $DB->query($query) or die("error populate glpi_plugin_action1_option " . $DB->error());
    }

    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_action1_uninstall()
{
    global $DB;

    $config = new Config();
    $config->deleteConfigurationValues('plugin:Action1', ['configuration' => false]);

    if ($DB->tableExists("glpi_plugin_action1_option")) {
        $query = "DROP TABLE `glpi_plugin_action1_option`";
        $DB->query($query) or die("error deleting glpi_plugin_action1_option");
    }

    return true;
}