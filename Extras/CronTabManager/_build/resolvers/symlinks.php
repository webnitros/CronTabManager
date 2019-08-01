<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/CronTabManager/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/crontabmanager')) {
            $cache->deleteTree(
                $dev . 'assets/components/crontabmanager/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/crontabmanager/', $dev . 'assets/components/crontabmanager');
        }
        if (!is_link($dev . 'core/components/crontabmanager')) {
            $cache->deleteTree(
                $dev . 'core/components/crontabmanager/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/crontabmanager/', $dev . 'core/components/crontabmanager');
        }
    }
}

return true;