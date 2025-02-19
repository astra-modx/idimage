idimage.grid.Closes = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'idimage-grid-closes';
    }

    if (!config.multiple) {
        config.multiple = 'close'
    }

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/close/getlist',
            sort: 'id',
            dir: 'DESC'
        },
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
            'id', 'pid', 'status_code', 'min_scope', 'version', 'total_close', 'status', 'picture', 'tags', 'received_at', 'received', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('idimage_close_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('idimage_close_pid'), dataIndex: 'pid', width: 70, sortable: true, renderer: idimage.utils.resourceLink},
            {header: _('idimage_close_status'), dataIndex: 'status', width: 70, sortable: true},
            {header: _('idimage_close_picture'), dataIndex: 'picture', sortable: false, width: 70, hidden: true},
            {header: _('idimage_close_min_scope'), dataIndex: 'min_scope', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_total_close'), dataIndex: 'total_close', sortable: true, width: 70},
            {header: _('idimage_close_status_code'), dataIndex: 'status_code', sortable: true, width: 70},
            {header: _('idimage_close_version'), dataIndex: 'version', sortable: true, width: 70},
            {header: _('idimage_close_tags'), dataIndex: 'tags', sortable: true, width: 150},
            {header: _('idimage_close_received'), dataIndex: 'received', sortable: true, width: 75, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_close_received_at'), dataIndex: 'received_at', sortable: true, width: 75, renderer: idimage.utils.formatDate},
            {header: _('idimage_close_createdon'), dataIndex: 'createdon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_close_updatedon'), dataIndex: 'updatedon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_close_active'), dataIndex: 'active', width: 75, renderer: idimage.utils.renderBoolean, hidden: true},
            {
                header: _('idimage_grid_actions'),
                dataIndex: 'actions',
                id: 'actions',
                width: 50,
                renderer: idimage.utils.renderActions
            }
        ];
    },

    getTopBar: function () {
        return [{
            text: '<i class="icon icon-plus"></i>&nbsp;' + _('idimage_close_create'),
            handler: this.createItem,
            scope: this
        }, {
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
        }, {
            xtype: 'idimage-combo-filter-resource',
            name: 'pid',
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
            '->', this.getSearchField()];
    },

    getListeners: function () {
        return {
            rowDblClick: function (grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateItem(grid, e, row);
            },
        };
    },

    createItem: function (btn, e) {
        var w = MODx.load({
            xtype: 'idimage-close-window-create',
            id: Ext.id(),
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues({active: true});
        w.show(e.target);
    },

    updateItem: function (btn, e, row) {
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

    removeItem: function () {
        this.action('remove')
    },
    disableItem: function () {
        this.action('disable')
    },
    enableItem: function () {
        this.action('enable')
    },
});
Ext.reg('idimage-grid-closes', idimage.grid.Closes);
