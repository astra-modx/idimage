idimage.panel.Toolbar = function (config) {
    config = config || {};

    Ext.apply(config, {
        id: 'idimage-gallery-page-toolbar',
        items: [

            {
                text: '<i class="icon icon-refresh action-green"></i> ' + _('idimage_actions_product_task_indexed'),
                cls: 'idimage-btn-action',
                handler: function () {
                    this.fileAction('indexedImage')
                },
                scope: this,
            },
            {
                text: '<i class="icon icon-cogs"></i> ',
                cls: 'idimage-btn-actions',
                menu: [
                    {
                        text: '<i class="icon icon-refresh"></i> ' + _('idimage_close_action_upload'),
                        cls: 'idimage-btn-action',
                        handler: function () {
                            this.fileAction('uploadImage')
                        },
                        scope: this,
                    },
                    '-',
                    {
                        text: '<i class="icon icon-trash-o action-red"></i> ' + _('idimage_close_action_similarremove'),
                        cls: 'idimage-btn-action',
                        handler: function () {
                            this.fileAction('removeSimilar')
                        },
                        scope: this,
                    },
                ]
            }


        ]
    });
    idimage.panel.Toolbar.superclass.constructor.call(this, config);
    this.config = config;
};
Ext.extend(idimage.panel.Toolbar, Ext.Toolbar, {

    sourceWarning: function (combo) {
        var source_id = this.config.record.source;
        var sel_id = combo.getValue();
        if (source_id != sel_id) {
            Ext.Msg.confirm(_('warning'), _('ms2_product_change_source_confirm'), function (e) {
                if (e == 'yes') {
                    combo.setValue(sel_id);
                    MODx.activePage.submitForm({
                        success: {
                            fn: function (r) {
                                var page = 'resource/update';
                                MODx.loadPage(page, 'id=' + r.result.object.id);
                            }, scope: this
                        }
                    });
                } else {
                    combo.setValue(source_id);
                }
            }, this);
        }
    },

    fileAction: function (method) {
        var view = Ext.getCmp('idimage-gallery-images-view');
        if (view && typeof view[method] === 'function') {
            return view[method].call(view, arguments);
        }
    },

});
Ext.reg('idimage-gallery-page-toolbar', idimage.panel.Toolbar);
