<?php

namespace app\model;
use app\helper\Logger;

class Monster {
    private $id;
    private $box_drop = null;
    private $defeated = null;
    
    private $money = null;
    private $exp = null;
    
    public function __construct($id)
    {
        $this->id = $id;
        $this->defeated = false;
    }
    
    public static function createInstanceFromArray(array $data)
    {
        if (!isset($data['id'])) {
            Logger::warning('data has no id', __LINE__, __FILE__);
            throw new \RuntimeException('data has no id');
        }
        $monster = new self($data['id']);
        if (isset($data['drop'])) {
            $monster->setBoxDrop($data['drop']);
        }
        if (isset($data['exp'])) {
            $monster->setExp($data['exp']);
        }
        if (isset($data['money'])) {
            $monster->setMoney($data['money']);
        }
        return $monster;
    }
    
    public function __toString() {
        return "monster id:{$this->id} defeated:"
        .($this->defeated?"true ":"false")
        ." drop:".(is_null($this->box_drop)?"null":$this->box_drop)
        ." money:{$this->money}"
        ." exp:{$this->exp}"
        ;
    }

    public function toString() {
        return $this.null;
    }

    public function isAlive() {
        return !$this->defeated;
    }
    
    public function battle(PlayerHandling $player)
    {
        $this->defeated = true;
        return \app\helper\DlxAccesser::battleMonster($player->getViewerData(), $this);
    }
    
    public function getId() {return $this->id;}
    public function setBoxDrop($int) {$this->box_drop = $int;}
    public function getBoxDrop() {return $this->box_drop;}    
    public function setMoney($money) {$this->money = $money;}
    public function setExp($exp) {$this->exp = $exp;}
}
