<?php

use PHPUnit\Framework\TestSuite;

/**
 * Демонстрация контроллера
 */
class CrontabControllerDemoPhpUnit extends modCrontabController
{

    public function run()
    {
        $this->addTest('DemoTest');
        $this->addTest('DemoTest2');
        $this->runTest();
    }
}
