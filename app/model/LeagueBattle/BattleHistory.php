<?php

namespace app\model\LeagueBattle;

class BattleHistory {
    
    private $histories = array();
    
    private static $required_history_params = array("id","userID","commandID");
    // "isMyTeam" // <- 必ずしもあるとは限らない
    
    /*
     * 相手チームに試合開始以来、必殺土台が作成されたか確認する
     */
    public function hasEnemySpecialBaseFromStart()
    {
        foreach ($this->histories as $h) {
            if ($h["isMyTeam"]) continue;
            $cmd = $h["commandID"];
            if ($cmd == BattleCommand::BEAM || $cmd == BattleCommand::HISSATU) {
                return true;
            }
        }
        return false;
    }

    public function push(Array $history)
    {
        foreach (self::$required_history_params as $p) {
            if (!isset($history["id"])) {
                throw new \RuntimeException("[{$p}] not found on battle history");
            }
        }
        array_push($this->histories, array(
            "id" => $history["id"],
            "userID" => $history["userID"],
            "commandID" => $history["commandID"],
            "isMyTeam" => isset($history["isMyTeam"])?$history["isMyTeam"]:false,
        ));
    }
    
    /**
     * @param array $json
     * @return \app\model\LeagueBattle\BattleHistory
     */
    static public function createFronJson(Array $json)
    {
        $history = new self();
        foreach ($json as $h) {
            $history->push($h);
        }
        return $history;
    }
    
}
