<?php

namespace criativa\lib;

class Router {
    protected $routers = array(
        'web' => 'web',
    );

    protected $routerOnDefault = 'web';

    protected $onDefault = true;
}