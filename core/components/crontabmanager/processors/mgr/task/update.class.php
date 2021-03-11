<?php

/**
 * Update an Task
 */
class CronTabManagerTaskUpdateProcessor extends modObjectUpdateProcessor
{
    /* @var CronTabManagerTask $object */
    public $object = 'CronTabManagerTask';
    public $objectType = 'CronTabManagerTask';
    public $classKey = 'CronTabManagerTask';
    public $languageTopics = array('crontabmanager:manager');
    public $permission = 'crontabmanager_save';

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
        $path_task_your = $this->setCheckbox('path_task_your');
        $path_task = trim($this->getProperty('path_task'));
        if (empty($path_task)) {
            return $this->modx->lexicon('crontabmanager_task_err_ns.path_task');
        }


        if (!$path_task_your) {
            // Контроллер
            $schedulerPath = $this->modx->getOption('crontabmanager_scheduler_path');
            $controller = $schedulerPath . '/Controllers/' . $path_task;
            if (!file_exists($controller)) {
                $this->modx->error->addField('path_task', $this->modx->lexicon('crontabmanager_task_err_ae_controller', array('controller' => $controller)));
            }
        } else {
            // Свой файл
            if (!file_exists($path_task)) {
                $this->modx->error->addField('path_task', $this->modx->lexicon('crontabmanager_task_year_err_ae_controller', array('controller' => $path_task)));
            }
        }



        $send = $this->getProperty('status');
        if ($send == 1 or $send == 3) {
            $this->setProperty('approach', 0);
            $this->setProperty('sent', '0000-00-00 00:00:00');
        }
        return parent::beforeSet();
    }


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        $active = $this->setCheckbox('active');
        if ($active) {
            $action = 'add';
            $response = $this->object->addCron();
        } else {
            $action = 'remove';
            $response = $this->object->removeCron();
        }
        if (!$response) {
            return $this->modx->lexicon('crontabmanager_task_err_' . $action . '_crontab', array('task_path' => $this->object->get('path_task')));
        }
        return true;
    }


}

return 'CronTabManagerTaskUpdateProcessor';
