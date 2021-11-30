<?php
/**
 * Created by PhpStorm.
 * User: Vendas
 * Date: 01/06/2017
 * Time: 08:47
 */

namespace criativa\lib;

use criativa\helper\Str;

class Model extends Config {

    protected $con;

    private $db;

    public function __construct($driver = 'mysql', $param = null){
        $this->db = self::$dbname;

        if (!defined('TIME_ZONE')){
            define('TIME_ZONE', '-04:00');
        }

        try {

            switch($driver){
                case 'mysql':
                    $this->con = new \PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$user, self::$pwd);
                    $this->con->exec("set names " . self::$charset);
                    $this->con->exec("SET GLOBAL sql_mode=''");
                    $this->con->exec("SET GLOBAL time_zone='".TIME_ZONE."'");
                    break;
                case 'firebird':
                    $this->con = new \PDO("firebird:dbname=" . $param->dbname . ";host=" . $param->host . ";charset=". self::$charset,$param->user,$param->pass);
                    break;
                case 'oracle':
                    $this->con = new \PDO("oci:dbname=".$param->tns,$param->user,$param->pass);
                    break;
                case 'mdb':
                    //$this->con = new \PDO("odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq=C:\wamp\www\ERPEmpresarial\db\inforlub.mdb;Uid=Admin;Pwd=masterkey");
                    break;
            }

            $this->con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $ex){
            throw new \Exception($ex->getMessage());
            //echo json_encode(array('sucess' => false, 'feedback' => $ex->getMessage() . ' host:' . self::$host . ' db:' . self::$dbname));
            //exit();
        }
    }

    public function Select($sql){
        try {
            $state = $this->con->prepare($sql);
            $state->execute();
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);
            //die($ex->getMessage() . ' ' . $sql);
            throw new \Exception($ex->getMessage());
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

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage(), 'sql' => $sql);
        }
    }

    public function Query($sql){
        try {
            return array('sucess'=>$this->con->query($sql));
        } catch(\PDOException $ex){
            $this->Log($ex->getMessage(), $sql);

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage() .' '. $sql);
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

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage() . $sql);
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

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage(). $sql);
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

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage() . $sql);
        }
        return array('sucess'=>true, 'feedback'=> '');
    }

    public function Last($table){
        try {
            $state = $this->con->prepare("SELECT last_insert_id() as last FROM {$table}");
            $state->execute();
            $state = $state->fetchObject();
        } catch(\PDOException $ex){

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage() . $table);
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
            $prefix = self::$prefix;

            $sql = "INSERT INTO {$prefix}logmysql (descricao, sintaxe) VALUES ('" . str_replace("'","`", $descricao) . "','" . str_replace("'","`", $sintaxe) . "')";
            $state = $this->con->prepare($sql);
            $state->execute();
        } catch(\PDOException $ex){

            throw new \Exception($ex->getMessage());
            //return array('sucess'=>false, 'feedback'=>$ex->getMessage());
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

    public function existsTable($elent){
        $query = $this->First($this->Select("SELECT count(1) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$this->db}' AND TABLE_NAME = '{$elent}'"));
        return $query->count == 0 ? false : true;
    }
    public function existsRotina($elent){
        $query = $this->First($this->Select("SELECT count(1) as count FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA = '{$this->db}' AND ROUTINE_NAME = '{$elent}'"));
        return $query->count == 0 ? false : true;
    }
    public function existsTrigger($elent){
        $query = $this->First($this->Select("SELECT count(1) as count FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA = '{$this->db}' AND TRIGGER_NAME = '{$elent}'"));
        return $query->count == 0 ? false : true;
    }
    public function getSizeDB(){
        $query = $this->First($this->Select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) as sizedb FROM information_schema.tables WHERE table_schema = '{$this->db}';"));
        return $query->sizedb;
    }

    protected function where($obj){
        $sql = "";
        foreach ($obj as $i => $v){

            if (isset($v['operador']) && isset($v['valor'])){
                if (!empty($v['valor']) || $v['operador'] == 'empty'){

                    switch ($v['operador']){
                        case 'equals':
                            $sql .= " AND t.{$i} = '{$v['valor']}'";
                            break;
                        case 'noequals':
                            $sql .= " AND t.{$i} != '{$v['valor']}'";
                            break;
                        case 'like':
                            $sql .= " AND t.{$i} LIKE '{$v['valor']}%'";
                            break;
                        case 'nolike':
                            $sql .= " AND t.{$i} NOT LIKE '{$v['valor']}%'";
                            break;
                        case 'range':
                            $sql .= " AND (t.{$i} BETWEEN '{$v['valor'][0]}' AND '{$v['valor'][1]}')";
                            break;
                        case 'empty':
                            $sql .= " AND t.{$i} = ''";
                            break;
                    }
                }
            } else {

                if (!empty($v) || is_numeric($v)){
                    if ($v == 'isNull'){
                        $sql .= " AND t.{$i} IS NULL ";
                    } elseif ($v == 'isNotNull'){
                        $sql .= " AND t.{$i} IS NOT NULL ";
                    } elseif ($v == 'isEmpty'){
                        $sql .= " AND t.{$i} = '' ";
                    } elseif ($v == 'isNotEmpty'){
                        $sql .= " AND t.{$i} != '' ";
                    } elseif (is_array($v)){
                        if (!empty($v[0]) && !empty($v[1])){
                            $sql .= " AND t.{$i} BETWEEN '".Str::convertData($v[0])." 00:00:00' AND '".Str::convertData($v[1])." 23:59:59' ";
                        }
                    } elseif (strpbrk($v, ',')){
                        $sql .= " AND t.{$i} IN({$v}) ";
                    } elseif (strpbrk($v, '%')){
                        $sql .= " AND t.{$i} LIKE '{$v}%' ";
                    } elseif (substr($v, 0, 1) == '!'){
                        $sql .= " AND t.{$i} != '".substr($v, 1)."' ";
                    } elseif (substr($v, 0, 2) == '<='){
                        $sql .= " AND t.{$i} <= ".substr($v, 2)." ";
                    } elseif (substr($v, 0, 2) == '>='){
                        $sql .= " AND t.{$i} >= ".substr($v, 2)." ";
                    } elseif (substr($v, 0, 1) == '<'){
                        $sql .= " AND t.{$i} < ".substr($v, 1)." ";
                    } elseif (substr($v, 0, 1) == '>'){
                        $sql .= " AND t.{$i} > ".substr($v, 1)." ";
                    } else {
                        $sql .= " AND t.{$i} = '{$v}'";
                    }
                }
            }
        }

        return $sql;
    }
}