<?php
/**
 * Created by PhpStorm.
 * User: maico
 * Date: 09/10/2021
 * Time: 11:24
 */

namespace mvc\controller\web;

use criativa\lib\Controller;

class homeController extends Controller {
    public function index(){
        $this->dados = array(
            'area' => $this->getArea(),
            'controller' => $this->getController(),
            'action' => $this->getAction(),
            'params' => array(
                'primeiro'=>$this->getParams(0),
                'segundo'=>$this->getParams(1),
                'terceiro'=>$this->getParams(2)
            )
        );
        $this->view();
    }
}