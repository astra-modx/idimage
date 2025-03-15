idimage.panel.Help = function (config) {
    config = config || {}

    Ext.apply(config, {
        cls: 'container form-with-labels',
        autoHeight: true,
        url: idimage.config.connector_url,
        progress: true,
        id: 'idimage-panel-help',
        items: [

            {
                border: false,
                cls: 'main-wrapper',
                style: 'padding: 0 15px 0 20px;',
                items: [
                    {
                        html: String.format(
                            idimage.config.stat
                        ),
                    },
                    {
                        html: String.format(
                            idimage.config.snippet
                        ),
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
    idimage.panel.Help.superclass.constructor.call(this, config)

}

Ext.extend(idimage.panel.Help, MODx.FormPanel, {})
Ext.reg('idimage-panel-help', idimage.panel.Help)

