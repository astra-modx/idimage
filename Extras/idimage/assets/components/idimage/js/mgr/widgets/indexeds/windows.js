idimage.window.CreateIndexed = function (config) {
    config = config || {}
    config.url = idimage.config.connector_url

    Ext.applyIf(config, {
        title: _('idimage_indexed_create'),
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
                    grid.updateItem(grid, '', {data: data.a.result.object})
                }
            }
        }
    }, this)
}
Ext.extend(idimage.window.CreateIndexed, idimage.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_indexed_picture'),
                name: 'picture',
                id: config.id + '-picture',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_indexed_pid'),
                name: 'pid',
                id: config.id + '-pid',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_indexed_hash'),
                name: 'hash',
                id: config.id + '-hash',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_indexed_indexed'),
                name: 'indexed',
                id: config.id + '-indexed',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_indexed_status'),
                name: 'status',
                id: config.id + '-status',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textarea',
                fieldLabel: _('idimage_indexed_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            }
            , {
                xtype: 'idimage-combo-filter-resource',
                fieldLabel: _('idimage_indexed_resource_id'),
                name: 'resource_id',
                id: config.id + '-resource_id',
                height: 150,
                anchor: '99%'
            }
            , {
                xtype: 'xcheckbox',
                boxLabel: _('idimage_indexed_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('idimage-indexed-window-create', idimage.window.CreateIndexed)

idimage.window.UpdateIndexed = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('idimage_indexed_update'),
        baseParams: {
            action: 'mgr/indexed/update',
            resource_id: config.resource_id
        },
    })
    idimage.window.UpdateIndexed.superclass.constructor.call(this, config)
}
Ext.extend(idimage.window.UpdateIndexed, idimage.window.CreateIndexed)
Ext.reg('idimage-indexed-window-update', idimage.window.UpdateIndexed)
