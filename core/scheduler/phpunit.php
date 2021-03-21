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
    'testsPath' => dirname(MODX_CORE_PATH) . '/tests/',
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

$CrontabPhpUnit->print_msg('PHP unit tests');
$CrontabPhpUnit->print_msg('-----------');
$response = $CrontabPhpUnit->runTest($test, $task);

$CrontabPhpUnit->print_msg('-----------');
if ($response !== true) {
    $CrontabPhpUnit->print_msg('Failure ✘');
    if (!empty($response)) {
        $CrontabPhpUnit->print_msg('Message: ' . $response);
    }
} else {
    $CrontabPhpUnit->print_msg('Success ✔');
}
$CrontabPhpUnit->print_msg('-----------');
exit('COMPLITED');