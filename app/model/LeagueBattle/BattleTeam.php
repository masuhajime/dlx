<?php

namespace app\model\LeagueBattle;

class BattleTeam {
    private $team_id = 0;
    private $members = array();
    private $warning_ids = null;

    public function getWarningIds(\app\model\PlayerHandling $player) {
        if (!is_null($this->warning_ids)) {
            return;
        }
        \app\helper\Logger::info("try to get warning players on team:{$this->team_id}");
        if ($this->team_id == 0) {
            \app\helper\Logger::info("warning player : 0");
            $this->warning_ids = array();
        }
        $ti = \app\model\TeamIndex::getTeamIndexOf($player, $this->team_id);
        if (!$ti->is_parse_success) {
            \app\helper\Logger::info("team parsing failed");
            $this->warning_ids = array();
        }
        //var_dump($ti);
        //var_dump($ti->getWarningPlayer());
        $this->warning_ids = $ti->getWarningPlayer();
    }
    
    public function isWarningPlayerAlive() {
        if (is_null($this->warning_ids)) {
            //var_dump($this->warning_ids);
            \app\helper\Logger::warning("isWarningPlayerAlive called on warning ids null");
            return $this->isAliveMoreThan(0);
        }
        $alive = $this->getAliveIds();
        foreach ($alive as $id) {
            if (in_array($id, $this->warning_ids)) {
                return true;
            }
        }
        return false;
    }

    public function __toString()
    {
        $r = array();
        foreach ($this->members as $m) {
            if ($m->getHP() <= 0) {
                continue;
            }
            $r[] = "[{$m->getId()}/HP:{$m->getHP()}/Status({$m->getStatus()})]";
        }
        //var_dump($this->warning_ids);
        $warnings = is_null($this->warning_ids)?"":" warnings[".  implode(', ', $this->warning_ids)."]";
        return implode(", ", $r).$warnings;
    }
    
        /*  "teamID":"111",
            "userID":"111",
            "job":"20",
            "number":"5",
            "position":"5",
            "hp":"0",
            "hpMax":"8400",
            "status":"0",
            "historyIndex":"227108",
            "jobGroup":2 */
    public static function createFromJson(Array $json)
    {
        $team = new BattleTeam();
        foreach ($json as $player) {
            $p = new \app\model\Player();
            $id = $player["userID"];
            $p->setId($id);
            $hp = isset($player["hp"])?$player["hp"]:0;
            $p->setHP($hp);
            $status = isset($player["status"])?$player["status"]:0;
            $p->setStatus($status);
            $team->addMember($p);
        }
        return $team;
    }
    
    public function setTeamWarningPlayers($team_no) {
        $ti = \app\model\TeamIndex::getTeamIndexIfExist($team_no);
        if (is_null($ti)) {
            return;
        }
        $this->warning_ids = $ti->getWarningPlayer(20);
    }
    
    public function getAliveIds() {
        $r = array();
        foreach ($this->members as $player) {
            if ($player->isAlive()) $r[] = $player->getId();
        }
        return $r;
    }
    
    public function countAlive()
    {
        $c = 0;
        foreach ($this->members as $player) {
            if ($player->isAlive()) $c++;
        }
        return $c;
    }
    
    public function isAliveMoreThan($number)
    {
        $alives = $this->countAlive();
        return $alives >= $number;
    }
    
    public function addMember(\app\model\Player $player)
    {
        $this->members[] = $player;
    }
    
    public function setTeamId($num) {$this->team_id = $num;}
    public function getTeamId() {return $this->team_id;}
}