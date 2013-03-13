<?php
require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();
require_once dirname(__FILE__).'/../vendor/autoload.php';


use app\helper\Logger;

use Guzzle\Http\Client;
function create_id()
{
    $o = array('u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9',);
    $r = '';
    for($i=0; $i<40; $i++) {
        $r .= $o[array_rand($o)];
    }
    return $r;
}

$client = new Client("http://dragonx.asobism.co.jp");
$request = $client->get("/top/index/gameIndex.php");
$request->setHeader('Viewer_ID', create_id());
$request->setHeader('isApp', 'true');
$request->getParams()->set('redirect.disable', true);
$request->send();
$response = $request->send();
$set_cookie = $response->getHeader("Set-Cookie")->raw();
if (!isset($set_cookie["Set-Cookie"][0])) {
    throw new Exception("no set cookie");
}
$set_cookie = $set_cookie["Set-Cookie"][0];
$m = array();
var_dump($set_cookie);
if (0 === preg_match("/viewer_data=(\d+)%2C([a-z0-9]+);/", $set_cookie, $m)) {
    throw new Exception("no viewer data match");
}
$viewer_id = $m[1].'%2C'.$m[2];
file_put_contents(dirname(__FILE__)."/../data/viewer_ids.txt", $viewer_id.PHP_EOL, FILE_APPEND);
$id = $m[1];
echo $viewer_id.PHP_EOL;

$route = array(
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialIndex.php?tutorialID=0&flow=10',
        'param' => array(
        ),
        "sleep" => 10,
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialIndex.php?',
        'param' => array(
            'phase' => 'create',
            'sex' => mt_rand(1, 2),
            'job' => mt_rand(1, 3),
            'name' => mt_rand(0,100)%2 ? horse_name() : hiragana_name_with_a(),//'tomo',
            'inviteID' => 'undefined',
        ),
        "sleep" => 10,
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialGachaProcess.php?tutorialID=100&flow=10',
        'param' => array(
        ),
        "sleep" => 10,
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialGachaResult.php?tutorialID=100&flow=20',
        'param' => array(
        ),
        "sleep" => 10,
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialTeamIndex.php?tutorialID=200&flow=1',
        'param' => array(
        ),
        "sleep" => 10,
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialMypage.php?tutorialID=300&flow=1',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialBattleIndex.php?tutorialID=300&flow=10',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialMypage.php?tutorialID=400&flow=1',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldMap.php?tutorialID=400&flow=10',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldIndex.php?tutorialID=400&flow=20',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldMap.php?tutorialID=400&flow=30',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldIndex.php?tutorialID=400&flow=40',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldMap.php?tutorialID=400&flow=50',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldMap.php?tutorialID=400&flow=110',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldBoss.php?tutorialID=400&flow=120',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/tutorial/tutorialFieldBossResult.php?tutorialID=400&flow=130&drop[]=4000009',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/field/fieldMap.php?tutorialID=500&flow=1',
        'param' => array(
        ),
    ),
);
$after_lv2 = array(
    array(
        'type' => 'GET',
        'url'  => 'top/mypage/mypage.php',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/header/checkHeaderStatus.php?HTTP_UTIL=1',
        'param' => array(
        ),
    ),
    array(
        'type' => 'GET',
        'url'  => 'top/invite/inviteIndex.php',
        'param' => array(
        ),
    ),
    array(
        'type' => 'POST',
        'url'  => 'top/invite/requestInviteProcess.php?HTTP_UTIL=1',
        'param' => array(
            'request' => 'e12f88'
        ),
    ),
    array(
        'type' => 'POST',
        'url'  => 'top/friend/friendDeleteProcess.php?HTTP_UTIL=1',
        'param' => array(
            'oid' => '290104'
        ),
    ),
    array(
        'type' => 'POST',
        'url'  => 'top/setup/pushNewsProcess.php?HTTP_UTIL=1',
        'param' => array(
            'userID' => $id,
            'number' => 1,
            'on' => 1,
        ),
    ),
    array(
        'type' => 'POST',
        'url'  => 'top/setup/pushNewsProcess.php?HTTP_UTIL=1',
        'param' => array(
            'userID' => $id,
            'number' => 5,
            'on' => 1,
        ),
    ),
    array(
        'type' => 'POST',
        'url'  => 'top/setup/pushNewsProcess.php?HTTP_UTIL=1',
        'param' => array(
            'userID' => $id,
            'number' => 6,
            'on' => 1,
        ),
    ),
    array(
        'type' => 'POST',
        'url'  => 'top/setup/pushNewsProcess.php?HTTP_UTIL=1',
        'param' => array(
            'userID' => $id,
            'number' => 7,
            'on' => 1,
        ),
    )
);

foreach ($route as $r) {
    echo $r['type'].':'.$r['url'].PHP_EOL;
    if ($r['type'] == 'GET') {
        $response = DlxAccesser::getRequest($r['url'], $viewer_id, $r['param']);
    } else {
        $response = DlxAccesser::postRequest($r['url'], $viewer_id, $r['param']);
    }
    if (isset($r['sleep'])) {
        sleep(intval($r['sleep']));
    } else {
        sleep(5);
    }
}
main($viewer_id);
foreach ($after_lv2 as $r) {
    echo $r['type'].':'.$r['url'].PHP_EOL;
    if ($r['type'] == 'GET') {
        $response = DlxAccesser::getRequest($r['url'], $viewer_id, $r['param']);
    } else {
        $response = DlxAccesser::postRequest($r['url'], $viewer_id, $r['param']);
    }
    if (isset($r['sleep'])) {
        sleep(intval($r['sleep']));
    } else {
        sleep(5);
    }
}

class DlxUrl {
    const URL_DRAGONX = 'http://dragonx.asobism.co.jp/';
}

class DlxAccesser {

    private function __construct() {}

    public static function postRequest($page, $id, $post_params = array())
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $request = $client->post($page);
        $request->setHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25');
        $request->addCookie('viewer_data', $id);
        $request->addPostFields($post_params);
        $request->getParams()->set('redirect.disable', true);
        return $request->send();
    }
    
    public static function getRequest($page, $id, $get_params = array())
    {
        $client = new Client(DlxUrl::URL_DRAGONX);
        $p = "";
        if (0 < count($get_params)) {
            $p = "?".http_build_query($get_params);
        }
        $request = $client->get($page.$p);
        $request->setHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25');
        $request->addCookie('viewer_data', $id);
        $request->getParams()->set('redirect.disable', true);
        //$request->addPostFields($post_params); TODO:
        echo "REQUEST_URL:".$request->getUrl().PHP_EOL;
        return $request->send();
    }
}

function createName()
{
    $k = array("ほご", "りんご", "みかん", "牛乳", "トマト", "リーダ", "毎週", "心得て", "Y",
        "一切", "噂", "りだ", "ちゃん", "ニル", "候補", "友達", "振る", "ごめん", "baby", "冗談", "嬉しい",
        "チラつか", "しかめ", "鹿目", "世界", "枝", "躾", "東京", "大阪", "宇宙", "いますぐ", "本当",
        "ワイド", "wide", "撫でる", "揺れる", "DJ", "Dr.", "Mr.", "Music", "ドル", "円", "陣", "みんな",
        "不満", "私", "朝日", "夜", "花", "遠く", "猫", "犬", "ワンダー", "伝える", "君", "今すぐ",
        "優しい", "春", "夏", "秋", "冬", "黒", "赤", "青", "黄色", "緑", "大原", "Bank", "どこへ",
        "痺れ", "両手", "間隔", "感覚", "My", "麻痺", "くらげ", "馬", "カシス", "ゼリー", "星",
        "アウト", "エブリ", "超える", "明日", "今日", "何も", "今は", "眠り", "古ぼけ", "シャツ",
        "あれ", "これ", "人形", "瞬間", "落ちた", "光", "闇", "火", "水", "風", "土", "ワナ", "buy",
        "親", "林檎", "永遠", "永久", "登る", "富士", "フジ", "那須", "滋賀", "ナス", "扇",
        "時", "雨", "雪", "晴れ", "雷", "電光", "ダーク", "星へ", "暗闇", "藍", "嘘", "偽", "蜘蛛",
        "雲", "星屑", "言葉", "ナゾル", "こころ", "ココロ", "照らし", "通り", "思い", "ブーケ", "額",
        "言い訳", "じかん", "時間", "逆さ", "探して", "あなた");
    $rand_keys = array_rand($k, 2);
    return $k[$rand_keys[0]].$k[$rand_keys[1]];
}

function horse_name()
{
    $words = file(__DIR__.'/../lib/horse_name.txt');
    $rand_keys = array_rand($words, 2);
    return trim($words[$rand_keys[0]]).trim($words[$rand_keys[1]]);
}

function hiragana_name()
{
    $words = file(__DIR__.'/../lib/hiragana.txt');
    $rand_keys = array_rand($words, 2);
    return trim($words[$rand_keys[0]]).trim($words[$rand_keys[1]]);
}

function hiragana_name_with_a()
{
    $name = hiragana_name();
    if (mt_rand(0, 1)) {
        return $name;
    }
    if (7 <= mb_strlen($name)/3) {
        return $name;
    }
    $a = ""; $b = "";
    switch (mt_rand(0, 100)%20) {
        case 0: $a = "*"; $b="*"; break;
        case 1: $a = "-"; $b="-"; break;
        case 2: $a = "☆"; $b="☆"; break;
        case 3: $a = "="; $b="="; break;
        case 4: $a = "+"; $b="+"; break;
        case 5: $a = "†"; $b="†"; break;
        case 6: $a = "♪"; $b="♪"; break;
        case 7: $a = "★"; $b="★"; break;
        case 8: $a = "<"; $b=">"; break;
        case 9: $a = "♡"; $b="♡"; break;
        case 10: $a = "♪"; $b=""; break;
        case 11: $a = ""; $b="♪"; break;
        case 12: $a = ""; $b="★"; break;
        case 13: $a = ""; $b="♫"; break;
        case 14: $a = "♬"; $b=""; break;
        case 15: $a = ""; $b="彡"; break;
        case 16: $a = "■"; $b="■"; break;
        case 17: $a = "♡"; $b=""; break;
        case 18: $a = ""; $b="♡"; break;
        case 19: $a = "♥"; $b=""; break;
    }
    return $a.$name.$b;
}

function main($viewer_data)
{
    $owner = new \app\model\PlayerHandling($viewer_data);
    $monster_defeat_count = 0;
    while (1) {
        $owner->updateAllInfo();
        $field = $owner->getField();
        sleep(2);
        if ($field->isMonsterAppear()) {
            Logger::info('monster appears');
            // battle monster
            while ($field->isMonsterAppear()) {
                $owner->battleMonster();
                $monster_defeat_count++;
                if (5 < $monster_defeat_count) {
                    return;//抜け出す
                }
                sleep(2);
            }
        }
        $stamina = $owner->getStamina();
        Logger::info(
                "stamina:{$stamina} milk:{$owner->getMilkNum()}(use border:".CONFIG_USER::AUTO_MILK_USING_BORDER.")"
                ." capture_count:{$owner->getCaptureCount()}"
                ." exp:{$owner->getExp()} money:{$owner->getMoney()}"
                ." wins:{$owner->getMonsterWin()}"
        );

        if ($stamina <= CONFIG_USER::LIFE_BORDER_FIELD_ACTION) {
                sleep(120);
                continue;
        }

        if (!$field->isMonsterAppear()) {
            if ($owner->touchAssignedEvent()) {
            } else {
                Logger::info('go to next map');
                $owner->fieldReset();
            }
        }
        sleep(3);
    }
}