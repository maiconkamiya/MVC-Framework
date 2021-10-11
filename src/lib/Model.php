<?php
/**
 * Created by PhpStorm.
 * User: Vendas
 * Date: 01/06/2017
 * Time: 08:47
 */

namespace criativa\lib;

class Model extends Config {

    protected $con;

    public function __construct(){
        if (!defined('TIME_ZONE')){
            define('TIME_ZONE', '-04:00');
        }

        try {
            $this->con = new \PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$user, self::$pwd);
            $this->con->exec("set names " . self::$charset);
            $this->con->exec("SET GLOBAL sql_mode=''");
            $this->con->exec("SET GLOBAL time_zone='".TIME_ZONE."'");

            $this->con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $ex){
            echo json_encode(array('sucess' => false, 'feedback' => $ex->getMessage()));
            exit();
        }
    }

    public function Select($sql){
        try {
            $state = $this->con->prepare($sql);
            $state->execute();
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            die($ex->getMessage() . ' ' . $sql);
        }
        $array = array();
        while($row = $state->fetchObject()){
            $array[] = $row;
        }

        return $array;
    }

    public function Execute($sql){
        try {
            $state = $this->con->prepare($sql);
            return array('sucess'=>$state->execute());
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            return array('sucess'=>false, 'feedback'=>$ex->getMessage(), 'sql' => $sql);
        }
    }

    public function Query($sql){
        try {
            return array('sucess'=>$this->con->query($sql));
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            return array('sucess'=>false, 'feedback'=>$ex->getMessage() .' '. $sql);
        }
    }

    public function Insert($object, $table){
        foreach ($object as $i => $v){
            if ($v == '')
                unset($object->$i);
        }

        try {
            $sql = "INSERT INTO {$table} (`".implode("`,`",array_keys((array)$object))."`) VALUES ('".implode("','",array_values((array)$object))."')";
        } catch (\Exception $ex){
            return array('sucess'=>false, 'feedback'=>$ex->getMessage());
        }

        try {
            $state = $this->con->prepare($sql);
            $state->execute();
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            return array('sucess'=>false, 'feedback'=>$ex->getMessage() . $sql);
        }

        return array('sucess'=>true, 'feedback'=>'Inserido', 'codigo'=>$this->Last($table));
    }

    public function Update($object, $condition, $table){
        try {
            foreach ($object as $ind => $val) {
                $dados[] = "`{$ind}` = " . (is_null($val) ? " NULL " : "'".str_replace("'","\\'",$val)."'");
            }
            foreach ($condition as $ind => $val) {
                $where[] = "`{$ind}` ". (is_null($val) ? " IS NULL " : " = " . (is_numeric($val) ? $val : "'{$val}'"));
            }
            $sql = "UPDATE {$table} SET " . implode(',', $dados) . " WHERE " . implode(' AND ', $where);

            $state = $this->con->prepare($sql);
            $state->execute();

        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            return array('sucess'=>false, 'feedback'=>$ex->getMessage(). $sql);
        }

        return array('sucess'=>true, 'feedback'=> 'Atualizado');
    }

    public function Delete($condition, $table){
        try {
            foreach ($condition as $ind => $val) {
                $where[] = "`{$ind}` = '{$val}'";
            }
            $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $where);
            $state = $this->con->prepare($sql);
            $state->execute();
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            return array('sucess'=>false, 'feedback'=>$ex->getMessage() . $sql);
        }
        return array('sucess'=>true, 'feedback'=> '');
    }

    public function Last($table){
        try {
            $state = $this->con->prepare("SELECT last_insert_id() as last FROM {$table}");
            $state->execute();
            $state = $state->fetchObject();
        } catch(\PDOException $ex){
            return array('sucess'=>false, 'feedback'=>$ex->getMessage() . $table);
        }
        return isset($state->last) ? $state->last : null;
    }

    public function First($object){
        if (isset($object[0])) {
            return $object[0];
        } else {
            return null;
        }
    }

    private function Log($descricao, $sintaxe){
        try {
            $sql = "INSERT INTO logmysql (descricao, sintaxe) VALUES ('" . str_replace("'","`", $descricao) . "','" . str_replace("'","`", $sintaxe) . "')";
            $state = $this->con->prepare($sql);
            $state->execute();
        } catch(\PDOException $ex){
            return array('sucess'=>false, 'feedback'=>$ex->getMessage());
        }
    }

    public function setObject($Object, $Values, $Exits = true){
        if (is_object($Object)){
            if (count((array)$Values)>0){
                foreach ($Values as $in => $va){
                    if (property_exists($Object,$in) || $Exits){
                        $Object->$in = $Values->$in;
                    }
                }
            }
        }
    }
}