<?php

abstract class MODxProcessorTestCase extends MODxTestCase
{
    /* @var GuzzleHttp\Psr7\Response $rest */
    public $rest = null;
    public $body = null;
    public $response = null;

    /**
     * Проверяем что изображение загрузилось после отправки триггера
     * @param $url
     * @return int
     */
    public function checkUrlCode($url, $params = null, $method = 'POST')
    {
        try {
            $config = [
                'verify' => false,
                'timeout' => 30.0,
                'headers' => [
                    'User-Agent' => 'MODX RestClient/1.0.0',
                ],
            ];

            if ($params) {
                $config['json'] = $params;
            }
            $this->client = new GuzzleHttp\Client($config);

            if ($method === 'POST') {
                $response = $this->client->post($url);
            } else {
                $response = $this->client->get($url);
            }
            $status = $response->getStatusCode();
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $message = $e->getMessage();
            $status = $response->getStatusCode();
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            $response = $e->getResponse();
            $message = $e->getMessage();
        } catch (GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
            $message = $e->getMessage();
            $status = $response->getStatusCode();
        }
        $this->body = $response->getBody()->getContents();
        $this->response = $this->modx->fromJSON($this->body);
        $this->rest = $response;
        return $status;
    }


    /**
     * Запускает крон задание
     * @param $task_id
     */
    public function runTask($task_id, $checkEnable = false)
    {

        /* @var CronTabManagerTask $Task */
        $Task = $this->modx->getObject('CronTabManagerTask', $task_id);
        self::assertInstanceOf('CronTabManagerTask', $Task);

        $task_name = $Task->get('path_task');
        $msg = "-------- TASK [{$task_name}]";
        echo $msg . PHP_EOL;
        self::assertInstanceOf('CronTabManagerTask', $Task, $msg . " error get task");
        self::assertTrue($Task->get('active'), $msg . ' disabled');
        self::assertNotTrue($Task->get('mode_develop'), $msg . ' enable mode_develop');
        self::assertTrue($Task->get('notification_enable'), $msg . ' disabled notification_enable');
        if (!$checkEnable) {
            if ($Task->isBlockUpTask() || $Task->isLock()) {
                // Разблокируем задание
                $Task->unBlockUpTask();
                $Task->unLock();
                self::assertTrue($Task->save(), $msg . 'Task save');
            }
            $path_link = $this->CronTabManager->config['linkPath'] . '/' . $task_name;
            self::assertFileExists($path_link);
        }

        $scheduler = $this->CronTabManager->loadSchedulerService();
        self::assertInstanceOf('SchedulerService', $scheduler);

        // Включаем выброс в исключение чтобы поймать правильный ответ
        if (!$checkEnable) {
            $scheduler->php(str_ireplace('.php', '', $task_name));
            $scheduler->enableEnabledException();
            try {
                $scheduler->process();
            } catch (Exception $e) {
                // Проверяем что ответ содержит Time all
                self::assertTrue(strripos($e->getMessage(), 'Time all') !== false, $msg . 'Error time all');
            }
        }
    }


}
