<?php

/**
 * Демонстрация контроллера
 */
class CrontabControllerDemo extends modCrontabController
{
    public function process()
    {
        echo '<pre>';
        print_r('Ошибка');
        die;
    }
}