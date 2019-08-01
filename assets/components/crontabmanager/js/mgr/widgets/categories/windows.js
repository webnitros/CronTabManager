CronTabManager.window.CreateCategory = function (config) {
    config = config || {}
    config.url = CronTabManager.config.connector_url

    Ext.applyIf(config, {
        title: _('crontabmanager_category_create'),
        width: 600,
        cls: 'crontabmanager_windows',
        baseParams: {
            action: 'mgr/category/create',
        }
    })
    CronTabManager.window.CreateCategory.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.window.CreateCategory, CronTabManager.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('crontabmanager_category_name'),
                name: 'name',
                id: config.id + '-name',
                anchor: '99%',
                allowBlank: false,
            }, {
                xtype: 'textarea',
                fieldLabel: _('crontabmanager_category_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('crontabmanager_category_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('crontabmanager-category-window-create', CronTabManager.window.CreateCategory)

CronTabManager.window.UpdateCategory = function (config) {
    config = config || {}
    Ext.applyIf(config, {
        title: _('crontabmanager_category_update'),
        baseParams: {
            action: 'mgr/category/update',
            resource_id: config.resource_id
        },
    })
    CronTabManager.window.UpdateCategory.superclass.constructor.call(this, config)

}
Ext.extend(CronTabManager.window.UpdateCategory, CronTabManager.window.CreateCategory)
Ext.reg('crontabmanager-category-window-update', CronTabManager.window.UpdateCategory)