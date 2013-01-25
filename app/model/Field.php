<?php

namespace app\model;

class Field {
    private $events = array();
    private $monsters = array();
    
    private $assigned_touch_events = array(FieldEvent::MONSTER);
    
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
               \app\helper\Logger::info($monster->toString());
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

    public function update($html)
    {
        if (0 < preg_match('/var monsterList[ \t]*=/', $html)) {
            // モンスター出現中の画面
            $this->parseMonsters($html);
            return;
        } else if (0 < preg_match('/var object[ \t]*=[ \t]*\[/', $html)) {
            // こっちはフィールドトップ画面
            $this->init();
            $this->parseFieldEvents($html);
            return;
        }
        \app\helper\Logger::alert("failed to get field/monster", __LINE__, __FILE__);
        throw new \app\helper\exception\UnexpectedResponse("failed to get field/monster");
    }
    
    public function touchAssignedEvent()
    {
        foreach ($this->events as $event) {
            if (!$event->isTouched() 
                && in_array($event->getEventId(), $this->assigned_touch_events)) {
                \app\helper\Logger::info($event->toString().' TOUCH');
                $event->touch();
                return true;
            }
            \app\helper\Logger::info($event->toString());
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
        if (0 === preg_match('/var object[ \t]*=[ \t]*\[(.*)\];/i', $html, $m)) {
            return false;
        }
        $event_data_json = '['.$m[1].']';
        $event_data = json_decode($event_data_json, true);
        //var_dump($event_data);
        foreach ($event_data as $data) {
            $fe = FieldEvent::createInstanceFromArray($data);
            array_push($this->events, $fe);
        }
        //var_dump($this->events);
        return true;
        
    }
    private function parseMonsters($html)
    {
        $m = array();
        if (0 === preg_match('/var monsterList[ \t]*=[ \t]*\{(.*)\};/i', $html, $m)) {
            return false;
        }
        $monser_data_json = '{'.$m[1].'}';
        $monster_data = json_decode($monser_data_json, true);
        //var_dump($monster_data);
        foreach ($monster_data as $monster_id => $data) {
            $m = \app\model\Monster::createInstanceFromArray($data);
            array_push($this->monsters, $m);
        }
        //var_dump($this->monsters);
        return true;
    }
    
    /**
     * タッチするイベントの種類
     */
    public function setAssignedTouchEvents(array $event_types) {$this->assigned_touch_events = $event_types;}
}

