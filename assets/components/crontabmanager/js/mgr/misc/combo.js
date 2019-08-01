CronTabManager.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    CronTabManager.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(CronTabManager.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('crontabmanager-combo-search', CronTabManager.combo.Search);
Ext.reg('crontabmanager-field-search', CronTabManager.combo.Search);


CronTabManager.combo.Parent = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        name: config.name || 'status'
        ,fieldLabel: _('crontabmanager_task_parent_empty')
        ,emptyText: _('crontabmanager_task_parent_empty')
        ,hiddenName: config.name || 'parent'
        ,displayField: 'name'
        ,valueField: 'id'
        ,anchor: '99%'
        ,fields: ['name','id']
        ,pageSize: 20
        ,url: CronTabManager.config.connector_url
        ,typeAhead: true
        ,editable: true
        ,allowBlank: true
        ,baseParams: {
            action: 'mgr/category/getlist'
            ,combo: 1
            ,id: config.value
        }
    });
    CronTabManager.combo.Parent.superclass.constructor.call(this,config);
};
Ext.extend(CronTabManager.combo.Parent,MODx.combo.ComboBox);
Ext.reg('crontabmanager-combo-parent',CronTabManager.combo.Parent);