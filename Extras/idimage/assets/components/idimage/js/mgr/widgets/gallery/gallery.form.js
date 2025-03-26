idimage.form.GallerySetting = function (config) {
    config = config || {}
    this.ident = 'idimage-gallery-setting'
    config.id = this.ident

    Ext.applyIf(config, {
        cls: 'container form-with-labels idimage-close-thumb'
        , autoHeight: true
        , labelWidth: 300
        , labelAlign: 'top'
        , buttonAlign: 'left'
        , url: idimage.config.connector_url
        , items: this.getFields(config)
        , listeners: {},

    })
    idimage.form.GallerySetting.superclass.constructor.call(this, config)
    this.on('afterrender', function () {
        //this.checkAvailability(); // `this` сохранён
    });
}
Ext.extend(idimage.form.GallerySetting, MODx.Panel, {

    getFields: function (config) {
        return [
            {
                layout: 'column',
                border: false,
                anchor: '80%',
                labelAlign: 'top',
                buttonAlign: 'left',
                style: 'padding: 0 0 0 0px',
                items: [

                    {
                        columnWidth: 0.3,
                        layout: 'form',
                        labelAlign: 'top',
                        cls: 'idimage-close-thumb-wrap',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        items: [
                            // <div class="idimage-similar-image"><div class="idimage-placeholder">Превью</div></div>
                            {
                                xtype: 'displayfield',
                                fieldLabel: _('idimage_gallery_main_image'),
                                hideLabel: true,
                                html: String.format(
                                    '<div>Основное изображение</div>' +
                                    '<div id="idimage-close-placeholder" class="idimage-similar-image {1}"><div class="idimage-placeholder">Превью</div></div>' +
                                    '<img src="{0}" id="idimage-close-thumb" class="{2}"/>',
                                    miniShop2.config.default_thumb,
                                    'idimage-placeholder-hide',
                                    'idimage-image-hide'
                                ),
                            },
                        ]

                    },
                    {
                        columnWidth: 0.3,
                        layout: 'form',
                        labelWidth: 200,
                        //labelAlign: 'top',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0px 0 0 0'},
                        items: [

                            {
                                xtype: 'displayfield',
                                name: 'embedding_exists',
                                value: idimage.config.close.embedding_exists,
                                fieldLabel: _('idimage_gallery_embedding_exists'),
                                //html: String.format('{0}2', idimage.config.close['thumb'])
                            },

                            {
                                xtype: 'displayfield',
                                name: 'similar_exists',
                                value: idimage.config.close.similar_exists,
                                fieldLabel: _('idimage_gallery_similar_exists'),
                            },
                            {
                                xtype: 'displayfield',
                                id: 'idimage-gallery-total',
                                name: 'similar_total',
                                value: idimage.config.close.similar_total,
                                fieldLabel: _('idimage_gallery_similar_total'),
                            },

                        ]

                    },
                    {
                        columnWidth: 0.3,
                        layout: 'form',
                        labelAlign: 'top',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '-15px 0 0 0'},
                        items: [
                            {
                                xtype: 'displayfield',
                                name: 'products_found',
                                value: idimage.config.close.maximum_products_found,
                                fieldLabel: _('idimage_gallery_maximum_products_found'),
                                allowBlank: true,
                            },
                            {
                                xtype: 'displayfield',
                                name: 'minimum_probability_score',
                                value: idimage.config.close.minimum_probability_score,
                                width: '99%',
                                fieldLabel: _('idimage_gallery_minimum_probability_score'),
                                help: _('setting_idimage_token'),
                                allowBlank: true,
                            },
                            {
                                xtype: 'displayfield',
                                id: 'idimage-gallery-products-indexed',
                                name: 'products_indexed',
                                fieldLabel: _('idimage_gallery_products_indexed'),
                            },


                            /*  {
                                  xtype: 'textfield',
                                  name: 'token',
                                  value: '',
                                  width: '99%',
                                  fieldLabel: _('setting_idimage_token'),
                                  help: _('setting_idimage_token'),
                                  allowBlank: true,
                                  description: 'Описание',
                              },*/
                            /* {
                                 style: 'margin: 5px 0 0 0px',
                                 html: _('setting_idimage_token_desc'),
                             },
                             {
                                 xtype: 'button',
                                 style: 'margin: 25px 0 0 2px',
                                 text: '<i class="icon icon-edit"></i> &nbsp;' + _('idimage_setting_submit'),
                                 handler: function () {
                                     this.submit(this)
                                 }, scope: this
                             },*/

                        ]

                    }

                ]
            }
        ]
    },

})
Ext.reg('idimage-form-gallery-setting', idimage.form.GallerySetting)

