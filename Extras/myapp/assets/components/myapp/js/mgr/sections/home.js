myapp.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'myapp-panel-home',
            renderTo: 'myapp-panel-home-div'
        }]
    });
    myapp.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(myapp.page.Home, MODx.Component);
Ext.reg('myapp-page-home', myapp.page.Home);