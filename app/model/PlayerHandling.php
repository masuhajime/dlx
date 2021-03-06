<?php

namespace app\model;
use app\helper\Logger;

class PlayerHandling extends Player{
    private $stamina;
    private $milk_num;
    private $capture_count;

    private $exp = 0;
    private $money = 0;
    private $monster_win = 0;
    
    /**
     * @var \app\model\Field 
     */
    private $field = null;
    
    private $viewer_data = null;

    public function __construct($viewer_data) {
        $this->viewer_data = $viewer_data;
        $this->id = self::getIdFromViewerData($viewer_data);
        $this->field = new Field();
        // もっと適切な場所に移動したいなー
        $this->field->setAssignedTouchEvents(array(\app\model\FieldEvent::MONSTER));
        if (\CONFIG_USER::FIELD_EVENT_BOX_OPEN) {
            $this->field->addAssignedTouchEvents(FieldEvent::BOX);
        }
        $this->field->addAssignedTouchEvents(FieldEvent::JOBEX);
        parent::__construct();
    }
    
    static private function getIdFromViewerData($viewer_data)
    {
        $separates = array('%2C', ',');
        foreach ($separates as $sepa) {
            if (false !== strpos($viewer_data, $sepa)) {
                list($id) = explode($sepa, $viewer_data);
                return $id;
            }
        }
        throw new \RuntimeException('invalid viewer_data: '.$viewer_data);
    }
    
    public function getStamina()
    {
        $this->stamina = \app\helper\DlxAccesser::getStamina($this->viewer_data);
        return $this->stamina;
    }
    
    public function battleCommand($command_num)
    {
        \app\helper\DlxAccesser::doBattleCommand($this->getViewerData(), $command_num);
    }
    
    /**
     * 
     * @return \app\model\LeagueBattle\BattleHistory
     * @throws \RuntimeException
     */
    public function getBattleHistory($team_no, $time = null)
    {
        if (is_null($time)) {
            $time = time();
        }
        $html = \app\helper\DlxAccesser::getBattleHistory($this->viewer_data, $team_no, $time);
        // 履歴がない場合*
        // function getDefaultHistory() {	return false; }
        $regexp_false = "[ \t]*function[ \t]+getDefaultHistory\(\)[ \t]+\{[ \t]+return[ \t]+false;[ \t]+\}";
        if (0 < preg_match("/{$regexp_false}/", $html)) {
            return LeagueBattle\BattleHistory::createFronJson(array());
        }
        // 履歴がある場合*
        // function getDefaultHistory() {	return (.*)unt":1}]; }
        $regexp = "[ \t]*function[ \t]+getDefaultHistory\(\)[ \t]+\{[ \t]+return[ \t]+(\[.*\]);[ \t]+\}";
        $m = array();
        if (0 == preg_match("/{$regexp}/", $html, $m)) {
            throw new \RuntimeException("battle history regexp does not match.");
        }
        $data = json_decode("".$m[1]."", true);
        if (is_null($data)) {
            throw new \RuntimeException("getHeaderStatus json_decoded is null");
        }
        return LeagueBattle\BattleHistory::createFronJson($data);
    }

    public function getHeaderStatus()
    {
        $json_string = \app\helper\DlxAccesser::getHeaderStatus($this->getViewerData());
        $data = json_decode($json_string, true);
        if (is_null($data)) {
            throw new \RuntimeException("getHeaderStatus json_decoded is null");
        }
        return new HeaderStatus($data);
    }
    
    public function updateAllInfo()
    {
        list($type, $html) = \app\helper\DlxAccesser::getMapHtml($this->viewer_data);
        
        $this->field->update($html);
        if ('MAP' === $type) {
            $this->parseCaptureCount($html);
            $this->parseMilkNum($html);
            $this->parseUserData($html);
        }
    }
    
    /**
     * 
     * @return \app\model\LeagueBattle\BattleStatus
     */
    public function getBattleStatus()
    {
        $json_string = \app\helper\DlxAccesser::getBattleStatus($this->getViewerData(), $this);
        return LeagueBattle\BattleStatus::createFromJson($json_string);
    }
    
    private function parseUserData($html)
    {
        $m = array();
        if (0 === preg_match('/var userData[ \t]*=[ \t]*\{(.*)\};/i', $html, $m)) {
            Logger::warning('failed to parse userdata', __LINE__, __FILE__);
            return;
        }
        $user_data = json_decode('{'.$m[1].'}', true);
        $this->exp = $user_data['exp'];
        $this->money = $user_data['money'];
        $this->monster_win = $user_data['monsterWin'];
    }
    
    private function parseCaptureCount($html)
    {
        //var captureCount = '2';
        if (0 < preg_match('/var captureCount[ \t]*=[ \t]*\'MAX\'/', $html)) {
            // 捕獲数は最大5
            $this->capture_count = 5;
            return;
        }
        $m = array();
        if (0 === preg_match('/var captureCount[ \t]*=[ \t]*\'([0-9]+)\';/', $html, $m)) {
            Logger::warning('failed to parse capture count', __LINE__, __FILE__);
            $this->capture_count = 0;
            return;
        }
        $this->capture_count = intval($m[1]);
    }
    
    public function getFP()
    {
        $html = \app\helper\DlxAccesser::getPlayerProfile($this, $this->getId());
        if (0 == preg_match("/function getUserFP\(\)[ \t]+\{[ \t]+return[ \t]+\"(\d+)\";[ \t]+\}/", $html, $match)) {
            throw new \RuntimeException("FP not found at profile page");
        }
        return intval($match[1]);
    }
    
    private function parseMilkNum($html)
    {
        // parse milk num
        // var recoveryItemNum = 61;
        $m = array();
        if (0 === preg_match('/var recoveryItemNum[ \t]*=[ \t]*([0-9]+);/', $html, $m)) {
            Logger::warning('failed to parse milk num(milk num set to 0)', __LINE__, __FILE__);
            $this->milk_num = 0;
            return;
        }
        $this->milk_num = intval($m[1]);
    }
    
    public function battleMonster()
    {
        $this->field->battleMonster($this);
    }
    
    public function getCapturedMonsters()
    {
        $json = \app\helper\DlxAccesser::getCaptureMonsters($this->viewer_data);
        
    }
    
    public function fieldReset()
    {
        $this->field->reset($this);
    }
    
    public function getFieldBoss()
    {
        $this->field->getFieldBoss($this);
    }
    
    public function touchAssignedEvent()
    {
        return $this->field->touchAssignedEvent($this);
    }

    //public function setMilkNum($num) {$this->milk_num = $num;}
    public function getCaptureCount() {return $this->capture_count;}
    public function setCaptureCount($count) {$this->capture_count = $count;}
    public function getMilkNum() {return $this->milk_num;}
    public function useMilk() {\app\helper\DlxAccesser::useMilk($this->viewer_data);}
    public function getExp() {return $this->exp;}
    public function getMoney() {return $this->money;}
    public function getMonsterWin() {return $this->monster_win;}
    public function getField() {return $this->field;}
    public function getViewerData() {return $this->viewer_data;}
}
