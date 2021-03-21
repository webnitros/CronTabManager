<?php

namespace mini;
use MODxProcessorTestCase;

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

}
