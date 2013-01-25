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

    private static function postRequest($page, $post_params = array())
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $request = $client->post($page);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->addPostFields($post_params);
        return $request->send();
    }
    
    private static function getRequest($page, $post_params = array())
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $request = $client->get($page);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        //$request->addPostFields($post_params); TODO:
        return $request->send();
    }

    /**
     * @return int/false
     */
    public static function getStamina()
    {
        $response = self::getRequest(DlxUrl::URL_CHECK_STAMINA);
        $json = $response->getBody();
        $j = json_decode($json, true);
        if (is_null($j)) {
            throw new exception\UnexpectedResponse("fail get stamina");
        }
        return intval($j['stamina']);
    }
    
    public static function touchFieldEvent(\app\model\FieldEvent $field)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_EVENT_TOUCH);
        $param = array(
                    'id' => $field->getEventId(),
                    'status' => $field->getParam(),
                    'obj' => $field->getId(),
                );
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        $response = self::postRequest($url, $param);
        // 返り値をみたほうがいいが...
        return true;
    }
    
    /**
     * なぜかリダイレクトが正しいURLにリダイレクトしないので
     * @return string html
     */
    public static function getMapHtml()
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $url_map = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_MAP);
        Logger::debug(__METHOD__.' url:'.$url_map);
        $request = $client->get($url_map);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
        if (!$response->isRedirect()) {
            return array('MAP', $response->getBody());
        }
        $url_index = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_INDEX);
        Logger::debug(__METHOD__.' url:'.$url_index);
        $request = $client->get($url_index);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
        return array('INDEX', $response->getBody());
    }
    
    public static function fieldReset()
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_OBJECT_RESET);
        Logger::debug(__METHOD__.' url:'.$url);
        $request = $client->post($url);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
    }
    
    public static function battleMonster(\app\model\Monster $monster)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_FIELD_REQUEST_BATTLE);
        $param = array(
                    'mid' => $monster->getId(),
                    'drop' => is_null($monster->getBoxDrop()) ? 'null' : '1',
                    // result = true で全勝利となる
                    'result' => 'true'//なぜか文字列
                );
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        return self::postRequest($url, $param);
    }
    
    public static function captureMonster(\app\model\Monster $monster)
    {
        $url = DlxUrl::url(self::$url_set, DlxUrl::URL_REQUEST_CAPTURE);
        $param = array(
                    'mid' => $monster->getId(),
                    'drop' => 'null',
                    'result' => 'true',//なぜか文字列
                    'prob' => '100'
                );
        Logger::debug(__METHOD__.' url:'.$url.' params:'.  var_export($param, true));
        return self::postRequest($url, $param);
    }
    
    /**
     * 捕獲済みリストの取得
     * @return boolean|array
     */
    public static function getCaptureMonsters()
    {
        $response = self::getRequest(
                DlxUrl::url(self::$url_set, DlxUrl::URL_CAPTURE_LIST)
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
    
    public static function useMilk()
    {
        //var recoveryItemNum = 50;
        //http://dragonx.asobism.co.jp/top/field/RecoveryStamina.php?HTTP_UTIL=1
        //{"error":false,"checkStamina":"10","recoveryItemNum":49}
        Logger::debug(__METHOD__.' url:'.DlxUrl::URL_USE_MILK);
        $response = self::getRequest(DlxUrl::URL_USE_MILK);
        $json = $response->getBody();
        
    }
}
