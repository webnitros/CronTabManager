<?php

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 04.03.2021
 * Time: 9:43
 */

namespace frontend;

use MODxProcessorTestCase;

class DemoTest2 extends MODxProcessorTestCase
{
    public function testSiteName()
    {
        $Resource = $this->modx->getObject('modResource', 1);
        self::assertNotInstanceOf('modResource', $Resource);
    }


}
