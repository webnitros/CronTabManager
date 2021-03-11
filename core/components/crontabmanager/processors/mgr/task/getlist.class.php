<?php

/**
 * Get a list of Tasks
 */
class CronTabManagerTaskGetListProcessor extends modObjectGetListProcessor
{
    /* @var CronTabManager $CronTabManager */
    public $CronTabManager = null;
    public $objectType = 'CronTabManagerTask';
    public $classKey = 'CronTabManagerTask';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $permission = 'crontabmanager_list';
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

        $orderColumns = $this->modx->getSelectColumns('CronTabManagerTask', 'CronTabManagerTask', '', array(), false);
        $c->select($orderColumns);


        $c->leftJoin('CronTabManagerCategory', 'Category', 'Category.id = CronTabManagerTask.parent');
        $c->select('Category.name as category_name');

        if ($query = $this->getProperty('query')) {
            $query = trim($query);
            $c->where(array(
                'message:LIKE' => '%' . $query . '%',
                'OR:description:LIKE' => '%' . $query . '%',
                'OR:path_task:LIKE' => '%' . $query . '%'
            ));
        }

        $query = $this->getProperty('parent');
        if (!empty($query)) {
            $c->where(array('parent' => $query));
        }

        $completed = $this->setCheckbox('completed');
        if (!empty($completed)) {
            $c->where(array('completed' => 0));
        }


        if (isset($this->properties['active'])) {
            $query = $this->setCheckbox('active');
            if ($query) {
                $c->where(array('active' => $query));
            }
        } else {
            $c->where(array('active' => 1));
        }
        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function eiEmpt($val)
    {
        if (is_numeric($val)) return $val;
        return empty($val) ? '*' : $val;
    }


    public function prepareRow(xPDOObject $object)
    {
        /* @var CronTabManagerTask $object */
        $array = $object->toArray();
        $array['actions'] = array();


        $time = array(
            $this->eiEmpt($array['minutes']),
            $this->eiEmpt($array['hours']),
            $this->eiEmpt($array['days']),
            $this->eiEmpt($array['months']),
            $this->eiEmpt($array['weeks']),
        );


        $array['lock'] = $object->isLockFile();
        $array['is_blocked_time'] = $object->isBlockUpTask();
        $array['time'] = implode(' ', $time);


        $is_blocked = false;
        if ($array['lock'] or $array['is_blocked_time']) {
            $is_blocked = true;
        }
        $array['is_blocked'] = $is_blocked;


        // sendSubscrib
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-play',
            'title' => $this->modx->lexicon('crontabmanager_task_start'),
            'action' => 'runTask',
            'button' => true,
            'menu' => true,
        );

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('crontabmanager_task_update'),
            'action' => 'updateItem',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('crontabmanager_task_enable'),
                'action' => 'enableItem',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('crontabmanager_task_disable'),
                'action' => 'disableItem',
                'button' => true,
                'menu' => true,
            );
        }

        if ($array['is_blocked_time'] == true) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-unlock',
                'title' => $this->modx->lexicon('crontabmanager_task_unblockup'),
                'action' => 'unblockupTask',
                'button' => true,
                'menu' => true,
            );
        }


        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('crontabmanager_task_remove'),
            'action' => 'removeItem',
            'button' => false,
            'menu' => true,
        );

        $array['actions'][] = '-';
        // unlock
        if ($array['lock'] == true) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-unlock',
                'title' => $this->modx->lexicon('crontabmanager_task_unlock'),
                'action' => 'unlockTask',
                'button' => true,
                'menu' => true,
            );
        }



        // readLog
        $path = $object->getFileLogPath();
        if (file_exists($path)) {
            $array['actions'][] = '-';
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-eye',
                'title' => $this->modx->lexicon('crontabmanager_task_readlog'),
                'action' => 'readLog',
                'button' => true,
                'menu' => true,
            );
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('crontabmanager_task_removeLog'),
                'action' => 'removeLog',
                'button' => false,
                'menu' => true,
            );
        }


       /* $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-stop',
            'title' => $this->modx->lexicon('crontabmanager_task_manualstop'),
            'action' => 'manualStopTask',
            'button' => false,
            'menu' => true,
        );*/


        return $array;
    }

}

return 'CronTabManagerTaskGetListProcessor';
