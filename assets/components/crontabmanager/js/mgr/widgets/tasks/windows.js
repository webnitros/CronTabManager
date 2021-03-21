CronTabManager.window.CreateTask = function (config) {
    config = config || {}
    config.url = CronTabManager.config.connector_url

    Ext.applyIf(config, {
        title: _('crontabmanager_task_create'),
        width: 800,
        cls: 'crontabmanager_windows',
        baseParams: {
            action: 'mgr/task/create',
        }
    })
    CronTabManager.window.CreateTask.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.window.CreateTask, CronTabManager.window.Default, {

    getFields: function (config) {
        return [{
            xtype: 'modx-tabs',
            deferredRender: true,
            items: [{
                title: _('crontabmanager_task'),
                layout: 'form',
                items: CronTabManager.window.CreateTask.prototype.getFieldsTask.call(this, config),
            }, {
                title: _('crontabmanager_task_log'),
                items: [{
                    xtype: 'crontabmanager-grid-tasks-logs',
                    record: config.record,
                }]
            }, {
                title: _('crontabmanager_task_setting'),
                layout: 'form',
                items: CronTabManager.window.CreateTask.prototype.getFieldsSetting.call(this, config),
            }]
        }];
    },

    getFieldsTask: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'crontabmanager-combo-parent',
                fieldLabel: _('crontabmanager_task_parent'),
                name: 'parent',
                id: config.id + '-parent',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('crontabmanager_task_path_task'),
                name: 'path_task',
                id: config.id + '-path_task',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'modx-description',
                style: 'margin-top: 8px;',
                html: _('crontabmanager_task_path_task_desc'),
                name: 'taskdescription',
                id: config.id + '-taskdescription',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'fieldset'
                , layout: 'column'
                , style: 'padding:15px 5px;text-align:center;'
                , defaults: {msgTarget: 'under', border: false}
                , items: [{
                columnWidth: .20
                , layout: 'form'
                , items: [
                    {
                        xtype: 'textfield',
                        name: 'minutes',
                        value: '*',
                        emptyText: '*',
                        fieldLabel: _('crontabmanager_task_minutes'),
                        anchor: '100%',
                    }
                ]
            }, {
                columnWidth: .20
                , layout: 'form'
                , items: [
                    {
                        xtype: 'textfield',
                        name: 'hours',
                        value: '*',
                        emptyText: '*',
                        fieldLabel: _('crontabmanager_task_hours'),
                        anchor: '100%',
                    }
                ]
            }, {
                columnWidth: .20
                , layout: 'form'
                , items: [
                    {
                        xtype: 'textfield',
                        name: 'days',
                        value: '*',
                        emptyText: '*',
                        fieldLabel: _('crontabmanager_task_days'),
                        anchor: '100%',
                    }
                ]
            }, {
                columnWidth: .20
                , layout: 'form'
                , items: [
                    {
                        xtype: 'textfield',
                        name: 'months',
                        value: '*',
                        emptyText: '*',
                        fieldLabel: _('crontabmanager_task_months'),
                        anchor: '100%',
                    }
                ]
            },
                {
                    columnWidth: .20
                    , layout: 'form'
                    , items: [
                    {
                        xtype: 'textfield',
                        name: 'weeks',
                        value: '*',
                        emptyText: '*',
                        fieldLabel: _('crontabmanager_task_weeks'),
                        anchor: '100%',
                    }
                ]
                }]
            }, {
                xtype: 'textarea',
                fieldLabel: _('crontabmanager_task_description'),
                name: 'description',
                id: config.id + '-description',
                height: 100,
                anchor: '99%'
            },
            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_task_mode_develop'),
                description: _('crontabmanager_task_mode_develop_desc'),
                name: 'mode_develop',
                id: config.id + '-mode_develop',
                checked: true,
            },
            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_task_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }

            ,

            {
                xtype: 'numberfield',
                fieldLabel: _('crontabmanager_task_max_number_attempts'),
                description: _('crontabmanager_task_max_number_attempts_desc'),
                name: 'max_number_attempts',
                id: config.id + '-max_number_attempts',
                anchor: '99%',
            },

            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_task_notification_enable'),
                name: 'notification_enable',
                id: config.id + '-notification_enable',
                checked: true,
            },{
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_task_add_output_email'),
                description: _('crontabmanager_task_add_output_email_desc'),
                name: 'add_output_email',
                id: config.id + '-add_output_email',
                checked: true,
            }, {
                xtype: 'textfield',
                fieldLabel: _('crontabmanager_task_notification_emails'),
                description: _('crontabmanager_task_notification_emails_desc'),
                name: 'notification_emails',
                id: config.id + '-notification_emails',
                anchor: '99%'
            },

            {
                xtype: 'numberfield',
                fieldLabel: _('crontabmanager_task_log_storage_time'),
                description: _('crontabmanager_task_log_storage_time_desc'),
                name: 'log_storage_time',
                id: config.id + '-log_storage_time',
                anchor: '99%',
            },
        ]
    },

/*
    getFieldsSetting: function (config) {
        return [
            {
                xtype: 'numberfield',
                fieldLabel: _('crontabmanager_task_max_number_attempts'),
                description: _('crontabmanager_task_max_number_attempts_desc'),
                name: 'max_number_attempts',
                id: config.id + '-max_number_attempts',
                anchor: '99%',
            },
            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_task_notification_enable'),
                name: 'notification_enable',
                id: config.id + '-notification_enable',
                checked: true,
            }, {
                xtype: 'textfield',
                fieldLabel: _('crontabmanager_task_notification_emails'),
                description: _('crontabmanager_task_notification_emails_desc'),
                name: 'notification_emails',
                id: config.id + '-notification_emails',
                anchor: '99%'
            }
        ]
    },
*/

    getFieldsSetting: function (config) {
        return [
            {
                xtype: 'textarea',
                height: 150,
                fieldLabel: _('crontabmanager_task_message'),
                description: _('crontabmanager_task_message_desc'),
                name: 'message',
                id: config.id + '-message',
                anchor: '99%',
            }
        ]
    },

})
Ext.reg('crontabmanager-task-window-create', CronTabManager.window.CreateTask)

CronTabManager.window.UpdateTask = function (config) {
    config = config || {}
    Ext.applyIf(config, {
        title: _('crontabmanager_task_update'),
        baseParams: {
            action: 'mgr/task/update',
            resource_id: config.resource_id
        },
    })
    CronTabManager.window.UpdateTask.superclass.constructor.call(this, config)

}
Ext.extend(CronTabManager.window.UpdateTask, CronTabManager.window.CreateTask)
Ext.reg('crontabmanager-task-window-update', CronTabManager.window.UpdateTask)
