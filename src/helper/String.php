<?php

function labelName($string)
{
    $ex = explode(' ', $string);

    if (count($ex) > 1) {
        $label = "";
        for ($i = 0; $i < count($ex); $i++) {
            $label .= (($i % 2) == 0 ? $ex[$i] : '<strong>' . $ex[$i] . '</strong>');
        }
        return $label;
    } else {
        return $string;
    }
}

function stringPath($string)
{
    return str_replace('\\', "\\\\", $string);
}

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}

function convertData($data)
{
    if (!strpos($data,'/')){
        return $data;
    }
    if (strpos($data,' ')){
        $dh = explode(' ', $data);
        $d = explode('/', $dh[0]);
        $data = $d[2] . '-' . $d[1] . '-' . $d[0] . ' ' . $dh[1];
        return $data;
    } else {
        $d = explode('/', $data);
        $data = $d[2] . '-' . $d[1] . '-' . $d[0];
        return $data;
    }

}

function convertDataOra($data)
{
    if (!strpos($data,'/')){
        return $data;
    }
    $d = explode('/', $data);
    $data = $d[0] . '-' . $d[1] . '-' . $d[2];
    return $data;
}

if (isset($_POST)) {
    foreach ($_POST as $i => $v) {
        $_POST[$i] = _clearString($v);
    }
}

function _clearString($string)
{
    //$string = htmlspecialchars($string);
    $string = str_replace("'", "`", $string);
    return $string;
}

function _cleanNotaString($value){
    return str_replace(array('.','/','-','(',')',' ',"'"),'',$value);
}

function _cleanText($value){
    return str_replace(array("'"),'',$value);
}

function _seoURL($string, $glu = "-", $toupper = false){
    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ"!@#$%&*()_-+={[}]/?;:.,´`\\\'<>';
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                ';

    $string = utf8_decode($string);
    $string = strtr($string, utf8_decode($a), $b);
    $string = strip_tags(trim($string));

    /*Agora, remove qualquer espaço em branco duplicado*/

    $string = preg_replace('/\s(?=\s)/', '', $string);

    /*Ssubstitui qualquer espaço em branco (não-espaço), com um espaço*/

    $string = preg_replace('/[\n\r\t]/', ' ', $string);

    /*Remove qualquer espaço vazio*/

    $string = str_replace(" ", $glu, $string);

    if ($toupper){
        return strtoupper(utf8_encode($string));
    } else {
        return strtolower(utf8_encode($string));
    }
}

function Month($value){
    switch ($value){
        case '01':
            return 'JAN';
            break;
        case '02':
            return 'FEB';
            break;
        case '03':
            return 'MAR';
            break;
        case '04':
            return 'APR';
            break;
        case '05':
            return 'MAY';
            break;
        case '06':
            return 'JUN';
            break;
        case '07':
            return 'JUL';
            break;
        case '08':
            return 'AUG';
            break;
        case '09':
            return 'SEP';
            break;
        case '10':
            return 'OCT';
            break;
        case '11':
            return 'NOV';
            break;
        case '12':
            return 'DEC';
            break;
    }
}


function Week($value){
    switch ($value){
        case '0':
            return 'Domingo';
            break;
        case '1':
            return 'Segunda-feira';
            break;
        case '2':
            return 'Terça-feira';
            break;
        case '3':
            return 'Quarta-feira';
            break;
        case '4':
            return 'Quinta-feira';
            break;
        case '5':
            return 'Sexta-feira';
            break;
        case '6':
            return 'Sabado';
            break;
    }
}

function MonthExtPTBR($value){
    switch ($value){
        case '01':
            return 'Janeiro';
            break;
        case '02':
            return 'Fevereiro';
            break;
        case '03':
            return 'Março';
            break;
        case '04':
            return 'Abril';
            break;
        case '05':
            return 'Maio';
            break;
        case '06':
            return 'Junho';
            break;
        case '07':
            return 'Julho';
            break;
        case '08':
            return 'Agosto';
            break;
        case '09':
            return 'Setembro';
            break;
        case '10':
            return 'Outubro';
            break;
        case '11':
            return 'Novembro';
            break;
        case '12':
            return 'Dezembro';
            break;
    }
}


function parseToXML($htmlStr)
{
    $xmlStr=str_replace('<','&lt;',$htmlStr);
    $xmlStr=str_replace('>','&gt;',$xmlStr);
    $xmlStr=str_replace('"','&quot;',$xmlStr);
    $xmlStr=str_replace("'",'&#39;',$xmlStr);
    $xmlStr=str_replace("&",'&amp;',$xmlStr);
    return $xmlStr;
}

function stringTab($string, $len = 1, $invert = false){

    //$string = strtoupper($string, " ");

    $lenght = strlen($string);

    if ($lenght > $len)
        return substr($string, 0, $len);
    else {
        if ($invert){
            $text = "";
            for ($i = 0; $i<($len-$lenght); $i++)
                $text .= "0";
            $text .= $string;
        } else {
            $text = $string;
            for ($i = 0; $i<($len-$lenght); $i++)
                $text .= " ";
        }
        return $text;
    }
}