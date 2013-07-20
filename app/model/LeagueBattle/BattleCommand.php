<?php

namespace app\model\LeagueBattle;

abstract class BattleCommand {
    CONST HISSATU = 10;
    CONST SHIELD = 104;
    CONST BEAM = 109;
    
    static public function getSkillName($num) {
        $name = "";
        switch ($num) {
            case self::HISSATU: $name = "HISSATU"; break;
            case self::SHIELD: $name = "SHIELD"; break;
            case self::BEAM: $name = "BEAM"; break;
        }
        return $name;
    }
    
    static public function getSkillFP($num) {
        $n = 0;
        switch ($num) {
            case self::HISSATU: $n = 40; break;
            case self::SHIELD: $n = 60; break;
            case self::BEAM: $n = 20; break;
        }
        return $n;
    }
}

/*

	// HistoryID
	// Regular
	this.BATTLE_HISTORY_ATTACK					= 1;		// 攻撃
	this.BATTLE_HISTORY_ATTACK_OVER				= 5;		// 追い討ち
	this.BATTLE_HISTORY_SKILL					= 10;		// 必殺技
	this.BATTLE_HISTORY_CURE					= 20;		// 回復
	// Sub
	this.BATTLE_HISTORY_STONE					= 30;		// 投石
	this.BATTLE_HISTORY_STONE_CRITICAL			= 31;		// 投石急所
	this.BATTLE_HISTORY_STONE_LARGE				= 32;		// 大投石
	this.BATTLE_HISTORY_CHEER					= 40;		// 応援
	this.BATTLE_HISTORY_CHEER_YELL				= 41;		// 応援(大)
	this.BATTLE_HISTORY_CHEER_WAVE 				= 42;	// ウェーブ達成（１人パワーアップ）観客用
	this.BATTLE_HISTORY_SUMMON					= 50;		// 召喚発動
	this.BATTLE_HISTORY_SUMMON_POWERUP			= 51;		// 召喚パワーアップ
	this.BATTLE_HISTORY_OUTFIELD_JOIN			= 60;		// 乱入攻撃
	this.BATTLE_HISTORY_OUTFIELD_JOIN_LARGE		= 61;		// 外野乱入強撃(強行)
	this.BATTLE_HISTORY_OUTFIELD_JOIN_OVER		= 62;		// 乱入追い撃ち攻撃
	// 召喚獣行動
	this.BATTLE_HISTORY_SUMMON_ATTACK			= 65;
	this.BATTLE_HISTORY_SUMMON_ATTACK_FAILED	= 66;
	this.BATTLE_HISTORY_SUMMON_DISAPPEAR		= 67;

	// Skill
	this.BATTLE_HISTORY_COUNTER					= 80;		// 反撃
	this.BATTLE_HISTORY_CURE_ALL				= 81;		// 治癒
	// Free
	this.BATTLE_HISTORY_FREE_JOIN				= 90;		// フリー参戦
	this.BATTLE_HISTORY_HALF_TIME				= 91;		// ハーフタイム(技P+50)

	// 追加コマンド
	this.BATTLE_HISTORY_INTERCEPT				= 100;	// 迎撃
	this.BATTLE_HISTORY_REVIVAL					= 101;	// 蘇生
	this.BATTLE_HISTORY_EVIL					= 102;	// 漆黒魔陣
	this.BATTLE_HISTORY_OUTFIELD_ATTACK			= 103;	// 外野攻撃(ジャブ)
	this.BATTLE_HISTORY_SHIELD					= 104;	// シールド
	this.BATTLE_HISTORY_CONTINUE_STONE			= 105;	// 連続投石
	this.BATTLE_HISTORY_CONTINUE_STONE_CRITICAL	= 106;	// 連続投石(急所)
	this.BATTLE_HISTORY_PLURALITY				= 107;	// 乱れ斬り
	this.BATTLE_HISTORY_CHARGE					= 108;	// 竜の呼吸
	this.BATTLE_HISTORY_BEAM					= 109;	// 魔導光線
	this.BATTLE_HISTORY_ZANTETSU				= 110;	// 斬鉄剣
	this.BATTLE_HISTORY_GARO					= 111;	// 神速八突
	this.BATTLE_HISTORY_GRIM					= 112;	// 死の刻印
	this.BATTLE_HISTORY_MUSCLE					= 113;	// マッスルシールド
	this.BATTLE_HISTORY_THRUST					= 114;	// 急所突き
	this.BATTLE_HISTORY_SUMMON_PACT				= 115;	// 召喚行使






 */