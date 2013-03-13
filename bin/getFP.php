<?php
define("TARGET_ID", 290104);
#define("TARGET_ID", 322092);
if (isset($argv[1])) {
    $talk_count = intval($argv[1]);
} else {
    $talk_count = 1;
}

require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

use app\helper\Logger;

\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);

//Logger::setLogLevel(Logger::LEVEL_DEBUG);
Logger::setLogLevel(Logger::LEVEL_INFO);

$target = new \app\model\Player();
$target->setId(TARGET_ID);

$pointer_file = DIR_DATA.'/pointer.txt';
if (file_exists($pointer_file)) {
    $pointer = intval(file_get_contents($pointer_file));
} else {
    $pointer = 0;
}
$viewer_id_list = new \app\model\ViewerIdList(DIR_DATA.'/viewer_ids.txt');
$viewer_id_list->seekPointerTo($pointer);

do {
    $player = new \app\model\PlayerHandling($viewer_id_list->getNext());
    echo "talk {$player->getId()} to {$target->getId()}".PHP_EOL;
    \app\helper\DlxAccesser::talkToUser($player, $target, true);
    usleep(1000*500);
} while (--$talk_count);

file_put_contents($pointer_file, $viewer_id_list->getPointerNow());
