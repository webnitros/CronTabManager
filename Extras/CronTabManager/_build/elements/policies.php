<?php
return [
    'CronTabManagerTemplate' => [
        'description' => 'A policy for users to download CronTabManagerTemplate.',
        'template_group_name' => 'Object',
        'lexicon' => 'crontabmanager:permissions',
        'permissions' => array(
            'crontabmanager_menu' => true,
            'crontabmanager_create' => true,
            'crontabmanager_save' => true,
            'crontabmanager_view' => true,
            'crontabmanager_list' => true,
            'crontabmanager_remove' => true,
            'crontabmanager_run' => true,
            'crontabmanager_add_blocked' => true,
            'crontabmanager_un_blocked' => true,
            'crontabmanager_unlock' => true,
        ),
    ]
];