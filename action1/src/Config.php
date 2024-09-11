<?php

namespace GlpiPlugin\Action1;
use CommonDBTM;
use CommonGLPI;
use Config as GlpiConfig;
use Html;
use Session;

class Config extends CommonDBTM {
    static protected $notable = true;

    static function getMenuName() {
        return __('Action1');
     }
}