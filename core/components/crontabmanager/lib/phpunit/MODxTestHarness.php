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
require_once dirname(__FILE__) . '/MODxTestCase.php';
require_once dirname(__FILE__) . '/MODxProcessorTestCase.php';
require_once dirname(__FILE__) . '/MODxTestResult.php';
require_once dirname(__FILE__) . '/MODxTestSuite.php';

/**
 * Main MODX test harness.
 *
 * Use by running this in command-line:
 *
 * sh modxtestharness.sh
 *
 * @package modx-test
 */
class MODxTestHarness
{
    /** @var array $fixtures */
    protected static $fixtures = array();
    /** @var array $properties */
    protected static $properties = array();
    /** @var boolean $debug */
    protected static $debug = false;

    /**
     * Create or grab a reference to a static xPDO/modX instance.
     *
     * The instances can be reused by multiple tests and test suites.
     *
     * @param string $class A fixture class to get an instance of.
     * @param string $name A unique identifier for the fixture.
     * @param boolean $new
     * @param array $options An array of configuration options for the fixture.
     * @return object|null An instance of the specified fixture class or null on failure.
     */
    public static function &getFixture($class, $name, $new = false, array $options = array())
    {

        if (!$new && array_key_exists($name, self::$fixtures) && self::$fixtures[$name] instanceof $class) {
            $fixture =& self::$fixtures[$name];
        } else {
            $properties = array();

            include_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/model/modx/modx.class.php';
            #@include(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/config.core.php');

            $properties['runtime'] = strftime("%Y%m%dT%H%M%S");
            self::$properties['logLevel'] = modX::LOG_LEVEL_INFO;
            self::$properties['xpdo_driver'] = 'mysql';
            self::$properties['context'] = 'web';
            self::$properties['debug'] = false;
            self::$properties['logTarget'] = array(
                'target' => 'file',
                'options' => array(
                    'filename' => "unit_test_{$properties['runtime']}.log",
                    'filepath' => MODX_CORE_PATH . 'cache/logs/'
                )
            );
            self::$properties['mysql_array_options'] = array(
                xPDO::OPT_HYDRATE_FIELDS => true,
                xPDO::OPT_HYDRATE_RELATED_OBJECTS => true,
                xPDO::OPT_HYDRATE_ADHOC_FIELDS => true,
            );
            self::$properties['mysql_array_driverOptions'] = array();

            // Проверяем какой конфиг подключить. Если переданы параметры то считаем что это был запуск с локальной машины
            $args = [];
            if (isset($GLOBALS['argv']) && count($GLOBALS['argv']) > 1) {
                $query = implode('&', array_slice($GLOBALS['argv'], 1));
                parse_str($query, $args);
            }
            $isTest =  isset($args['--configuration']);
            if ($isTest) {
                self::$properties['config_key'] = 'test';
            }

            $fixture = null;
            $driver = self::$properties['xpdo_driver'];
            switch ($class) {
                case 'modX':
                    if (!defined('MODX_REQP')) {
                        define('MODX_REQP', false);
                    }

                    if (!defined('MODX_CONFIG_KEY')) {
                        define('MODX_CONFIG_KEY', array_key_exists('config_key', self::$properties) ? self::$properties['config_key'] : 'test');
                    }


                    if (!defined('MODX_API_MODE')) {
                        define('MODX_API_MODE', true);
                    }

                    $fixture = new modX(
                        null,
                        self::$properties["{$driver}_array_options"]
                    );

                    if ($fixture instanceof modX) {
                        $logLevel = array_key_exists('logLevel', self::$properties) ? self::$properties['logLevel'] : modX::LOG_LEVEL_WARN;
                        $fixture->setLogLevel($logLevel);

                        if (!empty(self::$debug)) {
                            $fixture->setDebug(self::$properties['debug']);
                        }

                        $logTarget = array_key_exists('logTarget', self::$properties) ? self::$properties['logTarget'] : (XPDO_CLI_MODE ? 'ECHO' : 'HTML');
                        $fixture->setLogTarget($logTarget);

                        $fixture->initialize(self::$properties['context']);
                        $fixture->user = $fixture->newObject('modUser');
                        $fixture->user->set('id', $fixture->getOption('modx.test.user.id', null, 1));
                        $fixture->user->set('username', $fixture->getOption('modx.test.user.username', null, 'test'));
                        $fixture->getRequest();
                        $fixture->getParser();
                        $fixture->request->loadErrorHandler();
                    }
                    break;
                default:
                    $fixture = new $class($options);
                    break;
            }

            if ($fixture !== null && $fixture instanceof $class) {
                self::$fixtures[$name] = $fixture;
            } else {
                die("Error setting fixture {$name} of expected class {$class}.");
            }
        }
        return $fixture;
    }

}
