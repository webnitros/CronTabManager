<?php

/**
 * Get an Task
 */
class CronTabManagerTaskManualStopProcessor extends modObjectGetProcessor
{
    /* @var CronTabManagerTask $object */
    public $object;
    public $objectType = 'CronTabManagerTask';
    public $classKey = 'CronTabManagerTask';
    public $languageTopics = array('crontabmanager:default');
    public $permission = 'crontabmanager_view';

    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $schedulerPath = $this->modx->getOption('crontabmanager_scheduler_path');
        $path = $this->object->getFileManualStopPath($schedulerPath . '/Controllers/');
        $cache = $this->modx->getCacheManager();
        if (!$cache->writeFile($path, time())) {
            return $this->failure("Не удалось создать файл " . $path);
        }

        return parent::process();
    }

}

return 'CronTabManagerTaskManualStopProcessor';