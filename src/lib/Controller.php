<?php
/**
 * Created by PhpStorm.
 * User: Vendas
 * Date: 01/06/2017
 * Time: 08:48
 */

namespace criativa\lib;


class Controller extends System {
    public $dados;
    public $layout = '_layout';
    public $path;
    public $pathRender;

    public $captionController = null;
    public $captionAction = null;
    public $captionParams = null;

    //Metatag
    public $title;
    public $description;
    public $keywords;
    public $image;
    public $movie;

    public function __construct(){
        parent::__construct();

        if (class_exists('\criativaHelper\api\ApiParametro')) {
            new \criativaHelper\api\ApiParametro();
        }
    }

    public function checkPermissao($id, $redirect = false){
        $permissao = false;

        if ( class_exists('\criativaUser\api\ApiUsuarioPermissaoRotina') ) {
            $api = new \criativaUser\api\ApiUsuarioPermissaoRotina();
            $permissao = $api->checkPermissao($id);
        }

        if ($redirect){
            if (!$permissao){
                $this->view('../sessao/noacess');
                exit();
            }
        } else {
            return $permissao;
        }
    }

    public function view($name = null){
        $this->setSEO();

        //Run function set path
        $this->_setPath($name);

        if (is_null($this->layout)) {
            $this->render();
        } else {

            if (Router::$modCriativa){
                if (is_null(Config::getLayoutDefault())){
                    $reflector = new \ReflectionClass(get_called_class());
                    $this->layout = dirname($reflector->getFileName()) . '/../content/shared/' . $this->layout . '.phtml';
                } else {
                    $this->layout = Config::getLayoutDefault();
                }
            } else {
                $this->layout = "src/content/{$this->getArea()}/shared/{$this->layout}.phtml";
            }

            if (file_exists($this->layout)) {
                $this->render($this->layout);
            } else {
                define('ERROR', "Não foi localizado o layout! {$this->layout}");
                header("HTTP/1.0 404 Not Found");
                echo ERROR;
                exit();
            }
        }
    }

    public function render($view = null){
        if (is_array($this->dados) && count($this->dados) > 0) {
            extract($this->dados, EXTR_PREFIX_ALL, 'view');
            extract(array(
                'controller' => (is_null($this->captionController) ? '' : $this->captionController),
                'action' => (is_null($this->captionAction) ? '' : $this->captionAction),
                'params' => (is_null($this->captionParams) ? '' : $this->captionParams)
            ), EXTR_PREFIX_ALL, 'caption');
        }

        $this->_breadcrumb();

        if (!is_null($view) && is_array($view)) {
            foreach ($view as $l) {
                include($l);
            }
        } elseif (is_null($view) && is_array($this->path)) {
            foreach ($this->path as $l) {
                include($l);
            }
        } else {
            $file = is_null($view) ? $this->path : $view;
            file_exists($file) ? include ($file) : die($file);
        }
    }

    private function _setPath($render) {
        if (is_array($render)){
            foreach ($render as $l) {
                $path = 'src/view/' . $this->getArea() . '/' . $this->getController() . '/' . $l . '.phtml';
                $this->_fileExists($path);
                $this->path[] = $path;
            }
        } else {
            //Set path render
            $this->pathRender = is_null($render) ? $this->getAction() : $render;
            //Set path
            if (Router::$modCriativa){
                $reflector = new \ReflectionClass(get_called_class());

                $this->path = dirname($reflector->getFileName()) . '/../view/' . $this->getController() . '/' . $this->pathRender . '.phtml';
            } else {
                $this->path = 'src/view/' . $this->getArea() . '/' . $this->getController() . '/' . $this->pathRender . '.phtml';
            }
            $this->_fileExists($this->path);
        }
    }

    private function _breadcrumb(){
        if (!is_null($this->captionController) && !is_null($this->captionAction)){
            $file = './src/content/shared/_breadcrumb.phtml';
            if (file_exists($file)) {
                include($file);
            }
        }
    }

    private function _fileExists($file) {
        if (!file_exists($file)) {
            define('ERROR', "Não foi localizado a view!\n{$file}");
            header("HTTP/1.0 404 Not Found");
            echo ERROR;
            exit();
        }
    }

    private function setSEO(){
        if (!defined('TITLE')){ define('TITLE',''); }
        if (!defined('DESCRIPTION')){ define('DESCRIPTION',''); }
        if (!defined('KEYWORDS')){ define('KEYWORDS',''); }

        if (empty($this->title)){ $this->title = TITLE; }
        if (empty($this->description)){ $this->description = DESCRIPTION; }
        if (empty($this->keywords)){ $this->keywords = KEYWORDS; }

        $this->title = strip_tags($this->title);
        $this->description = strip_tags($this->description);
        $this->keywords = strip_tags($this->keywords);
    }
}
