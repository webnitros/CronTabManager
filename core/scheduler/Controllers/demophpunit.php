<?php

/**
 * Демонстрация контроллера
 * Директория с тестами для демонстрации core/scheduler/tests/
 */
class CrontabControllerDemoPhpUnit extends modCrontabController
{

    public function run()
    {
        // Демонстрационный тесты, Директория по умолчанию является корневая директория
        $this->setPathTests(MODX_CORE_PATH . 'scheduler/tests/');

        // Запустит тест из файла tests/DemoTest.php
        $this->addTest('DemoTest');

        // Запускает все тесты находящиеся в директории tests/frontend/
        $this->addTest('frontend');

        $this->runTest();
    }
}
