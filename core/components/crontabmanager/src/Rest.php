<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.11.2021
 * Time: 00:49
 */

namespace Webnitros\CronTabManager;

use CronTabManager;
use CronTabManagerNotification;
use CronTabManagerTask;
use modProcessorResponse;
use modUser;
use modX;
use Webnitros\CronTabManager\Exceptions\RestException;


class Rest
{
    /* @var CronTabManager $CronTabManager */
    protected $CronTabManager;

    /* @var int $_status_code */
    protected $_status_code = 200;
    /* @var null|array $_response */
    protected $_response = null;
    /* @var  bool|mixed $_response */
    protected $_protected = null;

    /* @var modX $modx */
    private $modx;

    public function __construct(CronTabManager $cronTabManager)
    {
        $this->CronTabManager = $cronTabManager;
        $this->modx = $this->CronTabManager->modx;


        $this->CronTabManager->modx->getRequest();
        $this->CronTabManager->modx->request->sanitizeRequest();
    }

    public function lexicon($key, $data = [])
    {
        return $this->CronTabManager->modx->lexicon('crontabmanager.' . $key, $data);
    }

    public function protected($value = true)
    {
        $this->_protected = $value;
    }

    public function isProtected()
    {
        return $this->_protected;
    }


    public function autification()
    {
        if ($this->isProtected()) {
            $token = $this->get('token');
            $response = $this->checkClientId();
            if ($response !== true) {
                throw new RestException($response, 403);
            } else {
                // Check username
                $username = strtolower(trim(@$this->get('username')));
                if (!empty($username)) {
                    if (!preg_match('/^[^\'\\x3c\\x3e\\(\\);\\x22]+$/', $username)) {
                        return 'Имя пользователя указанно не правильно';
                    } elseif (!$this->modx->getCount('modUser', array('username' => $username))) {
                        return 'Пользователь с таким именем не найден';
                    }
                }

                /* @var modUser $User */
                if (!$User = $this->modx->getObject('modUser', array('username' => $username))) {
                    return 'Не удалось получить пользователя';
                }

                $hasPermission = $this->modx->context->checkPolicy('crontabmanager_view', null, $User);

                if (!$User->get('active')) {
                    return 'Пользователь не активный, доступ запрещен';
                } else if (!$hasPermission) {
                    return 'Отсутствую права на удаленный доступ к rest, доступ запрещен';
                } else {

                    $tokens = null;
                    $q = $this->modx->newQuery('CronTabManagerToken');
                    $q->select($this->modx->getSelectColumns('CronTabManagerToken', 'CronTabManagerToken'));
                    $q->where(array(
                        'active' => true,
                        'user_id' => $User->get('id'),
                        'valid_until:>' => time(),
                    ));
                    if ($q->prepare() && $q->stmt->execute()) {
                        while ($row = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
                            $tokens[$row['token']] = true;
                        }
                    }

                    $tokenFound = false;
                    if ($tokens && array_key_exists($token, $tokens)) {
                        $tokenFound = true;
                    }
                    if (!$tokenFound) {
                        return 'Токен устарел, требуется авторизация';
                    }
                }
            }
        }
        return true;
    }

    public function checkClientId()
    {
        $client_id = $this->get('client_id');
        $SettingClinetId = $this->CronTabManager->modx->getOption('crontabmanager_rest_client_id', NULL, 'CLIENT_ID');
        if ($SettingClinetId != $client_id) {
            return 'Указан не верный client_id';
        }
        return true;
    }

    public function process()
    {
        $action = (string)@$this->get('action');

        if ($action != 'check') {
            $this->protected();
        }

        $response = $this->checkClientId();
        if ($response !== true) {
            $this->failure($response);
        } else {
            $response = $this->autification();
            if ($response !== true) {
                throw new RestException($response, 403);
            }

            $run = null;
            switch ($action) {
                case 'tasks':
                case 'check':
                case 'site':
                case 'unlock':
                case 'play':
                case 'log':
                case 'notifications':
                    $method = '_' . $action;
                    if (method_exists($this, $method)) {
                        $run = $method;
                    }
                    break;
                default:

                    break;
            }
            if (!$run) {
                throw new RestException('Method not allowed', 405);
            }
            $this->{$run}();
        }
    }

    protected function isAuth()
    {
        $Rest = new Auth($this->CronTabManager);
        $Rest->process();
    }


    public function getResponse()
    {
        return $this->_response;
    }

    public function getStatusCode()
    {
        return $this->_status_code;
    }

    protected function success($msg = '', $data = [])
    {
        $this->_response = $this->CronTabManager->success($msg, $data);
    }

    protected function failure($msg = '', $data = [])
    {
        $this->_response = $this->CronTabManager->error($msg, $data);
    }

    protected function statusCode($code)
    {
        $this->statusCode = $code;
    }

    protected function get($key)
    {
        return $this->CronTabManager->modx->request->getParameters($key);
    }

    /**
     * @return array
     */
    public function eiEmpt($val)
    {
        if (is_numeric($val)) return $val;
        return empty($val) ? '*' : $val;
    }


    protected function taskHandler(CronTabManagerTask $object)
    {
        $linkPath = $this->CronTabManager->config['linkPath'];
        $php_command = $this->CronTabManager->config['php_command'];
        $item = $object->toArray();
        $item['createdon'] = date('Y-m-d H:i:s', $item['createdon']);
        $item['updatedon'] = date('Y-m-d H:i:s', $item['updatedon']);
        $time = array(
            $this->eiEmpt($item['minutes']),
            $this->eiEmpt($item['hours']),
            $this->eiEmpt($item['days']),
            $this->eiEmpt($item['months']),
            $this->eiEmpt($item['weeks']),
        );
        $item['link_path'] = $php_command . ' ' . $linkPath . '/' . $object->get('path_task');
        $item['lock'] = $object->isLockFile();
        $item['is_blocked_time'] = $object->isBlockUpTask();
        $item['time'] = implode(' ', $time);
        $item['mode_develop'] = $object->get('mode_develop');

        $is_blocked = false;
        if ($item['lock'] or $item['is_blocked_time']) {
            $is_blocked = true;
        }
        $item['is_blocked'] = $is_blocked;
        $item['file_exists_log'] = file_exists($object->getFileLogPath());

        return $item;
    }


    protected function _task()
    {
        $task_id = (int)$this->get('task_id');
        echo '<pre>';
        print_r($task_id);
        die;
    }


    protected function _play()
    {
        if (!$Task = $this->getTask()) {
            $this->failure('Задание не найден');
        } else {
            $task = $Task->get('path_task');
            $scheduler_path = $this->modx->getOption('crontabmanager_scheduler_path');
            if (!file_exists($scheduler_path)) {
                $this->failure('Контроллер не найден');
            } else {
                $scheduler = $this->CronTabManager->loadSchedulerService();
                $scheduler->php(str_ireplace('.php', '', $task));
                $scheduler->process();
            }
        }
    }


    /**
     * @return CronTabManagerTask|null
     */
    protected function getTask()
    {
        $task_id = (int)$this->get('task_id');
        /* @var CronTabManagerTask $Task */
        if ($Task = $this->modx->getObject('CronTabManagerTask', $task_id)) {
            return $Task;
        }
        return null;
    }

    protected function _log()
    {
        if (!$Task = $this->getTask()) {
            $this->failure('Задание не найден');
        } else {
            if (!file_exists($Task->getFileLogPath())) {
                $this->failure('Файл с логом для задания отсутствует');
            } else {
                $tmp = $Task->readLogFile();
                $content = $this->readLogFileFormat($tmp);
                $this->success('', array(
                    'path_task' => $Task->get('path_task'),
                    'content' => $content
                ));
            }
        }
    }

    /**
     * Вернет текст из лог файла
     * @return string|boolean
     */
    public function readLogFileFormat($content)
    {
        $content = nl2br($content);
        $content = str_ireplace('✘', '❌', $content);
        $content = str_ireplace('✔', '✅', $content);
        #$content = '<pre>' . $content . '</pre>';
        return $content;
    }

    protected function _unlock()
    {
        if (!$Task = $this->getTask()) {
            $this->failure('Задание не найден');
        } else {
            if ($Task->isLockFile()) {
                $Task->unLock();
            }
            $this->success();
        }
    }

    protected function _tasks()
    {
        $categories = [];
        $q = $this->modx->newQuery('CronTabManagerCategory');
        $q->select('id,name');
        if ($q->prepare() && $q->stmt->execute()) {
            while ($row = $q->stmt->fetch(\PDO::FETCH_ASSOC)) {
                $categories[$row['id']] = $row['name'];
            }
        }

        $tasks = [];

        $q = $this->CronTabManager->modx->newQuery('CronTabManagerTask');
        if ($objectList = $this->modx->getCollection('CronTabManagerTask', $q)) {
            foreach ($objectList as $object) {
                $item = $this->taskHandler($object);
                $item['category_name'] = $categories[$item['parent']];
                $tasks[] = $item;
            }
        }

        $this->success('', ['tasks' => $tasks]);
    }

    protected function _notifications()
    {
        $notifications = [];
        /* @var CronTabManagerNotification $object */
        $q = $this->CronTabManager->modx->newQuery('CronTabManagerNotification');
        if ($objectList = $this->modx->getCollection('CronTabManagerNotification', $q)) {
            foreach ($objectList as $object) {
                $notifications[] = $object->toArray();
                #$object->set('read_application',true);
            }
        }

        /* @var modProcessorResponse $response */
        $response = $this->CronTabManager->runProcessor('mgr/notification/getlist');

        if ($response->isError()) {
            $this->failure($response->getMessage());
        } else {
            $res = $this->modx->fromJSON($response->response);
            $this->success('', ['notifications' => $res['results']]);
        }
    }

    protected function _check()
    {
        $this->success();
    }

    protected function _site()
    {
        $this->success('', [
            'site_name' => $this->CronTabManager->modx->getOption('site_name')
        ]);
    }
}