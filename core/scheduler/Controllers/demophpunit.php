<?php

use PHPUnit\Framework\TestSuite;

/**
 * Демонстрация контроллера
 */
class CrontabControllerDemoPhpUnit extends modCrontabControllerPhpUnit
{

    public function process()
    {
        $response = $this->runTest('order/OrderTest');
        echo '<pre>';
        print_r($response);
        die;
    }
}
