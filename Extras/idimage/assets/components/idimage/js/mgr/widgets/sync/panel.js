idimage.panel.Sync = function (config) {
    config = config || {}

    Ext.apply(config, {
        cls: 'container form-with-labels',
        autoHeight: true,
        url: idimage.config.connector_url,
        progress: true,
        id: 'idimage-panel-sync',
        baseParams: {
            action: 'mgr/poll'
        },
        items: [
            {
                columnWidth: 0.9,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                border: false,
                cls: 'idimage-navbar',
                style: {margin: 'margin: 0px 0 0px 20px', padding: '0 20px 0px 20px'},
                items: [
                    {
                        layout: 'column',
                        items: [

                            /*{
                                columnWidth: 0.3,
                                border: false,
                                style: 'margin: 0px 0 0 20px',
                                items: [
                                    {
                                        html: _('idimage_navbar_statistic_title')
                                    },


                                    {
                                        xtype: 'displayfield',
                                        name: 'total_completed',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },


                                    {
                                        xtype: 'button',
                                        cls: 'primary-button',
                                        style: 'margin: 20px 0 20px 0px',
                                        text: '<i class=" icon icon-refresh"></i> ' + _('idimage_navbar_statistic_btn'),
                                        handler: () => indexedPoll(true)
                                    },

                                ]

                            },
*/
                            {
                                columnWidth: 0.3,
                                border: false,
                                items: [
                                    {
                                        html: _('idimage_navbar_create_product_title'),
                                        cls: 'idimage-navbar-title'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        name: 'total_files',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },
                                    {
                                        xtype: 'displayfield',
                                        name: 'total',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },
                                    {
                                        xtype: 'button',
                                        cls: 'primary-button',
                                        style: 'margin: 20px 0 20px 0px',
                                        text: '<i class=" icon icon-play"></i> ' + _('idimage_navbar_create_product_btn'),
                                        handler: () => productCreation()
                                    },
                                    {
                                        html: _('idimage_navbar_create_product_text'),
                                        cls: 'idimage-navbar-text'
                                    },

                                    {
                                        xtype: 'button',
                                        style: 'margin: 20px 0 20px 0px',
                                        text: '<i class=" icon icon-refresh"></i> ' + _('idimage_navbar_statistic_btn'),
                                        handler: () => indexedPoll(true)
                                    },

                                ]
                            },
                            {
                                columnWidth: 0.3,
                                border: false,
                                style: 'margin: 0px 0 0 20px',
                                items: [
                                    {
                                        html: _('idimage_navbar_embedding_title')
                                    },



                                    {
                                        xtype: 'displayfield',
                                        name: 'total_tasks',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },

                                    {
                                        xtype: 'displayfield',
                                        name: 'total_tasks_pending',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },
                                    {
                                        xtype: 'displayfield',
                                        name: 'total_tasks_completed',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },


                                    {
                                        xtype: 'button',
                                        cls: 'primary-button',
                                        style: 'margin: 20px 0 20px 0px',
                                        text: '<i class=" icon icon-refresh"></i> ' + _('idimage_navbar_embedding_btn'),
                                        handler: () => taskCreation()
                                    },
                                    {
                                        xtype: 'button',
                                        cls: 'primary-button',
                                        style: 'margin: 20px 0 20px 20px',
                                        text: '<i class=" icon icon-refresh"></i> ' + _('idimage_navbar_embedding_pull'),
                                        handler: () => taskPoll()
                                    },
                                    {
                                        html: _('idimage_navbar_embedding_text'),
                                        cls: 'idimage-navbar-text'
                                    },

                                ]

                            },

                            {
                                columnWidth: 0.3,
                                border: false,
                                style: 'margin: 0px 0 0 20px',
                                items: [

                                    {
                                        html: _('idimage_navbar_indexed_title'),
                                        cls: 'idimage-navbar-title'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        name: 'total_embedding',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },
                                    {
                                        xtype: 'displayfield',
                                        name: 'total_error',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },
                                    {
                                        xtype: 'displayfield',
                                        readOnly: true,
                                        name: 'total_similar',
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },
                                    {
                                        xtype: 'button',
                                        cls: 'primary-button',
                                        style: 'margin: 20px 0 20px 0px',
                                        text: '<i class=" icon icon-play"></i> ' + _('idimage_navbar_indexed_btn'),
                                        handler: () => indexedProducts()
                                    },
                                    {
                                        html: _('idimage_navbar_indexed_text'),
                                        cls: 'idimage-navbar-text'
                                    },
                                ]

                            },


                        ]
                    },

                ]
            },

        ],
        listeners: {
            success: {
                fn: function (response) {
                    const data = response.result
                    const alert = data.success === true ? _('success') : _('error')
                    MODx.msg.alert(alert, data.message)
                }, scope: this
            },
            failure: {
                fn: function (response) {
                }, scope: this
            },
            afterrender: {
                fn: function (response) {
                    indexedPoll()
                },
                scope: this
            },
        }
    })

    idimage.panel.Sync.superclass.constructor.call(this, config)

}

Ext.extend(idimage.panel.Sync, MODx.FormPanel, {})
Ext.reg('idimage-panel-sync', idimage.panel.Sync)


function gridDefault() {
    return Ext.getCmp('idimage-grid-closes')
}

function actionsProgress(processor) {
    gridDefault().actionsProgress(processor)
}

function productCreation() {
    this.gridDefault().assignSelected()
    //actionsProgress('image/creation')
}

function taskCreation() {
    actionsProgress('task/creation')
}

function apiBalance() {
    var progress = Ext.MessageBox.wait('', _('please_wait'))

    MODx.Ajax.request({
        url: idimage.config.connectorUrl,
        params: {
            action: 'mgr/balance',
        },
        listeners: {
            success: {
                fn: function (r) {
                    progress.hide()
                    if (r.success) {
                        MODx.msg.alert(_('success'), _('idimage_balance_text') + idimage.utils.formatPrice(r.object.balance))
                    }

                }, scope: this
            },
            failure: {
                fn: function (r) {
                    progress.hide()
                    MODx.msg.status({
                        title: _('error')
                        , message: r.message
                    })
                }, scope: this
            }
        }
    })

}

function indexedProducts() {
    actionsProgress('indexed/products')
}

function taskPoll() {
    actionsProgress('api/task/poll')
}


function indexedPoll(wait) {
    idImageState(wait);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';

    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));

    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}
