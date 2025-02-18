var myapp = function (config) {
    config = config || {};
    myapp.superclass.constructor.call(this, config);
};
Ext.extend(myapp, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}, buttons: {}
});
Ext.reg('myapp', myapp);

myapp = new myapp();