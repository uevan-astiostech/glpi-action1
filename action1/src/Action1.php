<?php

namespace GlpiPlugin\Action1;
use CommonDBTM;
use CommonGLPI;
use Computer;
use Html;
use Log;
use MassiveAction;
use Session;

class Action1 extends CommonDBTM
{

    static function getMenuName()
    {
        return __('Action1 Setup', 'action1');
    }

    static function getAdditionalMenuLinks()
    {
        global $CFG_GLPI;
        $links = [];

        $links['config'] = '/plugins/action1/index.php';
        $links["<img  src='" . $CFG_GLPI["root_doc"] . "/pics/menu_showall.png' title='" . __s('Show all') . "' alt='" . __s('Show all') . "'>"] = '/plugins/action1/index.php';
        $links[__s('Test link', 'action1')] = '/plugins/action1/index.php';

        return $links;
    }

    function defineTabs($options = [])
    {

        $ong = [];
        $this->addDefaultFormTab($ong);
        $this->addStandardTab('Link', $ong, $options);

        return $ong;
    }

}