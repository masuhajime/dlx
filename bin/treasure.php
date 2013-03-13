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


$url = 'top/gacha/treasureNormal.php?type=normal&mode=1&num=12';
$params = array(
);

$client = new Client(app\helper\DlxUrl::URL_DRAGONX);
$request = $client->get($url);
$request->setHeader('User-Agent', CONFIG_USER::USER_AGENT);
$request->addCookie('viewer_data', CONFIG_USER::VIEWER_ID);
#$request->addPostFields($params);
$request->getParams()->set('redirect.disable', true);
$response = $request->send();
echo $response->getBody();

// http://dragonx.asobism.co.jp/top/gacha/treasureNormal.php?type=normal&mode=1&num=12