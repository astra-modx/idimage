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
            {header: _('idimage_close_status'), dataIndex: 'status', width: 70, sortable: true, renderer: idimage.utils.statusClose},
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
        return [
            /*     {
                     text: '<i class="icon icon-plus"></i>&nbsp;' + _('idimage_close_create'),
                     handler: this.createItem,
                     scope: this
                 },*/
            {
                text: '<i class="icon icon-plus"></i>&nbsp;' + _('idimage_actions_bulk'),
                handler: this.actionsBulk,
                scope: this
            },
            {
                text: '<i class="icon icon-trash"></i>&nbsp;' + _('idimage_actions_clear_all'),
                handler: this.actionsClearAll,
                scope: this
            },
            {
                text: '<i class="icon icon-upload"></i>&nbsp;' + _('idimage_actions_upload'),
                handler: this.actionsUpload,
                scope: this
            },
            {
                text: '<i class="icon icon-upload"></i>&nbsp;' + _('idimage_actions_reindex'),
                handler: this.actionsReIndex,
                scope: this
            },
            /*{
                text: '<i class="icon icon-upload"></i>&nbsp;' + _('idimage_actions_upversion'),
                handler: this.actionsUpVersion,
                scope: this
            },*/
            {
                text: '<i class="icon icon-upload"></i>&nbsp;' + _('idimage_actions_status_poll'),
                handler: this.actionsStatusPoll,
                scope: this
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
    statusPoll: function () {
        this.action('statuspoll')
    },
    actionsReIndex: function () {
        this.actions('reindex')
    },
    actionsUpVersion: function () {
        this.actions('upversion')
    },
    actionsBulk: function () {
        this.actions('bulk')
    },
    actionsClearAll: function () {
        this.actions('clearall')
    },

    actions: function (name) {

        //MODx.Ajax.request({
        MODx.msg.confirm({
            title: _('idiamge_actions_confirm_title'),
            text: _('idiamge_actions_confirm_text') + ': ' + name,
            url: this.config.url,
            params: {
                action: 'mgr/actions/' + name,
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh()
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                        this.refresh()
                    }, scope: this
                }
            }
        })
    },

    actionsUpload: function () {
        this.actionsProgress('upload')
    },

    actionsStatusPoll: function () {
        this.actionsProgress('statuspoll')
    },

    totalRecords: 0,
    iterations: null,
    iterationNext: 0,
    iterationPrevTotal: 0,
    progress: null,

    actionsCall: function (controller) {

        if (this.iterations[this.iterationNext] && this.iterations[this.iterationNext].length > 0) {

            var ids = this.iterations[this.iterationNext];
            delete this.iterations[this.iterationNext]
            this.iterationNext++;
            this.iterationPrevTotal += ids.length;

            this.actionsAjax({
                action: 'mgr/actions/' + controller,
                ids: Ext.util.JSON.encode(ids)
            }, function (grid, response) {
                if (response.success) {
                    idimage.progress.updateText('Обработано ' + grid.iterationPrevTotal + ' из ' + grid.totalRecords)
                    grid.actionsCall(controller)
                }
            })

        } else {
            idimage.progress.hide()
            this.refresh()
        }
    },

    actionsProgress: function (controller) {

        idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
        this.actionsAjax({
                action: 'mgr/actions/' + controller,
                count_iteration: true
            },
            function (grid, response) {
                if (response.success) {

                    grid.totalRecords = response.object.total
                    grid.iterationPrevTotal = 0;
                    grid.iterationNext = 0;
                    grid.iterations = response.object.iterations;

                    if (grid.totalRecords === 0) {
                        MODx.msg.alert(_('success'), 'Изменений не найдено')
                    } else {
                        idimage.progress.updateText('В обработке 0 из ' + grid.totalRecords)

                        grid.actionsCall(controller)
                    }

                }
            })
    },
    actionsAjax: function (params, callback) {
        //this.actions('upload')

        MODx.Ajax.request({
            url: this.config.url,
            params: params,
            listeners: {
                success: {
                    fn: function (response) {
                        callback(this, response);
                        //this.refresh()
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        MODx.msg.alert(_('error'), r.message);
                        this.refresh()
                    }, scope: this
                }
            }
        })
    },
});
Ext.reg('idimage-grid-closes', idimage.grid.Closes);
