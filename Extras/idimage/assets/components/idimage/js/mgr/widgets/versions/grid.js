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
            sort: 'version',
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
                console.log(rec.data.use_version);
                return (!rec.data.active && !rec.data.use_version)
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
            'id', 'version', 'upload', 'size', 'download_link', 'images', 'active', 'run', 'closes', 'launch', 'completed', 'sealed', 'use_version', 'upload', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('idimage_indexed_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('idimage_indexed_version'), dataIndex: 'version', width: 20, sortable: true},
            {header: _('idimage_indexed_launch'), dataIndex: 'launch', width: 20, sortable: true, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_indexed_completed'), dataIndex: 'completed', sortable: true, width: 70, hidden: true, renderer: idimage.utils.renderBoolean},
            {
                header: _('idimage_indexed_upload'),
                dataIndex: 'upload',
                sortable: true,
                width: 70,
                hidden: true,
                renderer: idimage.utils.renderBoolean
            },
            {header: _('idimage_indexed_run'), dataIndex: 'run', sortable: true, width: 70, hidden: false, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_indexed_download_link'), dataIndex: 'download_link', sortable: true, width: 70, hidden: true},
            {header: _('idimage_indexed_images'), dataIndex: 'images', sortable: true, width: 70, hidden: true},
            {header: _('idimage_indexed_closes'), dataIndex: 'closes', sortable: true, width: 70, hidden: true},

            {header: _('idimage_indexed_size'), dataIndex: 'size', sortable: true, width: 70, hidden: true},
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


    getTopBar: function (config) {
        return [


            {
                text: '<i class="icon icon-cogs"></i> ' + _('idimage_actions_dropdown'),
                cls: 'primary-button',
                menu: [
                    this.actionMenu('indexed/poll', 'icon-refresh', true),
                    this.actionMenu('image/queue/add', 'icon-refresh', false, 'primary-button'),
                ]
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
            '->',
            this.widgetTotal(config.id), this.getSearchField()];
    },


    createIndexed: function (btn, e) {

        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/indexed/stat',
            },
            listeners: {
                success: {
                    fn: function (r) {


                        if (r.success) {


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
                            w.setValues(r.object);
                            w.show(e.target);
                        } else {
                            MODx.msg.alert(_('idimage_error'), 'error')
                        }

                        /*   var w = MODx.load({
                               xtype: 'idimage-indexed-window-create',
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
                           w.show(e.target);*/
                    }, scope: this
                }
            }
        });


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

    useVersionIndexed: function () {
        var ids = this._getSelectedIds()
        if (!ids.length) {
            return false
        }

        var grid = this
        Ext.Msg.confirm(_('idimage_actions_confirm_title'), _('idimage_actions_confirm_text'), function (e) {
            if (e == 'yes') {
                idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
                MODx.Ajax.request({
                    url: grid.config.url,
                    params: {
                        action: 'mgr/' + grid.config.multiple + '/multiple',
                        method: 'action/use_version',
                        ids: Ext.util.JSON.encode(ids),
                    },
                    listeners: {
                        success: {
                            fn: function (res) {
                                MODx.msg.alert(_('success'), 'Загрузка завершена')
                                grid.refresh()
                                idimage.progress.hide()
                            }, scope: this
                        },
                        failure: {
                            fn: function (r) {
                                MODx.msg.alert(_('error'), r.message);
                                grid.refresh()
                                idimage.progress.hide()
                            }, scope: this
                        }
                    }
                })

            }
        });

    },

    launchIndexed: function () {
        this.action('action/launch')
    },

    downloadIndexed: function () {
        this.action('action/download')
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
