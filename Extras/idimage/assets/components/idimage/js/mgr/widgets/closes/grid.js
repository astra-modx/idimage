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
            'id', 'pid', 'task_id', 'exists_thumbnail', 'similar_exists', 'picture_thumb', 'embedding_exists', 'pagetitle', 'similar', 'embedding', 'hash', 'images', 'total', 'status', 'picture', 'tags', 'errors', 'createdon', 'updatedon', 'active', 'actions'
        ];
    },

    getColumns: function () {
        return [
            {header: _('id'), dataIndex: 'id', width: 20, sortable: true, hidden: true},
            //{header: _('idimage_close_pid'), dataIndex: 'pid', width: 70, sortable: true, renderer: idimage.utils.resourceLink},
            {
                header: _('idimage_close_pagetitle'),
                dataIndex: 'pagetitle', width: 70,
                sortable: true,
                renderer: idimage.utils.resourceLinkProduct
            },
            {hidden: true, header: _('idimage_picture'), dataIndex: 'picture', width: 70, sortable: true, renderer: idimage.utils.renderImage},
            {

                header: _('idimage_picture_thumb'),
                dataIndex: 'picture_thumb',
                width: 70,
                sortable: true,
                renderer: idimage.utils.renderImage
            },
            {
                header: _('idimage_close_images'),
                dataIndex: 'images',
                width: 250,
                renderer: idimage.utils.renderImages
            },
            {hidden: true, header: _('idimage_pid'), dataIndex: 'pid', width: 70, sortable: true},
            {hidden: true, header: _('idimage_task_id'), dataIndex: 'task_id', width: 70, sortable: true},
            {
                hidden: true,
                header: _('idimage_embedding_exists'),
                dataIndex: 'embedding_exists',
                width: 75,
                renderer: idimage.utils.renderBoolean,
                sortable: true
            },
            {hidden: true, header: _('idimage_similar_exists'), dataIndex: 'similar_exists', width: 75, renderer: idimage.utils.renderBoolean, sortable: true},
            {hidden: true, header: _('idimage_exists_thumbnail'), dataIndex: 'exists_thumbnail', width: 75, renderer: idimage.utils.renderBoolean},
            {hidden: true, header: _('idimage_status'), dataIndex: 'status', width: 70, sortable: true, renderer: idimage.utils.statusClose},


            {
                header: _('idimage_close_errors'),
                dataIndex: 'errors', sortable: true, width: 70, renderer: idimage.utils.jsonDataError
            },
            {hidden: true, header: _('idimage_total'), dataIndex: 'total', sortable: true, width: 70},
            {hidden: true, header: _('idimage_hash'), dataIndex: 'hash', sortable: true, width: 70},
            {hidden: true, header: _('idimage_createdon'), dataIndex: 'createdon', width: 75, renderer: idimage.utils.formatDate},
            {hidden: true, header: _('idimage_updatedon'), dataIndex: 'updatedon', width: 75, renderer: idimage.utils.formatDate},
            {hidden: true, header: _('idimage_active'), dataIndex: 'active', sortable: true, width: 75, renderer: idimage.utils.renderBoolean},
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

                    this.actionMenu('product/task/upload', 'icon-upload'),
                    //this.actionMenu('product/task/embedding', 'icon-upload'),
                    this.actionMenu('product/task/indexed', 'icon-refresh'),
                    '-',
                    // '-',
                    this.actionMenu('product/destroy', 'icon-trash action-red'),
                ]
            },
            //this.actionMenu('task/upload', 'icon-upload'),

            this.actionMenu('product/creation', 'icon-plus action-green'),

            /* {
                 text: '<i class="icon icon-plus"></i> ' + _('idimage_navbar_create_product_btn'),
                 handler: this.assignSelected,
                 scope: this
             },*/


            {
                xtype: 'idimage-combo-filter-active',
                name: 'active',
                width: 210,
                custm: true,
                clear: true,
                addall: true,
                value: '',
                baseParams: {
                    action: 'mgr/misc/active/getlist',
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
                                grid.actionsCall(idimage.config.actions.product_creation)


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
    uploadClose: function () {
        this.action('action/upload')
    }
    ,
    embeddingClose: function () {
        this.action('action/embedding')
    }
    ,
    indexedClose: function () {
        this.action('action/indexed')
    }
    ,
    checkClose: function () {
        this.action('action/check')
    }
    ,
    thumbnailRemoveClose: function () {
        this.action('action/thumbnail/remove')
    },
    thumbnailCreateClose: function () {
        this.action('action/thumbnail/create')
    }
    ,
    similarRemoveClose: function () {
        this.action('action/similar/remove')
    }
    ,
    embeddingRemoveClose: function () {
        this.action('action/embedding/remove')
    }
    ,

});
Ext.reg('idimage-grid-closes', idimage.grid.Closes);
