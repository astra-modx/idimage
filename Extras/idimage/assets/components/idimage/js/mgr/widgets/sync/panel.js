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
                layout: 'column',
                border: false,
                anchor: '100%',
                cls: 'main-wrapper',
                labelAlign: 'top',
                buttonAlign: 'left',
                style: 'padding: 0 15px 0 0px;',
                items: [

                    {
                        columnWidth: 0.8,
                        layout: 'form',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0', padding: '0 20px 0 0'},
                        items: [

                            {
                                html: _('idimage_intro_msg'),
                                cls: 'panel-desc',
                                style: 'padding: 0 20px 0 20px; margin-top: 0px;',
                            }, {
                                xtype: 'idimage-grid-closes',
                                cls: 'main-wrapper',
                                style: 'padding: 15px 20px 0 0px',
                            },
                            /*
                            {
                                layout: 'column',
                                items: [
                                    {
                                        columnWidth: 0.4,
                                        layout: 'form',
                                        border: false,
                                        style: {margin: '0'},
                                        items: [

                                            {
                                                cls: ' panel-desc',
                                                html: _('idimage_manual_desc')
                                            },

                                        ]
                                    },
                                    {
                                        columnWidth: 0.4,
                                        layout: 'form',
                                        border: false,
                                        items: [
                                            {
                                                cls: ' panel-desc',
                                                html: idimage.config.snippet
                                            }
                                        ]

                                    },

                                ]
                            },*/


                        ]
                    },
                    {
                        columnWidth: 0.2,
                        layout: 'form',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0'},
                        items: [
                            {
                                html: String.format(
                                    idimage.config.stat
                                ),
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

Ext.extend(idimage.panel.Sync, MODx.FormPanel, {

    onUpdateNeed: function (cb) {
        var updateKey = Ext.getCmp('ms-utilities-import-key')
        if (cb.getValue()) {
            updateKey.show()
        } else {
            updateKey.hide()
        }
    },

    saveConfig: function () {
        var form = this.getForm()
        var values = form.getValues()

        MODx.Ajax.request({
            url: idimage.config['connector_url'],
            params: {
                action: 'mgr/utilities/import/saveconfig',
                fields: values.fields,
                delimiter: values.delimiter
            },
            listeners: {
                success: {
                    fn: function (r) {
                        MODx.msg.status({
                            title: _('ms2_utilities_import_save_fields_title'),
                            message: _('ms2_utilities_import_save_fields_message'),
                            delay: 7
                        })
                    }, scope: this
                }
            }
        })

    },


})
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

function apiGetEmbedding() {
    actionsProgress('api/embedding')
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


function indexedPoll(wait) {
    idImageState(wait);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';

    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));

    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}
