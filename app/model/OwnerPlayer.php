<?php

namespace app\model;
use app\helper\Logger;

class OwnerPlayer extends Player{
    private $stamina;
    private $milk_num;
    private $capture_count;

    private $exp = 0;
    private $money = 0;
    private $monster_win = 0;

    private static $instance = null;

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @return \app\model\OwnerPlayer
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getStamina()
    {
        $this->stamina = \app\helper\DlxAccesser::getStamina();
        return $this->stamina;
    }
    
    public function updateAllInfo()
    {
        list($type, $html) = \app\helper\DlxAccesser::getMapHtml();
        
        $field = \app\model\Field::getInstance();
        $field->update($html);
        if ('MAP' === $type) {
            $this->parseCaptureCount($html);
            $this->parseMilkNum($html);
            $this->parseUserData($html);
        }
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
        $m = array();
        if (0 === preg_match('/var captureCount[ \t]*=[ \t]*\'([0-9]+)\';/', $html, $m)) {
            Logger::warning('failed to parse capture count', __LINE__, __FILE__);
            $this->capture_count = 0;
            return;
        }
        $this->capture_count = intval($m[1]);
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
    
    //public function setMilkNum($num) {$this->milk_num = $num;}
    public function getCaptureCount() {return $this->capture_count;}
    public function setCaptureCount($count) {$this->capture_count = $count;}
    public function getMilkNum() {return $this->milk_num;}
    public function useMilk() {\app\helper\DlxAccesser::useMilk();}
    public function getExp() {return $this->exp;}
    public function getMoney() {return $this->money;}
    public function getMonsterWin() {return $this->monster_win;}
}
