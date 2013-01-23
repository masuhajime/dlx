<?php

namespace app\model;

class Monster {
    private $id;
    private $box_drop = null;
    private $defeated = null;
    
    public function __construct($id)
    {
        $this->id = $id;
        $this->defeated = false;
    }
    
    public function __toString() {
        return "monster id:{$this->id}, defeated:"
        .($this->defeated?"true ":"false")
        ." drop:".(is_null($this->box_drop)?"null":$this->box_drop);
    }


    public function isAlive() {
        return !$this->defeated;
    }
    
    public function getId() {return $this->id;}
    public function setBoxDrop($int) {$this->box_drop = $int;}
    public function getBoxDrop() {return $this->box_drop;}

    public function battle()
    {
        $this->defeated = true;
        return \app\helper\DlxAccesser::battleMonster($this);
    }
}
