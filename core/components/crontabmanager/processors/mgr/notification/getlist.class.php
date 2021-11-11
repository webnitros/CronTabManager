<?php

/**
 * Get a list of Tasks
 */
class CronTabManagerNotificationGetListProcessor extends modObjectGetListProcessor
{
    /* @var CronTabManager $CronTabManager */
    public $CronTabManager = null;
    public $objectType = 'CronTabManagerNotification';
    public $classKey = 'CronTabManagerNotification';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'crontabmanager_list';
    public $languageTopics = array('crontabmanager:manager');

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

        $orderColumns = $this->modx->getSelectColumns('CronTabManagerNotification', 'CronTabManagerNotification', '', array(), false);
        $c->select($orderColumns);
        $c->innerJoin('CronTabManagerTask', 'Task', 'Task.id = CronTabManagerNotification.task_id');
        $c->select('Task.path_task as path_task');
        $c->leftJoin('CronTabManagerCategory', 'Category', 'Category.id = Task.parent');
        $c->select('Category.name as category_name');
        if ($query = $this->getProperty('query')) {
            $query = trim($query);
            $c->where(array(
                'Task.message:LIKE' => '%' . $query . '%',
                'OR:Task.description:LIKE' => '%' . $query . '%',
                'OR:Task.path_task:LIKE' => '%' . $query . '%'
            ));
        }

        $parent = $this->getProperty('parent');
        if (!empty($parent)) {
            $c->where(array('Task.parent' => $parent));
        }

        $read = $this->setCheckbox('read');
        if (!empty($read)) {
            $c->where(array('CronTabManagerNotification.read' => 0));
        }
        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        /* @var CronTabManagerNotification $object */
        $array = $object->toArray();
        $array['actions'] = array();


        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('crontabmanager_notification_remove'),
            'action' => 'removeItem',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'CronTabManagerNotificationGetListProcessor';
