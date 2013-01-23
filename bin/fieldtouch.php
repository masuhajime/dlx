<?php
require 'vendor/autoload.php';
use Guzzle\Http\Client;

$client = new Client('http://dragonx.asobism.co.jp/');
$request = $client->post('top/field/fieldEvent.php?HTTP_UTIL=1');
$request->setHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25');
$request->addCookie('viewer_data', '290104%2Cyy479x373v131z031x4x8859u61z2yx79w683z9u');
$request->addPostFields(array('id' => '0',
'status' => '5',
'obj' => '2',
));
$response = $request->send();

echo $response->getBody();
// >>> {"type":"User", ...
//echo $response->getHeader('Content-Length');
// >>> 792
//$data = $response->xml();
//echo $data['type'];
// >>> User


