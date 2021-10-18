<?php

namespace criativa\lib;

class Router {

    protected static $routers = array(
        'criativa' => 'criativa'
    );

    protected static $routerOnDefault = 'web';

    protected static $onDefault = true;

    protected static $modCriativa = false;

    public static function setRouters($router){
        self::$routers[] = $router;
    }

    public static function setRouterOnDefault($router){
        self::$routerOnDefault = $router;
    }

}
