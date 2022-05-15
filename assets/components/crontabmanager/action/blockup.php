<?php
/* @var string $hash */
/* @var CronTabManager $CronTabManager */
/* @var CronTabManagerTaskLog $Log */
include_once dirname(__FILE__) . '/_action.php';


$minutes = (int)$_REQUEST['minutes'];
$email = (int)$_REQUEST['email'];
$reset = (boolean)$_REQUEST['reset'];

$action = 'blockup';
if ($reset) {
    $action = 'unblockup';
}

$response = $CronTabManager->runProcessor('mgr/task/' . $action, array(
    'id' => $Log->get('task_id'),
    'minutes' => $minutes,
));

echo 'success: ' . $response->response['success'] . '<br>';
echo 'message: ' . $response->response['message'] . PHP_EOL;
@session_write_close();
exit();
