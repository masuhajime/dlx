<?php

require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

use app\helper\Logger;

\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);

//Logger::setLogLevel(Logger::LEVEL_DEBUG);
Logger::setLogLevel(Logger::LEVEL_INFO);

$nubobos = \app\helper\DlxAccesser::getCaptureMonsters();
if (0 === count($nubobos)) {
    Logger::info('0 nubo');
}
foreach ($nubobos as $nubo) {
    Logger::info('capture: '.$nubo->toString());
    \app\helper\DlxAccesser::captureMonster($nubo);
    sleep(3);
}

Logger::info('bye');