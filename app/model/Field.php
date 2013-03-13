<?php

namespace app\model;

class Field {
    private $events = array();
    private $monsters = array();
    
    private $assigned_touch_events = array(FieldEvent::MONSTER);
    
    public function isFieldBossAppear()
    {
        foreach ($this->events as $event) {
            if (!$event->isTouched()
                && FieldEvent::BOSS_NORMAL === $event->getEventId()) {
                return true;
            }
        }
        return false;
    }
    
    public function isMonsterAppear()
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
    
    public function battleMonster(PlayerHandling $player)
    {
        if (0 === count($this->monsters)) {
            return false;
        }
        foreach ($this->monsters as $monster) {
           if ($monster->isAlive()) {
               \app\helper\Logger::info($monster->toString());
               $monster->battle($player);
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
    
    public function touchAssignedEvent(PlayerHandling $player)
    {
        foreach ($this->events as $event) {
            // 召喚の検証のために確保したいのでここで終わらせる処理
            /*
            if (!$event->isTouched() && $event->getEventId() === FieldEvent::BOSS_NORMAL) {
                \app\helper\Logger::alert($event->toString(), __LINE__, __FILE__);
                exit;
            }*/
            if (!$event->isTouched() 
                && (
                    in_array($event->getEventId(), $this->assigned_touch_events)
                    || $event->isSpecialEvent()
                    )
            ) {
                \app\helper\Logger::info($event->toString().' TOUCH');
                $event->touch($player);
                return true;
            }
            \app\helper\Logger::info($event->toString());
        }
        return false;
    }
    
    /**
     * フィールドに出現するボスは仕組み上タッチ処理が要らなく
     * 捕獲実行をした際に減るので通常のタッチ処理とは別になっているので
     * 外からタッチ済みとできるようにする必要がある
     */
    public function setFieldBossEventAsTouched()
    {
        foreach ($this->events as $event) {
            if (FieldEvent::BOSS_NORMAL === $event->getEventId()) {
                $event->setTouched(true);
            }
        }
    }
    
    public function reset(PlayerHandling $player)
    {
        \app\helper\DlxAccesser::fieldReset($player->getViewerData());
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
    
    public function getFieldBoss(PlayerHandling $player)
    {
        $html = \app\helper\DlxAccesser::getFieldBossHtml($player->getViewerData());
        $m = array();
        if (false !== strpos($html, '召喚獣の情報取得に失敗しました')) {
            return false;
        }
        if (0 === preg_match('/var bossData[ \t]*=[ \t]*\{(.*)\};/i', $html, $m)) {
            throw new \app\helper\exception\UnexpectedResponse('unexpect html from field boss page.');
        }
        $field_boss_data = json_decode('{'.$m[1].'}', true);
        $fb = FieldBoss::createInstanceFromArray($field_boss_data);
        return $fb;
    }
    
    /**
     * タッチするイベントの種類
     */
    public function setAssignedTouchEvents(array $event_types) {$this->assigned_touch_events = $event_types;}
    public function addAssignedTouchEvents($event_type) {array_push($this->assigned_touch_events, $event_type);}
}

