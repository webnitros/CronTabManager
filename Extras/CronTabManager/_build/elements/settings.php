<?php

return [

    // path
    'scheduler_path' => [
        'xtype' => 'textfield',
        'value' => '{core_path}scheduler',
        'area' => 'crontabmanager_path',
    ],
    'link_path' => [
        'xtype' => 'textfield',
        'value' => '{core_path}scheduler/ControllersLinks',
        'area' => 'crontabmanager_path',
    ],
    'lock_path' => [
        'xtype' => 'textfield',
        'value' => '{core_path}scheduler/lock',
        'area' => 'crontabmanager_path',
    ],
    'log_path' => [
        'xtype' => 'textfield',
        'value' => '{core_path}scheduler/logs',
        'area' => 'crontabmanager_path',
    ],

    // Main

    'php_command' => [
        'xtype' => 'textfield',
        'value' => 'php7.0',
        'area' => 'crontabmanager_main',
    ],

    'set_completion_time' => [
        'xtype' => 'combo-boolean',
        'value' => 1,
        'area' => 'crontabmanager_main',
    ],

    'user_id' => [
        'xtype' => 'numberfield',
        'value' => 1,
        'area' => 'crontabmanager_main',
    ],

    'log_storage_time' => [
        'xtype' => 'numberfield',
        'value' => 10080,
        'area' => 'crontabmanager_main',
    ],


    'email_administrator' => [
        'xtype' => 'textfield',
        'value' => 'info@bustep.ru',
        'area' => 'crontabmanager_main',
    ],


    'handler_class' => [
        'xtype' => 'textfield',
        'value' => 'CrontabManagerHandlerFile',
        'area' => 'crontabmanager_main',
    ],

    // blocking

    'blocking_time_minutes' => [
        'xtype' => 'numberfield',
        'value' => 1,
        'area' => 'crontabmanager_blocking',
    ],

    'allow_blocking_tasks' => [
        'xtype' => 'combo-boolean',
        'value' => 1,
        'area' => 'crontabmanager_blocking',
    ],

    'max_minuts_blockup' => [
        'xtype' => 'numberfield',
        'value' => 1440,
        'area' => 'crontabmanager_blocking',
    ],



];