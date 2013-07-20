<?php

namespace app\model\LeagueBattle;

class BattleStatus {
    private $result;//int
    /**
     * @var \app\model\LeagueBattle\BattleTeam
     */
    private $ally = null;
    /**
     * @var \app\model\LeagueBattle\BattleTeam
     */
    private $enemy = null;
    private $remain_sec = 0;
    private $comboChance = false;
    private $ownerFp = 0;
    private $shield = 0;
    
    private $charge = 0;
    private $counter = 0;
    
    public function __toString()
    {
        $r = "result:{$this->result} remain:{$this->remain_sec}".
                " comboChance:".($this->comboChance?"true":"false").
                " FP:{$this->ownerFp} charge:{$this->charge}".
                " counter:{$this->counter} shield:{$this->getShield()} isWarningEnemyAlive:".($this->isEnemyWarningPlayerAlive()?"true":"false").
                        "\n".
                "# Ally : ".$this->ally.PHP_EOL.
                "# Enemy: ".$this->enemy.PHP_EOL
                ;
        return $r;
    }
    
    public function getEnemyWarningPlayer(\app\model\PlayerHandling $p)
    {
        $this->enemy->getWarningIds($p);
    }
    
    public function isEnemyWarningPlayerAlive()
    {
        return $this->enemy->isWarningPlayerAlive();
    }

    private function __construct($result) {
        $this->result = $result;
    }
    
    public static function createFromJson($json)
    {
        $data = json_decode($json, true);
        if (is_null($data)) {
            throw new \RuntimeException("FAIL_PARSE_JSON");
        }
        if (!isset($data["result"])) {
            throw new \RuntimeException("result NOT FOUND ON JSON");
        }
        $bs = new self($data["result"]);
        if (!$bs->isResultSuccess()) {
            return $bs;
        }
        // battleUserList(ally)
        $ally  = BattleTeam::createFromJson($data["battleUserList"]);
        if (isset($data["teamID"])) {
            $ally->setTeamId($data["teamID"]);
        }
        // battleTargetUserList(enemy)
        $enemy = BattleTeam::createFromJson($data["battleTargetUserList"]);
        if (isset($data["battleTargetUserList"][0]["teamID"])) {
            $enemy_team_no = ($data["battleTargetUserList"][0]["teamID"]);
            $enemy->setTeamId($enemy_team_no);
            $enemy->setTeamWarningPlayers($enemy_team_no);
        }
        
        $bs->setAlly($ally);
        $bs->setEnemy($enemy);
        
        if (isset($data["battleRemainSec"])) {
            $bs->setRemainSecond(intval($data["battleRemainSec"]));
        }
        if (isset($data["comboChance"])) {
            $bs->setComboChance($data["comboChance"]);//きわどい判定
        }
        if (isset($data["battleUser"]["fp"])) {
            $bs->setOwnerFp(intval($data["battleUser"]["fp"]));
        }
        if (isset($data["battleUser"]["shield"])) {
            $bs->setShield(intval($data["battleUser"]["shield"]));
        }
        return $bs;
    }
    
    public function setAlly(BattleTeam $t){$this->ally = $t;}
    public function setEnemy(BattleTeam $t){$this->enemy = $t;}
    public function getResult(){return $this->result;}
    public function isResultSuccess(){return $this->getResult() === 200;}
    public function setRemainSecond($int){$this->remain_sec = $int;}
    public function getRemainSecond(){return $this->remain_sec;}
    public function setComboChance($bool){$this->comboChance = $bool;}
    public function getComboChance(){return $this->comboChance;}
    public function setOwnerFp($fp){$this->ownerFp = intval($fp);}
    public function getOwnerFp(){return $this->ownerFp;}
    
    public function setCharge($v){$this->charge = intval($v);}
    public function getCharge(){return $this->charge;}
    public function setCounter($v){$this->counter = intval($v);}
    public function getCounter(){return $this->counter;}
    public function setShield($v){$this->shield = intval($v);}
    public function getShield(){return $this->shield;}
    
    public function getAliveAlly() {return $this->ally->countAlive();}
    public function getAliveEnemy() {return $this->enemy->countAlive();}
}

