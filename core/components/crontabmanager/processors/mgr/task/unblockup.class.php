<?php

/**
 * Блокировка задания на указанное количество минут
 */
class CronTabManagerTaskUnBlockUpProcessor extends modObjectProcessor
{
    /* @var CronTabManager $CronTabManager */
    public $CronTabManager = null;
    public $objectType = 'CronTabManagerTask';
    public $classKey = 'CronTabManagerTask';
    public $languageTopics = array('crontabmanager:manager');
    public $permission = 'crontabmanager_un_blocked';


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


        /** @var CronTabManagerTask $object */
        if (!$object = $this->modx->getObject($this->classKey, $id)) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_nf'));
        }

        $object->unBlockUpTask();
        $object->unLock();
        if (!$object->save()) {
            return $this->failure($this->modx->lexicon('crontabmanager_task_err_save'));
        }
        return $this->success($this->modx->lexicon('crontabmanager_task_un_look'));
    }
}

return 'CronTabManagerTaskUnBlockUpProcessor';
