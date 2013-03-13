<?php
namespace app\model;

class FieldEvent {
    
    const MONSTER     = 0;
    const BOX         = 1;
    const COIN        = 2;
    const FP          = 3;//技ポイントの回復
    const JOBEX       = 4;//熟練度
    const BOSS_NORMAL = 5;//召喚
    // EVENT_ 前置詞はイベントとするか
    const EVENT_MAGIC_MACHINE = 101;//魔導器兵イベントid
    
    private static $EVENT_NAME = array(
        self::MONSTER => 'monster', 
        self::BOX=> 'box', 
        self::COIN => 'coin', 
        self::FP => 'fp', 
        self::JOBEX => 'jobex', 
        self::BOSS_NORMAL => 'boss',
        self::EVENT_MAGIC_MACHINE => 'event'
            );
    
    // タッチ位置
    //  0  1
    // 2    3
    //   4
    private $event_unique_id;//たぶんdbに割り当てられているidか何か
    private $id;//画面の位置にあたるid
    private $event_id;//イベントの種類
    private $param;// coinの値や敵の数等
    private $touched;//実行済みのイベントか
    
    public function __construct($event_unique_id, $id, $event_id, $param, $touched) {
        $this->event_unique_id = intval($event_unique_id);
        $this->id = intval($id);
        $this->event_id = intval($event_id);
        $this->param = $param;
        $this->touched = $touched;
    }
    
    public static function createInstanceFromArray(array $data)
    {
        if (!isset($data['objectID']) || !isset($data['eventID']) 
            || !isset($data['param']) || !isset($data['checkFlag'])) {
            Logger::warning('invalid data', __LINE__, __FILE__);
            throw new \RuntimeException('field event invalid data array');
        }
        $fe = new self($data["id"], $data['objectID'], $data['eventID'], $data['param'], 
                1 === intval($data['checkFlag']));
        return $fe;
    }
    
    public function __toString() {
        return "id:{$this->id} ".
                "event_id:".sprintf("%-7s", self::$EVENT_NAME[$this->event_id])."({$this->event_id})".
                " touched:".($this->touched?"TRUE ":"false").
                " param:{$this->param}";
    }
    
    public function toString()
    {
        return $this.null;
    }

    public function isUntouchedMonsterEvent()
    {
        return !$this->touched && $this->event_id === self::MONSTER;
    }
    
    private function execMagicMachineEvent(PlayerHandling $player)
    {
        sleep(4);
        \app\helper\DlxAccesser::getEventMagicMachineWriteResult($player->getViewerData(), $player, $this->event_unique_id);
    }

    public function touch(PlayerHandling $player)
    {
        $this->touched = true;
        if (self::EVENT_MAGIC_MACHINE == $this->event_id) {
            $this->execMagicMachineEvent($player);
            return;
        }
        return \app\helper\DlxAccesser::touchFieldEvent($player->getViewerData(), $this);
    }
    
    public function isSpecialEvent()
    {
        $ids = self::getConstantEventIds();
        return in_array($this->event_id, $ids);
    }
    
    private static function getConstantEventIds()
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = $class->getConstants();
        $r = array();
        foreach ($constants as $constant_key => $value) {
            if (false === strpos($constant_key, 'EVENT_')) {
                continue;
            }
            $r[$constant_key] = $value;
        }
        return $r;
    }

    public function getEventUniqueId(){return $this->event_unique_id;}
    public function getId(){return $this->id;}
    public function getEventId(){return $this->event_id;}
    public function getParam(){return $this->param;}
    public function isTouched(){return $this->touched;}
    public function setTouched($bool){$this->touched = $bool;}
}
