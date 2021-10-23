<?php

namespace criativa\helper;

class File {
    private $File;
    private $Image;
    private $NewImage;
    private $Type;
    private $Width;
    private $Height;
    private $OWidth;
    private $OHeight;
    private $Dir;

    public static function existsImageType($file){
        if (file_exists($file . '.jpg')){
            return $file . '.jpg';
        } elseif (file_exists($file . '.jpeg')){
            return $file . '.jpeg';
        } elseif (file_exists($file . '.png')){
            return $file . '.png';
        } elseif (file_exists($file . '.bmp')){
            return $file . '.bmp';
        } elseif (file_exists($file . '.gif')){
            return $file . '.gif';
        }
    }

    public function resizeImage($dir, $filename, $type, $max_width = 1024, $max_height = 746)
    {

        $this->Type = $type;
        $this->File = $filename;
        $this->Dir = $dir;

        $this->getSize();

        if (!is_null($max_height)) {
            if ($this->Height > $max_height) {
                $this->Width = ($max_height / $this->Height) * $this->Width;
                $this->Height = $max_height;
            }
        }

        if (!is_null($max_width)) {
            if ($this->Width > $max_width) {
                $this->Height = ($max_width / $this->Width) * $this->Height;
                $this->Width = $max_width;
            }
        }

        $this->imageCreateNew();
        $this->imageCreate();
        $this->imageEdit();
        return $this->saveImage();
    }

    private function imageCreate() {
        switch ($this->Type) {
            case "image/jpg":
            case "image/jpeg":
                $this->Image = imagecreatefromjpeg($this->File);
                break;
            case "image/gif":
                $this->Image = imagecreatefromgif($this->File);
                break;
            case "image/png":
                $this->Image = imagecreatefrompng($this->File);

                break;
            default:
                exit();
                break;
        }
    }

    private function imageCreateNew() {
        $this->NewImage = imagecreatetruecolor($this->Width, $this->Height);

        if ($this->Type == "image/png"){
            imagealphablending($this->NewImage, false);
            $colorTransparent = imagecolorallocatealpha($this->NewImage, 0, 0, 0, 127);
            imagefill($this->NewImage, 0, 0, $colorTransparent);
            imagesavealpha($this->NewImage, true);
        }
    }

    private function imageEdit() {
        imagecopyresampled($this->NewImage, $this->Image, 0, 0, 0, 0, $this->Width, $this->Height, $this->OWidth, $this->OHeight);
    }

    private function getSize() {
        list($this->OWidth, $this->OHeight) = getimagesize($this->File);

        $this->Width = $this->OWidth;
        $this->Height = $this->OHeight;
    }

    private function saveImage() {
        ob_start();
        if ($this->Type == 'image/gif') {
            imagegif($this->NewImage, $this->Dir);
        } elseif ($this->Type == 'image/png') {
            imagepng($this->NewImage, $this->Dir);
        } else {
            imagejpeg($this->NewImage, $this->Dir);
        }
        return ob_get_contents();
        ob_end_clean();
    }

    public static function ExportCSV($name, $dados){
        header('Content-disposition: attachment; filename='.str_replace('/','-',$name).'.csv');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

        if (is_object($dados))
            $dados = (array) $dados;

        if (empty($dados))
            die("Não possui dados a serem exportados");

        $header = array();
        foreach ($dados[0] as $i => $v){
            $header[] = $i;
        }

        echo implode("\t",$header) . "\n";

        foreach ($dados as $r){
            $row = array();
            foreach ($r as $i => $v){
                $row[] = $v;
            }
            echo implode("\t",$row) . "\n";
        }
    }

    public static function ExportXLS($name, $dados){
        header('Content-disposition: attachment; filename='.str_replace('/','-',$name).'.xls');
        header("Content-type: application/vnd.ms-excel");

        if (is_object($dados))
            $dados = (array) $dados;

        if (empty($dados))
            die("Não possui dados a serem exportados");

        echo "<table>";
        echo "<tr>";
        foreach ($dados[0] as $i => $v){
            echo "<th>{$i}</th>";
        }
        echo "</tr>";

        foreach ($dados as $r){
            echo "<tr>";
            foreach ($r as $i => $v){
                echo "<td>{$v}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}