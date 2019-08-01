<?php
$path_task = $_REQUEST['path_task'];
$scheduler_path = $_REQUEST['scheduler_path'];
$path_task = str_ireplace('.php', '' , $path_task);
#define('MODX_CRONTAB_MODE', true);
require_once $scheduler_path.'/index.php';
if (!$modx->hasPermission('crontabmanager_task_run')) {
   echo '<pre>';
   print_r($modx->lexicon('access_denied')); die;
}
$scheduler->generateCronLink();
$scheduler->php($path_task);
$scheduler->process();