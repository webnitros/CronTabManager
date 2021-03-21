<?php

use PHPUnit\Framework\TestSuite;

/**
 * Демонстрация контроллера
 */
class CrontabControllerDemoPhpUnit extends modCrontabController
{

    public function run()
    {

        // Запуск тестов в директории /tests/minishop2/ - запустить все тесты в этой директории
        $this->addTest('minishop2');

        // Запуск тестов в директории /tests/mini/Setting.php - запустить все тесты в этой папке
        $this->addTest('mini/Setting');

        // Запустит тест из файла tests/DemoTest.php
        $this->addTest('DemoTest');

        $this->runTest();
    }
}
