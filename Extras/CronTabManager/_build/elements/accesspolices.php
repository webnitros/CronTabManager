<?php
return [
    'CronTabManager' => [
        'name' => 'CronTabManagerPolicy',
        'templateName' => 'CronTabManagerTemplate',
        'description' => 'The security policy for a CronTabManager',
        'parent' => 0,
        'class' => '',
        'lexicon' => 'crontabmanager:permissions',
        'data' => json_encode(array(
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
        ))
    ]
];