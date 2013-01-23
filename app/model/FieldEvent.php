<?php
namespace app\model;

class FieldEvent {
    
    const MONSTER     = 0;
    const BOX         = 1;
    const COIN        = 2;
    const FP          = 3;//?
    const JOBEX       = 4;//熟練度
    const BOSS_NORMAL = 5;//召喚
    
    private static $EVENT_NAME = array('monster', 'box', 'coin', 'fp?', 'jobex', 'boss');
    
    // タッチ位置
    //  0  1
    // 2    3
    //   4
    private $id;
    private $event_id;
    // coinの値や敵の数等
    private $param;
    private $touched;//実行済みのイベントか
    
    public function __construct($id, $event_id, $param, $touched) {
        $this->id = intval($id);
        $this->event_id = intval($event_id);
        $this->param = $param;
        $this->touched = $touched;
    }
    
    public function __toString() {
        return "id:{$this->id}, event_id:".
                sprintf("%7s", self::$EVENT_NAME[$this->event_id]).
                ", touched:".($this->touched?"true":"false");
    }

        public function isUntouchedMonsterEvent()
    {
        return !$this->touched && $this->event_id === self::MONSTER;
    }
    
    public function touch()
    {
        $this->touched = true;
        return \app\helper\DlxAccesser::touchFieldEvent($this);
    }
    
    public function getId(){return $this->id;}
    public function getEventId(){return $this->event_id;}
    public function getParam(){return $this->param;}
    public function isTouched(){return $this->touched;}
}