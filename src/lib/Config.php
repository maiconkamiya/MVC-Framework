<?php
/**
 * Created by PhpStorm.
 * User: Vendas
 * Date: 01/06/2017
 * Time: 08:47
 */

namespace criativa\lib;

class Config {

    private static $prefix = "tab";

    private static $srvMyhost = 'localhost';
    private static $srvMyuser = 'root';
    private static $srvMypass = '';
    private static $srvMydbname = '';

    /**/

    private static $charset = 'utf8';

    public static function setConfig($con){
        if (isset($con->prefix)){
            self::$prefix = $con->prefix;
        }

        if (isset($con->host)){
            self::$srvMyhost = $con->host;
        }

        if (isset($con->user)){
            self::$srvMyuser = $con->user;
        }

        if (isset($con->pwd)){
            self::$srvMypass = $con->pwd;
        }

        if (isset($con->dbname)){
            self::$srvMydbname = $con->dbname;
        }

        if (isset($con->charset)){
            self::$charset = $con->charset;
        }

        constant(self::$prefix);
        constant(self::$srvMyhost);
        constant(self::$srvMyuser);
        constant(self::$srvMypass);
        constant(self::$srvMydbname);
        constant(self::$charset);
    }

}