<?php

/**
 * Class CrontabPhpUnit
 * Класс для запуска PHP unit тестов из под CrontabManager
 */
class CrontabPhpUnit
{
    protected $vendorPath;
    protected $testsPath;

    /* @var MODxTestSuite $TestSuite */
    protected $TestSuite;

    public function __construct()
    {
        $this->vendorPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
    }

    public function initialize($config = [])
    {
        $this->testsPath = $config['testsPath'];

        $response = $this->loadVendorAutoload();
        if ($response !== true) {
            return $response;
        }
        $response = $this->loadHarness();
        if ($response !== true) {
            return $response;
        }

        if (!file_exists($this->testsPath)) {
            return 'Директория с тестами не найден ' . $this->testsPath;
        }

        if (!class_exists('PHPUnit\Framework\TestSuite')) {
            return 'Не удалось загрузить TestSuite';
        }

        $this->TestSuite = new MODxTestSuite();
        return true;
    }

    /**
     * Загрузка composer
     * @return bool
     */
    private function loadVendorAutoload()
    {
        if (!file_exists($this->vendorPath)) {
            return "Не удалось загрузить " . $this->vendorPath;
        }
        include_once $this->vendorPath;
        return true;
    }

    /**
     * Загрузка modx для phpunit тестов
     * @return bool
     */
    private function loadHarness()
    {
        $root = MODX_CORE_PATH . 'components/crontabmanager/lib/phpunit/MODxTestHarness.php';
        if (!file_exists($root)) {
            return 'не удалось загрузить класс для MODxTestHarness';
        }
        include $root;
        return true;
    }

    private function scanDirs($path, $files = [])
    {
        $tmp = scandir($path);
        foreach ($tmp as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $files[] = $path . '/' . $item;
        }
        return $files;
    }

    private function addTests($tests = [])
    {
        $files = [];
        foreach ($tests as $test) {
            $path = $this->testsPath . ltrim($test, '/');
            if (is_dir($path)) {
                $files = $this->scanDirs($path, $files);
            } else {
                $test = $path . '.php';
                $files[] = $test;
            }
        }
        $this->TestSuite->addTestFiles($files);
        return true;
    }

    public $errors = null;

    /**
     * Запускает тест
     * @param $name
     * @param $task_id
     * @return array|string
     * @throws ReflectionException
     */
    public function runTest($tests, $task_id = null)
    {
        $tests = explode(',', $tests);
        $response = $this->addTests($tests);
        if ($response !== true) {
            return $response;
        }

        $response = false;
        $isError = false;
        try {
            $result = $this->runTestSuite();
            if ($result->errorCount() > 0 || $result->failureCount() > 0) {
                $isError = true;
            } else {
                $response = true;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (!$isError) {
            // Если ошибок нету то задание успешно завершается
            $this->taskCrontab($task_id);
        }
        return $response;
    }

    /**
     * @return \PHPUnit\Framework\TestResult|null
     */
    private function runTestSuite()
    {
        $TestResult = new MODxTestResult();
        $TestResult->CronTab = $this;
        return $this->TestSuite->run($TestResult);
    }


    public function taskCrontab($task_id)
    {
        if ($task_id) {
            /* @var modX $Modx */
            $modx = null;
            /* @var PHPUnit\Framework\TestSuite $group */
            foreach ($this->TestSuite->getGroupDetails() as $groupDetail) {
                $group = $groupDetail[0];
                $modx = $group->testAt(0)->modx;
                break;
            }
            if ($modx instanceof modX) {
                /* @var CronTabManagerTask $task */
                if ($task = $modx->getObject('CronTabManagerTask', $task_id)) {
                    $task->setSaveLog(1);
                    $task->set('end_run', time());
                    $task->set('completed', true); // Устанавливаем метку завершенности

                    // Снятие блокировки
                    $task->unLock();
                    $task->save();
                }
            }
        }
    }

    public function print_msg($msg)
    {
        if (isset($_GET['connector_base_path_url'])) {
            echo $msg . '<br>';
        } else {
            echo $msg . PHP_EOL;
        }
    }

    /**
     * Prepare exec function arguments.
     * @return array
     */
    function get_exec_args()
    {
        $args = [];
        if (isset($GLOBALS['argv']) && count($GLOBALS['argv']) > 1) {
            $query = implode('&', array_slice($GLOBALS['argv'], 1));
            parse_str($query, $args);
        }
        return $args;
    }


}
