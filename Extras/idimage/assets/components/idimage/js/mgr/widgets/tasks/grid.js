idimage.grid.Tasks = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'idimage-grid-tasks';
    }

    config.multiple = 'task'

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description} <br>{message}</p>'),
        /*  getRowClass: function (rec) {
              if (!rec.data.active) {
                  return 'idimage-row-disabled'
              }
              return ''
          },*/
        renderer: function (v, p, record) {
            return record.data.description !== '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;'
        }
    })

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/task/getlist',
            sort: 'id',
            dir: 'DESC'
        },
        multi_select: true,
        plugins: this.exp,
        stateful: true,
        stateId: config.id,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0,
            getRowClass: function (rec) {
                return !rec.data.active
                    ? 'idimage-grid-row-disabled'
                    : '';
            }
        },
        paging: true,
        remoteSort: true,
        autoHeight: true,
    });
    idimage.grid.Tasks.superclass.constructor.call(this, config);

};
Ext.extend(idimage.grid.Tasks, idimage.grid.Default, {

    getFields: function () {
        return [
            'id', 'operation', 'processing', 'attempt', 'can_be_launched', 'execute_at', 'attempt_failure', 'msg', 'pid', 'error', 'status', 'createdon', 'updatedon', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('id'), dataIndex: 'id', width: 50, sortable: true, hidden: false},
            {header: _('idimage_pid'), dataIndex: 'pid', width: 70, sortable: true},
            {header: _('idimage_operation'), dataIndex: 'operation', width: 70, sortable: true},
            {header: _('idimage_status'), dataIndex: 'status', width: 70, sortable: true, renderer: idimage.utils.statusTask},
            {header: _('idimage_attempt'), dataIndex: 'attempt', width: 70, sortable: true},
            {header: _('idimage_attempt_failure'), dataIndex: 'attempt_failure', width: 70, sortable: true},
            {header: _('idimage_error'), dataIndex: 'error', width: 70, sortable: true, hidden: true},
            {header: _('idimage_execute_at'), dataIndex: 'execute_at', width: 70, sortable: true, hidden: true},

            {header: _('idimage_createdon'), dataIndex: 'createdon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_updatedon'), dataIndex: 'updatedon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {
                header: _('idimage_grid_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 150,
                renderer: idimage.utils.renderActions
            }
        ];
    },


    getTopBar: function (config) {

        return [
            {
                text: '<i class="icon icon-cogs"></i> ' + _('idimage_actions_dropdown'),
                cls: 'primary-button',
                menu: [

                    this.actionMenu('product/task/upload', 'icon-upload'),
                    this.actionMenu('product/task/embedding', 'icon-upload'),
                    this.actionMenu('product/task/indexed', 'icon-refresh'),
                    '-',
                    this.actionMenu('task/destroy', 'icon-trash action-red')
                ]
            },

            this.actionMenu('task/send', 'icon-send'),

            {
                xtype: 'idimage-combo-filter-task-status',
                name: 'status',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    },
                    afterrender: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },

            {
                xtype: 'idimage-combo-filter-task-operation',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: '',
                listeners: {
                    select: {
                        fn: this._filterByCombo,
                        scope: this
                    },
                    afterrender: {
                        fn: this._filterByCombo,
                        scope: this
                    }
                }
            },


            '->',
            this.widgetTotal(config.id),
            this.getSearchField()
        ];
    }
    ,

    removeTask: function () {
        this.action('remove')
    }
    ,
    receivedTask: function () {
        this.action('action/received')
    }
    ,
    pollTask: function () {
        this.action('action/poll')
    }
    ,
    uploadTask: function () {
        this.action('action/upload')
    }
    ,
    disableTask: function () {
        this.action('disable')
    }
    ,
    enableTask: function () {
        this.action('enable')
    }
    ,
    resetAttemptsTask: function () {
        this.action('resetAttempts')
    }
    ,
    sendTask: function () {
        this.action('action/send')
    }
    ,

});
Ext.reg('idimage-grid-tasks', idimage.grid.Tasks);
