CronTabManager.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'crontabmanager-panel-home',
            renderTo: 'crontabmanager-panel-home-div'
        }]
    });
    CronTabManager.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(CronTabManager.page.Home, MODx.Component);
Ext.reg('crontabmanager-page-home', CronTabManager.page.Home);