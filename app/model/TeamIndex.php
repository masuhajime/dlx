<?php

namespace app\model;

class TeamIndex {
    
    public $ranking_attack = array();
    public $ranking_hissatu = array();
    
    public $is_parse_success = false;
    
    // 危険人物として設定する攻撃/必殺値の最大からの閾値
    private static $warning_rate = 5;
    
    private static $warning_min_hissatu = 100000;
    private static $warning_min_attack  = 100000;
    
    public static function setWarningRate($value) {
        self::$warning_rate = $value;
    }
    
    public static function setMinAttcks($attack, $hissatu) {
        self::$warning_min_attack = $attack;
        self::$warning_min_hissatu = $hissatu;
    }

    /**
     * なんか危険そうなプレイヤーのidを取得する
     * @var rate = 20 なら最大値の20%以内は危険とするか.
     */
    public function getWarningPlayer() {
        $ids = array();
        // attack
        $ids = array_merge($ids, $this->getWarningAttackId());
        // hissatu
        $ids = array_merge($ids, $this->getWarningHissatuId());
        return array_unique($ids);
    }
    
    private function getWarningHissatuId() {
        $max = $this->ranking_hissatu[0]->getHissatu();
        if (self::$warning_min_hissatu > $max) {
            return array();
        }
        $ids = array();
        $min = $max * ((100-self::$warning_rate)/100.0);
        foreach ($this->ranking_hissatu as $p) {
            $num = $p->getHissatu();
            if ($num > $min) {
                $ids[] = $p->getId();
            } else {
                break;
            }
        }
        return $ids;
    }
    
    private function getWarningAttackId() {
        $max = $this->ranking_attack[0]->getAttack();
        if (self::$warning_min_attack > $max) {
            return array();
        }
        $ids = array();
        $min = $max * ((100-self::$warning_rate)/100.0);
        foreach ($this->ranking_attack as $p) {
            $num = $p->getAttack();
            if ($num > $min) {
                $ids[] = $p->getId();
            } else {
                break;
            }
        }
        return $ids;
    }
    
    private static $static_team_no = null;
    private static $static_team_no_instance = null;
    public static function getTeamIndexOf(PlayerHandling $player, $team_num) {
        if (is_null(self::$static_team_no) || $team_num != self::$static_team_no) {
            self::$static_team_no = $team_num;
            $html = \app\helper\DlxAccesser::getTeamIndex($player->getViewerData(), $team_num);
            self::$static_team_no_instance = new self($html);
        }
        return self::$static_team_no_instance;
    }
    public static function getTeamIndexIfExist($team_num) {
        if (!is_null(self::$static_team_no_instance) && $team_num == self::$static_team_no) {
            return self::$static_team_no_instance;
        }
        return null;
    }
    
    function __construct($html) {
        if (0 == preg_match_all("/profileIndex\.php\?oid=(\d+)/", $html, $m)) {
            $this->is_parse_success = false;
            return;
        }
        $ids = $m[1];
        // 数が5で割れる数字でないとオカシイ
        if (0 != (count($ids)%5)) {
            $this->is_parse_success = false;
            return;
        }
        $team_member_num = count($ids)/5;
        $rankers_attack = array_slice($ids, $team_member_num, $team_member_num);
        $rankers_hissatu = array_slice($ids, $team_member_num*2, $team_member_num);
        
        for ($rank = 0; $rank < count($rankers_attack); $rank++) {
            $player = new \app\model\Player();
            $player->setId($rankers_attack[$rank]);
            $this->ranking_attack[$rank] = $player;
        }
        for ($rank = 0; $rank < count($rankers_hissatu); $rank++) {
            $player = new \app\model\Player();
            $player->setId($rankers_hissatu[$rank]);
            $this->ranking_hissatu[$rank] = $player;
        }
        preg_match_all("/div class=\"Best5LvValue\"\>(\d+)<\/div/", $html, $m);
        $numbers = array();
        if (count($ids) != count($m[1])/2) {
            $this->is_parse_success = false;
            return;
        }
        for($i = 1; $i < count($m[1]); $i+=2) {
            $numbers[] = $m[1][$i];
        }
        $rankers_attack_num = array_slice($numbers, $team_member_num, $team_member_num);
        $rankers_hissatu_num = array_slice($numbers, $team_member_num*2, $team_member_num);
        
        for ($rank = 0; $rank < count($rankers_attack_num); $rank++) {
            $this->ranking_attack[$rank]->setAttack($rankers_attack_num[$rank]);
        }
        for ($rank = 0; $rank < count($rankers_hissatu_num); $rank++) {
            $this->ranking_hissatu[$rank]->setHissatu($rankers_hissatu_num[$rank]);
        }
        $this->is_parse_success = true;
    }
}