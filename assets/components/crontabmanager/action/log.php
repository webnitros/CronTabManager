<?php

/* @var string $hash */
/* @var CronTabManager $CronTabManager */
/* @var CronTabManagerTaskLog $Log */
include_once dirname(__FILE__) . '/_action.php';


$response = $CronTabManager->runProcessor('mgr/task/readlog', array(
    'return' => true,
    'id' => $Log->get('task_id'),
));

$log = $response->response['object']['content'];

echo $log;
@session_write_close();
exit();
