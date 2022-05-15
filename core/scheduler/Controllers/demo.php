<?php
/**
 * Демонстрация контроллера
 */
class CrontabControllerDemo extends modCrontabController
{
    public function process()
    {
        echo '<pre>';
        print_r(2332); die;
        $this->modx->log(modX::LOG_LEVEL_ERROR, "Задание завершено");
    }
}
