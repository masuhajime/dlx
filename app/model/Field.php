<?php

namespace app\model;

class Field {
    private $events = array();
    private $monsters = array();
    
    private static $instance = null;
    
    private function __construct() {}
    
    /**
     * @return \app\model\Field
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function hasMonster()
    {
        if (0 === count($this->monsters)) {
            return false;
        }
        foreach ($this->monsters as $monster) {
           if ($monster->isAlive()) {
               return true;
           }
        }
        return false;
    }
    
    public function battleMonster()
    {
        if (0 === count($this->monsters)) {
            return false;
        }
        foreach ($this->monsters as $monster) {
           if ($monster->isAlive()) {
               echo $monster.PHP_EOL;
               $monster->battle();
               return true;
           }
        }
        return false;
    }
    
    private function init()
    {
        $this->monsters = array();
        $this->events = array();
    }

    public function update()
    {
        $this->init();
        $html = \app\helper\DlxAccesser::getMapHtml();
        if (false !== strpos($html, 'var monsterList =')) {
            $this->parseMonsters($html);
            return;
        } else if (false !== strpos($html, 'var object = [')) {
            // こっちはフィールドトップ画面
            $this->parseFieldEvents($html);
            return;
        }
        throw new \app\helper\exception\UnexpectedResponse("failed to get field/monster");
    }
    
    public function touchMonsterEvent()
    {
        foreach ($this->events as $event) {
            echo $event.PHP_EOL;
            if ($event->isUntouchedMonsterEvent()) {
                $event->touch();
                return true;
            }
        }
        return false;
    }
    
    /**
     * 全てのモンスターと戦闘し、次のマップへ進めるかどうか
     * @return bool
     */
    public function canReset()
    {
        if (0 === count($this->events)) {
            throw new \RuntimeException("no Field event");
        }
        foreach ($this->events as $event) {
            if($event->isUntouchedMonsterEvent) {
                return false;
            }
        }
        return true;
    }
    
    public function reset()
    {
        \app\helper\DlxAccesser::fieldReset();
    }

    private function parseFieldEvents($html)
    {
        $m = array();
        if (0 === preg_match('/var object = \[(.*)\];/i', $html, $m)) {
            return false;
        }
        $event_data_json = '['.$m[1].']';
        $event_data = json_decode($event_data_json, true);
        //var_dump($event_data);
        foreach ($event_data as $data) {
            $e = new FieldEvent($data['objectID'], $data['eventID'], $data['param'],
                    intval($data['checkFlag']) === 1);
            array_push($this->events, $e);
        }
        //var_dump($this->events);
        return true;
        
    }
    private function parseMonsters($html)
    {
        $m = array();
        if (0 === preg_match('/var monsterList = \{(.*)\};/i', $html, $m)) {
            return false;
        }
        $monser_data_json = '{'.$m[1].'}';
        $monster_data = json_decode($monser_data_json, true);
        //var_dump($monster_data);
        foreach ($monster_data as $monster_id => $data) {
            $m = new Monster($monster_id);
            $m->setBoxDrop($data['drop']);
            array_push($this->monsters, $m);
        }
        //var_dump($this->monsters);
        return true;
    }
}

