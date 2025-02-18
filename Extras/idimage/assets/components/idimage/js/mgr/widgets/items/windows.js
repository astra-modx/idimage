idimage.window.CreateItem = function (config) {
    config = config || {}
    config.url = idimage.config.connector_url

    Ext.applyIf(config, {
        title: _('idimage_item_create'),
        width: 600,
        cls: 'idimage_windows',
        baseParams: {
            action: 'mgr/item/create',
            resource_id: config.resource_id
        }
    })
    idimage.window.CreateItem.superclass.constructor.call(this, config)

    this.on('success', function (data) {
        if (data.a.result.object) {
            // Авто запуск при создании новой подписик
            if (data.a.result.object.mode) {
                if (data.a.result.object.mode === 'new') {
                    var grid = Ext.getCmp('idimage-grid-items')
                    grid.updateItem(grid, '', {data: data.a.result.object})
                }
            }
        }
    }, this)
}
Ext.extend(idimage.window.CreateItem, idimage.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_item_name'),
                name: 'name',
                id: config.id + '-name',
                anchor: '99%',
                allowBlank: false,
            }, {
                xtype: 'textarea',
                fieldLabel: _('idimage_item_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            },  {
                xtype: 'idimage-combo-filter-resource',
                fieldLabel: _('idimage_item_resource_id'),
                name: 'resource_id',
                id: config.id + '-resource_id',
                height: 150,
                anchor: '99%'
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('idimage_item_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('idimage-item-window-create', idimage.window.CreateItem)

idimage.window.UpdateItem = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('idimage_item_update'),
        baseParams: {
            action: 'mgr/item/update',
            resource_id: config.resource_id
        },
    })
    idimage.window.UpdateItem.superclass.constructor.call(this, config)
}
Ext.extend(idimage.window.UpdateItem, idimage.window.CreateItem)
Ext.reg('idimage-item-window-update', idimage.window.UpdateItem)