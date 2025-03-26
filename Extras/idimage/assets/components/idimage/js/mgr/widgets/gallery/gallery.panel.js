idimage.panel.Gallery = function (config) {
    config = config || {};

    Ext.apply(config, {
        border: false,
        id: 'idimage-gallery-page',
        baseCls: 'x-panel',
        items: [
            {
                xtype: 'idimage-form-gallery-setting',
            },
            {
                border: false,
                style: {padding: '10px 5px'},
                xtype: 'idimage-gallery-page-toolbar',
                id: 'idimage-gallery-page-toolbar',
                record: config.record,
            },
            {
                border: false,
                style: {padding: '5px'},
                layout: 'anchor',
                items: [

                    {
                        border: false,
                        xtype: 'idimage-gallery-images-panel',
                        id: 'idimage-gallery-images-panel',
                        cls: 'modx-pb-view-ct',
                        close_id: config.close_id,
                        product_id: config.record.id,
                        pageSize: config.pageSize
                    }
                ]
            }
        ]
    });
    idimage.panel.Gallery.superclass.constructor.call(this, config);

};
Ext.extend(idimage.panel.Gallery, MODx.Panel, {
    errors: '',
    progress: null,

});
Ext.reg('idimage-gallery-page', idimage.panel.Gallery);
