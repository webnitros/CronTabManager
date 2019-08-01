<?php

/**
 * Create an Task
 */
class CronTabManagerTaskCreateProcessor extends modObjectCreateProcessor
{
    /* @var CronTabManagerTask $object */
    public $object = 'CronTabManagerTask';
    public $objectType = 'CronTabManagerTask';
    public $classKey = 'CronTabManagerTask';
    public $languageTopics = array('crontabmanager:manager');
    public $permission = 'crontabmanager_create';


    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        return parent::initialize();
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {

        $path_task = trim($this->getProperty('path_task'));
        if (empty($path_task)) {
            $this->modx->error->addField('path_task', $this->modx->lexicon('crontabmanager_task_err_path_task'));
        } elseif ($this->modx->getCount($this->classKey, array('path_task' => $path_task))) {
            $this->modx->error->addField('path_task', $this->modx->lexicon('crontabmanager_task_err_ae'));
        }
        $schedulerPath = $this->modx->getOption('crontabmanager_scheduler_path');


        $controller = $schedulerPath . '/Controllers/' . $path_task;
        if (!file_exists($controller)) {
            $this->modx->error->addField('path_task', $this->modx->lexicon('crontabmanager_task_err_ae_controller', array('controller' => $controller)));
        }

        $this->setProperty('status', 1);
        $this->setProperty('active', false);
        $this->setCheckbox('active');
        return parent::beforeSet();
    }
}

return 'CronTabManagerTaskCreateProcessor';