myapp.window.CreateItem = function (config) {
    config = config || {}
    config.url = myapp.config.connector_url

    Ext.applyIf(config, {
        title: _('myapp_item_create'),
        width: 600,
        cls: 'myapp_windows',
        baseParams: {
            action: 'mgr/item/create',
            resource_id: config.resource_id
        }
    })
    myapp.window.CreateItem.superclass.constructor.call(this, config)

    this.on('success', function (data) {
        if (data.a.result.object) {
            // Авто запуск при создании новой подписик
            if (data.a.result.object.mode) {
                if (data.a.result.object.mode === 'new') {
                    var grid = Ext.getCmp('myapp-grid-items')
                    grid.updateItem(grid, '', {data: data.a.result.object})
                }
            }
        }
    }, this)
}
Ext.extend(myapp.window.CreateItem, myapp.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('myapp_item_name'),
                name: 'name',
                id: config.id + '-name',
                anchor: '99%',
                allowBlank: false,
            }, {
                xtype: 'textarea',
                fieldLabel: _('myapp_item_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            },  {
                xtype: 'myapp-combo-filter-resource',
                fieldLabel: _('myapp_item_resource_id'),
                name: 'resource_id',
                id: config.id + '-resource_id',
                height: 150,
                anchor: '99%'
            }, {
                xtype: 'xcheckbox',
                boxLabel: _('myapp_item_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('myapp-item-window-create', myapp.window.CreateItem)

myapp.window.UpdateItem = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('myapp_item_update'),
        baseParams: {
            action: 'mgr/item/update',
            resource_id: config.resource_id
        },
    })
    myapp.window.UpdateItem.superclass.constructor.call(this, config)
}
Ext.extend(myapp.window.UpdateItem, myapp.window.CreateItem)
Ext.reg('myapp-item-window-update', myapp.window.UpdateItem)