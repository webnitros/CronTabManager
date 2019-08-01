<?php
/*
 * MODX Revolution
 *
 * Copyright 2006-2012 by MODX, LLC.
 *
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 */

/**
 * Abstract controller class for modRestService; all REST controllers must extend this class to be properly
 * implemented.
 *
 * @package modx
 * @subpackage rest
 */
abstract class modCrontabController
{
    /** @var modX $modx The modX instance */
    public $modx;
    /** @var SchedulerService $service The SchedulerService instance */
    public $service;
    /** @var array $session */
    public $session = array();
    /** @var array $config An array of configuration properties, passed from modRestService */
    public $config = array();
    /** @var array $properties An array of request parameters passed */
    public $properties = array();
    /** @var string $classKey The xPDO class to use */
    public $classKey;
    /** @var string $classAlias The alias of the class when used in the getList method */
    public $classAlias;
    /** @var string $defaultSortField The default field to sort by in the getList method */
    public $defaultSortField = 'id';
    /** @var string $defaultSortDirection The default direction to sort in the getList method */
    public $defaultSortDirection = 'ASC';
    /** @var int $defaultLimit The default number of records to return in the getList method */
    public $defaultLimit = 3000;
    /** @var int $defaultOffset The default offset in the getList method */
    public $defaultOffset = 0;
    /** @var xPDOObject $object */
    public $object;
    /* @var integer|null $total */
    public $total = null;
    /* @var integer|null $totalStop произведена остановка на позици */
    public $totalStop = null;

    /* @var boolean $schedulerOffsetSession - Включение записи сессии, для пропуска записией. (управление offset через последние записи) */
    public $schedulerOffsetSession = false;

    /* @var modManagerLog $managerLog */
    protected $managerLog = null;

    /**
     * @param modX $modx The modX instance
     * @param array $config An array of configuration properties, passed through from modRestService
     */
    public function __construct(modX $modx, array $config = array())
    {
        $this->modx =& $modx;
    }

    /**
     * Initialize the controller
     * @return bool
     */
    public function initialize()
    {
        return true;
    }

    /**
     * Запуск процесса
     */
    public function run()
    {
        if ($this->initialize()) {
            $this->startSession();
            $this->process();
            $this->endSession();
        }
    }


    protected $listDeleted = null;

    /**
     * Запуск процесса
     */
    public function remove($log)
    {
        $this->listDeleted[] = $log->get('id');
    }


    /**
     * Запуск процесса
     */
    public function deletedLog()
    {
        if ($this->listDeleted) {
            $ids = implode(',', $this->listDeleted);
            $this->modx->exec("DELETE FROM {$this->modx->getTableName('modManagerLog')} WHERE id IN ({$ids})");
        }
    }


    /**
     * Запуск процесса
     */
    public function process()
    {
        $this->getList();
        $this->deletedLog();
    }


    protected function print_msg($msg)
    {
        echo $msg . '<br>';
    }

    /**
     * Установит следующие количество записие из сессии или из параметров get
     */
    public function setOffsetLimit()
    {
        // Устанавливаем лимит из сессии
        if ($this->schedulerOffsetSession) {
            if ($offset = $this->getSession('offset')) {
                $this->defaultOffset = $offset;
            }
        }
        if (isset($_GET['offset'])) {
            $offset = (int)$_GET['offset'];
            if (is_int($offset)) {
                if ($offset > 0) {
                    $this->defaultOffset = $offset;
                }
            }
        };
    }

    /**
     * Вернет true если надо пропустить
     * @return bool
     */
    public function isOffsetTotal()
    {
        if ($this->total <= 0) {
            return true;
        }
        return $this->defaultOffset > $this->total;
    }

    /**
     * Abstract method for routing GET requests without a primary key passed. Must be defined in your derivative
     * controller. Handles fetching of collections of objects.
     *
     * @abstract
     * @return array
     */
    public function getList()
    {
        $this->setOffsetLimit();
        $this->service->recordsLimit = $this->defaultLimit;
        $this->service->recordsOffset = $this->defaultOffset;
        $this->getProperties();

        $c = $this->modx->newQuery($this->classKey);

        $alias = !empty($this->classAlias) ? $this->classAlias : $this->classKey;
        $c->select($this->modx->getSelectColumns($this->classKey, $alias));

        $c = $this->prepareListQueryAfterCount($c);

        $c = $this->prepareListQueryBeforeCount($c);
        $this->total = $this->modx->getCount($this->classKey, $c);


        $list = array();
        $objects = array();
        if (!$this->isOffsetTotal()) {

            $c->sortby($this->getProperty($this->getOption('propertySort', 'sort'), $this->defaultSortField), $this->getProperty($this->getOption('propertySortDir', 'dir'), $this->defaultSortDirection));
            $limit = $this->getProperty($this->getOption('propertyLimit', 'limit'), $this->defaultLimit);
            if (empty($limit)) $limit = $this->defaultLimit;
            $c->limit($limit, $this->getProperty($this->getOption('propertyOffset', 'start'), $this->defaultOffset));
            $objects = $this->modx->getIterator($this->classKey, $c);


            /** @var xPDOObject $object */
            foreach ($objects as $object) {
                $this->prepare($object);

                // Фиксируем остановку времени
                $this->setRecordsStop();
                if ($this->service->timeIsOver()) {
                    break;
                }
                $this->service->manualStopExecution();
            }
        }

        $this->afterPassingAllRecords();
        return $this->collection($list);
    }




    /**
     * После обработки списка записей
     */
    protected function afterPassingAllRecords(){}

    /**
     * @param $message
     */
    public function log_error($message)
    {
        $backtrace = debug_backtrace();
        $FILE = isset($backtrace[0]['file']) ? $backtrace[0]['file'] : __FILE__;
        $LINE = isset($backtrace[0]['line']) ? $backtrace[0]['line'] : __LINE__;
        $this->modx->log(modX::LOG_LEVEL_ERROR, '[Crontab] ' . $message, '', '', $FILE, $LINE);
    }

    /**
     * Получение имя сесии для задания
     * @return null|string
     */
    protected function schedulerSessionGenerate()
    {
        return 'scheduler_offset_' . str_ireplace('/', '_', $this->service->scheduler);
    }

    /**
     * Старт сесии
     */
    public function startSession()
    {
        if (!$ml = $this->modx->getObject('modManagerLog', array('action' => $this->schedulerSessionGenerate()))) {
            $ml = $this->modx->newObject('modManagerLog');
            $ml->set('user', 0);
            $ml->set('occurred', strftime('%Y-%m-%d %H:%M:%S'));
            $ml->set('action', $this->schedulerSessionGenerate());
            $ml->set('item', $this->getSession('offset', $this->defaultOffset));
        }
        $this->managerLog = $ml;
        $this->setSession('offset', $ml->get('item'));
    }

    /**
     * Сохранение переменной в сессиию
     */
    public function endSession()
    {
        $this->managerLog->set('item', $this->getSession('offset', $this->defaultOffset));
        $this->managerLog->save();
    }


    /**
     * Get a REQUEST property for the controller
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSession($key, $default = null)
    {
        $value = $default;
        if (array_key_exists($key, $this->session)) {
            $value = $this->session[$key];
        }
        return $value;
    }


    /**
     * Set a request property for the controller
     *
     * @param string $key
     * @param string $value
     */
    public function setSession($key, $value)
    {
        $this->session[$key] = $value;
    }

    /**
     * Unset a request property for the controller
     * @param string $key
     */
    public function unsetSession($key)
    {
        unset($this->session[$key]);
    }

    /**
     * Get the request session for the controller
     * @return array
     */
    public function getSessions()
    {
        return $this->session;
    }

    /**
     * Вернет номер записи на которой была произведена остановка
     */
    public function setRecordsStop()
    {

        #$defaultLimit = $this->defaultLimit;
        $defaultOffset = $this->defaultOffset;

        if (!$this->totalStop) {
            $this->totalStop = 0;
            if ($defaultOffset) {
                $this->totalStop = $defaultOffset;
            }
        }
        $this->totalStop++;
        $this->service->recordsCount = $this->totalStop;
        $this->setSession('offset', $this->totalStop);
    }

    /**
     * Вернет номер записи на которой была произведена остановка
     *
     * @return integer
     */
    public function getRecordsStop()
    {
        return $this->totalStop;
    }


    /**
     * Output a collection of objects as a list.
     *
     * @param array $list
     * @return array
     */
    public function collection($list = array())
    {
        return $list;
    }


    /**
     * Returns an array of field-value pairs for the object when listing. Override to provide custom functionality.
     *
     * @param xPDOObject|xPDOSimpleObject $object The current iterated object
     */
    protected function prepare($object)
    {
        $this->beforePrepare($object);
        $this->afterPrepare($object);
    }

    /**
     * Returns an array of field-value pairs for the object when listing. Override to provide custom functionality.
     *
     * @param xPDOObject $object The current iterated object
     */
    protected function afterPrepare(xPDOObject $object)
    {

    }

    /**
     * Returns an array of field-value pairs for the object when listing. Override to provide custom functionality.
     *
     * @param xPDOObject $object The current iterated object
     */
    protected function beforePrepare(xPDOObject $object)
    {

    }

    /**
     * Allows manipulation of the query object after the COUNT statement is called on listing calls. Override to
     * provide custom functionality.
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    protected function prepareListQueryAfterCount(xPDOQuery $c)
    {
        return $c;
    }

    /**
     * Allows manipulation of the query object before the COUNT statement is called on listing calls. Override to
     * provide custom functionality.
     *
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    protected function prepareListQueryBeforeCount(xPDOQuery $c)
    {
        return $c;
    }


    /**
     * Get a REQUEST property for the controller
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getProperty($key, $default = null)
    {
        $value = $default;
        if (array_key_exists($key, $this->properties)) {
            $value = $this->properties[$key];
        }
        return $value;
    }

    /**
     * Set a request property for the controller
     *
     * @param string $key
     * @param string $value
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;
    }

    /**
     * Unset a request property for the controller
     * @param string $key
     */
    public function unsetProperty($key)
    {
        unset($this->properties[$key]);
    }

    /**
     * Get the request properties for the controller
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set a collection of properties for the controller
     *
     * @param array $properties An array of properties
     * @param bool $merge Optionally, only merge properties in if this is true
     */
    public function setProperties(array $properties = array(), $merge = false)
    {
        $this->properties = $merge ? array_merge($this->properties, $properties) : $properties;
    }

    /**
     * Get a configuration option for this controller
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }
}
