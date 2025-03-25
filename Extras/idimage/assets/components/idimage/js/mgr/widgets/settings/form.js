idimage.form.UpdateSetting = function (config) {
    config = config || {}
    this.ident = config.ident || 'setting' + Ext.id()
    config.id = this.ident


    Ext.applyIf(config, {
        cls: 'container form-with-labels main-wrapper idimage_buttons_settings'
        , autoHeight: true
        , title: _('search_criteria')
        , labelWidth: 300
        , url: idimage.config.connector_url
        , items: this.getFields(config)
        , style: {margin: '15px 15px'}
        , defaults: {
            anchor: '80%'
        },
        labelAlign: 'top',
        buttonAlign: 'left',
        listeners: {},
        baseParams: {
            action: 'mgr/setting'
        },

    })
    idimage.form.UpdateSetting.superclass.constructor.call(this, config)


    this.on('afterrender', function () {
        setTimeout(() => { // Используем стрелочную функцию
            var sourceDiv = document.getElementById('idimage-panel-home-div-help');
            var targetDiv = document.getElementById('idimage_help');

            // Проверяем, что элементы найдены
            if (sourceDiv && targetDiv) {
                // Удаляем атрибут display: none из style
                // Копируем содержимое из sourceDiv в targetDiv
                targetDiv.innerHTML = sourceDiv.innerHTML;
                targetDiv.style.display = '';  // Это удалит inline стиль display: none

                sourceDiv.remove()
            }

            //this.checkAvailability(); // `this` сохранён
        }, 300);
    });
}
Ext.extend(idimage.form.UpdateSetting, MODx.FormPanel, {

    getFields: function (config) {
        return [

            {
                layout: 'column',
                border: false,
                anchor: '100%',
                cls: 'main-wrapper',
                labelAlign: 'top',
                buttonAlign: 'left',
                style: 'padding: 0 0 0 7px',
                items: [
                    {
                        columnWidth: 0.4,
                        layout: 'form',
                        defaults: {msgTarget: 'under'},
                        border: false,
                        style: {margin: '0'},
                        items: [


                            {
                                xtype: 'idimage-combo-filter-indexed-type',
                                name: 'indexed_type',
                                value: idimage.config.settings.indexed_type,
                                width: '99%',
                                fieldLabel: _('idimage_indexed_type'),
                                allowBlank: false,
                            },
                            {
                                style: 'margin: 5px 0 0 0px',
                                html: _('setting_idimage_indexed_type_desc'),
                            },
                            {
                                xtype: 'textfield',
                                name: 'token',
                                value: idimage.config.settings.token,
                                width: '99%',
                                fieldLabel: _('setting_idimage_token'),
                                help: _('setting_idimage_token'),
                                allowBlank: false,
                                description: _('setting_idimage_token_desc'),
                            },
                            {
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
                            },

                            {
                                html: '<div id="idimage_help"></div>'
                            },
                        ]

                    }]
            }
            ,
            {
                html: String.format(
                    idimage.config.stat
                ),
            },

        ]
    },

})
Ext.reg('idimage-form-setting-update', idimage.form.UpdateSetting)

