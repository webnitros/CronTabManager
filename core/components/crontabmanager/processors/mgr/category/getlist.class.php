<?php

/**
 * Get a list of Items
 */
class CronTabManagerCategoryGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'CronTabManagerCategory';
    public $classKey = 'CronTabManagerCategory';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $languageTopics = array('crontabmanager:manager');
    public $permission = 'crontabmanager_list';

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

        $orderColumns = $this->modx->getSelectColumns('CronTabManagerCategory', 'CronTabManagerCategory', '', array(), false);
        $c->select($orderColumns);

        if ($query = $this->getProperty('query')) {
            $c->where(array(
                'name:LIKE' => '%' . $query . '%'
            ));
        }
        if ($query = $this->getProperty('active')) {
            $c->where(array('active' => $query));
        }
        return $c;
    }


    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['actions'] = array();

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('crontabmanager_category_update'),
            'action' => 'updateItem',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('crontabmanager_category_enable'),
                'multiple' => $this->modx->lexicon('crontabmanager_categories_enable'),
                'action' => 'enableItem',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('crontabmanager_category_disable'),
                'multiple' => $this->modx->lexicon('crontabmanager_categories_disable'),
                'action' => 'disableItem',
                'button' => true,
                'menu' => true,
            );
        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('crontabmanager_category_remove'),
            'multiple' => $this->modx->lexicon('crontabmanager_categories_remove'),
            'action' => 'removeItem',
            'button' => true,
            'menu' => true,
        );
        return $array;
    }

}

return 'CronTabManagerCategoryGetListProcessor';