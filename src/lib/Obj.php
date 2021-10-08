<?php

namespace criativa\lib;

class Obj {
    public function __construct($method = null, $exists = true, $type = 'raw'){
        if ($method == 'POST'){
            switch ($type){
                case 'raw':

                    foreach ($_POST as $i => $v){
                        if (isset($this->{$i}) || $exists){
                            $this->{$i} = $v;
                        }
                    }

                    if (isset($_FILES)) {
                        foreach ($_FILES as $ind => $val) {
                            if ($exists || isset($this->$ind)) {
                                $this->$ind = $val;
                            }
                        }
                    }

                    break;
                case 'json':

                    $json = file_get_contents('php://input');
                    if (!empty($json)){
                        $obj = json_decode($json);
                        foreach ($obj as $i => $v){
                            if (isset($this->{$i}) || $exists){
                                $this->{$i} = $v;
                            }
                        }
                    }

                    break;
            }
        }
    }
}