<?php

/**
 * Get a list of Tasks
 */
class CronTabManagerTaskLogGetListProcessor extends modObjectGetListProcessor
{
    /* @var CronTabManager $CronTabManager*/
    public $CronTabManager = null;
    public $objectType = 'CronTabManagerTaskLog';
    public $classKey = 'CronTabManagerTaskLog';
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $permission = 'crontabmanager_list';
    public $languageTopics = array('crontabmanager:manager');

    /** {@inheritDoc} */
    public function initialize() {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        return parent::initialize();
    }

    /**
     * * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }
        return true;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $orderColumns = $this->modx->getSelectColumns('CronTabManagerTaskLog', 'CronTabManagerTaskLog', '', array(), false);
        $c->select($orderColumns);

        $completed = $this->setCheckbox('completed');
        if (!empty($completed)) {
            $c->where(array('completed' => 0));
        }

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'id' => (int)$query
            ));
        }

        $task_id = $this->getProperty('task_id');
        if (!empty($task_id)) {
            $c->where(array('task_id' => $task_id));
        }
        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        /* @var CronTabManagerTaskLog $object*/
        $array = $object->toArray();
        $array['actions'] = array();


        $array['end_run'] = !empty($array['end_run']) ? date('Y-m-d H:i:s', $array['end_run']) : '';
        $array['last_run'] = !empty($array['last_run']) ? date('Y-m-d H:i:s', $array['last_run']) : '';


        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('crontabmanager_task_log_remove'),
            'multiple' => $this->modx->lexicon('crontabmanager_task_logs_remove'),
            'action' => 'removeItem',
            'button' => false,
            'menu' => true,
        );

        return $array;
    }

}

return 'CronTabManagerTaskLogGetListProcessor';