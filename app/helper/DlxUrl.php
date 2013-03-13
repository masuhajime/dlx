<?php

namespace app\helper;

class DlxUrl {
    
    const URL_DRAGONX = 'http://dragonx.asobism.co.jp/';
    const URL_CHECK_STAMINA = 'top/field/checkStamina.php?HTTP_UTIL=1';
    const URL_USE_MILK = 'top/field/RecoveryStamina.php?HTTP_UTIL=1';
    const URL_BATTLE_COMMAND = 'top/battle/battleCommandProcess.php?HTTP_UTIL=1';
    const URL_TALK = 'top/talk/talkProcess.php?HTTP_UTIL=1';//個人に語る
    
    // 戦闘状況
    // http://dragonx.asobism.co.jp/top/battle/checkBattle.php
    // FP100回復(mypage)
    // http://dragonx.asobism.co.jp/top/mypage/mypageFP.php
    // top/field/fieldBoss.php?param=1
    
    const URL_SET_DEFAULT = 0;
    const URL_SET_GOLD_EVENT = 1;
    const URL_SET_MAGIC_MACHINE = 2;
    
    const URL_FIELD_MAP = 0;//モンスター出現なし
    const URL_FIELD_INDEX = 1;//モンスター出現中フィールド
    const URL_FIELD_EVENT_TOUCH = 2;//フィールドイベントタッチ
    const URL_FIELD_OBJECT_RESET = 3;//次のフィールドへ
    const URL_FIELD_REQUEST_BATTLE = 4;//モンスターとの戦闘/捕獲
    const URL_CAPTURE_LIST = 5;//捕獲済みリストの表示
    const URL_REQUEST_CAPTURE = 6;//捕獲済みリストからの捕獲(戦闘)実行
    const URL_FIELD_BOSS = 7;
    const URL_FIELD_BOSS_PROCESS = 8;
    const URL_FIELD_EVENT_ACTION = 9;//特殊なイベント用
    
    private static $URL_SET = array(
        self::URL_SET_DEFAULT => array(
            self::URL_FIELD_MAP => 'top/field/fieldMap.php?HTTP_UTIL=1',
            self::URL_FIELD_INDEX => 'top/field/fieldIndex.php?HTTP_UTIL=1',
            self::URL_FIELD_EVENT_TOUCH => 'top/field/fieldEvent.php?HTTP_UTIL=1',
            self::URL_FIELD_OBJECT_RESET => 'top/field/fieldObjectReset.php?HTTP_UTIL=1',
            self::URL_FIELD_REQUEST_BATTLE => 'top/field/fieldBattle.php?HTTP_UTIL=1',
            self::URL_CAPTURE_LIST => 'top/field/fieldCaptureIndex.php',
            self::URL_REQUEST_CAPTURE => 'top/field/fieldBattle.php?HTTP_UTIL=1',//URL_REQUEST_CAPTURE と同じ
            self::URL_FIELD_BOSS => 'top/field/fieldBoss.php?param=1',// param=1が通常で param=0がフィールドチェンジかも
            self::URL_FIELD_BOSS_PROCESS => 'top/field/fieldBossProcess.php?HTTP_UTIL=1',
        ),
        self::URL_SET_MAGIC_MACHINE => array(
            self::URL_FIELD_MAP => 'top/event/magicMachine/eventMagicMachineMap.php',
            self::URL_FIELD_INDEX => 'top/event/magicMachine/eventMagicMachineIndex.php?noChangeHeader=true',
            self::URL_FIELD_EVENT_TOUCH => 'top/event/magicMachine/eventMagicMachineFieldEvent.php?HTTP_UTIL=1',
            self::URL_FIELD_OBJECT_RESET => 'top/event/magicMachine/eventFieldObjectReset.php?HTTP_UTIL=1',
            self::URL_FIELD_REQUEST_BATTLE => 'top/event/magicMachine/requestMagicMachineBattleMonster.php?HTTP_UTIL=1',
            self::URL_CAPTURE_LIST => 'top/field/fieldCaptureIndex.php',//めんどいので同じにした
            //self::URL_REQUEST_CAPTURE => 'top/event/gold/requestGoldBattleMonster.php?HTTP_UTIL=1',
            //なんか動かないのでデフォと同じにしている, 原因は調べてない
            self::URL_REQUEST_CAPTURE => 'top/field/fieldBattle.php?HTTP_UTIL=1',
            self::URL_FIELD_BOSS => 'top/field/fieldBoss.php?param=1',//調べてない
            self::URL_FIELD_BOSS_PROCESS => 'top/field/fieldBossProcess.php?HTTP_UTIL=1',//調べてない
            self::URL_FIELD_EVENT_ACTION => 'top/event/magicMachine/magicMachineApp.php?id=',
        ),
        self::URL_SET_GOLD_EVENT => array(
            self::URL_FIELD_MAP => 'top/event/gold/eventGoldMap.php',
            self::URL_FIELD_INDEX => 'top/event/gold/eventGoldIndex.php?noChangeHeader=true',
            self::URL_FIELD_EVENT_TOUCH => 'top/event/gold/eventGoldFieldEvent.php?HTTP_UTIL=1',
            self::URL_FIELD_OBJECT_RESET => 'top/event/gold/eventFieldObjectReset.php?HTTP_UTIL=1',
            self::URL_FIELD_REQUEST_BATTLE => 'top/event/gold/requestGoldBattleMonster.php?HTTP_UTIL=1',
            self::URL_CAPTURE_LIST => 'top/field/fieldCaptureIndex.php',//めんどいので同じにした
            //self::URL_REQUEST_CAPTURE => 'top/event/gold/requestGoldBattleMonster.php?HTTP_UTIL=1',
            //なんか動かないのでデフォと同じにしている, 原因は調べてない
            self::URL_REQUEST_CAPTURE => 'top/field/fieldBattle.php?HTTP_UTIL=1',
            self::URL_FIELD_BOSS => 'top/field/fieldBoss.php?param=1',//調べてない
            self::URL_FIELD_BOSS_PROCESS => 'top/field/fieldBossProcess.php?HTTP_UTIL=1',//調べてない
        )
    );
    
    public static function url($set, $url_num)
    {
        if (!isset(self::$URL_SET[$set])) {
            throw new \RuntimeException("undefined url set: {$set}");
        }
        if (!isset(self::$URL_SET[$set][$url_num])) {
            throw new \RuntimeException("undefined url: {$url_num} set: {$set}");
        }
        return self::$URL_SET[$set][$url_num];
    }
}
