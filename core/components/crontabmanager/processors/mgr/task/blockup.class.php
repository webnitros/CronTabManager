<?php

/**
 * Блокировка задания на указанное количество минут
 */
class CronTabManagerTaskBlockUpProcessor extends modObjectProcessor
{
    /* @var CronTabManager $CronTabManager */
    public $CronTabManager = null;
    public $objectType = 'CronTabManagerTask';
    public $classKey = 'CronTabManagerTask';
    public $languageTopics = array('crontabmanager:manager');
    public $permission = 'crontabmanager_add_blocked';


    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        $this->CronTabManager = $this->modx->getService('crontabmanager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');
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

        $id = $this->modx->fromJSON($this->getProperty('id'));
        if (empty($id)) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_ns'));
        }

        $minutes = (int)$this->getProperty('minutes');
        if (!is_int($minutes) or $minutes == 0) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_ns_minutes'));
        }


        $max_minuts_blockup = $this->modx->getOption('crontabmanager_max_minuts_blockup', null, 1440);
        if ($minutes > $max_minuts_blockup) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_ns_max_minuts_blockup', array('max_minuts_blockup' => $max_minuts_blockup)));
        }

        $allow_blocking_tasks = $this->modx->getOption('crontabmanager_allow_blocking_tasks', null, false);
        if (!$allow_blocking_tasks) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_ns_allow_blocking_tasks'));
        }

        /** @var CronTabManagerTask $object */
        if (!$object = $this->modx->getObject($this->classKey, $id)) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_nf'));
        }

        $object->addBlockUpTask($minutes);
        if (!$object->save()) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_save'));
        }
        return $this->success($this->modx->lexicon('crontabmanager_task_blockup_minutes_add',array('minutes' => $minutes)));
    }
}

return 'CronTabManagerTaskBlockUpProcessor';
