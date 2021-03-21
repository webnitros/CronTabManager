<?php

class SchedulerService
{
    /* @var modX $modx */
    public $modx;

    /* @var CronTabManager $CronTabManager */
    public $CronTabManager;


    /* @var string $action */
    /* @var string $task */
    /* @var string $lockFile */
    public $action;
    public $task;
    public $lockFile;
    protected $enabledException = false;
    public $recordsCount = 0;
    public $recordsLimit = 0;
    public $recordsOffset = 0;
    public $ForcedStop = false;

    /* @var null|int $item - одна запись при массов */
    public $item = null;

    public $scheduler = null;
    /* @var array $config */
    public $config;

    /* @var boolean $isSetCompletionTime - будет писать логи для задания */
    public $isSetCompletionTime = true;
    /* @var int $user_id */
    public $user_id = null;

    /* @var CronTabManagerTask $CronTabManagerTask */
    protected $CronTabManagerTask = null;


    /** @var int|string $requestPrimaryKey The primary key requested on the object/id route */
    public $requestPrimaryKey;

    /* @var boolean|null $mode */
    public $mode = null;

    /**
     * @param CronTabManager $CronTabManager
     * @param array $config
     */
    function __construct(CronTabManager &$CronTabManager, array $config = array())
    {
        $this->modx =& $CronTabManager->modx;
        $this->config = array_merge(array(
            'basePath' => dirname(__FILE__) . '/Controllers/',
            'start_time' => microtime(true),
            'max_exec_time' => @ini_get("max_execution_time") - 5,
            'blocking_time_minutes' => $this->modx->getOption('crontabmanager_blocking_time_minutes', $config, 60),
            'controllerClassPrefix' => 'modController',
            'controllerClassSeparator' => '_',
            'controllerClassFilePostfix' => '.php',
        ), $config);

        // Включения механизма блокирования
        $this->isSetCompletionTime = (boolean)$this->modx->getOption('crontabmanager_set_completion_time', $config, true);

        // Авторизоваться под пользователем
        $this->user_id = (int)$this->modx->getOption('crontabmanager_user_id', $config, 0);
        if ($this->user_id != 0) {
            if ($User = $this->modx->getObject('modUser', $this->user_id)) {
                $this->modx->user = $User;
            }
        }
    }


    /**
     * Process the response and format in the proper response format.
     */
    public function process($unLock = null)
    {
        $this->setMode();
        if ($this->scheduler) {


            if ($controllerName = $this->getController()) {

                if (null == $controllerName) {
                    throw new Exception('Method not allowed', 405);
                }

                /** @var modCrontabController $controller */
                try {
                    $controller = new ReflectionClass($controllerName);
                    if (!$controller->isInstantiable()) {
                        throw new Exception('Bad Request', 400);
                    }

                    try {
                        /** @var ReflectionMethod $method */
                        $method = $controller->getMethod('run');
                    } catch (ReflectionException $e) {
                        throw new Exception('Unsupported HTTP method process', 405);
                    }

                    if (!$method->isStatic()) {
                        $controller = $controller->newInstance($this->modx, $this->config);
                        $controller->service = $this;

                        if ($task = $this->getTask()) {


                            // Записываем путь перед стартом
                            if (is_null($this->manualStopExecutionPath)) {
                                $this->manualStopExecutionPath = $task->getFileManualStopPath($this->config['basePath']);
                                if (file_exists($this->manualStopExecutionPath)) {
                                    unlink($this->manualStopExecutionPath);
                                }
                            }


                            $this->runProcess($task, $method, $unLock, $controller);
                        }

                    } else {
                        throw new Exception('Static methods not supported in Controllers', 500);
                    }

                } catch (ReflectionException $e) {
                    $this->errors = $e->getMessage();
                    $this->modx->log(modX::LOG_LEVEL_ERROR, '[Crontab] ' . $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
                }

            }
        }

    }


    /**
     * Получение задания и проверка блокировки
     * @return CronTabManagerTask|object|null
     * @throws Exception
     */
    public function getTask()
    {
        $this->task = $this->scheduler . '.php';
        $criteria = array(
            'path_task' => $this->task,
        );

        /* @var CronTabManagerTask $CronTabManagerTask */
        if (!$this->CronTabManagerTask = $this->modx->getObject('CronTabManagerTask', $criteria)) {
            $this->response('Error get task: ' . $this->task);
        }

        // Проверка срока хранения логов, чтобы не забивать логами всю базу автоматически требуется отчищать логи
        $log_storage_time = (int)$this->CronTabManagerTask->get('log_storage_time');
        if ($log_storage_time > 0) {
            $task_id = $this->CronTabManagerTask->get('id');
            $createdon = strtotime(date('Y-m-d H:i:s', strtotime('-' . $log_storage_time . ' minutes', time())));
            $criteria = array(
                'task_id' => $this->CronTabManagerTask->get('id'),
                'createdon:<' => $createdon
            );
            if ($count = (boolean)$this->modx->getCount('CronTabManagerTaskLog', $criteria)) {
                $sql = "DELETE FROM {$this->modx->getTableName('CronTabManagerTaskLog')} WHERE task_id = {$task_id} and createdon <= {$createdon}";
                $this->modx->exec($sql);
            }
        }

        return $this->CronTabManagerTask;
    }

    /**
     * Исполнение контраллера с фиксацией времени
     * @param ReflectionMethod $method
     * @param CronTabManagerTask $task
     * @param $unLock - Снятие блокировки
     * @param modCrontabController $controller
     */
    public function runProcess(CronTabManagerTask $task, ReflectionMethod $method, $unLock, modCrontabController $controller)
    {

        // 1. Проверка блокировок
        if (!$task->isModeDevelop()) {


            if (!$task->get('active')) {
                $this->response('job deactivated id:' . $task->get('id'));
            }

            if ($task->isBlockUpTask()) {
                $this->response('task blocked until: ' . $task->get('blockupdon'));
            }

            // Если установлено снятие блокировки
            if ($unLock) {
                $task->unLock();
            }


            // Проверям время блокировки файла
            if (!$task->isLock()) {
                $task->unLock();
            }

            // Проверка существование файла
            if ($task->isLockFile()) {
                $this->response('Исполнение скрипта не завершено ждите окончания');
            }


        }


        if ($this->isSetCompletionTime) {
            $task->start();
        }

        // 2. Запуск контроллера
        $response = $method->invoke($controller);


        // 3. Остановка задания
        if ($this->isSetCompletionTime) {
            $task->end();
        }

        $this->response($this->GetUsage());
    }


    public function itemLog($id)
    {
        $this->item = $id;
    }

    public function response($data = '')
    {
        if ($this->isEnabledException()) {
            throw new Exception($data);
        } else {
            @session_write_close();
            exit($data);
        }
    }

    /**
     * Генерирует карту контроллеров
     * @throws Exception
     */
    public function generateCronLink()
    {
        if (!class_exists('SheldulerGeneratorLink')) {
            include_once dirname(__FILE__) . '/sheldulergeneratorlink.class.php';
        }
        $SheldulerGeneratorLink = new SheldulerGeneratorLink($this->modx);
        $SheldulerGeneratorLink->process($this->getOption('basePath'), $this->getOption('linkPath'));
    }

    /* This method returns an error response
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data Additional data, for example cart status
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 * */
    public function error($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false
        , 'message' => $this->modx->lexicon($message, $placeholders)
        , 'data' => $data
        );

        $this->response($response);
    }

    /* This method returns an success response
     *
     * @param string $message A lexicon key for success message
     * @param array $data Additional data, for example cart status
     * @param array $placeholders Array with placeholders for lexicon entry
     *
     * @return array|string $response
     * */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true
        , 'message' => $this->modx->lexicon($message, $placeholders)
        , 'data' => $data
        );
        $this->response($response);
    }

    /**
     * Ссылка на следующие результаты
     */
    public function nextResults()
    {
        $request = array();
        if ($this->mode) {
            $request['mode'] = 1;
        }
        $request['offset'] = $this->recordsCount;
        $query = http_build_query($request);

        return $this->modx->getOption('site_url') . 'scheduler/' . $this->scheduler . '?' . $query;
    }

    /**
     * Вермен логировние времени
     * @return string
     */
    private function GetUsage()
    {
        global $tstart, $modx;


        $exec_time = microtime(true) - $this->getOption('start_time');

        $out = '';
        $memory = round(memory_get_usage(true) / 1024 / 1024, 4) . ' Mb';


        if (isset($_GET['connector_base_path_url'])) {
            $prefix = '<br>';
        } else {
            $prefix = PHP_EOL;
        }


        if ($this->ForcedStop) {
            $out .= "Forced stop, max_exec_time: {$this->getOption('max_exec_time')} s".$prefix;
        }
        $out .= "Time all: {$exec_time}".$prefix;
        $out .= "Records process: {$this->recordsCount}".$prefix;
        $out .= "Memory: {$memory}".$prefix;
        $totalTime = (microtime(true) - $tstart);
        $totalTime = sprintf("%2.4f s", $totalTime);
        if (!empty($modx)) {
            $queryTime = $modx->queryTime;
            $queryTime = sprintf("%2.4f s", $queryTime);
            $queries = isset ($modx->executedQueries) ? $modx->executedQueries : 0;

            $phpTime = $totalTime - $queryTime;
            $phpTime = sprintf("%2.4f s", $phpTime);
            $out .= "queries: {$queries}".$prefix;
            $out .= "queryTime: {$queryTime}".$prefix;
            $out .= "phpTime: {$phpTime}".$prefix;
        }
        $out .= "TotalTime: {$totalTime}".$prefix;
        return $out;
    }



    public function setMode()
    {
        if (isset($_GET['mode'])) {
            $this->mode = (boolean)$_GET['mode'];
        }
    }

    /**
     * Првоерка выброса ответа в место возвращения массива
     * @return bool
     */
    public function isEnabledException()
    {
        return $this->enabledException;
    }

    /**
     * Get a configuration option for this service
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }

    /**
     * Get the correct controller path for the class
     *
     * @return string
     */
    protected function getController()
    {
        $expectedFile = $this->scheduler;

        $basePath = $this->getOption('basePath');
        $controllerClassPrefix = $this->getOption('controllerClassPrefix', 'modController');
        $controllerClassSeparator = $this->getOption('controllerClassSeparator', '_');
        $controllerClassFilePostfix = $this->getOption('controllerClassFilePostfix', '.php');

        /* handle [object]/[id] pathing */
        $expectedArray = explode('/', $expectedFile);


        if (empty($expectedArray)) $expectedArray = array(rtrim($expectedFile, '/') . '/');
        $id = array_pop($expectedArray);
        if (!file_exists($basePath . $expectedFile . $controllerClassFilePostfix) && !empty($id)) {
            $expectedFile = implode('/', $expectedArray);
            if (empty($expectedFile)) {
                $expectedFile = $id;
                $id = null;
            }
            $this->requestPrimaryKey = $id;
        }

        foreach ($this->iterateDirectories($basePath . '/*' . $controllerClassFilePostfix, GLOB_NOSORT) as $controller) {
            $controller = $basePath != '/' ? str_replace($basePath, '', $controller) : $controller;
            $controller = trim($controller, '/');
            $controllerFile = str_replace(array($controllerClassFilePostfix), array(''), $controller);
            $controllerClass = str_replace(array('/', $controllerClassFilePostfix), array($controllerClassSeparator, ''), $controller);
            if (strnatcasecmp($expectedFile, $controllerFile) == 0) {
                require_once $basePath . $controller;
                return $controllerClassPrefix . $controllerClassSeparator . $controllerClass;
            }
        }
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Could not find expected controller: ' . $expectedFile);
        return null;
    }

    /**
     * Iterate across directories looking for files based on a pattern
     *
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    public function iterateDirectories($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        $dirs = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

        if ($dirs) {
            foreach ($dirs as $dir) {
                $files = array_merge($files, $this->iterateDirectories($dir . '/' . basename($pattern), $flags));
            }
        }

        return $files;
    }

    /**
     * @param $controller путь к контроллеру
     */
    public function php($controller)
    {
        $this->scheduler = $controller;
    }

    public function getPath()
    {
        if (isset($_REQUEST['_scheduler'])) {
            $this->scheduler = $_REQUEST['_scheduler'];
        }
    }


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
     * Проверка завершения времени
     * @return boolean;
     */
    public function timeIsOver()
    {
        $max_exec_time = $this->config['max_exec_time'];
        $exec_time = microtime(true) - $this->getOption('start_time');
        if ($exec_time + 1 >= $max_exec_time) {
            $this->ForcedStop = true;
            return true;
        }
        return false;
    }


    private $manualStopExecutionPath = null;

    /**
     * Вызвать прерывание задания
     */
    public function manualStopExecution()
    {
        if ($this->manualStopExecutionPath) {
            if (file_exists($this->manualStopExecutionPath)) {
                echo 'Ручная остановка выполнения задания<br>';
                unlink($this->manualStopExecutionPath);
                $this->response($this->GetUsage());
            }
        }

    }

    /**
     * Велючаем выброс
     */
    public function enableEnabledException()
    {
        $this->enabledException = true;
    }
}
