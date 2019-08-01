<?php
if (!defined('MODX_CRONTAB_MAX_TIME') OR !MODX_CRONTAB_MAX_TIME) {
    ini_set("max_execution_time", 1000);
} else {
    ini_set("max_execution_time", MODX_CRONTAB_MAX_TIME);
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(__FILE__))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('ECHO');
$modx->error->message = null;

$modx->getRequest();

/* @var CronTabManager $CronTabManager */
$CronTabManager = $modx->getService('crontabmanager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');
$scheduler = $CronTabManager->loadSchedulerService();
if (!defined('MODX_CRONTAB_MODE') OR !MODX_CRONTAB_MODE) {
    $scheduler->getPath();
    $scheduler->process();
}