<?php

/**
 * MODX Revolution
 *
 * Copyright 2006-2014 by MODX, LLC.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package modx-test
 */

use PHPUnit\Framework\TestCase;

/**
 * Extends the basic PHPUnit TestCase class to provide MODX specific methods
 *
 * @package modx-test
 */
abstract class MODxTestCase extends TestCase
{
    /**
     * @var modX $modx
     */
    public $modx = null;
    /**
     * @var CronTabManager|null $CronTabManager
     */
    public $CronTabManager = null;
    /**
     * @var bool
     */
    public $debug = false;

    public $path;

    /**
     * Ensure all tests have a reference to the MODX object
     */
    public function setUp()
    {
        $this->modx =& MODxTestHarness::getFixture('modX', 'modx');
        if ($this->modx->request) {
            $this->modx->request->loadErrorHandler();
            $this->modx->error->reset();
        }
        /* @var CronTabManager $CronTabManager */
        $this->CronTabManager = $this->modx->getService('crontabmanager', 'CronTabManager', MODX_CORE_PATH . 'components/crontabmanager/model/');
    }

}
