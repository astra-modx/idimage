idimage.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'idimage-panel-home',
            renderTo: 'idimage-panel-home-div'
        }]
    });
    idimage.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(idimage.page.Home, MODx.Component);
Ext.reg('idimage-page-home', idimage.page.Home);