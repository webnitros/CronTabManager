var CronTabManager = function (config) {
    config = config || {};
    CronTabManager.superclass.constructor.call(this, config);
};
Ext.extend(CronTabManager, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('crontabmanager', CronTabManager);

CronTabManager = new CronTabManager();