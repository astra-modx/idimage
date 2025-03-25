idimage.panel.Indexed = function (config) {
    config = config || {}

    Ext.apply(config, {
        cls: 'container form-with-labels',
        autoHeight: true,
        url: idimage.config.connector_url,
        progress: true,
        id: 'idimage-panel-indexed',
        items: [

            {
                border: false,
                cls: 'main-wrapper',
                style: 'padding: 0 15px 0 20px;',
                items: [

                    {
                        xtype: 'button',
                        style: 'margin: 20px 0 20px 0px',
                        text: _('idimage_navbar_indexed_btn'),
                        handler: () => indexedProducts()
                    },
                    {
                        html: ''
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
    idimage.panel.Indexed.superclass.constructor.call(this, config)

}

Ext.extend(idimage.panel.Indexed, MODx.FormPanel, {})
Ext.reg('idimage-panel-indexed', idimage.panel.Indexed)

