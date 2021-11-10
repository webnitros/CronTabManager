<?php
define('MODX_CRONTAB_MODE', true);
define('MODX_CRONTAB_MAX_TIME', 33);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$task = preg_replace('/[^a-zA-Z0-9\-\._]/', '/', $_REQUEST['path_task']);
$scheduler_path = preg_replace('/[^a-zA-Z0-9\-\.:_]/', DIRECTORY_SEPARATOR, $_REQUEST['scheduler_path']);

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

/* @var CronTabManagerTask $ManagerTask */
if (!$ManagerTask = $modx->getObject('CronTabManagerTask', ['path_task' => $task])) {
    exit($modx->lexicon('Task not found ' . $task));

}

if ($ManagerTask->get('path_task_your')) {
    $path_link = $task;
} else {
    $path_link = $CronTabManager->config['linkPath'] . '/' . $task;
    if (!file_exists($path_link)) {
        // Проверяем ссылку на контроллер. Если нету то генерируем новый
        $scheduler->generateCronLink();
    }
}

$modx->lexicon->load('crontabmanager:manager');
$windows = $modx->lexicon('crontabmanager_cron_connector_run_task_windows');
$windows_btn = $modx->lexicon('crontabmanager_cron_connector_run_task_windows_btn');

$unlock = $modx->lexicon('crontabmanager_cron_connector_unlock');
$unlock_btn = $modx->lexicon('crontabmanager_cron_connector_unlock_btn');

$read_log = $modx->lexicon('crontabmanager_cron_connector_read_log');
$read_log_btn = $modx->lexicon('crontabmanager_cron_connector_read_log');

$connector_args = $modx->lexicon('crontabmanager_cron_connector_args');

$connector_args_value = trim(@$_GET['connector_args']);

echo '<button class="crontabmanager-btn crontabmanager-btn-default icon icon-play" onclick="runTaskWindow()" title="' . $windows . '"> <small >' . $windows_btn . '</small></button>';
echo '<button class="crontabmanager-btn crontabmanager-btn-default icon icon-unlock" onclick="unlockTask()" title="' . $unlock . '"> <small>' . $unlock_btn . '</small></button>';
echo '<button class="crontabmanager-btn crontabmanager-btn-default icon icon-eye" onclick="readLogFileBody()" title="' . $read_log . '"> <small>' . $read_log_btn . '</small></button>';
echo '<input type="text" placeholder="' . $connector_args . '" class="crontabmanager-cron-args x-form-text x-form-field " id="crontabmanager_connector_args" name="connector_args" value="' . $connector_args_value . '">';
echo '<hr>';

$str = str_ireplace('.php', '', $task);
if (!empty($connector_args_value)) {
    $scheduler->setArgs(['', $connector_args_value]);
}
$scheduler->php(str_ireplace('.php', '', $task));
$scheduler->process();
