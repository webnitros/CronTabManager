CronTabManager.grid.Tasks = function (config) {
    config = config || {}
    if (!config.id) {
        config.id = 'crontabmanager-grid-tasks'
        config.namegrid = 'tasks'
        config.processor = 'mgr/task/'
    }

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description} <br>{message}</p>'),
        renderer: function (v, p, record) {return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;'}
    })

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/task/getlist',
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
    CronTabManager.grid.Tasks.superclass.constructor.call(this, config)
}
Ext.extend(CronTabManager.grid.Tasks, CronTabManager.grid.Default, {

    getFields: function (config) {
        return ['id', 'description', 'message', 'createdon', 'completed', 'updatedon', 'add_output_email', 'mode_develop', 'status', 'is_blocked_time', 'is_blocked', 'max_number_attempts', 'parent', 'time', 'path_task', 'last_run', 'category_name', 'end_run', 'active', 'actions']
    },

    getColumns: function (config) {

        return [this.exp, {
            header: _('crontabmanager_task_id'),
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
        }, {
            header: _('crontabmanager_task_time'),
            dataIndex: 'time',
            sortable: true,
            width: 70,
        }, {
            header: _('crontabmanager_task_createdon'),
            dataIndex: 'createdon',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.formatDate,
            hidden: true
        }, {
            header: _('crontabmanager_task_updatedon'),
            dataIndex: 'updatedon',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.formatDate,
            hidden: true
        }, {
            header: _('crontabmanager_task_last_run'),
            dataIndex: 'last_run',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.formatDate,
        }, {
            header: _('crontabmanager_task_end_run'),
            dataIndex: 'end_run',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.formatDate,
        }, {
            header: _('crontabmanager_task_completed'),
            dataIndex: 'completed',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.renderBoolean,
        }, {
            header: _('crontabmanager_task_add_output_email'),
            dataIndex: 'add_output_email',
            sortable: true,
            width: 70,
            renderer: CronTabManager.utils.renderBoolean,
        }, {
            header: _('crontabmanager_task_max_number_attempts'),
            dataIndex: 'max_number_attempts',
            sortable: true,
            width: 60,
            hidden: true
        }, {
            header: _('crontabmanager_task_active'),
            dataIndex: 'active',
            renderer: CronTabManager.utils.renderBoolean,
            sortable: true,
            width: 60,
        }, {
            header: _('crontabmanager_task_is_blocked'),
            dataIndex: 'is_blocked',
            renderer: CronTabManager.utils.renderBoolean,
            sortable: true,
            width: 60,
        }, {
            header: _('crontabmanager_task_mode_develop'),
            dataIndex: 'mode_develop',
            renderer: CronTabManager.utils.renderBoolean,
            sortable: true,
            width: 60,
            hidden: true
        }, {
            header: _('crontabmanager_grid_actions'),
            dataIndex: 'actions',
            renderer: CronTabManager.utils.renderActions,
            sortable: false,
            width: 100,
            id: 'actions'
        }]
    },

    getTopBar: function (config) {
        return [
            {
                text: '<i class="icon icon-cogs"></i> Действия',
                menu: [
                    {
                        tooltip: _('crontabmanager_task_create'),
                        text: '<i class="icon icon-plus"></i>&nbsp;' + _('crontabmanager_task_create'),
                        handler: this.createItem,
                        scope: this
                    }, {
                        text: '<i class="icon icon-plus"></i>&nbsp;' + _('crontabmanager_task_manualstop'),
                        handler: this.manualStopTask,
                        scope: this
                    },
                ]
            },

            {
                xtype: 'crontabmanager-combo-parent',
                id: config.id + '-parent',
                emptyText: _('crontabmanager_task_parent'),
                name: 'parent',
                width: 200,
                listeners: {
                    select: {fn: this.fireParent, scope: this}
                }
            }, {
                text: '<i class="icon icon-eye"></i>&nbsp;' + _('crontabmanager_show_crontabs') + ' <small>(' + _('crontabmanager_time_server') + ': ' + CronTabManager.config.time_server + ')</small>',
                handler: this.ShowCrontabs,
                scope: this,
            }, '->', {
                xtype: 'xcheckbox',
                name: 'active',
                id: config.id + '-active',
                width: 130,
                boxLabel: _('crontabmanager_task_filter_active'),
                ctCls: 'tbar-checkbox',
                checked: true,
                listeners: {
                    check: {fn: this.activeFilter, scope: this}
                }
            }/*, {
            xtype: 'xcheckbox',
            name: 'completed',
            id: config.id + '-completed',
            width: 150,
            boxLabel: _('crontabmanager_task_filter_completed'),
            ctCls: 'tbar-checkbox',
            checked: false,
            listeners: {
                check: {fn: this.completedFilter, scope: this}
            }
        }*/, this.getSearchField()]
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex)
                this.updateItem(grid, e, row)
            },
        }
    },

    activeFilter: function (checkbox, checked) {
        var s = this.getStore()
        s.baseParams.active = checked ? 1 : 0
        this.getBottomToolbar().changePage(1)
    },

    completedFilter: function (checkbox, checked) {
        var s = this.getStore()
        s.baseParams.completed = checked ? 1 : 0
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
        this.getStore().baseParams.completed = 0

        var active = Ext.getCmp('crontabmanager-grid-tasks-active')
        active.setValue(1)

        var completed = Ext.getCmp('crontabmanager-grid-tasks-completed')
        completed.setValue(0)

        var parent = Ext.getCmp('crontabmanager-grid-tasks-parent')
        parent.setValue('')

        this.getBottomToolbar().changePage(1)
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'crontabmanager-task-window-create',
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
                action: 'mgr/task/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'crontabmanager-task-window-update',
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
        this.processors.confirm('remove', 'task_remove')
    },
    disableItem: function (grid, row, e) {
        this.processors.multiple('disable')
    },
    unlockTask: function (act, btn, e) {
        this.processors.confirm('unlock', 'task_unlock')
    },
    unblockupTask: function (act, btn, e) {
        this.processors.confirm('unblockup', 'task_unblockup', {multiple: false})
    },

    readLog: function (act, btn, e) {
        if (this.win !== null) {
            this.win.destroy()
        }
        this.win = new Ext.Window({
            id: this.config.id + 'readlog'
            , title: 'Task crontab: ' + this.menu.record.path_task
            , width: 900
            , height: 550
            , layout: 'fit'
            , autoLoad: {
                url: CronTabManager.config['connector_url'] + '?action=mgr/task/readlog&id=' + this.menu.record.id,
                scripts: true
            }
        })
        this.win.show()
    },

    removeLog: function (act, btn, e) {
        this.processors.confirm('removelog', 'task_removelog', {multiple: false})
    },

    manualStopTask: function (act, btn, e) {
        this.processors.confirm('manualstop', 'task_manualstop', {multiple: false})
    },

    win: null,
    runTask: function (act, btn, e) {
        this.runTaskWindow()
    },
    runTaskWindow: function () {
        if (this.win !== null) {
            this.win.destroy()
        }
        this.elementLog = false
        this.win = new Ext.Window({
            id: this.config.id + 'runtask'
            , title: this.menu.record.path_task
            , width: 700
            , height: 450
            , layout: 'fit'
            , autoLoad: {
                url: CronTabManager.config['connector_cron_url'] + '?path_task=' + this.menu.record.path_task + '&scheduler_path=' + CronTabManager.config.schedulerPath + '&connector_base_path_url=' + CronTabManager.config.schedulerPath,
                scripts: true
            }
        })
        this.win.show()

    },

    ShowCrontabs: function () {
        if (this.win !== null) {
            this.win.destroy()
        }
        this.win = new Ext.Window({
            id: this.config.id + 'showcrontabs'
            , title: _('crontabmanager_show_crontabs')
            , width: 1100
            , height: 450
            , layout: 'fit'
            , autoLoad: {
                url: CronTabManager.config['connector_url'] + '?action=mgr/showcrontabs',
                scripts: true
            }
        })
        this.win.show()
    },

    readLogFile: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data
        } else if (!this.menu.record) {
            return false
        }
        this.readLogFileBody(this.menu.record)
    },

    elementLog:  false,
    readLogFileBody: function (record) {

        if (!this.elementLog) {
            var $win = this.win;
            var wrapper = document.createElement("div")
            wrapper.setAttribute("id", 'crontabmanager_area_reading')
            $win.body.dom.appendChild(wrapper)


            this.elementLog = true
        }

        //<div class="loading-indicator">Loading...</div>
        this.setLogFile('<div class="loading-indicator">Loading...</div>')


        MODx.Ajax.request({
            url: CronTabManager.config['connector_url'],
            params: {
                action: 'mgr/task/readlog',
                id: record.id,
                return: true,
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var log = r.object.yesLog ? r.object.content : r.message
                        this.setLogFile(log)
                    }, scope: this
                }
            }
        })

    },

    setLogFile: function (content){
        var area = document.getElementById('crontabmanager_area_reading')
        area.innerHTML = '<hr>'+content
    }

})
Ext.reg('crontabmanager-grid-tasks', CronTabManager.grid.Tasks)

function runTaskWindow () {
    var Tasks = Ext.getCmp('crontabmanager-grid-tasks')
    Tasks.runTaskWindow()
}

function unlockTask () {
    var Tasks = Ext.getCmp('crontabmanager-grid-tasks')
    Tasks.processors.confirm('unlock', 'task_unlock')
}

function readLogFileBody () {
    var Tasks = Ext.getCmp('crontabmanager-grid-tasks')
    Tasks.readLogFile()
}
