CronTabManager.window.CreateNotification = function (config) {
    config = config || {}
    config.url = CronTabManager.config.connector_url

    Ext.applyIf(config, {
        title: _('crontabmanager_notification_create'),
        width: 800,
        cls: 'crontabmanager_windows',
        baseParams: {
            action: 'mgr/notification/create',
        }
    })
    CronTabManager.window.CreateNotification.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.window.CreateNotification, CronTabManager.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'crontabmanager-combo-parent',
                fieldLabel: _('crontabmanager_notification_parent'),
                name: 'parent',
                id: config.id + '-parent',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('crontabmanager_notification_path_notification'),
                name: 'path_notification',
                id: config.id + '-path_notification',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'modx-description',
                style: 'margin-top: 8px;',
                html: _('crontabmanager_notification_path_notification_desc'),
                name: 'notificationdescription',
                id: config.id + '-notificationdescription',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_notification_mode_develop'),
                description: _('crontabmanager_notification_mode_develop_desc'),
                name: 'mode_develop',
                id: config.id + '-mode_develop',
                checked: true,
            },
            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_notification_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }

            ,

            {
                xtype: 'numberfield',
                fieldLabel: _('crontabmanager_notification_max_number_attempts'),
                description: _('crontabmanager_notification_max_number_attempts_desc'),
                name: 'max_number_attempts',
                id: config.id + '-max_number_attempts',
                anchor: '99%',
            },

            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_notification_notification_enable'),
                name: 'notification_enable',
                id: config.id + '-notification_enable',
                checked: true,
            },{
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_notification_add_output_email'),
                description: _('crontabmanager_notification_add_output_email_desc'),
                name: 'add_output_email',
                id: config.id + '-add_output_email',
                checked: true,
            }, {
                xtype: 'textfield',
                fieldLabel: _('crontabmanager_notification_notification_emails'),
                description: _('crontabmanager_notification_notification_emails_desc'),
                name: 'notification_emails',
                id: config.id + '-notification_emails',
                anchor: '99%'
            },

            {
                xtype: 'numberfield',
                fieldLabel: _('crontabmanager_notification_log_storage_time'),
                description: _('crontabmanager_notification_log_storage_time_desc'),
                name: 'log_storage_time',
                id: config.id + '-log_storage_time',
                anchor: '99%',
            },
        ]
    },

})
Ext.reg('crontabmanager-notification-window-create', CronTabManager.window.CreateNotification)

CronTabManager.window.UpdateNotification = function (config) {
    config = config || {}
    Ext.applyIf(config, {
        title: _('crontabmanager_notification_update'),
        baseParams: {
            action: 'mgr/notification/update',
            resource_id: config.resource_id
        },
    })
    CronTabManager.window.UpdateNotification.superclass.constructor.call(this, config)

}
Ext.extend(CronTabManager.window.UpdateNotification, CronTabManager.window.CreateNotification)
Ext.reg('crontabmanager-notification-window-update', CronTabManager.window.UpdateNotification)
