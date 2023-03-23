<?php

namespace App\Controller;

class Senderfeed
{
    private $title;
    private $description;

    public function SetTitles($title){
        return $this->title;
    }
    public function SetDescriptions($description){
        return $this->description;
    }
    public function GetTitles(){
        return $this->title;
    }
    public function GetDescriptions(){
        return $this->description;
    }


}