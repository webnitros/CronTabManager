<?php
/**
 * Демонстрация контроллера
 */
class CrontabControllerDemo extends modCrontabController
{
    public function process()
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, "Задание завершено");
    }
}
