<?php

namespace criativa\lib;

class Router {

    protected static $routers;

    protected static $routerOnDefault = 'web';

    protected static $onDefault = true;

    public static function setRouters($router){
        self::$routers = $router;
    }

    public static function setRouterOnDefault($router){
        self::$routerOnDefault = $router;
    }

}