CronTabManager.grid.Notifications = function (config) {
    config = config || {}
    if (!config.id) {
        config.id = 'crontabmanager-grid-notifications'
        config.namegrid = 'notifications'
        config.processor = 'mgr/notification/'
    }

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description} <br>{message}</p>'),
        renderer: function (v, p, record) {return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;'}
    })

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/notification/getlist',
        },
        plugins: this.exp,
        stateful: true,
        stateId: config.id,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                return !rec.data.active
                    ? 'crontabmanager-grid-row-disabled'
                    : ''
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    })
    CronTabManager.grid.Notifications.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.grid.Notifications, CronTabManager.grid.Default, {

    getFields: function (config) {
        return ['id', 'description', 'message', 'createdon', 'read', 'updatedon', 'number_attempts', 'notification_emails', 'read', 'send_email', 'parent',  'path_task',  'category_name',  'actions']
    },

    getColumns: function (config) {

        return [this.exp, {
            header: _('crontabmanager_notification_id'),
            dataIndex: 'id',
            sortable: true,
            width: 40
        }, {
            header: _('crontabmanager_task_category_name'),
            dataIndex: 'category_name',
            sortable: true,
            width: 70,
        }, {
            header: _('crontabmanager_task_path_task'),
            dataIndex: 'path_task',
            sortable: true,
            width: 200,
        },  {
            header: _('crontabmanager_notification_createdon'),
            dataIndex: 'createdon',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.formatDate,
            hidden: true
        }, {
            header: _('crontabmanager_notification_updatedon'),
            dataIndex: 'updatedon',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.formatDate,
            hidden: true
        }, {
            header: _('crontabmanager_notification_read'),
            dataIndex: 'read',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.renderBoolean,
        }, {
            header: _('crontabmanager_notification_notification_emails'),
            dataIndex: 'notification_emails',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.renderBoolean,
        }, {
            header: _('crontabmanager_notification_number_attempts'),
            dataIndex: 'number_attempts',
            sortable: true,
            width: 60,
            hidden: true
        }, {
            header: _('crontabmanager_grid_actions'),
            dataIndex: 'actions',
            renderer: CronTabManager.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actioeens'
        }]
    },

    getTopBar: function (config) {
        return [
            {
                xtype: 'crontabmanager-combo-parent',
                width: 300,
                custm: true,
                clear: true,
                addall: true,
                emptyText: _('crontabmanager_task_parent'),
                name: 'parent',
                id: config.id + '-parent',
                value: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    },
                    afterrender: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            }, '->', {
                xtype: 'xcheckbox',
                name: 'read',
                id: config.id + '-read',
                width: 130,
                boxLabel: _('crontabmanager_notification_filter_read'),
                ctCls: 'tbar-checkbox',
                checked: true,
                listeners: {
                    check: {fn: this.activeFilter, scope: this}
                }
            }, this.getSearchField()]
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex)
                this.updateItem(grid, e, row)
            },
        }
    },

    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value
        this.getBottomToolbar().changePage(1)
    },

    activeFilter: function (checkbox, checked) {
        var s = this.getStore()
        s.baseParams.active = checked ? 1 : 0
        this.getBottomToolbar().changePage(1)
    },


    fireParent: function (checkbox, value) {
        var s = this.getStore()
        s.baseParams.parent = value.id
        this.getBottomToolbar().changePage(1)
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = ''
        this.getStore().baseParams.parent = ''
        this.getStore().baseParams.active = 1

        var active = Ext.getCmp('crontabmanager-grid-notifications-active')
        active.setValue(1)

        var parent = Ext.getCmp('crontabmanager-grid-notifications-parent')
        parent.setValue('')

        this.getBottomToolbar().changePage(1)
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'crontabmanager-notification-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh()
                    }, scope: this
                }
            }
        })
        w.reset()
        w.setValues({active: false})
        w.setValues({log_storage_time: CronTabManager.config.log_storage_time})
        w.show(e.target)
    },

    enableItem: function () {
        this.processors.multiple('enable')
    },
    removeItem: function () {
        this.processors.confirm('remove', 'notification_remove')
    },


})
Ext.reg('crontabmanager-grid-notifications', CronTabManager.grid.Notifications)

