<?php
namespace app\helper;

require DIR_VENDOR.'/autoload.php';
use Guzzle\Http\Client;
use app\helper\Logger;

class DlxAccesser {

    private function __construct() {}
    
    private static $url_set = DlxUrl::URL_SET_DEFAULT;
    
    public static function setUrlSet($set_num)
    {
        self::$url_set = $set_num;
    }

    public static function postRequest($page, $post_params = array(), $cookie = array(), $header = array())
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $request = $client->post($page);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        foreach ($header as $key => $value) {
            $request->setHeader($key, $value);
        }
        foreach ($cookie as $key => $value) {
            $request->addCookie($key, $value);
        }
        $request->addPostFields($post_params);
        return $request->send();
    }
    
    public static function getRequest($page, $post_params = array(), $cookie = array(), $header = array())
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $request = $client->get($page);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        foreach ($header as $key => $value) {
            $request->setHeader($key, $value);
        }
        foreach ($cookie as $key => $value) {
            $request->addCookie($key, $value);
        }
        //$request->addPostFields($post_params); TODO:
        return $request->send();
    }

    /**
     * @return int/false
     */
    public static function getStamina($viewer_data)
    {
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest(DlxUrl::URL_CHECK_STAMINA, array(), $cookie);
        $json = $response->getBody();
        $j = json_decode($json, true);
        if (is_null($j)) {
            throw new exception\UnexpectedResponse("fail get stamina");
        }
        return intval($j['stamina']);
    }
    
    public static function touchFieldEvent($viewer_data, \app\model\FieldEvent $field)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_EVENT_TOUCH);
        $param = array(
                    'id' => $field->getEventId(),
                    'status' => $field->getParam(),
                    'obj' => $field->getId(),
                );
        $cookie = array('viewer_data' => $viewer_data);
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        $response = self::postRequest($url, $param, $cookie);
        // 返り値をみたほうがいいが...
        return true;
    }
    
    /**
     * なぜかリダイレクトが正しいURLにリダイレクトしないので
     * @return string html
     */
    public static function getMapHtml($viewer_data)
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $url_map = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_MAP);
        Logger::debug(__METHOD__.' url:'.$url_map);
        $request = $client->get($url_map);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', $viewer_data);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
        if (!$response->isRedirect()) {
            return array('MAP', $response->getBody());
        }
        $url_index = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_INDEX);
        Logger::debug(__METHOD__.' url:'.$url_index);
        $request = $client->get($url_index);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', $viewer_data);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
        return array('INDEX', $response->getBody());
    }
    
    public static function fieldReset($viewer_data)
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_OBJECT_RESET);
        Logger::debug(__METHOD__.' url:'.$url);
        $request = $client->post($url);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', $viewer_data);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
    }
    
    public static function battleMonster(\app\model\PlayerHandling $player, \app\model\Monster $monster)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_REQUEST_BATTLE);
        $param = array(
                    'mid' => $monster->getId(),
                    'drop' => is_null($monster->getBoxDrop()) ? 'null' : '1',
                    // result = true で全勝利となる
                    'result' => 'true',//なぜか文字列
                    'key' => $player->getId(),
                    'value' => $monster->getValue(),
                );
        $cookie = array('viewer_data' => $player->getViewerData());
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        return self::postRequest($url, $param, $cookie);
    }
    
    public static function captureMonster($viewer_data, \app\model\Monster $monster)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_REQUEST_CAPTURE);
        $param = array(
                    'mid' => $monster->getId(),
                    'drop' => 'null',
                    'result' => 'true',//なぜか文字列
                    'prob' => '100'
                );
        $cookie = array('viewer_data' => $viewer_data);
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        return self::postRequest($url, $param, $cookie);
    }
    
    /**
     * 捕獲済みリストの取得
     * @return boolean|array
     */
    public static function getCaptureMonsters($viewer_data)
    {
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest(
                DlxUrl::url(self::$url_set, DlxUrl::URL_CAPTURE_LIST),
                array(),
                $cookie
                );
        $html = $response->getBody();
        
        $m = array();
        if (0 === preg_match('/var monsterList = \{(.*)\};/i', $html, $m)) {
            return false;
        }
        $monser_data_json = '{'.$m[1].'}';
        $monster_data = json_decode($monser_data_json, true);
        
        $monsters = array();
        foreach ($monster_data as $monster_id => $data) {
            $m = \app\model\Monster::createInstanceFromArray($data);
            array_push($monsters, $m);
        }
        return $monsters;
    }
    
    public static function useMilk($viewer_data)
    {
        //var recoveryItemNum = 50;
        //http://dragonx.asobism.co.jp/top/field/RecoveryStamina.php?HTTP_UTIL=1
        //{"error":false,"checkStamina":"10","recoveryItemNum":49}
        $cookie = array('viewer_data' => $viewer_data);
        Logger::debug(__METHOD__.' url:'.DlxUrl::URL_USE_MILK);
        $response = self::getRequest(DlxUrl::URL_USE_MILK, array(), $cookie);
        $json = $response->getBody();
    }
    
    public static function fieldBossProcess($viewer_data, \app\model\FieldBoss $boss)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_BOSS_PROCESS);
        $param = array(
                    'bid' => $boss->getId(),
                    'param' => 1,
                    'result' => 90,//高い値を設定しても変化はなかったはずだが
                    'continueFlag' => 1,//1でも0でも変化がわからない
                );
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        $cookie = array('viewer_data' => $viewer_data);
        return self::postRequest($url, $param, $cookie);
        //{"error":false,"dropSummon":0,"result":false}
    }
    
    public static function getFieldBossHtml($viewer_data)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_BOSS);
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest($url, array(), $cookie);
        return $response->getBody();
    }
    
    /**
     * @param \app\model\PlayerHandling $from 語り手
     * @param \app\model\Player $to 語られる側
     * @param type $isFP
     * @param type $message
     * @return type
     */
    public static function talkToUser($viewer_id, \app\model\Player $from, \app\model\Player $to, $isFP, $message = "")
    {
        $url = DlxUrl::URL_TALK;
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_id);
        $param = array(
            "uid" => $from->getId(),
            "oid" => $to->getId(),
            "isFP" => $isFP?"1":"0",
            "message" => $message
        );
        $response = self::postRequest($url, $param, $cookie);
        return $response->getBody();
    }

    public static function getBattleStatus($viewer_data, \app\model\PlayerHandling $player_handle)
    {
        //return '{"result":200,"historyCount":0,"bbsCount":0,"shoutCount":0,"battleUser":{"kind":"7","teamID":"111","userID":"111","job":"18","number":"1","position":"1","fp":80,"hp":5866,"hpMax":"10500","status":"0","charge":"0","counter":"0","shield":"0","historyIndex":"227108","jobGroup":3},"battleTeam":{"teamID1":"111","bp1":"222608","pet1":"0","userID1":"0","count1":"0","teamID2":"111","bp2":"34242","pet2":"0","userID2":"0","count2":"0","petImage1":0,"petKind1":0,"petName1":"","petRare1":0,"petCountMax1":0,"petRemainSec1":0,"summonAbilityName1":"","summonAbilityInfo1":"","petImage2":0,"petKind2":0,"petName2":"","petRare2":0,"petCountMax2":0,"petRemainSec2":0,"summonAbilityName2":"","summonAbilityInfo2":""},"battleUserList":[{"teamID":"111
        $url = DlxUrl::URL_CHECK_BATTLE;
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $param = array(
            "userID" => $player_handle->getId(),
            "timeID" => date("YmdH00"),//201304180800
        );
        $response = self::postRequest($url, $param, $cookie);
        //var_dump($param);
        //echo $response->getBody();exit;
        return $response->getBody();
    }
    
    public static function getHeaderStatus($viewer_data)
    {
        //return '{"remainTime":10,"isBattle":1,"bbsCount":0,"bbsBlink":0,"useCharm":false,"charmEndTime":"","nowtime":1366239799,"endtime":1366243200}';
        $url = DlxUrl::URL_HEADER_STATUS;
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $param = array(
        );
        $header = array(
            'X-Requested-With' => 'XMLHttpRequest'
        );
        $response = self::getRequest($url, $param, $cookie, $header);
        return $response->getBody();
    }

    public static function doBattleCommand($viewer_data, $command_num, $pow = 3)
    {
        $url = DlxUrl::URL_BATTLE_COMMAND;
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $params = array(
        "commandID" => $command_num,
        "power" => $pow
        );
        $response = self::postRequest($url, $params, $cookie);
        return $response->getBody();
    }
    
    public static function getEventMagicMachineAppTutorial($viewer_data, $id)
    {
        $url = "top/event/magicMachine/magicMachineAppTutorial.php?id={$id}";
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest($url, array(), $cookie);
        return $response->getBody();
    }
    
    public static function getEventMagicMachineApp($viewer_data, $id)
    {
        $url = "top/event/magicMachine/magicMachineApp.php?id={$id}";
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest($url, array(), $cookie);
        return $response->getBody();
    }
    
    public static function getEventMagicMachineWriteResult($viewer_data, \app\model\PlayerHandling $player, $objid)
    {
        $url = "top/event/magicMachine/magicMachineWriteResult.php";
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $param = array(
            "uid" => $player->getId(),
            "objid" => $objid,
            "score" => 4,
            "gage" => 0,
        );
        $response = self::postRequest($url, $param, $cookie);
        return $response;
    }
    
    public static function getUserTalkList(\app\model\PlayerHandling $player, $target_uid)
    {
        $url = DlxUrl::URL_TALK_LIST;
        $cookie = array('viewer_data' => $player->getViewerData());
        $param = array(
            "ownerUID" => $target_uid,
            "myUID" => $player->getId(),
            "ofs" => 0,//謎
            "limit" => 3
        );
        Logger::debug(__METHOD__.' url:'.$url);
        Logger::debug($param);
        $response = self::postRequest($url, $param, $cookie);
        return $response->getBody();
    }
    
    public static function getPlayerProfile(\app\model\PlayerHandling $player, $target_uid)
    {
        $url = DlxUrl::URL_PLAYER_PROFILE.$target_uid;
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $player->getViewerData());
        $param = array(
        );
        $response = self::getRequest($url, $param, $cookie);
        return $response->getBody();
    }
    
    public static function getBattleHistory($viewer_data, $team_no, $time)
    {
        $param = array("teamID" => $team_no, "timeID" => date("YmdH00", $time));
        $url = DlxUrl::URL_BATTLE_HISTORY."?".  http_build_query($param);
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest($url, $param, $cookie);
        return $response->getBody();
    }
    
    public static function getTeamIndex($viewer_data, $team_no)
    {
        Logger::info("########## access getTeamIndex:".$team_no);
        $param = array("teamID" => $team_no);
        $url = DlxUrl::URL_TEAM_INFO."?".http_build_query($param);
        Logger::debug(__METHOD__.' url:'.$url);
        $cookie = array('viewer_data' => $viewer_data);
        $response = self::getRequest($url, $param, $cookie);
        return $response->getBody();
    }
}
