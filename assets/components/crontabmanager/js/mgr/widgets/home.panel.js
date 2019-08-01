CronTabManager.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'crontabmanager-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('crontabmanager') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('crontabmanager_tasks'),
                layout: 'anchor',
                items: [{
                    html: _('crontabmanager_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'crontabmanager-grid-tasks',
                    cls: 'main-wrapper',
                }]
            },{
                title: _('crontabmanager_categories'),
                layout: 'anchor',
                items: [{
                    html: _('crontabmanager_categories_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'crontabmanager-grid-categories',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    CronTabManager.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(CronTabManager.panel.Home, MODx.Panel);
Ext.reg('crontabmanager-panel-home', CronTabManager.panel.Home);
