<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 15.05.2022
 * Time: 10:25
 */
if (empty($_REQUEST['hash'])) {
    die('Access denied');
}

$hash = (string)$_REQUEST['hash'];
if (strlen($hash) !== 32) {
    die('Error hash code');
}

function isValidMd5($md5 = '')
{
    return preg_match('/^[a-f0-9]{32}$/', $md5);
}

if (!isValidMd5($hash)) {
    die('Invalide hash code');
}

define('MODX_API_MODE', true);
require_once dirname(__FILE__, 5) . '/index.php';

/* @var CronTabManager $CronTabManager */
$CronTabManager = $modx->getService('crontabmanager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');


// hash is valid for 24 hours
#$timeout = 86400;
$timeout = strtotime('-1 days', time());
$criteria = [
    'createdon:>' => $timeout,
    'hash' => $hash,
];

/* @var CronTabManagerTaskLog $Log */
if (!$Log = $modx->getObject('CronTabManagerTaskLog', $criteria)) {
    die('Log Task not found or the hash link is outdated. hash: ' . $hash);
}
