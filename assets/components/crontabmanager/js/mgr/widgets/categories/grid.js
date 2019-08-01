CronTabManager.grid.Categories = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'crontabmanager-grid-categories';
    }
    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/category/getlist',
        },
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
                  : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    CronTabManager.grid.Categories.superclass.constructor.call(this, config);
};
Ext.extend(CronTabManager.grid.Categories, CronTabManager.grid.Default, {

    getFields: function (config) {
        return ['id', 'name', 'description',  'active', 'actions'];
    },

    getColumns: function (config) {

        return [
            {
            header: _('crontabmanager_category_id'),
            dataIndex: 'id',
            sortable: true,
            width: 40
        }, {
            header: _('crontabmanager_category_name'),
            dataIndex: 'name',
            sortable: true,
            width: 60,
        }, {
            header: _('crontabmanager_category_description'),
            dataIndex: 'description',
            sortable: true,
            width: 60,
        }, {
            header: _('crontabmanager_category_active'),
            dataIndex: 'active',
            renderer: CronTabManager.utils.renderBoolean,
            sortable: true,
            width: 60,
        }, {
            header: _('crontabmanager_grid_actions'),
            dataIndex: 'actions',
            renderer: CronTabManager.utils.renderActions,
            sortable: false,
            width: 140,
            id: 'actions'
        }];

    },

    getTopBar: function (config) {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('crontabmanager_category_create'),
            handler: this.createItem,
            scope: this
        }, '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateItem(grid, e, row);
            },
        };
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'crontabmanager-category-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues({active: true});
        w.show(e.target);
    },

    updateItem: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/category/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'crontabmanager-category-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    removeItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.msg.confirm({
            title: ids.length > 1
              ? _('crontabmanager_categories_remove')
              : _('crontabmanager_category_remove'),
            text: ids.length > 1
              ? _('crontabmanager_categories_remove_confirm')
              : _('crontabmanager_category_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/category/remove',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        return true;
    },

    disableItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/category/disable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },

    enableItem: function () {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/category/enable',
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        })
    },


    activeFilter: function(checkbox, checked) {
        var s = this.getStore();
        s.baseParams.active = checked ? 1 : 0;
        this.getBottomToolbar().changePage(1);
    },

    fireParent: function(checkbox, value) {
        var s = this.getStore();
        s.baseParams.parent = value.id;
        this.getBottomToolbar().changePage(1);
    },

});
Ext.reg('crontabmanager-grid-categories', CronTabManager.grid.Categories);