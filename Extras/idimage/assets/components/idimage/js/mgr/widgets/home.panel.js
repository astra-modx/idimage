idimage.panel.Home = function (config) {
    config = config || {}


    var tabs = [

        {
            title: _('idimage_sync'),
            layout: 'anchor',
            items: [
                {
                    html: _('idimage_sync_gallery_intro'),
                    bodyCssClass: 'panel-desc',
                }
                , {
                    xtype: 'idimage-panel-sync',
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
                    xtype: 'idimage-grid-tasks',
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

                        document.getElementById('idimage-panel-sync-stat').innerHTML = r.object.tpl;

                        var rec = r.object;
                        var stat = rec.stat;

                        var object = {
                            status: '<span class="idimage-status idimage-status-color-' + rec.status + '">' + rec.status + '</span>',
                            total: stat.total + ' шт.',
                            total_similar: stat.total_similar + ' шт.',
                            total_completed: stat.total_completed + ' шт.',
                            total_error: stat.total_error + ' шт.',
                            enable: idimage.utils.renderBoolean(rec.enable),
                            balance: idimage.utils.formatPrice(rec.balance),
                        };

                        form.setValues(object)


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

