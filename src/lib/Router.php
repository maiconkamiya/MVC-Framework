<?php

namespace criativa\lib;

class Router {

    protected static $routers = array(
        'criativa' => 'criativa'
    );

    protected static $routerOnDefault = 'criativa';

    protected static $onDefault = true;

    protected static $prefixCriativa = 'criativa\/([a-z]+)';
    
    protected static $modCriativa = false;

    public static function setRouters($router){
        foreach ($router as $i => $v){
            self::$routers[$i] = $v;
        }
    }

    public static function setRouterOnDefault($router){
        self::$routerOnDefault = $router;
    }

}
