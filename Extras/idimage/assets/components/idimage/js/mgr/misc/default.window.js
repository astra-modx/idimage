idimage.window.Default = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        title: '',
        url: idimage.config['connector_url'],
        cls: 'modx-window idimage-window ' || config['cls'],
        width: 600,
        autoHeight: true,
        allowDrop: false,
        record: {},
        baseParams: {},
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config),
        listeners: this.getListeners(config),
    });
    idimage.window.Default.superclass.constructor.call(this, config);

    this.on('hide', function() {
        var w = this;
        window.setTimeout(function() {
            w.close();
        }, 200);
    });
};
Ext.extend(idimage.window.Default, MODx.Window, {

    getFields: function() {
        return [];
    },

    getButtons: function(config) {
        return [{
            text: config.cancelBtnText || _('cancel'),
            scope: this,
            handler: function() {
                config.closeAction !== 'close'
                  ? this.hide()
                  : this.close();
            }
        }, {
            text: config.saveBtnText || _('save'),
            cls: 'primary-button',
            scope: this,
            handler: this.submit,
        }];
    },

    getKeys: function() {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: function() {
                this.submit();
            }, scope: this
        }];
    },

    getListeners: function() {
        return {};
    },

});
Ext.reg('idimage-window-default', idimage.window.Default);