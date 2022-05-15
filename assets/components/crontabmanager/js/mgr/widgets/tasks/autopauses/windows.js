CronTabManager.window.CreateAutoPause = function (config) {
    config = config || {}
    config.url = CronTabManager.config.connector_url

    Ext.applyIf(config, {
        title: _('crontabmanager_task_autopause_create'),
        width: 600,
        cls: 'crontabmanager_windows',
        baseParams: {
            action: 'mgr/task/autopause/create',
        }
    })
    CronTabManager.window.CreateAutoPause.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.window.CreateAutoPause, CronTabManager.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {xtype: 'hidden', name: 'task_id', id: config.id + '-task_id'},

            {
                layout: 'column',
                items: [{
                    columnWidth: .4,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'crontabmanager-combo-filter-when',
                        fieldLabel: _('crontabmanager_task_autopause_when'),
                        name: 'when',
                        id: config.id + '-when',
                        anchor: '99%',
                        allowBlank: false,
                    }]
                }, {
                    columnWidth: .3,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'timefield',
                        fieldLabel: _('crontabmanager_task_autopause_from'),
                        format: 'H:i',
                        altFormats:'H:i',
                        name: 'from',
                        id: config.id + '-from',
                        anchor: '99%',
                        allowBlank: false,
                    }]
                }, {
                    columnWidth: .3,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    items: [{
                        xtype: 'timefield',
                        fieldLabel: _('crontabmanager_task_autopause_to'),
                        name: 'to',
                        format: 'H:i',
                        altFormats:'H:i',
                        id: config.id + '-to',
                        anchor: '99%',
                        allowBlank: false,
                    }],
                }]
            },

            {
                html: 'Выберите дни автоматической остановки задания и время с & по &',
                cls: '',
                style: {margin: '15px 0 0 0'}
            },

            {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_task_autopause_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]
    },

})
Ext.reg('crontabmanager-autopause-window-create', CronTabManager.window.CreateAutoPause)

CronTabManager.window.UpdateAutoPause = function (config) {
    config = config || {}
    Ext.applyIf(config, {
        title: _('crontabmanager_task_autopause_update'),
        baseParams: {
            action: 'mgr/task/autopause/update'
        },
    })
    CronTabManager.window.UpdateTask.superclass.constructor.call(this, config)

}
Ext.extend(CronTabManager.window.UpdateAutoPause, CronTabManager.window.CreateAutoPause)
Ext.reg('crontabmanager-autopause-window-update', CronTabManager.window.UpdateAutoPause)
