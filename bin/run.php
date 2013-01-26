<?php

require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

use app\helper\Logger;

//\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_GOLD_EVENT);
\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);

//Logger::setLogLevel(Logger::LEVEL_DEBUG);
Logger::setLogLevel(Logger::LEVEL_INFO);
$field = app\model\Field::getInstance();
$field->setAssignedTouchEvents(array(
\app\model\FieldEvent::MONSTER,
\app\model\FieldEvent::BOX,
//\app\model\FieldEvent::COIN,
));

while (1) {
    try {
        main();
    } catch (app\helper\exception\UnexpectedResponse $e) {
        Logger::alert($e->getTraceAsString());
    } catch (Exception $e) {
        Logger::alert($e->getTraceAsString());
    }
    sleep(60*5);
}

function main()
{
    $field = app\model\Field::getInstance();
    $owner = \app\model\OwnerPlayer::getInstance();
    while (1) {
        $owner->updateAllInfo();
        sleep(2);
        if ($field->isMonsterAppear()) {
            Logger::info('monster appears');
            // battle monster
            while ($field->isMonsterAppear()) {
                $field->battleMonster();
                sleep(3);
            }
        }
        if (2 < $owner->getCaptureCount()) {
            Logger::info("capture start (count:{$owner->getCaptureCount()})");
            $nubobos = \app\helper\DlxAccesser::getCaptureMonsters();
            foreach ($nubobos as $nubo) {
                Logger::info('capture: '.$nubo->toString());
                \app\helper\DlxAccesser::captureMonster($nubo);
                sleep(3);
            }
            $owner->setCaptureCount(0);
        }
        $stamina = $owner->getStamina();
        Logger::info(
                "stamina:{$stamina} milk:{$owner->getMilkNum()}(use border:".CONFIG_USER::USE_AUTO_MILK_BORDER.")"
                ." capture_count:{$owner->getCaptureCount()}"
                ." exp:{$owner->getExp()} money:{$owner->getMoney()}"
                ." wins:{$owner->getMonsterWin()}"
        );
        // もしかすると召喚獣の連続出現に残りスタミナが関係しているのでは?
        // 2にして様子を見よう -> 牛乳は機能しません^-^
        if ($stamina <= 3) {
            if (CONFIG_USER::USE_AUTO_MILK 
             && $stamina === 0
             && 0 < $owner->getMilkNum()
             && CONFIG_USER::USE_AUTO_MILK_BORDER < $owner->getMilkNum()) {
                Logger::info("using MILK");
                $owner->useMilk(); sleep(1);
                $stamina = $owner->getStamina(); sleep(1);
                Logger::info("stamina:".$stamina);
            } else {
                sleep(120);
                continue;
            }
        }

        if (!$field->isMonsterAppear()) {
            //Logger::info('no monster appear');
            if ($field->isFieldBossAppear()) {
                Logger::info('field boss appear');
                while (1) {//連続出現処理
                    $fb = $field->getFieldBoss();
                    sleep(5);
                    if (false === $fb) {
                        Logger::warning('failed to get boss data', __LINE__, __FILE__);
                        break;
                    }
                    Logger::info($fb->toString());
                    $fb->process();
                    sleep(5);
                }
            }
            if ($field->touchAssignedEvent()) {
            } else {
                Logger::info('go to next map');
                $field->reset();
            }
        }
        sleep(3);
    }
}