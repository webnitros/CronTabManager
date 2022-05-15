<?php

/**
 * Create an Task
 */
class CronTabManagerAutoPauseCreateProcessor extends modObjectCreateProcessor
{
    /* @var CronTabManagerAutoPause $object */
    public $object = 'CronTabManagerAutoPause';
    public $objectType = 'CronTabManagerAutoPause';
    public $classKey = 'CronTabManagerAutoPause';
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

        $when = trim($this->getProperty('when'));
        if (empty($when)) {
            $this->modx->error->addField('when', $this->modx->lexicon('crontabmanager_task_autopause_err_when'));
        }

        $from = trim($this->getProperty('from'));
        if (empty($from)) {
            $this->modx->error->addField('from', $this->modx->lexicon('crontabmanager_task_autopause_err_from'));
        }


        $to = trim($this->getProperty('to'));
        if (empty($to)) {
            $this->modx->error->addField('to', $this->modx->lexicon('crontabmanager_task_autopause_err_to'));
        }


        $this->setCheckbox('active');


        return parent::beforeSet();
    }
}

return 'CronTabManagerAutoPauseCreateProcessor';
