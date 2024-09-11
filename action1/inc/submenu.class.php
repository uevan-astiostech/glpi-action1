<?php

class PluginAction1SubTabs extends CommonGLPI {
    
    // Define the subtab structure
    function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        $tabs = [];
        $tabs[1] = __('Tab 1', 'action1');
        $tabs[2] = __('Tab 2', 'action1');
        $tabs[3] = __('Tab 3', 'action1');
        return $tabs;
    }

    function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        switch ($tabnum) {
            case 1:
                // Fetch and display API data for Tab 1
                echo 'Content for Tab 1';
                break;
            case 2:
                // Fetch and display API data for Tab 2
                echo 'Content for Tab 2';
                break;
            case 3:
                // Fetch and display API data for Tab 3
                echo 'Content for Tab 3';
                break;
        }
    }
}