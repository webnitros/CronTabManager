<?php

/**
 * Remove an Items
 */
class CronTabManagerTaskLogClearProcessor extends modObjectProcessor
{
    public $objectType = 'CronTabManagerTaskLog';
    public $classKey = 'CronTabManagerTaskLog';
    public $languageTopics = array('crontabmanager:manager');
    public $permission = 'crontabmanager_remove';

    /** {@inheritDoc} */
    public function initialize()
    {

        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $task_id = (int)$this->getProperty('task_id');
        if (empty($task_id)) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_log_err_ns'));
        }

        if ($task_id > 0) {
            $this->modx->exec("DELETE FROM {$this->modx->getTableName($this->classKey)} WHERE task_id = {$task_id}");
        }
        return $this->success();
    }

}

return 'CronTabManagerTaskLogClearProcessor';