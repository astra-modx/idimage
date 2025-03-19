idimage.panel.Home = function (config) {
    config = config || {}


    var tabs = [

        {
            title: _('idimage_tab_products'),
            layout: 'anchor',
            items: [
                {
                    html: _('idimage_tab_products_desc'),
                    bodyCssClass: 'panel-desc',
                }
                , {
                    xtype: 'idimage-grid-closes',
                    cls: 'main-wrapper',
                }
            ]
        },

        {
            title: _('idimage_tab_tasks'),
            layout: 'anchor',
            items: [
                {
                    html: _('idimage_tab_tasks_desc'),
                    bodyCssClass: 'panel-desc',
                }
                , {
                    xtype: 'idimage-grid-tasks',
                    cls: 'main-wrapper',
                }
            ]
        },

        {
            title: _('idimage_tab_help'),
            layout: 'anchor',
            items: [
                {
                    html: _('idimage_tab_help_desc'),
                    bodyCssClass: 'panel-desc',
                }, {
                    xtype: 'idimage-panel-stat',
                    cls: 'main-wrapper',
                },
                {
                    xtype: 'idimage-panel-help',
                    cls: 'main-wrapper',
                }
            ]
        },


    ];

    Ext.apply(config, {
        cls: 'container',
        layout: 'anchor',
        hideMode: 'offsets',
        items: [
            {
                html: '<h2>' + _('idimage') + '</h2>',
                cls: 'modx-page-header',
            },
            {
                xtype: 'idimage-panel-navbar',
                style: {margin: '15px 0'}
            },
            {
                xtype: 'modx-tabs',
                defaults: {border: false, autoHeight: true},
                border: true,
                hideMode: 'offsets',
                stateful: true,
                stateId: 'idimage-panel-home',
                stateEvents: ['tabchange'],
                getState: function () {
                    return {activeTab: this.items.indexOf(this.getActiveTab())}
                },
                items: tabs
            }
        ]
    })
    idimage.panel.Home.superclass.constructor.call(this, config)
}
Ext.extend(idimage.panel.Home, MODx.Panel)
Ext.reg('idimage-panel-home', idimage.panel.Home)


function idImageState(wait) {

    var progress
    if (wait === true) {
        progress = Ext.MessageBox.wait('', _('please_wait'))
    }

    MODx.Ajax.request({
        url: idimage.config.connectorUrl,
        params: {
            action: 'mgr/stat',
        },
        listeners: {
            success: {
                fn: function (r) {
                    if (progress) {
                        progress.hide()
                    }
                    if (r.success) {
                        var navbarForm = Ext.getCmp('idimage-panel-navbar');
                        navbarForm.formFill(r.object);
                    }

                }, scope: this
            },
            failure: {
                fn: function (r) {
                    if (progress) {
                        progress.hide()
                    }
                    MODx.msg.status({
                        title: _('error')
                        , message: r.message
                    })
                }, scope: this
            }
        }
    })
}

