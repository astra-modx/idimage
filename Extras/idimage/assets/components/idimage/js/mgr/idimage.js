var idimage = function (config) {
    config = config || {};
    idimage.superclass.constructor.call(this, config);
};
Ext.extend(idimage, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}, buttons: {}
});
Ext.reg('idimage', idimage);

idimage = new idimage();