# CronTabManager

Компонент позволяет управлять крон заданиями из админки

Добавляет задания в crontab без использования адресной строки

Для работы необходимо чтобы на хостинге был доступ к функциями:  system и passthru для запуска из под php

А так же доступ к задания crontab
https://proselyte.net/tutorials/junit/api/

```bash
./vendor/bin/phpunit --filter fdkOrderTest tests/order/fdkOrderTest.php --bootstrap tests/MODxTestHarness.php  --testdox
```

### Добавление phpunit тестов

```bash
composer install
```

Установка phpunit 

```bash
composer require --dev phpunit/phpunit ^latest
```

Создаем файл с названием в корне сайта tests/DemoTest.php

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
