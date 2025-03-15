idimage.panel.Home = function (config) {
    config = config || {}


    var tabs = [

        {
            title: _('idimage_sync'),
            layout: 'anchor',
            items: [
                {
                    html: _('idimage_desc_sync'),
                    bodyCssClass: 'panel-desc',
                }
                , {
                    xtype: 'idimage-panel-sync',
                    cls: 'main-wrapper',
                },
                {
                    html: _('idimage_desc_products'),
                    cls: 'panel-desc',
                }
                , {
                    xtype: 'idimage-grid-closes',
                    cls: 'main-wrapper',
                }
            ]
        },

        {
            title: _('idimage_tasks'),
            layout: 'anchor',
            items: [
                {
                    html: _('idimage_tasks_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'idimage-panel-stat',
                    cls: 'main-wrapper',
                }, {
                    xtype: 'idimage-grid-tasks',
                    cls: 'main-wrapper',
                }
            ]
        },

        {
            title: _('idimage_help'),
            layout: 'anchor',
            items: [
                {
                    xtype: 'idimage-panel-help',
                    cls: 'main-wrapper',
                }
            ]
        }
        /* {
             title: _('idimage_closes'),
             layout: 'anchor',
             items: [
                 {
                     html: _('idimage_intro_msg'),
                     cls: 'panel-desc',
                 }, {
                     xtype: 'idimage-grid-closes',
                     cls: 'main-wrapper',
                 }
             ]
         }*/
    ];

    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',

        hideMode: 'offsets',


        items: [{
            html: '<h2>' + _('idimage') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
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
        }]
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

    var panel = Ext.getCmp('idimage-panel-sync')

    var form = panel.getForm();

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


                        function formatTotal(stat, keys) {
                            return keys.reduce((acc, key) => {
                                const template = _(`idimage_navbar_${key}`) || '';
                                const value = stat[key] !== undefined ? stat[key] : '';
                                acc[key] = template ? String.format(template, value) : value;
                                return acc;
                            }, {});
                        }

                        var rec = r.object;
                        var stat = rec.stat || {}; // Защита от undefined

                        var object = {
                            ...formatTotal(stat, [
                                'total',
                                'total_similar',
                                'total_completed',
                                'total_error',
                                'total_embedding',
                                'total_files',
                                'total_tasks',
                                'total_tasks_completed',
                                'total_tasks_pending'
                            ]),
                            enable: idimage.utils.renderBoolean(rec.enable),
                            balance: idimage.utils.formatPrice(rec.balance),
                        };

                        form.setValues(object);


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

