<?php
// 戦闘実行
require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();
use app\helper\Logger;
use app\model\LeagueBattle\BattleCommand;
\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);
//Logger::setLogLevel(Logger::LEVEL_DEBUG);
Logger::setLogLevel(Logger::LEVEL_INFO);
//-----------------
define("OWNER_TEAM", 1161255);
define("FP_INTERVAL_NORMAL_MODE", 60);//SECOND
define("FP_USE_MAX_IN_BATTLE", 180);
define("TYPE_TALK_MSG", \app\model\TalkMessage::INVITE);//\app\model\TalkMessage::INVITE / FP
define("REGEN_FP_TO", 100);//\app\model\TalkMessage::INVITE / FP
\app\model\TeamIndex::setWarningRate(1);
\app\model\TeamIndex::setMinAttcks(1200000, 900000);

/*
// FOR DRAGON_BATTLE
define("FP_INTERVAL_NORMAL_MODE", 2);//SECOND
define("FP_USE_MAX_IN_BATTLE", 120);
\app\model\TeamIndex::setWarningRate(20);
 */
//-----------------

function wait(app\model\PlayerHandling $p) {
    $hs = $p->getHeaderStatus();
    if ($hs->isBattle()) {
        Logger::info("battle starting. sleep 3sec");
        sleep(3);
        return BATTLE_START;
    }
    $remain = $hs->getRemainTime();
    $remain_and_waits = array(
        3600 => 3000, 600 => 540,
        60 => 50, 20 => 10, 10 => 3,
        4 => 2, 2 => 5, 1 => 4
    );
    foreach ($remain_and_waits as $remain_more_if => $wait) {
        if ($remain >= $remain_more_if) {
            // 余裕があればfpを回復しておく
            if ($remain > 2400) {
                $fp = $p->getFP();
                Logger::info("checking fp: {$fp}");
                if (REGEN_FP_TO > $fp) {
                    fp($p, $fp, REGEN_FP_TO, 2*60*1000*1000);
                }
            }
            Logger::info("remain: {$remain}seconds. wait {$wait}sec.");
            sleep($wait);
            return BATTLE_WAIT;
        }
    }
    Logger::info("remain: {$remain}seconds. but battle not start. sleep(3600)");
    sleep(3600);
    return BATTLE_WAIT;
}

define("BATTLE_MODE_START_BUFFER", 0);
define("BATTLE_MODE_STARTING", 1);
define("BATTLE_MODE_NORMAL", 2);
function battle(app\model\PlayerHandling $p) {
    $battle_mode = BATTLE_MODE_NORMAL;
    $bs = $p->getBattleStatus();
    $bm = BattleManager::getInstance();
    if (!$bs->isResultSuccess()) {
        // case 1.試合が終わった、試合待機を返してheader statusを取得して試合街に入る
        // case 2.時刻がずれていて戦闘履歴取得が失敗した場合: 30秒待ちループに入る
        $bm->reset();
        Logger::warning("battle is not starting. sleep 30 sec.");sleep(30);
        return BATTLE_WAIT;// 待機状態を返さないと試合待機に入らない.
    }
    echo $bs;
    $remain = $bs->getRemainSecond();
    $passing = 3600-$remain;
    $fp = $bs->getOwnerFp();
    $sleep_time = 3;
    $battle_mode_starting_period = 30;
    if (0 > $passing) {$battle_mode = BATTLE_MODE_START_BUFFER; }
    else if ($battle_mode_starting_period > $passing) { $battle_mode = BATTLE_MODE_STARTING; }
    if ($battle_mode == BATTLE_MODE_START_BUFFER) {
        $sleep_time = 1;//何もしない
    } else if ($battle_mode == BATTLE_MODE_STARTING) {
        // battlehistoryを監視して土台があればシールドを挟みたい
        // 特別にこの中でループを行う
        $sleep_time = 5;
        $loops = $battle_mode_starting_period - $passing;
        Logger::info("battle start loops: {$loops}");
        for ($i = 0; $i < $loops; $i++) {
            sleep(1);
            $bh = $p->getBattleHistory(OWNER_TEAM, time());
            $is_enemy_special_base = $bh->hasEnemySpecialBaseFromStart();
            Logger::info("enemy special base: ".($is_enemy_special_base?"true":"false"));
            if (!$is_enemy_special_base) { continue; }
            battle_command($p, BattleCommand::SHIELD);
            Logger::info("sleep 30");
            sleep(30);
            break;
        }
    } else if ($battle_mode == BATTLE_MODE_NORMAL) {
        $bs->getEnemyWarningPlayer($p);
        if (REGEN_FP_TO > $fp) {
            /* // ここを解除するとFPが無い場合は何もしなくなる(fp手動回復になる)
            $sleep_time = 30;
            Logger::info("sleep {$sleep_time}");
            sleep($sleep_time);
            return BATTLE_START;
            */
            fp($p, $fp, REGEN_FP_TO, FP_INTERVAL_NORMAL_MODE*1000*1000);
        } else {
            if (!$bm->canAction()) {
                $sleep_time = 300;
            } else {
                //var_dump("alives:".$bs->getAliveAlly()." > ".$bs->getAliveEnemy()." && shield:".$bs->getShield()." && ".($bs->isEnemyWarningPlayerAlive()?"true":"false"));
                if (
                        //($bs->getAliveAlly() >= $bs->getAliveEnemy()) &&
                        ($bs->getAliveEnemy() > 0) && 
                        ($bs->getAliveAlly() > 0) && 
                        ($bs->getShield() == 0) && 
                        $bs->isEnemyWarningPlayerAlive()
                        ) {
                    battle_command($p, BattleCommand::SHIELD);
                    $sleep_time = 3;
                }
            }
        }
    }
    Logger::info("sleep {$sleep_time}");
    sleep($sleep_time);
    return BATTLE_START;
}

function battle_command(app\model\PlayerHandling $player, $command_num) {
    $skill_name = BattleCommand::getSkillName($command_num);
    $skill_fp = BattleCommand::getSkillFP($command_num);
    $bm = BattleManager::getInstance();
    $fp_before = $bm->getUsedFP();
    $bm->useFp($skill_fp);
    $fp_after = $bm->getUsedFP();
    $player->battleCommand($command_num);
    Logger::info("########### using skill: {$skill_name}(FP:{$skill_fp} / {$fp_before} -> {$fp_after}) ###########");
}

function fp(app\model\PlayerHandling $player, $now, $fp_to, $usleep = 1000000)
{
    $tries = ceil(($fp_to - $now)/5);
    $retired_list = DIR_DATA.'/retired_id.dat';
    $pointer_file = DIR_DATA.'/pointer/battle_'.date("Y_m_d");
    $user_list = new \app\model\FileLineList($retired_list);
    $user_list->getPointerFromFile($pointer_file);
    for ($i = 0; $i < $tries; $i++) {
        $talk_user_id = $user_list->getNext();
        
        $talk_player = new \app\model\Player();
        $talk_player->setId($talk_user_id);
        $message = \app\model\TalkMessage::getRandom(TYPE_TALK_MSG);
        $sec = round($usleep/1000/1000, 3);
        Logger::info("FP: {$talk_player->getId()} to {$player->getId()} (wait {$sec}sec): ".mb_substr($message,0,48))."...";
        $b = \app\helper\DlxAccesser::talkToUser($player->getViewerData(), $talk_player, $player, true, $message);
        $user_list->savePointerToFile();// 本当はここでsaveしたくないが、短くても0.2秒なのでいいか...
        usleep($usleep);
    }
}

$owner = new \app\model\PlayerHandling(CONFIG_USER::VIEWER_ID);
define("BATTLE_WAIT", 0);
define("BATTLE_START", 1);
define("BATTLE_EXIT", 2);
$mode = BATTLE_WAIT;
while(1) {
    try {
        if(BATTLE_WAIT === $mode) {
            $mode = wait($owner);
        } else if(BATTLE_START === $mode) {
            $mode = battle($owner);
        } else if(BATTLE_EXIT === $mode) {
            Logger::info("exit battle.");
            break;
        }
    } catch (Exception $e) {
        Logger::warning("Exception:".$e->getMessage());
        sleep(5*60);
    }
}

class BattleManager
{
    static private $i = null;
    
    private $fp_use = 0;
    
    public function canAction() {return FP_USE_MAX_IN_BATTLE > $this->fp_use;}
    public function useFp($int) {$this->fp_use += $int;}
    public function reset() {$this->fp_use = 0;}
    public function getUsedFP() {return $this->fp_use;}
    
    /**
     * @return \self
     */
    static public function getInstance() {
        if (is_null(self::$i)) {
            self::$i = new self();
        }
        return self::$i;
    }
}