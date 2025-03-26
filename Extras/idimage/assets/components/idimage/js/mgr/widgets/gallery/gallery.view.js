idimage.panel.Images = function (config) {
    config = config || {};

    this.view = MODx.load({
        xtype: 'idimage-gallery-images-view',
        id: 'idimage-gallery-images-view',
        cls: 'idimage-gallery-images',
        containerScroll: true,
        pageSize: parseInt(config.pageSize || MODx.config.default_per_page),
        product_id: config.product_id,
        close_id: config.close_id,
        emptyText: _('idimage_gallery_emptymsg'),
    });

    Ext.applyIf(config, {
        id: 'idimage-gallery-images',
        cls: 'browser-view',
        border: false,
        items: [this.view],
        tbar: this.getTopBar(config),
        bbar: this.getBottomBar(config),
    });
    idimage.panel.Images.superclass.constructor.call(this, config);

};
Ext.extend(idimage.panel.Images, MODx.Panel, {

    _doSearch: function (tf) {
        this.view.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.view.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    getTopBar: function () {
        return new Ext.Toolbar({
            items: [
                '->',
                /*    {
                        xtype: 'idimage-field-search',
                        width: 300,
                        listeners: {
                            search: {
                                fn: function (field) {
                                    //noinspection JSUnresolvedFunction
                                    this._doSearch(field);
                                }, scope: this
                            },
                            clear: {
                                fn: function (field) {
                                    field.setValue('');
                                    //noinspection JSUnresolvedFunction
                                    this._clearSearch();
                                }, scope: this
                            },
                        },
                    }*/
            ]
        })
    },

    getBottomBar: function (config) {
        return new Ext.PagingToolbar({
            pageSize: parseInt(config.pageSize || MODx.config.default_per_page),
            store: this.view.store,
            displayInfo: true,
            autoLoad: true,
            items: [
                '-',
                _('per_page') + ':',
                {
                    xtype: 'textfield',
                    value: parseInt(config.pageSize || MODx.config.default_per_page),
                    width: 50,
                    listeners: {
                        change: {
                            fn: function (tf, nv) {
                                if (Ext.isEmpty(nv)) {
                                    return;
                                }
                                nv = parseInt(nv);
                                //noinspection JSUnresolvedFunction
                                this.getBottomToolbar().pageSize = nv;
                                this.view.getStore().load({params: {start: 0, limit: nv}});
                            }, scope: this
                        },
                        render: {
                            fn: function (cmp) {
                                new Ext.KeyMap(cmp.getEl(), {
                                    key: Ext.EventObject.ENTER,
                                    fn: function () {
                                        this.fireEvent('change', this.getValue());
                                        this.blur();

                                        return true;
                                    },
                                    scope: cmp
                                });
                            }, scope: this
                        }
                    }
                }
            ]
        });
    },

});
Ext.reg('idimage-gallery-images-panel', idimage.panel.Images);


idimage.view.Images = function (config) {
    config = config || {};

    this._initTemplates();

    Ext.applyIf(config, {
        url: idimage.config.connector_url,
        fields: [
            'id',
            'product_id',
            'name',
            'url', 'class', 'thumbnail', 'active', 'probability', 'actions'
        ],
        id: 'idimage-gallery-images-view',
        baseParams: {
            action: 'mgr/gallery/getlist',
            product_id: config.product_id,
            limit: config.pageSize || MODx.config.default_per_page
        },
        //loadingText: _('loading'),
        //enableDD: true,
        //multiSelect: true,
        tpl: this.templates.thumb,
        itemSelector: 'div.modx-browser-thumb-wrap',
        listeners: {},
        prepareData: this.formatData.createDelegate(this)
    });
    idimage.view.Images.superclass.constructor.call(this, config);

    this.addEvents('sort', 'select');
    this.on('sort', this.onSort, this);
    this.on('dblclick', this.onDblClick, this);

    var widget = this;
    this.getStore().on('beforeload', function () {
        widget.getEl().mask(_('loading'), 'x-mask-loading');
    });
    this.getStore().on('load', function (res) {
        /* Ext.u…l.MixedCollection store */


        widget.getEl().unmask();

        const count = document.querySelectorAll('.idimage-type-similar').length;

        var form = Ext.getCmp('idimage-gallery-total')
        form.setValue(count + ' ' + _('idimage_gallery_unit'))

        var data = res.reader['jsonData']
        if (data.close && data.close.picture_thumb) {
            widget.updateThumb(data.close.picture_thumb)
        } else {
            widget.updateThumb()
        }

        if (data.close && data.close) {
            var total = data.close.product_indexed || 0;
            var formTotal = Ext.getCmp('idimage-gallery-products-indexed')
            if (formTotal) {
                formTotal.setValue(total + ' ' + _('idimage_gallery_unit'))
            }
        }
    });


};
Ext.extend(idimage.view.Images, MODx.DataView, {

    templates: {},
    windows: {},

    onSort: function (o) {
        var el = this.getEl();
        el.mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: idimage.config.connector_url,
            params: {
                action: 'mgr/gallery/sort',
                product_id: this.config.product_id,
                source: o.source.id,
                target: o.target.id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        el.unmask();
                        this.store.reload();
                        //noinspection JSUnresolvedFunction
                    }, scope: this
                }
            }
        });
    },

    onDblClick: function (e) {
        var node = this.getSelectedNodes()[0];
        if (!node) {
            return;
        }
        var data = this.lookup[node.id];
        window.open(data.url);
    },

    updateFile: function (btn, e) {
        var node = this.cm.activeNode;
        var data = this.lookup[node.id];
        if (!data) {
            return;
        }

        var w = MODx.load({
            xtype: 'idimage-gallery-image',
            record: data,
            listeners: {
                success: {
                    fn: function () {
                        this.store.reload()
                    }, scope: this
                }
            }
        });
        w.setValues(data);
        w.show(e.target);
    },

    showFile: function () {
        var node = this.cm.activeNode;
        var data = this.lookup[node.id];
        if (!data) {
            return;
        }

        window.open(data.url);
    },

    fileAction: function (method) {
        var ids = this._getSelectedIds();
        if (!ids.length) {
            return false;
        }
        this.getEl().mask(_('loading'), 'x-mask-loading');
        MODx.Ajax.request({
            url: idimage.config.connector_url,
            params: {
                action: 'mgr/gallery/multiple',
                method: method,
                ids: Ext.util.JSON.encode(ids),
            },
            listeners: {
                success: {
                    fn: function (r) {
                        if (method == 'remove') {
                            //noinspection JSUnresolvedFunction
                            this.updateThumb(r.object['thumb']);
                        }
                        this.store.reload();
                    }, scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    }, scope: this
                },
            }
        })
    },


    updateThumb: function (url) {
        var thumb = Ext.get('idimage-close-thumb');
        const thumbImage = document.getElementById('idimage-close-thumb');
        const placeholder = document.getElementById('idimage-close-placeholder');

        if (thumbImage) {
            if (thumbImage.classList.contains('idimage-image-hide')) {
                thumbImage.classList.remove('idimage-image-hide');
            }
        }
        if (placeholder) {
            if (placeholder.classList.contains('idimage-placeholder-hide')) {
                placeholder.classList.remove('idimage-placeholder-hide');
            }
        }

        if (thumb && url) {
            thumb.set({'src': url});
            placeholder.classList.add('idimage-placeholder-hide');
        } else {
            thumbImage.classList.add('idimage-image-hide');
        }
    },

    removeSimilar: function () {
        var close_id = this.config.close_id || '';
        var product_id = this.config.product_id || '';

        Ext.MessageBox.confirm(
            _('idimage_close_action_similarremove'),
            _('idimage_actions_confirm_text'),
            function (val) {
                if (val == 'yes') {
                    this.getEl().mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: idimage.config.connector_url,
                        params: {
                            action: 'mgr/close/action//similar/remove',
                            id: close_id,
                            product_id: product_id,
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    //noinspection JSUnresolvedFunction
                                    this.store.reload();
                                }, scope: this
                            },
                            failure: {
                                fn: function (response) {
                                    MODx.msg.alert(_('error'), response.message);
                                }, scope: this
                            },
                        }
                    })
                }
            },
            this
        );
    },

    indexedImage: function () {
        var close_id = this.config.close_id || '';
        var product_id = this.config.product_id || '';
        var grid = this;

        Ext.MessageBox.confirm(
            _('idimage_actions_product_task_indexed'),
            _('idimage_actions_confirm_text'),
            function (val) {
                if (val == 'yes') {
                    this.getEl().mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: idimage.config.connector_url,
                        params: {
                            action: 'mgr/close/action/indexed',
                            id: close_id,
                            product_id: product_id,
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    //noinspection JSUnresolvedFunction
                                    if (r.object) {
                                        grid.config.close_id = r.object.close_id;
                                    }
                                    this.store.reload();
                                }, scope: this
                            },
                            failure: {
                                fn: function (response) {
                                    MODx.msg.alert(_('error'), response.message);
                                }, scope: this
                            },
                        }
                    })
                }
            },
            this
        );
    },

    uploadImage: function () {
        var close_id = this.config.close_id || '';
        var product_id = this.config.product_id || '';
        var grid = this;
        Ext.MessageBox.confirm(
            _('idimage_close_action_upload'),
            _('idimage_actions_confirm_text'),
            function (val) {
                if (val == 'yes') {
                    this.getEl().mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: idimage.config.connector_url,
                        params: {
                            action: 'mgr/close/action/upload',
                            id: close_id,
                            product_id: product_id,
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    //noinspection JSUnresolvedFunction

                                    if (r.object) {
                                        grid.config.close_id = r.object.close_id;
                                    }

                                    this.store.reload();
                                }, scope: this
                            },
                            failure: {
                                fn: function (response) {
                                    MODx.msg.alert(_('error'), response.message);
                                }, scope: this
                            },
                        }
                    })
                }
            },
            this
        );
    },


    run: function (p) {
        p = p || {};
        var v = {};
        Ext.apply(v, this.store.baseParams);
        Ext.apply(v, p);
        this.changePage(1);
        this.store.baseParams = v;
        this.store.load();
    },

    formatData: function (data) {
        data.shortName = Ext.util.Format.ellipsis(data.name, 20);
        data.createdon = idimage.utils.formatDate(data.createdon);
        this.lookup['ms2-gallery-image-' + data.id] = data;
        return data;
    },

    _initTemplates: function () {
        this.templates.thumb = new Ext.XTemplate(
            '<tpl for=".">\
                <div class="modx-browser-thumb-wrap modx-pb-thumb-wrap idimage-gallery-thumb-wrap {class}" id="ms2-gallery-image-{id}">\
                    <div class="modx-browser-thumb modx-pb-thumb idimage-gallery-thumb">\
                    <a href="{url}" target="_blank">\
                        <img width="224" height="224" src="{thumbnail}" title="{name}" />\
                    </a> \
                    </div>\
                    <small><b>{probability}</b> - {shortName}</small>\
                </div>\
            </tpl>'
        );
        this.templates.thumb.compile();
    },

    _showContextMenu: function (v, i, n, e) {
        e.preventDefault();
        var data = this.lookup[n.id];
        var m = this.cm;
        m.removeAll();

        var menu = idimage.utils.getMenu(data.actions, this, this._getSelectedIds());
        for (var item in menu) {
            if (!menu.hasOwnProperty(item)) {
                continue;
            }
            m.add(menu[item]);
        }

        m.show(n, 'tl-c?');
        m.activeNode = n;
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectedRecords();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

});
Ext.reg('idimage-gallery-images-view', idimage.view.Images);
