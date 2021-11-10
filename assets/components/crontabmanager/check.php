<?php

namespace Webnitros\CronTabManager;

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET POST DELETE REQUEST');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

use CronTabManager;
use Webnitros\CronTabManager\Exceptions\AuthException;
use modX;


define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';


/* @var modX $modx */
/* @var CronTabManager $CronTabManager */
$CronTabManager = $modx->getService('crontabmanager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/', ['json_response' => true]);

$action = @$_GET['action'];
switch ($action) {
    case 'check':
        $Server = new Server($CronTabManager);
        $Server->process();
        break;
    default:
        break;
}
