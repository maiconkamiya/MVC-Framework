<?php
namespace criativa\helper;

class Console {
    public static function printer($text){
        echo date('d-m-Y H:i:s') . " | {$text} \n";
    }
}