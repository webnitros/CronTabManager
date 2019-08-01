<?php

/**
 * Get an Task
 */
class CronTabManagerTaskRemoveLogProcessor extends modObjectGetProcessor
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
        $path = $this->object->getFileLogPath();
        if (file_exists($path)) {
            unlink($path);
        }
        return parent::process();
    }

}

return 'CronTabManagerTaskRemoveLogProcessor';