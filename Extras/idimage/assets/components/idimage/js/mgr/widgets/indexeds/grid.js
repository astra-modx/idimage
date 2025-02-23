idimage.grid.Indexeds = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'idimage-grid-indexeds';
    }

    config.multiple = 'indexed'

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
            action: 'mgr/indexed/getlist',
            sort: 'id',
            dir: 'DESC'
        },
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
    idimage.grid.Indexeds.superclass.constructor.call(this, config);

};
Ext.extend(idimage.grid.Indexeds, idimage.grid.Default, {

    getFields: function () {
        return [
            'id', 'version', 'cloud_upload', 'cloud_size', 'current_version', 'images', 'closes', 'completed', 'sealed', 'use_version', 'start_at', 'finished_at', 'upload_at', 'upload', 'processed', 'status_code', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('idimage_indexed_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('idimage_indexed_version'), dataIndex: 'version', width: 20, sortable: true},
            {
                header: _('idimage_indexed_cloud_upload'),
                dataIndex: 'cloud_upload',
                sortable: true,
                width: 70,
                hidden: true,
                renderer: idimage.utils.renderBoolean
            },
            {header: _('idimage_indexed_cloud_size'), dataIndex: 'cloud_size', sortable: true, width: 70, hidden: true},
            {header: _('idimage_indexed_current_version'), dataIndex: 'current_version', sortable: true, width: 70, hidden: true},
            {header: _('idimage_indexed_images'), dataIndex: 'images', sortable: true, width: 70, hidden: true},
            {header: _('idimage_indexed_closes'), dataIndex: 'closes', sortable: true, width: 70, hidden: true},

            {header: _('idimage_indexed_status_code'), dataIndex: 'status_code', sortable: true, width: 70, hidden: true},
            {header: _('idimage_indexed_completed'), dataIndex: 'completed', sortable: true, width: 70, hidden: true, renderer: idimage.utils.renderBoolean},
            {
                header: _('idimage_indexed_use_version'),
                dataIndex: 'use_version',
                sortable: true,
                width: 70,
                hidden: true,
                renderer: idimage.utils.renderBoolean
            },
            {header: _('idimage_indexed_upload'), dataIndex: 'upload', sortable: true, width: 70, hidden: true, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_indexed_processed'), dataIndex: 'processed', sortable: true, width: 70, hidden: true, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_indexed_sealed'), dataIndex: 'processed', sortable: true, width: 70, hidden: true, renderer: idimage.utils.renderBoolean},

            {header: _('idimage_indexed_start_at'), dataIndex: 'start_at', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_indexed_finished_at'), dataIndex: 'finished_at', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_indexed_upload_at'), dataIndex: 'upload_at', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_indexed_updatedon'), dataIndex: 'updatedon', width: 75, renderer: idimage.utils.formatDate, hidden: true},
            {header: _('idimage_indexed_active'), dataIndex: 'active', width: 75, renderer: idimage.utils.renderBoolean, hidden: true},
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
        return [

            this.actionMenu('indexed/poll', 'icon-refresh', true),

            {
                text: '<i class="icon icon-cogs"></i> ' + _('crontabmanager_actions_dropdown'),
                cls: 'primary-button',
                menu: []
            },

            {
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

    createIndexed: function (btn, e) {
        var w = MODx.load({
            xtype: 'idimage-indexed-window-create',
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

    updateIndexed: function (btn, e, row) {
        if (typeof (row) != 'undefined') {
            this.menu.record = row.data;
        } else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/indexed/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var w = MODx.load({
                            xtype: 'idimage-indexed-window-update',
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

    removeIndexed: function () {
        this.action('remove')
    },
    disableIndexed: function () {
        this.action('disable')
    },
    enableIndexed: function () {
        this.action('enable')
    },

});
Ext.reg('idimage-grid-indexeds', idimage.grid.Indexeds);
