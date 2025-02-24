idimage.grid.Clouds = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'idimage-grid-clouds';
    }

    config.multiple = 'cloud'

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/close/getlist',
            sort: 'id',
            dir: 'DESC',
            cloud: true
        },
        plugins: this.exp,
        stateful: true,
        stateId: config.id,
        multiple: true,
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
    idimage.grid.Clouds.superclass.constructor.call(this, config);

};

Ext.extend(idimage.grid.Clouds, idimage.grid.Closes, {


    getColumns: function () {
        return [
            {header: _('idimage_close_id'), dataIndex: 'id', width: 20, sortable: true},
            {header: _('idimage_close_pid'), dataIndex: 'pid', width: 70, sortable: true, renderer: idimage.utils.resourceLink},
            {header: _('idimage_close_upload'), dataIndex: 'upload', sortable: true, width: 70, hidden: false, renderer: idimage.utils.renderBoolean},
            {header: _('idimage_close_upload_link'), dataIndex: 'upload_link', sortable: true, width: 70, hidden: false},
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
            this.actionMenu('image/upload/cloud', 'icon-upload', false, 'primary-button'),
            '->',
            this.widgetTotal(config.id), this.getSearchField()];
    },
    /*
    *
                cls: 'primary-button',*/


    uploadClose: function () {

        var ids = this._getSelectedIds()
        if (!ids.length) {
            return false
        }
        var grid = this
        Ext.Msg.confirm(_('idimage_actions_confirm_title'), _('idimage_actions_confirm_text'), function (e) {

            if (e == 'yes') {
                idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
                grid.actionsAjax({
                    action: 'mgr/actions/image/upload/cloud',
                    ids: Ext.util.JSON.encode(ids)
                }, function (response) {

                    idimage.progress.hide()
                    MODx.msg.alert(_('success'), 'Загрузка завершена')
                })

            }
        });
    },


});
Ext.reg('idimage-grid-clouds', idimage.grid.Clouds);
