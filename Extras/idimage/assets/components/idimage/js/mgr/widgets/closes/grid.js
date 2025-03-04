idimage.grid.Closes = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'idimage-grid-closes';
    }

    config.multiple = 'close'

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description} <br>{message}</p>'),
        getRowClass: function (rec) {
            if (!rec.data.active) {
                return 'idimage-row-disabled'
            }
            return ''
        },
        renderer: function (v, p, record) {
            return record.data.description !== '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;'
        }
    })

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/close/getlist',
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
    idimage.grid.Closes.superclass.constructor.call(this, config);

};
Ext.extend(idimage.grid.Closes, idimage.grid.Default, {

    getFields: function () {
        return [
            'id', 'pid', 'status_service', 'min_scope', 'total', 'status', 'picture', 'upload', 'upload_link', 'tags', 'errors', 'received_at', 'received', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('idimage_close_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('idimage_close_pid'), dataIndex: 'pid', width: 70, sortable: true, renderer: idimage.utils.resourceLink},
            {header: _('idimage_close_status'), dataIndex: 'status', width: 70, sortable: true, renderer: idimage.utils.statusClose},
            {header: _('idimage_close_status_service'), dataIndex: 'status_service', width: 70, sortable: true, renderer: idimage.utils.statusServiceClose},
            {header: _('idimage_close_picture'), dataIndex: 'picture', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_min_scope'), dataIndex: 'min_scope', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_errors'), dataIndex: 'errors', sortable: true, width: 70, hidden: true, renderer: idimage.utils.jsonDataError},
            {header: _('idimage_close_total'), dataIndex: 'total', sortable: true, width: 70},
            //{header: _('idimage_close_tags'), dataIndex: 'tags', sortable: true, width: 150, renderer: idimage.utils.jsonDataTags},
            {header: _('idimage_close_received'), dataIndex: 'received', sortable: true, width: 75, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_close_received_at'), dataIndex: 'received_at', sortable: true, width: 75, renderer: idimage.utils.formatDate},
            {header: _('idimage_close_createdon'), dataIndex: 'createdon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_close_updatedon'), dataIndex: 'updatedon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_close_active'), dataIndex: 'active', width: 75, renderer: idimage.utils.renderBoolean, hidden: true},
            /*{
                header: _('idimage_grid_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 50,
                renderer: idimage.utils.renderActions
            }*/
        ];
    },


    getTopBar: function (config) {
        return [


            {
                text: '<i class="icon icon-cogs"></i> ' + _('idimage_actions_dropdown'),
                cls: 'primary-button',
                menu: [

                    this.actionMenu('image/status/processing', 'icon-refresh'),
                    this.actionMenu('image/status/queue', 'icon-refresh'),

                    '-',
                    this.actionMenu('image/queue/delete', 'icon-refresh'),
                    '-',
                    this.actionMenu('image/destroy', 'icon-trash action-red'),
                ]
            },


            /*{
                xtype: 'idimage-combo-filter-active',
                name: 'received',
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
            },*/
            {
                xtype: 'idimage-combo-status',
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
                xtype: 'idimage-combo-status-service',
                name: 'status_service',
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
            this.widgetTotal(config.id), this.getSearchField()
        ];
    },


    updateClose: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/close/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'idimage-close-window-update',
                            id: Ext.id(),
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(r.object);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },

    removeClose: function () {
        this.action('remove')
    },
    disableClose: function () {
        this.action('disable')
    },
    enableClose: function () {
        this.action('enable')
    },

});
Ext.reg('idimage-grid-closes', idimage.grid.Closes);
