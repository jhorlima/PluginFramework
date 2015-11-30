<?php
namespace Core\Service;

class Assets {

    private $publicUrl;
    private $javascriptList;
    private $cssList;

    public function __construct($publicUrl){
        $this->publicUrl = $publicUrl;
        $this->javascriptList = [];
        $this->cssList = [];
    }

    public function setJs($js){
        $this->javascriptList[] = $this->publicUrl . $js;
    }

    public function setCss($css){
        $this->cssList[] = $this->publicUrl . $css;
    }

    public function getJs(){
        return $this->javascriptList;
    }

    public function getCss(){
        return $this->cssList;
    }
}