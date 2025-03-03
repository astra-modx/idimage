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
                style: 'padding: 0 0 0 7px',
                items: [

                    {
                        columnWidth: 0.7,
                        layout: 'form',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0'},
                        items: [
                            {
                                html: '<h3>Индекс товаров</h3>',
                            },


                            {
                                layout: 'column',
                                items: [
                                    {
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        border: false,
                                        style: {margin: '0'},
                                        items: [


                                            {
                                                xtype: 'displayfield',
                                                name: 'code',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Идентификатор каталог',
                                            },


                                            {
                                                xtype: 'displayfield',
                                                name: 'active',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Индексация разрешена',
                                            },

                                            {
                                                xtype: 'displayfield',
                                                name: 'upload_api',
                                                hidden: !idimage.config.cloud,
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Загрузка в хранилище разрешена',
                                            },


                                            {
                                                xtype: 'displayfield',
                                                name: 'created_at',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Дата создания каталога',
                                            },

                                            {
                                                cls: ' panel-desc',
                                                width: '80%',
                                                style: 'margin-right:50px',
                                                html: _('idimage_manual_desc')
                                            },
                                            {
                                                cls: ' panel-desc',
                                                style: 'margin-right:50px',
                                                html: idimage.config.snippet
                                            },

                                        ]
                                    },
                                    {
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        border: false,
                                        style: {margin: '8px 0 0 15px'},
                                        items: [


                                            {
                                                xtype: 'displayfield',
                                                name: 'status',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Статус индексации',
                                            },


                                            {
                                                xtype: 'displayfield',
                                                name: 'version',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Версия индекса',
                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'download_link',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Ссылка на скачивание',

                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'size',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Размер файла индекса',
                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'closes',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Товаров с похожими',
                                            },


                                            {
                                                xtype: 'displayfield',
                                                name: 'awaiting_processing',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Товаров ожидающих индексации',
                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'updated_at',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Дата обновления',
                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'start_at',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Дата начала индексации',
                                            },
                                            {
                                                xtype: 'displayfield',
                                                name: 'finished_at',
                                                value: '---',
                                                width: '99%',
                                                fieldLabel: 'Дата завершения индексации',
                                            },
                                            {
                                                xtype: 'button',
                                                style: 'margin: 20px 10px 15px 0px',
                                                text: '<i class="icon icon-refresh"></i> &nbsp;' + _('idimage_indexed_action_poll'),
                                                handler: function () {
                                                    indexedPoll(true)
                                                }, scope: this
                                            },


                                        ]

                                    },

                                ]
                            },


                        ]
                    },
                    {
                        columnWidth: 0.3,
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
            }
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

    checkAvailability: function () {

        checkAvailability()
    }

})
Ext.reg('idimage-panel-sync', idimage.panel.Sync)


function gridDefault() {
    return Ext.getCmp('idimage-grid-closes')
}

function actionsProgress(processor) {
    gridDefault().actionsProgress(processor)
}

function productCreation() {
    actionsProgress('image/creation')
}

function productUpload() {
    actionsProgress('image/upload/cloud')
}


function productQueueAdd() {
    actionsProgress('image/queue/add')
}

function indexedProducts() {
    actionsProgress('indexed/products')
}

function indexedRunning() {
    actionsProgress('indexed/running')
}


function checkAvailability() {

    var progress = Ext.MessageBox.wait('', _('please_wait'))

    MODx.Ajax.request({
        url: idimage.config.connectorUrl,
        params: {
            action: 'mgr/catalog/check',
        },
        listeners: {
            success: {
                fn: function (r) {
                    progress.hide()
                    idImageState()
                    if (r.success) {
                        MODx.msg.status({
                            title: _('success')
                            , message: 'Соединение установлено'
                        })
                    }

                }, scope: this
            },
            failure: {
                fn: function (r) {
                    progress.hide()
                    idImageState()
                    MODx.msg.status({
                        title: _('error')
                        , message: r.message
                    })
                }, scope: this
            }
        }
    })
}

/*
* function indexedPoll() {
    actionsProgress('indexed/poll')
}
* */
function indexedPoll(wait) {

    idImageState();

    var panel = Ext.getCmp('idimage-panel-sync')

    var form = panel.getForm();


    var progress
    if (wait) {
        progress = Ext.MessageBox.wait('', _('please_wait'))
    }

    MODx.Ajax.request({
        url: idimage.config.connectorUrl,
        params: {
            action: 'mgr/poll',
        },
        listeners: {
            success: {
                fn: function (r) {
                    if (progress) {
                        progress.hide()
                    }

                    if (r.success) {
                        var object = r.object;
                        object.download_link = object.download_link ? '<a href="' + object.download_link + '" target="_blank">' + object.download_link + '</a>' : '---'


                        object.status = '<span class="idimage-status idimage-status-color-' + object.status + '">' + object.status + '</span>'

                        object.size = formatFileSize(object.size)
                        object.closes = object.closes + ' шт.'
                        object.awaiting_processing = object.awaiting_processing + ' шт.'
                        object.active = idimage.utils.renderBoolean(object.active)
                        object.sealed = idimage.utils.renderBoolean(object.sealed)
                        object.upload = idimage.utils.renderBoolean(object.upload)
                        object.upload_api = idimage.utils.renderBoolean(object.upload_api)
                        object.created_at = idimage.utils.formatDate(object.created_at)
                        object.updated_at = idimage.utils.formatDate(object.updated_at)
                        object.start_at = '---'
                        object.finished_at = '---'


                        if (object.logs.length > 0) {
                            for (var log of object.logs) {
                                switch (log.status) {
                                    case "running":
                                        object.start_at = idimage.utils.formatDate(log.timestamp)
                                        break;
                                    case "finished":
                                        object.finished_at = idimage.utils.formatDate(log.timestamp)
                                        break;
                                    default:
                                        break;
                                }


                            }
                        }


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

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';

    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));

    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}
