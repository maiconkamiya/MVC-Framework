<?php

namespace criativa\helper;

class Security {
    public function __construct($area = 'SESSAO', $ws = false, $redirect = null){
        if ($ws){
            if (!isset($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_USER'])){
                die('Não foi autenticado');
            }

            if ($_SERVER['PHP_AUTH_USER'] != base64_decode($_SERVER['PHP_AUTH_PW'])){
                die('Acesso inválido');
            }

            $_SESSION['tokenID']=$_SERVER['PHP_AUTH_USER'];
        } else {
            if (!isset($_SESSION[$area]->ID) || empty($_SESSION[$area]->ID) || $_SESSION[$area]->keyprivate != md5('m2' . CLIENT_IP . $_SERVER['HTTP_USER_AGENT'])){
                header("HTTP/1.0 401 Unauthorized");
                if (!is_null($redirect)){
                    header("location: " . $redirect);
                }
                exit();
            }
        }
    }

    public static function Recaptcha(){
        //return true;
        if (isset($_POST["g-recaptcha-response"])) {

            if (!defined('RECAPTCH_KEYPRIVATE')){
                return false;
            }

            # Os parâmetros podem ficar em um array
            $vetParametros = array (
                "secret" => RECAPTCH_KEYPRIVATE,
                "response" => $_POST["g-recaptcha-response"],
                "remoteip" => CLIENT_IP
            );

            //print_r($vetParametros);
            # Abre a conexão e informa os parâmetros: URL, método POST, parâmetros e retorno numa string
            $curlReCaptcha = curl_init();
            curl_setopt($curlReCaptcha, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($curlReCaptcha, CURLOPT_POST, true);
            curl_setopt($curlReCaptcha, CURLOPT_POSTFIELDS, http_build_query($vetParametros));
            curl_setopt($curlReCaptcha, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curlReCaptcha, CURLOPT_SSL_VERIFYPEER, false);
            # A resposta é um objeto json em uma string, então só decodificar em um array (true no 2º parâmetro)
            //$vetResposta = curl_exec($curlReCaptcha);
            $vetResposta = json_decode(curl_exec($curlReCaptcha), true);

            //print_r($vetResposta);
            # Fecha a conexão
            curl_close($curlReCaptcha);

            # Analisa o resultado (no caso de erro, pode informar os códigos)
            if ($vetResposta["success"]){
                return true;
            }
            else
            {
                return false;
            }
        } else {
            return false;
        }
    }
}