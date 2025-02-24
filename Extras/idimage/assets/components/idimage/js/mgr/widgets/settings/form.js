idimage.form.UpdateSetting = function (config) {
    config = config || {}
    this.ident = config.ident || 'setting' + Ext.id()
    config.id = this.ident


    Ext.applyIf(config, {
        cls: 'container form-with-labels main-wrapper idimage_buttons_settings'
        , labelAlign: 'left'
        , autoHeight: true
        , title: _('search_criteria')
        , labelWidth: 300
        , url: idimage.config.connector_url
        , items: this.getFields(config)
        , style: {margin: '15px 30px'}
        , buttonAlign: 'top'
        , defaults: {
            anchor: '80%'
        },
        listeners: {},

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
                layout: 'form',
                items: [
                    {
                        xtype: 'displayfield',
                        style: {margin: '0px 0 15px 0px', color: '#666666'},
                        hideLabel: true,
                        name: 'transport_desc',
                        anchor: '70%',
                        id: config.id + '-transport_desc',
                        //html: atob(idimage.config.html),
                    },

                ]
            },
        ]
    },
    checkAvailability: function () {

        checkAvailability()
    }
})
Ext.reg('idimage-form-setting-update', idimage.form.UpdateSetting)


function checkAvailability() {

    MODx.Ajax.request({
        url: idimage.config.connectorUrl,
        params: {
            action: 'mgr/catalog/check',
        },
        listeners: {
            success: {
                fn: function (r) {
                    if (r.success) {
                        MODx.msg.status({
                            title: _('success')
                            , message: 'Соединение установлено'
                        })
                    }

                }, scope: this
            },
            failure: {
                fn: function (r) {
                    MODx.msg.status({
                        title: _('error')
                        , message: r.message
                    })
                }, scope: this
            }
        }
    })
}

