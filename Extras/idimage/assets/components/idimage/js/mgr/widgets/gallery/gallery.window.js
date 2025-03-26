idimage.window.Image = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: config.record['name'],
        width: 700,
        baseParams: {
            action: 'mgr/gallery/update',
        },
        resizable: false,
        maximizable: false,
        minimizable: false,
    });
    idimage.window.Image.superclass.constructor.call(this, config);
};
Ext.extend(idimage.window.Image, idimage.window.Default, {

    getFields: function (config) {
        var img = config.record['thumbnail'];
        var fields = {};
        var details = '';
        for (var i in fields) {
            if (!fields.hasOwnProperty(i)) {
                continue;
            }
            if (fields[i]) {
                details += '<tr><th>' + _(i) + ':</th><td>' + fields[i] + '</td></tr>';
            }
        }

        return [
            {xtype: 'hidden', name: 'id', id: this.ident + '-id'},
            {
                layout: 'column',
                border: false,
                anchor: '100%',
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [{
                        xtype: 'displayfield',
                        hideLabel: true,
                        html: '\
                            <a href="' + config.record['url'] + '" target="_blank" class="idimage-gallery-window-link">\
                                <img src="' + img + '" class="idimage-gallery-window-thumb"  />\
                            </a>\
                            <table class="idimage-gallery-window-details">' + details + '</table>'
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    defaults: {msgTarget: 'under'},
                    border: false,
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: _('idimage_close_pagetitle'),
                            name: 'name',
                            id: this.ident + '-name',
                            anchor: '100%'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: _('idimage_probability'),
                            name: 'probability',
                            id: this.ident + '-probability',
                            anchor: '100%'
                        }
                    ]
                }]
            }
        ];
    }

});
Ext.reg('idimage-gallery-image', idimage.window.Image);
