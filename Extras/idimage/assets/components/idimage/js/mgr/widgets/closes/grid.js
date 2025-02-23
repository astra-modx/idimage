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
            'id', 'pid', 'status_code', 'min_scope', 'total_close', 'status', 'picture', 'picture_cloud', 'tags', 'errors', 'received_at', 'received', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('idimage_close_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('idimage_close_pid'), dataIndex: 'pid', width: 70, sortable: true, renderer: idimage.utils.resourceLink},
            {header: _('idimage_close_status'), dataIndex: 'status', width: 70, sortable: true, renderer: idimage.utils.statusClose},
            {header: _('idimage_close_picture'), dataIndex: 'picture', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_picture_cloud'), dataIndex: 'picture_cloud', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_min_scope'), dataIndex: 'min_scope', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_errors'), dataIndex: 'errors', sortable: true, width: 70, hidden: true, renderer: idimage.utils.jsonDataError},
            {header: _('idimage_close_total_close'), dataIndex: 'total_close', sortable: true, width: 70},
            {header: _('idimage_close_status_code'), dataIndex: 'status_code', sortable: true, width: 70},
            {header: _('idimage_close_tags'), dataIndex: 'tags', sortable: true, width: 150, renderer: idimage.utils.jsonDataTags},
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

    action: function (action, icon, one) {
        var lex = action.replace('/', '_'); // Заменяет первый слэш

        var label = _('idimage_actions_' + lex);

        label = label === undefined ? action : label;

        icon = icon !== undefined ? '<i class="icon ' + icon + '"></i>&nbsp;' : '';

        var handlerFunction = () => this.actions(action);
        if (one !== true) {
            handlerFunction = () => this.actionsProgress(action);
        }

        return {
            cls: 'idimage-context-menu',
            text: icon + '' + label,
            handler: handlerFunction,
            scope: this
        };
    },
    getTopBar: function () {
        return [

            this.actionMenu('creation', 'icon-plus'),
            {
                text: '<i class="icon icon-cogs"></i> ' + _('crontabmanager_actions_dropdown'),
                cls: 'primary-button',
                menu: [

                    this.actionMenu('reindex', 'icon-refresh', true),
                    '-',
                    this.actionMenu('queue/add', 'icon-refresh'),
                    this.actionMenu('queue/delete', 'icon-refresh'),
                    '-',
                    this.actionMenu('upload', 'icon-upload'),
                    '-',
                    this.actionMenu('destroy', 'icon-trash action-red'),
                ]
            },

            {
                text: '<i class="icon icon-cogs"></i> ' + _('crontabmanager_actions_dropdown_status'),
                cls: 'primary-button',
                menu: [
                    this.actionMenu('status/proccessing', 'icon-refresh'),
                    this.actionMenu('status/queue', 'icon-refresh'),
                    this.actionMenu('status/upload', 'icon-refresh'),
                ]
            },

            this.actionMenu('poll', 'icon-refresh', true),
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

    actionsReIndex: function () {
        this.actions('reindex')
    },

    actionsCreation: function () {
        this.actionsProgress('creation')
    },

    actionsUpload: function () {
        this.actionsProgress('upload')
    },

    actionsStatusPoll: function () {
        this.actions('statuspoll')
    },

});
Ext.reg('idimage-grid-closes', idimage.grid.Closes);
