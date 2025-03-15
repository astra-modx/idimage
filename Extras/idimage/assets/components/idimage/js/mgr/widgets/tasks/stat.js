idimage.panel.Stat = function (config) {
    config = config || {}

    Ext.apply(config, {
        cls: 'container form-with-labels',
        autoHeight: true,
        url: idimage.config.connector_url,
        progress: true,
        id: 'idimage-panel-stat',
        baseParams: {
            action: 'mgr/actions/api/task/stat'
        },
        items: [
            {
                layout: 'column',
                border: false,
                anchor: '100%',
                cls: 'main-wrapper',
                labelAlign: 'top',
                buttonAlign: 'left',
                items: [

                    {
                        columnWidth: 0.8,
                        layout: 'form',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0', padding: '0 20px 0 0'},
                        items: [
                            {
                                layout: 'column',
                                items: [
                                    {
                                        columnWidth: 0.3,
                                        layout: 'form',
                                        border: false,
                                        items: [
                                            {
                                                html: _('idimage_stat_title')
                                            },

                                            {
                                                html: _('idimage_stat_title_intro')
                                            },
                                            {
                                                xtype: 'button',
                                                style: 'margin: 20px 0 20px 0px',
                                                text: _('idimage_button_balance'),
                                                handler: () => apiBalance(true)
                                            },
                                            {
                                                html: ''
                                            },
                                            {
                                                xtype: 'button',
                                                style: 'margin: 10px 0 0 0px',
                                                text: _('idimage_queue_refresh'),
                                                handler: this.submit, scope: this
                                            },

                                        ]
                                    },
                                    {
                                        columnWidth: 0.3,
                                        layout: 'form',
                                        border: false,
                                        style: 'margin: 25px 0 0 20px',
                                        items: [


                                            {
                                                xtype: 'displayfield',
                                                name: 'pending',
                                                readOnly: true,
                                                value: '---',
                                                width: '99%',
                                                allowBlank: true,
                                                fieldLabel: _('idimage_stat_pending'),
                                            },
                                            {
                                                xtype: 'displayfield',
                                                readOnly: true,
                                                name: 'failed',
                                                value: '---',
                                                width: '99%',
                                                allowBlank: true,
                                                fieldLabel: _('idimage_stat_failed'),
                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'completed',
                                                readOnly: true,
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: _('idimage_stat_completed'),
                                                allowBlank: true,
                                            },

                                        ]

                                    },

                                    {
                                        columnWidth: 0.3,
                                        layout: 'form',
                                        border: false,
                                        style: 'margin: 20px 0 0 20px',
                                        items: []

                                    },

                                ]
                            },

                        ]
                    },
                    {
                        columnWidth: 0.2,
                        layout: 'form',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0'},
                        items: []
                    },
                ]
            },
        ],
        listeners: {
            success: {
                fn: function (response) {

                    if (response.result.success) {
                        var form = this.getForm()
                        form.setValues(response.result.object);
                    } else {
                        const data = response.result
                        const alert = data.success === true ? _('success') : _('error')
                        MODx.msg.alert(alert, data.message)
                    }

                }, scope: this
            },
            failure: {
                fn: function (response) {
                }, scope: this
            }
        }
    })
    idimage.panel.Stat.superclass.constructor.call(this, config)
}

Ext.extend(idimage.panel.Stat, MODx.FormPanel, {})
Ext.reg('idimage-panel-stat', idimage.panel.Stat)
