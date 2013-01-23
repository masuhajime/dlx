<?php
namespace app\helper;

require DIR_VENDOR.'/autoload.php';
use Guzzle\Http\Client;

class DlxAccesser {

    private function __construct() {}
    
    private static function postRequest($page, $post_params = array())
    {
        $client = new Client('http://dragonx.asobism.co.jp/');
        $request = $client->post($page);
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->addPostFields($post_params);
        return $request->send();
    }
    
    private static function getRequest($page, $post_params = array())
    {
        $client = new Client('http://dragonx.asobism.co.jp/');
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
        $response = self::postRequest('top/field/checkStamina.php?HTTP_UTIL=1');
        $json = $response->getBody();
        $j = json_decode($json, true);
        if (is_null($j)) {
            return false;
        }
        return intval($j['stamina']);
    }
    
    public static function touchFieldEvent(\app\model\FieldEvent $field)
    {
        $response = self::postRequest('top/field/fieldEvent.php?HTTP_UTIL=1',
                array(
                    'id' => $field->getEventId(),
                    'status' => $field->getParam(),
                    'obj' => $field->getId(),
                ));
        // 返り値をみたほうがいいが...
        return true;
    }
    
    /**
     * なぜかリダイレクト動かんので別で書く
     * どういうこと！
     * @return string(html)
     */
    public static function getMapHtml()
    {
        $client = new Client('http://dragonx.asobism.co.jp/');
        $request = $client->get('top/field/fieldMap.php?HTTP_UTIL=1');
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
        if (!$response->isRedirect()) {
            return $response->getBody();
        }
        $request = $client->get('top/field/fieldIndex.php?HTTP_UTIL=1');
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
        return $response->getBody();
    }
    
    public static function fieldReset()
    {
        //http://dragonx.asobism.co.jp/top/field/fieldObjectReset.php?HTTP_UTIL=1
        $client = new Client('http://dragonx.asobism.co.jp/');
        $request = $client->post('top/field/fieldObjectReset.php?HTTP_UTIL=1');
        $request->setHeader('User-Agent', \CONFIG_USER::USER_AGENT);
        $request->addCookie('viewer_data', \CONFIG_USER::VIEWER_ID);
        $request->getParams()->set('redirect.disable', true);
        $response = $request->send();
    }
    
    public static function battleMonster(\app\model\Monster $monster)
    {
        return self::postRequest('top/field/fieldBattle.php?HTTP_UTIL=1',
                array(
                    'mid' => $monster->getId(),
                    'drop' => is_null($monster->getBoxDrop()) ? 'null' : '1',
                    // result = true で全勝利となる
                    'result' => 'true'//なぜか文字列
                ));
    }
}
