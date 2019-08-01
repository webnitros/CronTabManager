Ext.namespace('CronTabManager.processors')

CronTabManager.processors = {
    grid: null,
    action: null,
    callback: [],
    params: {},
    paramsRequest: {},

    /* Проверка наличия функции обратного вызова после отправки ajax запроса */
    runCallback: function (response) {
        if (typeof this.callback === 'function') {
            this.callback(response)
        }
        this.callback = null;
        this.paramsRequest = {}
    },
    Ajax: function () {
        MODx.Ajax.request({
            url: CronTabManager.config.connector_url,
            params: this.paramsRequest,
            listeners: this.listeners
        })
    },
    listeners: {
        success: {
            fn: function (response) {
                CronTabManager.processors.runCallback(response)
                CronTabManager.processors.grid.refresh()
            }, scope: this
        },
        failure: {
            fn: function (r) {
                return true
            }
        }
    },
    prepareData: function () {

        var ids = this._getSelectedIds()
        if (!ids) {
            console.error('Could not ids')
            return false;
        }

        var multiple = true
        if (this.params !== undefined) {
            if (this.params['multiple'] !== undefined) {
                multiple = this.params['multiple']
                delete this.params['multiple']
            }
        }

        if (multiple) {
            this.paramsRequest['ids'] = Ext.util.JSON.encode(ids)
            this.paramsRequest['method'] = this.action
            this.action = this.grid.processor + 'multiple'
        } else {
            this.paramsRequest['id'] = ids.join(',')
            this.action = this.grid.processor + this.action
        }

        this.paramsRequest['action'] = this.action
        if (this.params !== undefined && typeof this.params !== 'boolean') {
            for (var name in this.params) {
                this.paramsRequest[name] = this.params[name]
            }
        }

        return true
    },

    setCallback: function (callback) {
        // callback functions
        if (typeof callback !== 'function') {
            callback = null
        }
        this.callback = callback
    },

    /* Отправить запрос на подтверждение действий */
    run: function ($this, action, lexicon, params, callback, type) {
        this.grid = $this
        this.action = action
        this.params = params
        this.utils.onAjax(this.grid.getEl())
        this.setCallback(callback)
        if (this.prepareData()) {

            switch (type) {
                case 'confirm':

                    MODx.msg.confirm({
                        title: _('crontabmanager_' + lexicon),
                        text: _('crontabmanager_' + lexicon + '_confirm'),
                        url: CronTabManager.config.connector_url,
                        params: this.paramsRequest,
                        listeners: this.listeners
                    })

                    break
                case 'multiple':

                    this.Ajax()

                    break
                default:
                    break
            }

        }
    },

    /* Отправить запрос на процессор выполняющий только одно действие */
    one: function ($this, action, btn, e, row) {
        this.Ajax()
    },

    /* Открыть полученное содержимое в окне */
    message: function ($this, action, btn, e, row) {
        if (typeof(row) !== 'undefined') {
            $this.menu.record = row.data
        }
        var id = $this.menu.record.id
        this.utils.onAjax($this.getEl())
        MODx.Ajax.request({
            url: $this.config.url
            , params: {
                action: action,
                id: id
            }
            , listeners: {
                success: {
                    fn: function (r) {
                        var id = r.object.id
                        var subject = r.object.email_subject
                        var output = r.object.output
                        var win = new Ext.Window({
                            id: 'crontabmanager_message_window' + id
                            , title: subject
                            , width: 700
                            , maxWidth: 700
                            , maxHeight: 450
                            , height: 450
                            , layout: 'fit'
                            , html: output,
                        })
                        win.show()
                    }, scope: $this
                }
            }
        })
    },
    _getSelectedIds: function () {
        var ids = []
        var selected = this.grid.getSelectionModel().getSelections()
        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue
            }
            ids.push(selected[i]['id'])
        }
        return ids
    },
    utils: {
        onAjax: function (el) {
            Ext.Ajax.el = el
            Ext.Ajax.on('beforerequest', CronTabManager.processors.utils.beforerequest)
            Ext.Ajax.on('requestcomplete', CronTabManager.processors.utils.requestcomplete)
        },
        beforerequest: function () {
            Ext.Ajax.el.mask(_('loading'), 'x-mask-loading')
        },
        requestcomplete: function () {
            Ext.Ajax.el.unmask()
            Ext.Ajax.el = null
            Ext.Ajax.un('beforerequest', CronTabManager.processors.utils.beforerequest)
            Ext.Ajax.un('requestcomplete', CronTabManager.processors.utils.requestcomplete)
        },
    }
}
CronTabManager.processors.extend = function ($this) {
    Ext.extend($this, MODx.grid.Grid, {
        windows: {}
        , getMenu: function (grid, rowIndex) {
            var row = grid.getStore().getAt(rowIndex)
            var menu = CronTabManager.utils.getMenu(row.data.actions, this)
            this.addContextMenuItem(menu)
        }
        , onClick: function (e) {
            var elem = e.getTarget()
            if (elem.nodeName === 'BUTTON') {
                var row = this.getSelectionModel().getSelected()
                if (typeof(row) !== 'undefined') {
                    var type = elem.getAttribute('type')
                    var processor = elem.getAttribute('data-processor')
                    var action = elem.getAttribute('data-action')

                    if (type === 'menu') {
                        var ri = this.getStore().find('id', row.id)

                        return this._showMenu(this, ri, e)
                    }
                    else {
                        this.menu.record = row.data
                        if (this[type] !== undefined) {
                            return this[type](this, e)
                        } else {
                            return this.processors[processor](action, this, e, row)
                        }
                    }
                }
            }
            return this.processEvent('click', e)
        }
        , processor: function (elem, processor, $this, e, row) {
            this.processors[elem.options.processor](elem.options.action, $this, e, row)
        },
        _doSearch: function (tf, nv, ov) {
            this.getStore().baseParams.query = tf.getValue()
            this.getBottomToolbar().changePage(1)
            this.refresh()

        },
        _clearSearch: function (btn, e) {
            Ext.getCmp(this.config.id + '-search-field').setValue('')
            this.getStore().baseParams.query = ''
            this.getBottomToolbar().changePage(1)
            this.refresh()
        }
    })
}


