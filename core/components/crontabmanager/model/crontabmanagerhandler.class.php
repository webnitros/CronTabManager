<?php

interface CrontabManagerHandlerInterface
{
    /**
     * Initializes class create crontab file
     * @return boolean
     */
    public function initialize();

    /**
     * @param CronTabManagerTask $task
     * @param string $action
     * @return array|string $response
     */
    public function process(CronTabManagerTask $task, $action);

    /**
     * @return array|string $response
     */
    public function getList();

    /**
     * Вернет хеш задания по контроллеру или по id задания
     *
     * @param null $path_task путь к контроллеру
     * @param null $task_id id задания
     * @return bool|string
     */
    public function findHashTask($path_task = null, $task_id = null);

}

class CrontabManagerHandler implements CrontabManagerHandlerInterface
{
    /** @var modX $modx */
    public $modx;

    /* @var CrontabManager|null $CrontabManager */
    protected $CrontabManager = null;
    protected $loadClass = 'Bin';

    /* @var CrontabManagerManual|CrontabManagerManualFile $crontab */
    protected $crontab = null;
    /* @var CronEntry $job */
    private $job;

    /* @var CronTabManagerTask $task */
    protected $task;


    /* @var null|int $task_id */
    protected $task_id = null;

    /* @var string $path_task */
    protected $path_task = null;

    public function __construct(CrontabManager &$CrontabManager, $config = array())
    {
        $this->CrontabManager = $CrontabManager;
        $this->modx = $CrontabManager->modx;
    }

    /**
     * @return bool
     */
    public function initialize()
    {
        if (!class_exists('CrontabManagerManual')) {
            require $this->CrontabManager->config['corePath'] . '/lib/crontab/CrontabManagerManual.php';
        }

        //CrontabManagerManualBin
        $class = 'CrontabManagerManual' . $this->loadClass;
        if (!class_exists($class)) {
            require $this->CrontabManager->config['corePath'] . '/lib/crontab/' . $class . '.class.php';
        }

        if (!class_exists($class)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error load class {$class}", '', __METHOD__, __FILE__, __LINE__);
            return false;
        }
        $this->crontab = new $class();
        $this->crontab->file_crontab_path = $this->CrontabManager->config['schedulerPath'] . '/crontabs/' . $_SERVER['USER'];
        return true;
    }

    /**
     * @param CronTabManagerTask $task
     * @param $action
     * @return bool
     */
    public function process(CronTabManagerTask $task, $action)
    {
        if (!$task instanceof CronTabManagerTask) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "CronTabManagerTask class not passed", '', __METHOD__, __FILE__, __LINE__);
            return false;
        }

        if ($action != 'add' and $action != 'remove') {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Error action with task should be add or remove", '', __METHOD__, __FILE__, __LINE__);
            return false;
        }

        $result = false;
        try {
            $this->task = $task;

            if (!$this->task->isNew()) {
                $this->task_id = $this->task->get('id');
            }

            $this->path_task = $this->task->get('path_task');
            switch ($action) {
                case 'add':
                    if (!$result = $this->add()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Crontab error add task:" . $this->hash(), '', __METHOD__, __FILE__, __LINE__);
                    }
                    break;
                case 'remove':
                    if (!$result = $this->remove()) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR, "Crontab error remove empty hash:" . $this->hash(), '', __METHOD__, __FILE__, __LINE__);
                    }
                    break;
                default:
                    break;
            }
        } catch (UnexpectedValueException $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[Crontab] " . $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
            $result = false;
        } catch (InvalidArgumentException $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[Crontab] " . $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
            $result = false;
        }
        return $result;
    }


    /**
     * Добавление крон задания
     * @return bool
     */
    protected function add()
    {
        $saveOld = true;


        // Если произошло изменения пути то поиск задания будет производится по старому пути
        if ($this->task->old_path_task) {
            $path_task = $this->task->old_path_task;
            $this->task->old_path_task = null;
        } else {
            $path_task = $this->task->get('path_task');
        }


        // Если задание небыло найдено по hash
        // Ищим его по пути
        $hash = $this->findHashTask($path_task, $this->task_id);
        if ($hash !== false) {
            $this->remove();
            $saveOld = false; // Метка о удалении старого задания
        }


        $this->job = $this->crontab->newJob();
        $this->job->on('* * * * *');
        $this->minutes();
        $this->hour();
        $this->days();
        $this->months();
        $this->weeks();


        $php_command = $this->CrontabManager->config['php_command'];
        $linkPath = $this->CrontabManager->config['linkPath'];
        $logPath = $this->task->getFileLogPath();

        // Создаем строку с заданием
        $task_str = $php_command . " {$linkPath}/" . $this->path_task . " > {$logPath} 2>&1";

        $this->job->doJob($task_str);
        $this->crontab->add($this->job);
        $this->crontab->save($saveOld);

        // Записываем хеш в задание
        $response = $this->setNewHash();
        if ($response !== false) {
            $this->task->set('hash', $hash);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Не удалось получить хешь для задания" . $path_task, '', __METHOD__, __FILE__, __LINE__);
            return false;
        }
        return true;
    }

    /**
     * Удаление заданий
     * @return bool
     */
    protected function remove()
    {
        $result = false;

        // Ищим задание по ID или по контроллеру
        $hash = $this->findHashTask($this->task->get('path_task'), $this->task_id);
        if (!empty($hash)) {
            $response = $this->crontab->deleteJob($hash);
            if (empty($response)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Crontab remove empty hash:" . $hash, '', __METHOD__, __FILE__, __LINE__);
            } else {
                $result = true;
                $this->crontab->save(false);
            }
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * Вернет список заданий
     * @return array|bool
     */
    public function getList()
    {
        $response = $this->crontab->listJobs();
        if (!empty($response)) {
            $jobs = explode("\n", $response); // get the old jobs
            $jobs = array_filter($jobs);
            if (count($jobs) > 0) {
                return $jobs;
            }
        }
        return false;
    }


    /**
     * Вернет hash задания
     * @return string
     */
    private function hash()
    {
        return $this->task->get('hash');
    }

    /**
     * Вернет хеш задания по контроллеру или по id задания
     *
     * @param null $path_task путь к контроллеру
     * @param null $task_id id задания
     * @return bool|string
     */
    public function findHashTask($path_task = null, $task_id = null)
    {
        if ($path_task or $task_id) {

            if ($jobs = $this->getList()) {

                // Префикс кэша
                $task_id_find = null;
                if ($task_id) {
                    $task_id_find = $this->task->getFileLogPath();
                }
                foreach ($jobs as $oneJob) {
                    if ($oneJob != '') {
                        if ($task_id_find) {
                            // Поиск по id задания
                            if (strripos($oneJob, $task_id_find) !== false) {
                                return substr($oneJob, -6);
                            }
                        }

                        if ($path_task) {

                            // Поиск контроллеру
                            if (strripos($oneJob, $path_task) !== false) {
                                return substr($oneJob, -6);
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Записываем новый хеш задания
     * @return bool
     */
    private function setNewHash()
    {
        if ($jobs = $this->getList()) {
            foreach ($jobs as $oneJob) {
                if (!empty($oneJob)) {
                    if (strripos($oneJob, $this->path_task) !== false) {
                        return substr($oneJob, -6);
                    }
                }
            }
        }
        return false;
    }

    private function minutes()
    {
        $val = $this->task->get('minutes');
        if (!empty($val) or $val == 0) {
            $this->job->onMinute($val);
        }
    }

    private function hours()
    {
        $val = $this->task->get('hours');
        if (!empty($val) or $val == 0) {
            $this->job->onHour($val);
        }
    }

    private function hour()
    {
        $val = $this->task->get('hours');
        if (!empty($val)) {
            $this->job->onHour($val);
        }
    }

    private function days()
    {
        $val = $this->task->get('days');
        if (!empty($val)) {
            $this->job->onDayOfMonth($val);
        }
    }

    private function months()
    {
        $val = $this->task->get('months');
        if (!empty($val)) {
            $this->job->onMonth($val);
        }
    }

    private function weeks()
    {
        $val = $this->task->get('weeks');
        if (!empty($val)) {
            $this->job->onDayOfWeek($val);
        }
    }

    protected function eiEmpt($val)
    {
        if (is_numeric($val)) return $val;
        return empty($val) ? '*' : $val;
    }
}
