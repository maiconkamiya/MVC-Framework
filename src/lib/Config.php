<?php
/**
 * Created by PhpStorm.
 * User: Vendas
 * Date: 01/06/2017
 * Time: 08:47
 */

namespace criativa\lib;

class Config {

    protected static $prefix = "tab";

    protected static $host = 'localhost';
    protected static $user = 'root';
    protected static $pwd = '';
    protected static $dbname = '';

    /**/

    protected static $charset = 'utf8';

    public static function setConfig($con){
        if (isset($con->prefix)){
            self::$prefix = $con->prefix;
        }

        if (isset($con->host)){
            self::$host = $con->host;
        }

        if (isset($con->user)){
            self::$user = $con->user;
        }

        if (isset($con->pwd)){
            self::$pwd = $con->pwd;
        }

        if (isset($con->dbname)){
            self::$dbname = $con->dbname;
        }

        if (isset($con->charset)){
            self::$charset = $con->charset;
        }
    }

}