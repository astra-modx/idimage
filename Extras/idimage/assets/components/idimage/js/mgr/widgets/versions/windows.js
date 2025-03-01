idimage.window.CreateIndexed = function (config) {
    config = config || {}
    config.url = idimage.config.connector_url

    Ext.applyIf(config, {
        title: _('idimage_indexed_window_create'),
        width: 600,
        cls: 'idimage_windows',
        baseParams: {
            action: 'mgr/indexed/create',
            resource_id: config.resource_id
        }
    })
    idimage.window.CreateIndexed.superclass.constructor.call(this, config)

    this.on('success', function (data) {
        if (data.a.result.object) {
            // Авто запуск при создании новой подписик
            if (data.a.result.object.mode) {
                if (data.a.result.object.mode === 'new') {
                    var grid = Ext.getCmp('idimage-grid-indexeds')
                    grid.updateIndexed(grid, '', {data: data.a.result.object})
                }
            }
        }
    }, this)
}
Ext.extend(idimage.window.CreateIndexed, idimage.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'modx-description', html: _('idimage_indexed_create_description')},
            {
                xtype: 'textfield',
                readOnly: true,
                fieldLabel: _('idimage_indexed_create_images'),
                name: 'images',
                id: config.id + '-images',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                readOnly: true,
                fieldLabel: _('idimage_indexed_create_closes'),
                name: 'closes',
                id: config.id + '-closes',
                anchor: '99%',
                allowBlank: false,
            }
        ]

    }
})
Ext.reg('idimage-indexed-window-create', idimage.window.CreateIndexed)

idimage.window.UpdateIndexed = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('idimage_indexed_window_update'),
        baseParams: {
            action: 'mgr/indexed/update',
            resource_id: config.resource_id
        },
    })
    idimage.window.UpdateIndexed.superclass.constructor.call(this, config)
}
Ext.extend(idimage.window.UpdateIndexed, idimage.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                readOnly: true,
                fieldLabel: _('idimage_indexed_images'),
                name: 'images',
                id: config.id + '-images',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                readOnly: true,
                fieldLabel: _('idimage_indexed_closes'),
                name: 'closes',
                id: config.id + '-closes',
                anchor: '99%',
                allowBlank: false,
            }

            /*  , {
                  xtype: 'xcheckbox',
                  boxLabel: _('idimage_indexed_active'),
                  name: 'active',
                  id: config.id + '-active',
                  checked: true,
              }*/
        ]


    }
})
Ext.reg('idimage-indexed-window-update', idimage.window.UpdateIndexed)
