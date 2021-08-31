<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx->addPackage('crontabmanager', MODX_CORE_PATH . 'components/crontabmanager/model/');
            $manager = $modx->getManager();
            $objects = [];
            $schemaFile = MODX_CORE_PATH . 'components/crontabmanager/model/schema/crontabmanager.mysql.schema.xml';
            if (is_file($schemaFile)) {
                $schema = new SimpleXMLElement($schemaFile, 0, true);
                if (isset($schema->object)) {
                    foreach ($schema->object as $obj) {
                        $objects[] = (string)$obj['class'];
                    }
                }
                unset($schema);
            }
            foreach ($objects as $class) {
                $table = $modx->getTableName($class);
                $sql = "SHOW TABLES LIKE '" . trim($table, '`') . "'";
                $stmt = $modx->prepare($sql);
                $newTable = true;
                if ($stmt->execute() && $stmt->fetchAll()) {
                    $newTable = false;
                }
                // If the table is just created
                if ($newTable) {
                    $manager->createObjectContainer($class);
                } else {
                    // If the table exists
                    // 1. Operate with tables
                    $tableFields = [];
                    $c = $modx->prepare("SHOW COLUMNS IN {$modx->getTableName($class)}");
                    $c->execute();
                    while ($cl = $c->fetch(PDO::FETCH_ASSOC)) {
                        $tableFields[$cl['Field']] = $cl['Field'];
                    }
                    foreach ($modx->getFields($class) as $field => $v) {
                        if (in_array($field, $tableFields)) {
                            unset($tableFields[$field]);
                            $manager->alterField($class, $field);
                        } else {
                            $manager->addField($class, $field);
                        }
                    }
                    foreach ($tableFields as $field) {
                        $manager->removeField($class, $field);
                    }
                    // 2. Operate with indexes
                    $indexes = [];
                    $c = $modx->prepare("SHOW INDEX FROM {$modx->getTableName($class)}");
                    $c->execute();
                    while ($row = $c->fetch(PDO::FETCH_ASSOC)) {
                        $name = $row['Key_name'];
                        if (!isset($indexes[$name])) {
                            $indexes[$name] = [$row['Column_name']];
                        } else {
                            $indexes[$name][] = $row['Column_name'];
                        }
                    }
                    foreach ($indexes as $name => $values) {
                        sort($values);
                        $indexes[$name] = implode(':', $values);
                    }
                    $map = $modx->getIndexMeta($class);
                    // Remove old indexes
                    foreach ($indexes as $key => $index) {
                        if (!isset($map[$key])) {
                            if ($manager->removeIndex($class, $key)) {
                                $modx->log(modX::LOG_LEVEL_INFO, "Removed index \"{$key}\" of the table \"{$class}\"");
                            }
                        }
                    }
                    // Add or alter existing
                    foreach ($map as $key => $index) {
                        ksort($index['columns']);
                        $index = implode(':', array_keys($index['columns']));
                        if (!isset($indexes[$key])) {
                            if ($manager->addIndex($class, $key)) {
                                $modx->log(modX::LOG_LEVEL_INFO, "Added index \"{$key}\" in the table \"{$class}\"");
                            }
                        } else {
                            if ($index != $indexes[$key]) {
                                if ($manager->removeIndex($class, $key) && $manager->addIndex($class, $key)) {
                                    $modx->log(modX::LOG_LEVEL_INFO,
                                        "Updated index \"{$key}\" of the table \"{$class}\""
                                    );
                                }
                            }
                        }
                    }
                }
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:

            /* @var CronTabManagerCategory $object */
            /* @var CronTabManagerCategory $Task */
            if (!$Category = $modx->getObject('CronTabManagerCategory', array('name' => 'Демо категория'))) {
                $Category = $modx->newObject('CronTabManagerCategory');
                $Category->set('name', 'Демо категория');
                $Category->save();

                /* @var CronTabManagerTask $object */
                if (!$Task = $modx->getObject('CronTabManagerTask', array('path_task' => 'demo.php'))) {
                    $Task = $modx->newObject('CronTabManagerTask');
                    $Task->set('parent', $Category->get('id'));
                    $Task->set('path_task', 'demo.php');
                    $Task->set('description', 'Тестовое задание для демонстрации работы контроллеров');
                    $Task->set('minutes', '*/1');
                    $Task->set('hours', '*');
                    $Task->set('days', '*');
                    $Task->set('months', '*');
                    $Task->set('weeks', '*');
                    $Task->set('active', 1);
                    $Task->set('log_storage_time', 10080);
                    $Task->save();
                }
            }

            if (!$Category = $modx->getObject('CronTabManagerCategory', array('name' => 'Tests'))) {
                $Category = $modx->newObject('CronTabManagerCategory');
                $Category->set('name', 'Tests');
                $Category->save();

                /* @var CronTabManagerTask $object */
                if (!$Task = $modx->getObject('CronTabManagerTask', array('path_task' => 'demophpunit.php'))) {
                    $Task = $modx->newObject('CronTabManagerTask');
                    $Task->set('parent', $Category->get('id'));
                    $Task->set('path_task', 'demophpunit.php');
                    $Task->set('description', 'Тестовое задание для демонстрации работы PHPunit тестов');
                    $Task->set('minutes', '*/1');
                    $Task->set('hours', '*');
                    $Task->set('days', '*');
                    $Task->set('months', '*');
                    $Task->set('weeks', '*');
                    $Task->set('active', 1);
                    $Task->set('log_storage_time', 10080);
                    $Task->save();
                }

            }

            break;
        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;
