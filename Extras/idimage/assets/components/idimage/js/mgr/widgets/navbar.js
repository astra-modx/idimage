idimage.panel.Navbar = function (config) {
    config = config || {}

    Ext.apply(config, {
        cls: 'container form-with-labels main-wrapper',
        autoHeight: true,
        url: idimage.config.connector_url,
        progress: true,
        id: 'idimage-panel-navbar',
        items: [
            {
                columnWidth: 0.9,
                defaults: {msgTarget: 'under'},
                border: false,
                cls: 'idimage-navbar',
                style: {margin: 'margin: 0px 0 20px 20px', padding: '0 20px 20px 20px'},
                items: [
                    {
                        layout: 'column',
                        items: [
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
                                        xtype: 'displayfield',
                                        readOnly: true,
                                        name: 'total_similar',
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },


                                ]
                            },
                            {
                                columnWidth: 0.3,
                                border: false,
                                style: 'margin: 0px 0 0 20px',
                                items: [
                                    {
                                        html: _('idimage_navbar_tasks_title')
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
                                        xtype: 'displayfield',
                                        name: 'total_tasks_error',
                                        readOnly: true,
                                        value: '---',
                                        width: '99%',
                                        allowBlank: true,
                                    },

                                ]

                            },

                            {
                                columnWidth: 0.2,
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


                                ]

                            },
                            {
                                columnWidth: 0.1,
                                border: false,
                                style: 'margin: 0px 0 0 20px',
                                items: [

                                    {
                                        html: '',
                                        cls: 'idimage-navbar-title'
                                    },

                                    {
                                        xtype: 'button',
                                        style: 'margin: 20px 0 20px 0px',
                                        text: '<i class=" icon icon-refresh"></i> ' + _('idimage_navbar_statistic_btn'),
                                        handler: () => indexedPoll(true)
                                    },
                                    {
                                        xtype: 'button',
                                        cls: 'primary-button',
                                        style: 'margin: 0px 0 20px 0px',
                                        text: _('idimage_navbar_indexed_btn'),
                                        handler: () => indexedProducts()
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

    idimage.panel.Navbar.superclass.constructor.call(this, config)

}

Ext.extend(idimage.panel.Navbar, MODx.FormPanel, {

    formatTotal: function (stat, keys) {
        return keys.reduce((acc, key) => {
            const template = _(`idimage_navbar_${key}`) || '';
            const value = stat[key] !== undefined ? stat[key] : '';
            acc[key] = template ? String.format(template, value) : value;
            return acc;
        }, {});
    },

    formFill: function (data) {

        var navbarForm = Ext.getCmp('idimage-panel-navbar').getForm();

        var stat = data.stat || {}; // Защита от undefined

        var object = {
            ...this.formatTotal(stat, [
                'total',
                'total_similar',
                'total_completed',
                'total_error',
                'total_embedding',
                'total_files',
                'total_tasks',
                'total_tasks_completed',
                'total_tasks_pending',
                'total_tasks_error'
            ]),
            enable: idimage.utils.renderBoolean(data.enable),
            balance: idimage.utils.formatPrice(data.balance),
        };

        navbarForm.setValues(object);
    }
})
Ext.reg('idimage-panel-navbar', idimage.panel.Navbar)


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
