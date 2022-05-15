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
            },{
                title: _('crontabmanager_notifications'),
                layout: 'anchor',
                items: [{
                    html: _('crontabmanager_notifications_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'crontabmanager-grid-notifications',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    CronTabManager.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(CronTabManager.panel.Home, MODx.Panel);
Ext.reg('crontabmanager-panel-home', CronTabManager.panel.Home);



Ext.onReady(function () {

    if (CronTabManager.config.help_buttons.length > 0) {



        CronTabManager.buttons.help = function (config) {
            config = config || {}
            for (var i = 0; i < CronTabManager.config.help_buttons.length; i++) {
                if (!CronTabManager.config.help_buttons.hasOwnProperty(i)) {
                    continue
                }
                CronTabManager.config.help_buttons[i]['handler'] = this.loadPaneURl
            }
            Ext.applyIf(config, {
                buttons: CronTabManager.config.help_buttons
            })
            CronTabManager.buttons.help.superclass.constructor.call(this, config)
        }

        Ext.extend(CronTabManager.buttons.help, MODx.toolbar.ActionButtons, {
            loadPaneURl: function (b) {
                var url = b.url;
                var text = b.text;
                if (!url || !url.length) { return false }
                if (url.substring(0, 4) !== 'http') {
                    url = MODx.config.base_help_url + url
                }
                MODx.helpWindow = new Ext.Window({
                    title: text
                    , width: 850
                    , height: 350
                    , resizable: true
                    , maximizable: true
                    , modal: false
                    , layout: 'fit'
                    , bodyStyle: 'padding: 0;'
                    , items: [{
                        xtype: 'container',
                        layout: {
                            type: 'vbox',
                            align: 'stretch'
                        },
                        width: '100%',
                        height: '100%',
                        items: [{
                            autoEl: {
                                tag: 'iframe',
                                src: url,
                                width: '100%',
                                height: '100%',
                                frameBorder: 0
                            }
                        }]
                    }]
                    //,html: '<iframe src="' + url + '" width="100%" height="100%" frameborder="0"></iframe>'
                })
                MODx.helpWindow.show(b)
                return true
            }
        })

        Ext.reg('crontabmanager-buttons-help', CronTabManager.buttons.help)
        MODx.add('crontabmanager-buttons-help')
    }
})
