<?php
if (empty($_REQUEST['task_id'])) {
    die('Access denied');
} else {
    $task_id = (int)$_REQUEST['task_id'];
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';


$task_id = (int)$_REQUEST['task_id'];
$minutes = (int)$_REQUEST['minutes'];
$email = (int)$_REQUEST['email'];
$reset = (boolean)$_REQUEST['reset'];


/*if (!$modx->getAuthenticatedUser('mgr')) {
    die('Access denied log in required content mgr');
}*/

/* @var CronTabManager $CronTabManager */
$CronTabManager = $modx->getService('crontabmanager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');
$action = 'blockup';
if ($reset) {
    $action = 'unblockup';
}
$response = $CronTabManager->runProcessor('mgr/task/'.$action, array(
    'id' => $task_id,
    'minutes' => $minutes,
));
$response = $modx->toJSON($response->response);
@session_write_close();
exit($response);