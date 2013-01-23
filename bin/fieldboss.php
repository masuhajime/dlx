<?php
require 'vendor/autoload.php';
use Guzzle\Http\Client;

$client = new Client('http://dragonx.asobism.co.jp/');
$request = $client->post('top/field/fieldBossProcess.php?HTTP_UTIL=1');
$request->setHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25');
$request->addCookie('viewer_data', '');
$request->addPostFields(array('bid' => '5217742',
'param' => '1',
'result' => '90',
'continueFlag' => '1',
));
$response = $request->send();

echo $response->getBody();

