<?php

namespace minishop2;
use MODxProcessorTestCase;
use PDO;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 04.03.2021
 * Time: 9:43
 */
class Setting extends MODxProcessorTestCase
{
    public function testDEMO()
    {
        $test = true;
        self::assertTrue($test, '"success with custom message"');
    }

    public function testCrontab()
    {

        $q = $this->modx->newQuery('CronTabManagerTask');
        $q->select('id');
        if ($q->prepare() && $q->stmt->execute()){
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->runTask($row['id'],true);
            }
        }

    }
}
