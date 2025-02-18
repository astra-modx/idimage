myapp.panel.Home = function (config) {
    config = config || {}
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',

        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('myapp') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            stateful: true,
            stateId: 'myapp-panel-home',
            stateEvents: ['tabchange'],
            getState: function () {return {activeTab: this.items.indexOf(this.getActiveTab())}},
            items: [{
                title: _('myapp_items'),
                layout: 'anchor',
                items: [{
                    html: _('myapp_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'myapp-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    })
    myapp.panel.Home.superclass.constructor.call(this, config)
}
Ext.extend(myapp.panel.Home, MODx.Panel)
Ext.reg('myapp-panel-home', myapp.panel.Home)

Ext.onReady(function () {
    if (myapp.config.help_buttons.length > 0) {
        myapp.buttons.help = function (config) {
            config = config || {}
            for (var i = 0; i < myapp.config.help_buttons.length; i++) {
                if (!myapp.config.help_buttons.hasOwnProperty(i)) {
                    continue
                }
                myapp.config.help_buttons[i]['handler'] = this.loadPaneURl
            }
            Ext.applyIf(config, {
                buttons: myapp.config.help_buttons
            })
            myapp.buttons.help.superclass.constructor.call(this, config)
        }
        Ext.extend(myapp.buttons.help, MODx.toolbar.ActionButtons, {
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

        Ext.reg('myapp-buttons-help', myapp.buttons.help)
        MODx.add('myapp-buttons-help')
    }
})
