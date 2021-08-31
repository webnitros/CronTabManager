<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 11.03.2021
 * Time: 10:20
 */
ini_set('display_errors', 1);
ini_set("max_execution_time", 50);
define('MODX_API_MODE', true);
require 'index.php';

$crontabmanager = $modx->getService('crontabmanager', 'crontabmanager', MODX_CORE_PATH . 'components/crontabmanager/model/');

/* @var CronTabManagerTask $CronTabManagerTask */
if ($task = $modx->getObject('CronTabManagerTask', 22)) {

    $task->setSaveLog(1);
    $task->set('end_run', time());
    $task->set('completed', true); // Устанавливаем метку завершенности

    // Снятие блокировки
    $task->unLock();
    $task->save();

}


echo '<pre>';
print_r(55555);
die;
