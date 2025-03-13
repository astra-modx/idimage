idimage.window.CreateTask = function (config) {
    config = config || {}
    config.url = idimage.config.connector_url

    Ext.applyIf(config, {
        title: _('idimage_close_create'),
        width: 600,
        cls: 'idimage_windows',
        baseParams: {
            action: 'mgr/task/create',
            resource_id: config.resource_id
        }
    })
    idimage.window.CreateTask.superclass.constructor.call(this, config)

    this.on('success', function (data) {
        if (data.a.result.object) {
            // Авто запуск при создании новой подписик
            if (data.a.result.object.mode) {
                if (data.a.result.object.mode === 'new') {
                    var grid = Ext.getCmp('idimage-grid-closes')
                    grid.updateItem(grid, '', {data: data.a.result.object})
                }
            }
        }
    }, this)
}
Ext.extend(idimage.window.CreateTask, idimage.window.Default, {

    getFields: function (config) {
        return [
            {xtype: 'hidden', name: 'id', id: config.id + '-id'},
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_close_picture'),
                name: 'picture',
                id: config.id + '-picture',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_close_pid'),
                name: 'pid',
                id: config.id + '-pid',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_close_hash'),
                name: 'hash',
                id: config.id + '-hash',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_close_version'),
                name: 'version',
                id: config.id + '-version',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textfield',
                fieldLabel: _('idimage_close_status'),
                name: 'status',
                id: config.id + '-status',
                anchor: '99%',
                allowBlank: false,
            },
            {
                xtype: 'textarea',
                fieldLabel: _('idimage_close_description'),
                name: 'description',
                id: config.id + '-description',
                height: 150,
                anchor: '99%'
            }
            , {
                xtype: 'idimage-combo-filter-resource',
                fieldLabel: _('idimage_close_resource_id'),
                name: 'resource_id',
                id: config.id + '-resource_id',
                height: 150,
                anchor: '99%'
            }
            , {
                xtype: 'xcheckbox',
                boxLabel: _('idimage_close_active'),
                name: 'active',
                id: config.id + '-active',
                checked: true,
            }
        ]


    }
})
Ext.reg('idimage-task-window-create', idimage.window.CreateTask)

idimage.window.UpdateTask = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        title: _('idimage_close_update'),
        baseParams: {
            action: 'mgr/task/update',
            resource_id: config.resource_id
        },
    })
    idimage.window.UpdateTask.superclass.constructor.call(this, config)
}
Ext.extend(idimage.window.UpdateTask, idimage.window.CreateTask)
Ext.reg('idimage-task-window-update', idimage.window.UpdateTask)
