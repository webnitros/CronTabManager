<?php

/**
 * Get a list of Tasks
 */
class CronTabManagerAutoPauseGetListProcessor extends modObjectGetListProcessor
{
    /* @var CronTabManager $CronTabManager */
    public $CronTabManager = null;
    public $objectType = 'CronTabManagerAutoPause';
    public $classKey = 'CronTabManagerAutoPause';
    public $defaultSortField = 'createdon';
    public $defaultSortDirection = 'DESC';
    public $permission = 'crontabmanager_list';
    public $languageTopics = array('crontabmanager:manager');

    /** {@inheritDoc} */
    public function initialize()
    {
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
        $orderColumns = $this->modx->getSelectColumns('CronTabManagerAutoPause', 'CronTabManagerAutoPause', '', array(), false);
        $c->select($orderColumns);

        $completed = $this->setCheckbox('active');
        if (!empty($completed)) {
            $c->where(array('active' => 0));
        }

        $task_id = $this->getProperty('task_id');
        if (!empty($task_id)) {
            $c->where(array('task_id' => $task_id));
        }

        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        /* @var CronTabManagerAutoPause $object */
        $array = $object->toArray();
        $array['actions'] = array();


        $array['end_run'] = !empty($array['end_run']) ? date('Y-m-d H:i:s', $array['end_run']) : '';
        $array['last_run'] = !empty($array['last_run']) ? date('Y-m-d H:i:s', $array['last_run']) : '';



        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('crontabmanager_task_autopause_update'),
            'action' => 'updateItem',
            'button' => true,
            'menu' => true,
        );



        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('crontabmanager_task_autopause_remove'),
            'multiple' => $this->modx->lexicon('crontabmanager_task_autopauses_remove'),
            'action' => 'removeItem',
            'button' => false,
            'menu' => true,
        );

        return $array;
    }

}

return 'CronTabManagerAutoPauseGetListProcessor';
