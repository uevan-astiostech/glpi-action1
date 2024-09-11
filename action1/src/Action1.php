<?php

namespace GlpiPlugin\Action1;
use CommonDBTM;
use CommonGLPI;
use Computer;
use Html;
use Log;
use MassiveAction;
use Session;

class Action1 extends CommonDBTM {

    static function getMenuName() {
        return __('Example plugin');
    }


}