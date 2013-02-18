<?php
require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

require 'vendor/autoload.php';
use Guzzle\Http\Client;
use app\helper\Logger;

\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);

Logger::setLogLevel(Logger::LEVEL_INFO);

$url = 'top/battle/battleCommandProcess.php?HTTP_UTIL=1';
$params = array(
    'commandID' => 30,
    'power' => 3,
);
$url = 'http://dragonx.asobism.co.jp/top/beauty/beautySave.php?HTTP_UTIL=1';
$params = array(
"head" => 2305012,
"body" => 2405013,
"weapon" => 1300432,
"hair" => 3100118,
"face" => 3200012,
"bg" => 3300023,
"rare" => 2,
"hflag" => 0,
"job" => 15,
);
$url = 'http://dragonx.asobism.co.jp/top/equip/equipRequestChange.php?HTTP_UTIL=1';
$params = array(
'eid' => 11,
'aid' => 104137785
);
$url = 'http://dragonx.asobism.co.jp/top/battle/checkBattle.php?HTTP_UTIL=1';
$params = array(
"userID" => "",
"timeID" => "201302171200"
);
$url = 'http://dragonx.asobism.co.jp/top/battle/battleCommandProcess.php?HTTP_UTIL=1';
$params = array(
"commandID" => intval($argv[1]),
"power" => "3"
);

echo "url:".$url.PHP_EOL;
var_dump($params);

$client = new Client(app\helper\DlxUrl::URL_DRAGONX);
$request = $client->post($url);
$request->setHeader('User-Agent', CONFIG_USER::USER_AGENT);
$request->addCookie('viewer_data', CONFIG_USER::VIEWER_ID);
$request->addPostFields($params);
$response = $request->send();
echo $response->getBody();
