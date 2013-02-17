<?php

require_once dirname(__FILE__).'/../conf/conf.php';
$class_loader = new ClassLoader(null, DIR_BASE);
$class_loader->register();

use app\helper\Logger;

\app\helper\DlxAccesser::setUrlSet(app\helper\DlxUrl::URL_SET_DEFAULT);

//Logger::setLogLevel(Logger::LEVEL_DEBUG);
Logger::setLogLevel(Logger::LEVEL_INFO);


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
    $owner = new \app\model\PlayerHandling(CONFIG_USER::VIEWER_ID);
    while (1) {
        $owner->updateAllInfo();
        $field = $owner->getField();
        sleep(2);
        if ($field->isMonsterAppear()) {
            Logger::info('monster appears');
            // battle monster
            while ($field->isMonsterAppear()) {
                $owner->battleMonster();
                sleep(3);
            }
        }
        // ぬぼぼ捕獲処理
        if (2 < $owner->getCaptureCount()) {
            Logger::info("capture start (count:{$owner->getCaptureCount()})");
            $nubobos = $owner->getCapturedMonsters();
            foreach ($nubobos as $nubo) {
                Logger::info('capture: '.$nubo->toString());
                \app\helper\DlxAccesser::captureMonster($owner->getViewerData(), $nubo);
                sleep(3);
            }
            $owner->setCaptureCount(0);
        }
        $stamina = $owner->getStamina();
        Logger::info(
                "stamina:{$stamina} milk:{$owner->getMilkNum()}(use border:".CONFIG_USER::AUTO_MILK_USING_BORDER.")"
                ." capture_count:{$owner->getCaptureCount()}"
                ." exp:{$owner->getExp()} money:{$owner->getMoney()}"
                ." wins:{$owner->getMonsterWin()}"
        );

        if ($stamina <= CONFIG_USER::LIFE_BORDER_FIELD_ACTION) {
            if (CONFIG_USER::AUTO_MILK_ENABLE
             && $stamina === 0
             && 0 < $owner->getMilkNum()
             && CONFIG_USER::AUTO_MILK_USING_BORDER < $owner->getMilkNum()) {
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
            // 召喚獣関連の処理
            if (CONFIG_USER::FIELD_EVENT_BOSS && $field->isFieldBossAppear()) {
                Logger::info('field boss appear');
                while (1) {//連続出現処理
                    $fb = $field->getFieldBoss();
                    sleep(5);
                    if (false === $fb) {
                        Logger::info('field boss disappeared');
                        $field->setFieldBossEventAsTouched();
                        break;
                    }
                    Logger::info($fb->toString());
                    $fb->process();
                    sleep(5);
                }
            }
            if ($owner->touchAssignedEvent()) {
            } else {
                Logger::info('go to next map');
                $owner->fieldReset();
            }
        }
        sleep(3);
    }
}