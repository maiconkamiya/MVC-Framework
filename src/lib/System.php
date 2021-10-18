<?php


namespace criativa\lib;

class System extends Router {
    private $url;
    private $explode;
    private $area;
    private $controller;
    private $action;
    private $params;
    private $init;

    public function __construct(){
        $this->_setUrl();
        $this->_setExplode();
        $this->_setArea();
        $this->_setController();
        $this->_setAction();
        $this->_setParams();
    }

    private function _setUrl(){
        $this->url = isset($_GET['url']) ? $_GET['url'] : 'home/index';
    }
    private function _setExplode(){
        $this->explode = explode('/', $this->url);
    }
    private function _setArea(){
        Router::$onDefault = true;
        Router::$modCriativa = false;

        foreach (Router::$routers as $i => $v){
            if (Router::$onDefault && $this->explode[0] == $i){
                $this->area = $v;
                Router::$onDefault = false;
            } elseif (Router::$onDefault && strpbrk($this->explode[0], $i)){
                $this->area = $this->explode[0];
                Router::$onDefault = false;
                Router::$modCriativa = true;
            }
        }

        $this->area = empty($this->area) ? Router::$routers[Router::$routerOnDefault] : $this->area;

        if (!defined('APP_AREA')){
            define('APP_AREA', (Router::$onDefault ? './' : $this->area));
        }
    }
    private function _setController(){
        $this->controller = Router::$onDefault ? $this->explode[0] :
            (empty($this->explode[1]) || is_null($this->explode[1]) || !isset($this->explode[1]) ? 'home' : $this->explode[1]);
    }
    private function _setAction(){
        $this->action = Router::$onDefault ?
            (!isset($this->explode[1]) || is_null($this->explode[1]) || empty($this->explode[1]) ? 'index' : $this->explode[1]) :
            (!isset($this->explode[2]) || is_null($this->explode[2]) || empty($this->explode[2]) ? 'index' : $this->explode[2]);
    }
    private function _setParams(){
        if (self::$onDefault){
            unset($this->explode[0], $this->explode[1]);
        } else {
            unset($this->explode[0], $this->explode[1], $this->explode[2]);
        }

        if (end($this->explode) == null){
            array_pop($this->explode);
        }

        if (empty($this->explode)){
            $this->params = array();
        } else {
            foreach ($this->explode as $val){
                $this->params[] = $val;
            }
        }
    }

    public function getArea(){
        return $this->area;
    }
    public function getController(){
        return $this->controller;
    }
    public function getAction(){
        return $this->action;
    }
    public function getParams($indice){
        return isset($this->params[$indice]) ? $this->params[$indice] : null;
    }

    private function _validarController(){
        if (!(class_exists($this->init))) {
            header("HTTP/1.0 404 Not Found");
            define('ERROR', 'Não foi localizado o Controller: ' . $this->controller);
            echo ERROR;
            exit();
        }
    }
    private function _validarAction(){
        if (!(method_exists($this->init, $this->action))) {
            header("HTTP/1.0 404 Not Found");
            define('ERROR', 'Não foi localizado o Action: ' . $this->action);
            echo ERROR;
            exit();
        }
    }

    public function run(){
        
        if (Router::$modCriativa){
            
            print_r($this->area);
            
            $this->init = $this->area . '\\controller\\' . $this->controller . 'Controller';
        } else {
            $this->init = 'mvc\\controller\\' . $this->area . '\\' . $this->controller . 'Controller';
        }
        
        $this->_validarController();
        $this->init = new $this->init();
        $this->_validarAction();
        $act = $this->action;
        $this->init->$act();
    }
}
