<?php
/**
 * Демонстрация контроллера для запуска phpunit тестов с modx внутри версия php не ниже 7.3 и установленный phpunit "phpunit/phpunit": "^9.5"
// Создаем файл с название в корне сайта tests/DemoTest.php
 * Содержимое файла
<?php
class DemoTest extends MODxProcessorTestCase
{
public function testSuccess()
{
$test = true;
$this->assertTrue($test, '"success with custom message"');
}

public function testFailure()
{
$test = false;
$this->assertTrue($test, '"success with custom message"');
}
}
 */
class CrontabControllerDemoPhpUnit extends modCrontabControllerPhpUnit
{
    public function process()
    {
        try {
            $response = $this->runTest('DemoTest');
        } catch (Exception $e) {
            $response = $e->getMessage();
            echo '<pre>';
            print_r($response);
            die;
        }
    }
}
