CronTabManager.grid.AutoPause = function (config) {
    config = config || {}
    if (!config.id) {
        config.id = 'crontabmanager-grid-tasks-autopauses'
        config.namegrid = 'tasks-autopauses'
        config.processor = 'mgr/task/autopause/'
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/task/autopause/getlist',
            task_id: config.record ? config.record.object.id : 'autopause',
            type: 'task',
            dir: 'DESC',
            combo: 1
        },
        url: CronTabManager.config.connector_url,
        cls: 'crontabmanager-grid',
        multi_select: true,
        stateful: true,
        stateId: config.id,
        pageSize: 5,
    })
    CronTabManager.grid.AutoPause.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.grid.AutoPause, CronTabManager.grid.Default, {

    getFields: function () {
        return ['id', 'task_id', 'when', 'from', 'to', 'createdon', 'updatedon', 'active', 'actions']
    },

    getColumns: function () {
        return [
            {header: _('crontabmanager_task_autopause_id'), dataIndex: 'id', width: 20, sortable: true, hidden: true},
            {header: _('crontabmanager_task_autopause_task_id'), dataIndex: 'task_id', sortable: true, width: 100},
            {header: _('crontabmanager_task_autopause_when'), dataIndex: 'when', sortable: true, width: 50},
            {header: _('crontabmanager_task_autopause_from'), dataIndex: 'from', sortable: true, width: 50},
            {header: _('crontabmanager_task_autopause_to'), dataIndex: 'to', sortable: true, width: 50},
            {header: _('crontabmanager_task_autopause_active'), dataIndex: 'active', sortable: true, width: 50, renderer: this._renderBoolean},
            {header: _('crontabmanager_task_autopause_createdon'), dataIndex: 'createdon', sortable: true, width: 70, hidden: true, renderer: CronTabManager.utils.formatDate},
            {header: _('crontabmanager_task_autopause_updatedon'), dataIndex: 'updatedon', sortable: true, width: 70, hidden: true, renderer: CronTabManager.utils.formatDate},
        ]
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('crontabmanager_task_autopause_create'),
            handler: this.createItem,
            scope: this
        }, '->', this.getSearchField()]
    },
    _renderBoolean: function (value, cell, row) {
        var color, text
        if (value == 0 || value == false || value == undefined) {
            color = 'red'
            text = _('no')
        } else {
            color = 'green'
            text = _('yes')
        }
        return String.format('<span class="{0}">{1}</span>', color, text)
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'crontabmanager-autopause-window-create',
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

        console.log(this.config.record.object.id)
        w.setValues({active: true, task_id: this.config.record.object.id})
        w.setValues({log_storage_time: CronTabManager.config.log_storage_time})
        w.show(e.target)
    },
    updateItem: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data
        } else if (!this.menu.record) {
            return false
        }
        var id = this.menu.record.id

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/task/autopause/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'crontabmanager-autopause-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh()
                                    }, scope: this
                                }
                            }
                        })
                        w.reset()
                        w.setValues(r.object)
                        w.show(e.target)
                    }, scope: this
                }
            }
        })
    },
    enableItem: function () {
        this.processors.multiple('enable')
    },
    removeItem: function () {
        this.processors.confirm('remove', 'task_autopause_remove')
    },
    disableItem: function (grid, row, e) {
        this.processors.multiple('disable')
    },
})
Ext.reg('crontabmanager-grid-tasks-autopauses', CronTabManager.grid.AutoPause)
