# CronTabManager

Компонент позволяет управлять крон заданиями из админки

Добавляет задания в crontab без использования адресной строки

Для работы необходимо чтобы на хостинге был доступ к функциями:  system и passthru для запуска из под php

### Добавление phpunit тестов

```bash
composer install
```

Установка phpunit

```bash
composer require --dev phpunit/phpunit ^latest
```

Создаем файл с названием в корне сайта **tests/DemoTest.php**

```php
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
```

Создаем контроллер **core/scheduler/Controllers/demophpunit.php**

```php
<?php
use PHPUnit\Framework\TestSuite;

/**
 * Демонстрация контроллера
 */
class CrontabControllerDemoPhpUnit extends modCrontabControllerPhpUnit
{

    public function process()
    {
        $response = $this->runTest('DemoTest');
        echo '<pre>';
        print_r($response);
        die;
    }
}
```

После добавляем заадание в crontab менеджер и запускаем его

https://file.modx.pro/files/e/5/c/e5cb48ccffaeef677442972630484d8f.png

### Запуск из консоли

```bash
./vendor/bin/phpunit --filter DemoTest tests/DemoTest.php --bootstrap core/components/crontabmanager/lib/phpunit/MODxTestHarness.php  --testdox
```


Схема работы 
https://github.com/sebastianbergmann/phpunit/issues/3213
