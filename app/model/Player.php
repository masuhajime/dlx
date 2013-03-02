<?php

namespace app\model;

class Player {
    private $name;
    protected $id;
    private $bp;
    
    public function __construct() {
        ;
    }
    
    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}
}
