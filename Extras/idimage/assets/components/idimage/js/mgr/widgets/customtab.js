Ext.onReady(function () {

    Ext.ComponentMgr.onAvailable("modx-resource-tabs", function () {


        this.on("beforerender", function () {
            if (!idimage.config.init_tab) {
                idimage.config.init_tab = true;

                this.add({
                    title: _('idimage_tab_products'),
                    layout: 'anchor',
                    items: [
                        {
                            html: _('idimage_gallery_desc'),
                            bodyCssClass: 'panel-desc',
                        }
                        , {
                            xtype: 'idimage-gallery-page',
                            record: {
                                id: idimage.config.record.id
                            },
                            close_id: idimage.config.close_id,
                            pageSize: idimage.config.pageSize,
                            cls: 'main-wrapper',
                        }
                    ]
                });
            }
        });

        Ext.apply(this, {
            stateful: true,
            stateId: "modx-resource-tabs-state",
            stateEvents: ["tabchange"],
            getState: function () {
                return {activeTab: this.items.indexOf(this.getActiveTab())};
            }
        });
    });
});

