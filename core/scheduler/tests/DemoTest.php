<?php

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 04.03.2021
 * Time: 9:43
 */
class DemoTest extends MODxProcessorTestCase
{
    public function testSiteName()
    {
        self::assertEquals('REVOLUTION', $this->modx->getOption('site_name'));
    }

    public function testSiteStatus()
    {
        $site = (boolean)$this->modx->getOption('site_status');
        self::assertTrue($site);
    }
}
