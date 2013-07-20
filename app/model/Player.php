<?php

namespace app\model;

class Player {
    private $name = false;//使う機会あるのかな...
    protected $id;
    private $fp = 0;// Fp
    private $hp = 0;// HP
    private $status = 0;// バトル中にある謎のステータス
    
    private $attack = 0;
    private $hissatu = 0;
    
    public function __construct() {
        ;
    }
    
    public function getId(){return $this->id;}
    public function setId($id){$this->id = $id;}
    
    public function getFP(){return $this->fp;}
    public function setFP($v){$this->fp = $v;}
    
    public function getHP(){return $this->hp;}
    public function setHP($v){$this->hp = $v;}
    
    public function getStatus(){return $this->status;}
    public function setStatus($v){$this->status = $v;}
    public function isAlive(){return $this->hp > 0;}
    
    public function getAttack(){return $this->attack;}
    public function setAttack($v){$this->attack = $v;}
    
    public function getHissatu(){return $this->hissatu;}
    public function setHissatu($v){$this->hissatu = $v;}
    
}
