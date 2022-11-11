<?php

namespace criativa\helper;

class Security {
    public function __construct($area = 'SESSAO', $ws = false, $redirect = false){
        if (!defined('REDIRECT_SESSAO')){
            define('REDIRECT_SESSAO', APP_ROOT . APP_AREA . "/sessao");
        }

        if ($ws){
            if (!isset($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_USER'])){
                die('Não foi autenticado');
            }

            if ($_SERVER['PHP_AUTH_USER'] != base64_decode($_SERVER['PHP_AUTH_PW'])){
                die('Acesso inválido');
            }

            $_SESSION['tokenID']=$_SERVER['PHP_AUTH_USER'];
            $_SESSION['SESSAO'] = (object)array('ID'=>'8888','codusuario'=>'8888');
        } else {
            $token = $this->_getBearerToken();

            if (!is_null($token)){
                if ( class_exists('\criativa\user\api\ApiUsuarioSessao') ) {
                    $api = new \criativa\user\api\ApiUsuarioSessao();
                    $permissao = $api->checkLogin($token);
                    if (isset($permissao->codusuario)){
                        $_SESSION[$area] = $permissao;
                    }
                }

                if (!isset($_SESSION[$area]->ID) || empty($_SESSION[$area]->ID)){
                    header("HTTP/1.0 401 Unauthorized");
                    exit();
                }
            } else {
                if (!isset($_SESSION[$area]->ID) || empty($_SESSION[$area]->ID) || $_SESSION[$area]->privatekey != md5('m2' . CLIENT_IP . $_SERVER['HTTP_USER_AGENT'])){
                    header("HTTP/1.0 401 Unauthorized");
                    if ($redirect){
                        header("location: " . REDIRECT_SESSAO);
                    }
                    exit();
                }
            }
        }
    }

    public static function Recaptcha($token){
        //return true;
        if (isset($token)) {

            if (!defined('RECAPTCH_KEYPRIVATE')){
                return false;
            }

            # Os parâmetros podem ficar em um array
            $vetParametros = array (
                "secret" => RECAPTCH_KEYPRIVATE,
                "response" => $token,
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

    /**
     * Get header Authorization
     * */
    private function _getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
     * get access token from header
     * */
    private function _getBearerToken() {
        $headers = $this->_getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}