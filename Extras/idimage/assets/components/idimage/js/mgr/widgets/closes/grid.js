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
            'id', 'pid', 'max_scope', 'attempt', 'pagetitle', 'min_scope', 'search_scope', 'total', 'status', 'picture', 'upload', 'upload_link', 'tags', 'errors', 'received_at', 'received', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('idimage_close_id'), dataIndex: 'id', width: 20, sortable: true, hidden: true},
            {header: _('idimage_close_picture'), dataIndex: 'picture', width: 70, sortable: true, renderer: idimage.utils.renderImage},
            //{header: _('idimage_close_pid'), dataIndex: 'pid', width: 70, sortable: true, renderer: idimage.utils.resourceLink},
            {header: _('idimage_close_pagetitle'), dataIndex: 'pagetitle', width: 70, sortable: true, renderer: idimage.utils.resourceLinkProduct},
            {header: _('idimage_close_status'), dataIndex: 'status', width: 70, sortable: true, hidden: true, renderer: idimage.utils.statusClose},

            {
                header: _('idimage_close_ball'), dataIndex: 'search_scope', sortable: true, width: 70, renderer: function (v, row, r) {
                    var max = r.data.max_scope || '-';
                    var min = r.data.min_scope || '-';
                    var search = r.data.search_scope || '-';
                    var total = r.data.total || '-';

                    return String.format('Total: {0}<br>  Min scope: {1}<br>Max scope: {2}', total, min, max);
                }
            },
            {header: _('idimage_close_search_scope'), dataIndex: 'search_scope', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_min_scope'), dataIndex: 'min_scope', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_max_scope'), dataIndex: 'max_scope', sortable: true, width: 70, hidden: true},
            {header: _('idimage_close_errors'), dataIndex: 'errors', sortable: true, width: 70, hidden: true, renderer: idimage.utils.jsonDataError},
            {header: _('idimage_close_total'), dataIndex: 'total', sortable: true, width: 70},
            {header: _('idimage_close_attempt'), dataIndex: 'attempt', width: 70, sortable: true, hidden: true},

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

            /*  {
                  text: '<i class="icon icon-plus"></i> Создать товар',
                  handler: this.assignSelected,
                  scope: this
              },*/

            {
                text: '<i class="icon icon-cogs"></i> ' + _('idimage_actions_dropdown'),
                cls: 'primary-button',
                menu: [

                    //this.actionMenu('image/status/processing', 'icon-refresh'),
                    //this.actionMenu('image/status/queue', 'icon-refresh'),

                    //'-',
                    //this.actionMenu('image/queue/delete', 'icon-refresh'),
                    '-',
                    this.actionMenu('image/destroy', 'icon-trash action-red'),
                ]
            },


            {
                xtype: 'idimage-combo-filter-received',
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

            {
                xtype: 'idimage-combo-filter-similar',
                name: 'similar',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: '',
                baseParams: {
                    action: 'mgr/misc/similar/getlist',
                    combo: true,
                    addall: true
                },
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


            '->',
            //this.widgetTotal(config.id),
            this.getSearchField()
        ];
    }
    ,


    windows: {
        assignCategories: false,
    },

    assignSelected: function () {

        var grid = this;
        if (!grid.windows.assignCategories) {
            grid.windows.assignCategories = MODx.load({
                xtype: 'idimage-window-categorys-assign'
                , listeners: {
                    success: {
                        fn: function (response) {
                            var data = response.a.result.object
                            idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
                            grid.totalRecords = data.total
                            grid.iterationPrevTotal = 0;
                            grid.iterationNext = 0;
                            grid.iterations = data.iterations;

                            if (grid.totalRecords === 0) {
                                MODx.msg.alert(_('success'), 'Изменений не найдено')
                            } else {
                                idimage.progress.updateText('В обработке 0 из ' + grid.totalRecords)
                                grid.actionsCall('image/creation')
                            }
                            var tree = Ext.getCmp('idimage-tree-modal-categorys-assign-window')
                            tree.enable()
                            this.refresh()
                        }, scope: this
                    },
                    hide: {
                        fn: function () {
                            this.refresh()
                        }, scope: this
                    }
                }
            })
        }

        grid.windows.assignCategories.show()
    },


    removeClose: function () {
        this.action('remove')
    }
    ,
    disableClose: function () {
        this.action('disable')
    }
    ,
    enableClose: function () {
        this.action('enable')
    }
    ,

});
Ext.reg('idimage-grid-closes', idimage.grid.Closes);
