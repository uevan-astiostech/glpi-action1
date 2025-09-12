<?php
class PluginAction1Menu extends CommonGLPI
{
    public static function getMenuName()
    {

        return __('Action1', 'action1');
    }

    public static function getMenuContent()
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI;

        $injectionFormUrl = "/" . Plugin::getWebDir('action1', false) . '/front/action1.php';

        $menu = [
            'title' => self::getMenuName(),
            'page'  => $injectionFormUrl,
            'icon'  => 'fas fa-cog',
        ];

        return $menu;
    }
}