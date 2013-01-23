<?php
require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

$field = app\model\Field::getInstance();
$owner = \app\model\OwnerPlayer::getInstance();
while (1) {
    $field->update();
    sleep(2);
    if ($field->hasMonster()) {
        echo "has monster".PHP_EOL;
        // battle monster
        while ($field->hasMonster()) {
            $field->battleMonster();
            sleep(3);
        }
    }
    $stamina = $owner->getStamina();
    echo "stamina:".$stamina.PHP_EOL;
    if ($stamina === 0) {
        sleep(120);
    }
    
    $field->update();
    sleep(2);
    if (!$field->hasMonster()) {
        echo "has no monster".PHP_EOL;
        if (!$field->touchMonsterEvent()) {
            echo "map reset".PHP_EOL;
            $field->reset();
        } else {
            echo "touch monster event".PHP_EOL;
        }
    }
    sleep(3);
}
