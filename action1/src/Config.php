<?php

namespace GlpiPlugin\Action1;
use CommonDBTM;
use CommonGLPI;
use Config as GlpiConfig;
use Html;
use Session;

class Config extends CommonDBTM
{
    static protected $notable = true;

    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {

        if (!$withtemplate) {
            if ($item->getType() == 'Config') {
                return __('Example plugin');
            }
        }
        return '';
    }

    static function configUpdate($input) {
        $input['configuration'] = 1 - $input['configuration'];
        return $input;
     }

     function showFormExample() {
        global $CFG_GLPI;
  
        if (!Session::haveRight("config", UPDATE)) {
           return false;
        }
  
        $my_config = GlpiConfig::getConfigurationValues('plugin:Example');
  
        echo "<form name='form' action=\"".Toolbox::getItemTypeFormURL('Config')."\" method='post'>";
        echo "<div class='center' id='tabsbody'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='4'>" . __('Example setup') . "</th></tr>";
        echo "<td >" . __('My boolean choice :') . "</td>";
        echo "<td colspan='3'>";
        echo "<input type='hidden' name='config_class' value='".__CLASS__."'>";
        echo "<input type='hidden' name='config_context' value='plugin:Example'>";
        Dropdown::showYesNo("configuration", $my_config['configuration']);
        echo "</td></tr>";
  
        echo "<tr class='tab_bg_2'>";
        echo "<td colspan='4' class='center'>";
        echo "<input type='submit' name='update' class='submit' value=\""._sx('button', 'Save')."\">";
        echo "</td></tr>";
  
        echo "</table></div>";
        Html::closeForm();
     }
  
     static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
  
        if ($item->getType() == 'Config') {
           $config = new self();
           $config->showFormExample();
        }
     }
}