<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var CronTabManager $CronTabManager */
$CronTabManager = $modx->getService('CronTabManager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');
$modx->lexicon->load('crontabmanager:default');

// handle request
$corePath = $modx->getOption('crontabmanager_core_path', null, $modx->getOption('core_path') . 'components/crontabmanager/');
$path = $modx->getOption('processorsPath', $CronTabManager->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);