<?php

/**
 * Get an Task
 */
class CronTabManagerTaskGetProcessor extends modObjectGetProcessor
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

        $content = $this->object->readLogFileFormat();
        $yesLog = !empty($content);
        $return = $this->getProperty('return', false);
        if (!$return) {
            exit($content);
        } else {
            return $this->success($this->modx->lexicon('crontabmanager_not_log_content'), ['yesLog' => $yesLog, 'content' => $content]);
        }

    }

}

return 'CronTabManagerTaskGetProcessor';
