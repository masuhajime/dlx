<?php

require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

require 'vendor/autoload.php';
use app\helper\Logger;
use Guzzle\Http\Client;

\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);

//Logger::setLogLevel(Logger::LEVEL_DEBUG);
Logger::setLogLevel(Logger::LEVEL_INFO);


$url = 'top/bag/bagIndex.php';
$client = new Client(app\helper\DlxUrl::URL_DRAGONX);
$request = $client->get($url);
$request->setHeader('User-Agent', CONFIG_USER::USER_AGENT);
$request->addCookie('viewer_data', CONFIG_USER::VIEWER_ID);
#$request->addPostFields($params);
$request->getParams()->set('redirect.disable', true);
$response = $request->send();
$html = $response->getBody();

$m = array();
if (0 === preg_match('/var armsList[ \t]*=[ \t]*\[(.*)\];/i', $html, $m)) {
    echo "item parse fail.";
    exit;
}
$item_list_json = "[{$m[1]}]";
$item_list = json_decode($item_list_json);
//var_dump($item_list);
$list_sell = array();
foreach($item_list as $item) {
    $rare = intval($item->rare);
    // 装備しているものは省く
    if (0 !== intval($item->equip)) {
        continue;
    }
    if (!in_array($rare, array(0,1))) {
        continue;
    }
    if ($rare === 0 && 15 < intval($item->point)) {
        continue;
    }
    if ($rare === 1 && 12 < intval($item->point)) {
        continue;
    }
    // 保険
    if (false !== strpos($item->rareLabel, 'S')) {
        continue;
    }
    $list_sell[] = $item->id;
}
$client = new Client(app\helper\DlxUrl::URL_DRAGONX);
$request = $client->post("top/bag/bagRequestSale.php?HTTP_UTIL=1");
$request->setHeader('User-Agent', CONFIG_USER::USER_AGENT);
$request->addCookie('viewer_data', CONFIG_USER::VIEWER_ID);
$request->getParams()->set('redirect.disable', true);
$request->addPostFields(array(
    "id" => $list_sell
));
$response = $request->send();
$response->getBody();
$num = count($list_sell);
echo <<< RESUT
num:{$num}

RESUT;
