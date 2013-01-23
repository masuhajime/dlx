<?php

require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

$field = app\model\Field::getInstance();
$owner = \app\model\OwnerPlayer::getInstance();

$nubobos = \app\helper\DlxAccesser::getCaptureMonsters();
\app\helper\DlxAccesser::captureMonster($nubobos[0]);

