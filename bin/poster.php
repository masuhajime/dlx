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

$client = new Client(app\helper\DlxUrl::URL_DRAGONX);
$request = $client->post($url);
$request->setHeader('User-Agent', CONFIG_USER::USER_AGENT);
$request->addCookie('viewer_data', CONFIG_USER::VIEWER_ID);
$request->addPostFields($params);
$response = $request->send();
echo $response->getBody();
