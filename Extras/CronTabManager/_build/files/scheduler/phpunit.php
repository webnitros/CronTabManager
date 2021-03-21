<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.03.2021
 * Time: 11:10
 */

use PHPUnit\Framework\TestSuite;

define('MODX_CONFIG_KEY', 'config');
include_once dirname(__DIR__, 1) . '/config/config.inc.php';
include_once MODX_CORE_PATH . '/components/crontabmanager/lib/phpunit/CrontabPhpUnit.php';
$CrontabPhpUnit = new CrontabPhpUnit([
    'vendorPath' => dirname(MODX_CORE_PATH) . '/vendor/autoload.php',
    'testsPath' => MODX_CORE_PATH . 'tests/',
]);
$result = $CrontabPhpUnit->initialize();
if ($result !== true) {
    exit($result);
}

$ARGS = $CrontabPhpUnit->get_exec_args();
$test = @$ARGS['tests'];
if (empty($test)) {
    exit('Укажите тест test=NameTest');
}

$task = @$ARGS['task'];

$errors = $CrontabPhpUnit->runTest($test, $task);
if (!empty($errors)) {
    foreach ($errors as $error) {
        $msg = $error['msg'];
    }
}
exit('COMPLITED');
