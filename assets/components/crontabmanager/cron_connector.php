<?php
define('MODX_CRONTAB_MODE', true);
define('MODX_CRONTAB_MAX_TIME', 33);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$task = preg_replace('/[^a-zA-Z0-9\-\._]/', '/', $_REQUEST['path_task']);
$scheduler_path = preg_replace('/[^a-zA-Z0-9\-\._]/', '/', $_REQUEST['scheduler_path']);


if (!file_exists($scheduler_path)) {
    exit('Контроллер не найден');
}

require_once $scheduler_path . '/index.php';


if (!$CronTabManager instanceof CronTabManager) {
    exit('Error load class CronTabManager');
}

if (!$modx->hasPermission('crontabmanager_task_run')) {
    exit($modx->lexicon('access_denied'));
}

$path_link = $CronTabManager->config['linkPath'] . '/' . $task;
if (!file_exists($path_link)) {
    // Проверяем ссылку на контроллер. Если нету то генерируем новый
    $scheduler->generateCronLink();
}

echo '<button class="crontabmanager-btn crontabmanager-btn-default icon icon-play" onclick="runTaskWindow()" title="Запустить задание"> <small >Перезапустить</small></button>';
echo '<button class="crontabmanager-btn crontabmanager-btn-default icon icon-unlock" onclick="unlockTask()" title="Разблокировать"> <small>Разблокировать</small></button>';
echo '<hr>';
$scheduler->php(str_ireplace('.php', '', $task));
$scheduler->process();