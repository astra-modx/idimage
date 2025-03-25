idimage.tree.OptionCategories = function (config) {
    config = config || {}
    this.grid = null
    this.form = null

    var parents = Ext.util.JSON.encode(idimage.store.get('categories'))
    var context = idimage.config.context
    this.form = Ext.getCmp('idimage-form-panel')
    this.grid = Ext.getCmp(idimage.config.grid_id)

    Ext.applyIf(config, {
        url: idimage.config.connector_url
        , id: 'idimage-tree-option-categories-id'
        , title: ''
        , anchor: '100%'
        , rootVisible: false
        , expandFirst: true
        , enableDD: false
        , ddGroup: 'modx-treedrop-dd'
        , remoteToolbar: false
        , action: idimage.config.controllerPath + 'getcategorynodes'
        , tbarCfg: {id: config.id ? config.id + '-tbar' : 'modx-tree-resource-tbar'}
        , baseParams: {
            action: 'mgr/system/category/getcategorynodes'
            , currentResource: MODx.request.id || 0
            , currentAction: MODx.request.a || 0
            , context: context
            , categories: parents
        }
        , listeners: {
            checkchange: function (node, checked) {
                if (typeof this.optionGrid === 'undefined') return
                var checkedNodes = this.getChecked()
                var categories = []

                for (var i = 0; i < checkedNodes.length; i++) {
                    categories.push(checkedNodes[i].attributes.pk)
                }
                this.setFilterArray('categories', categories)
            },
            load: function (node) {
                // TODO отключили событие по причине сброса отмеченых категорий
                //this.fireEvent('checkchange', node)

            }
            , afterrender: function () {
                this.mask = new Ext.LoadMask(this.getEl())
            }
            , loadCreateMenus: function () {

            }
        }
    })
    idimage.tree.OptionCategories.superclass.constructor.call(this, config)
}
Ext.extend(idimage.tree.OptionCategories, MODx.tree.Resource, {

    setFilterArray: function (field, value) {
        value = this.getFilterArray(value)
        this.form._filterSet(field, value)
    },
    getFilterArray: function (value) {
        if (value !== undefined) {
            value = Object.assign({}, value)
        } else {
            value = {}
        }
        return value
    }

    /**
     * Gets a default toolbar setup
     */
    , getToolbar: function () {

        var parent = idimage.tree.ModalCategories.superclass.getToolbar.call(this)

        parent.push({
            //icon: iu + 'refresh.png'
            cls: 'x-btn x-btn-small x-btn-icon-small-left x-grid3-row-checker x-btn-noicon'
            , tooltip: {text: _('idimage_unchecked_categories')}
            , id: 'idimage-unchecked-idimage'
            , handler: this.unCheckedCategories
            , scope: this
        })


        parent.push('->')

        parent.push({
            //icon: iu + 'refresh.png'
            cls: 'x-btn x-btn-small x-btn-icon-small-left tree-trash x-btn-noicon x-item-disabled'
            , tooltip: {text: _('empty_recycle_bin')}
            , id: 'emptifier-idimage'
            , handler: this.emptyRecycleBin
            , scope: this
        })

        return parent
    },
    unCheckedCategories: function () {
        var form = Ext.getCmp('idimage-form-panel')
        var grid_ = Ext.getCmp(idimage.config.grid_id)

        form.saveState = true;
        form.setState('categories', {})
        this.refresh()
        grid_.refresh()

    },
    emptyRecycleBin: function () {
        MODx.msg.confirm({
            title: _('empty_recycle_bin')
            , text: _('empty_recycle_bin_confirm')
            , url: MODx.config.connector_url
            , params: {
                action: 'resource/emptyRecycleBin'
            }
            , listeners: {
                'success': {
                    fn: function () {
                        Ext.select('div.deleted', this.getRootNode()).remove()
                        MODx.msg.status({
                            title: _('success')
                            , message: _('empty_recycle_bin_emptied')
                        })

                        this.refresh()
                        Ext.getCmp(idimage.config.grid_id).refresh()
                        this.fireEvent('emptyTrash')
                    }, scope: this
                }
            }
        })
    }
})
Ext.reg('idimage-tree-option-categories', idimage.tree.OptionCategories)

idimage.window.AssignCategorys = function (config) {

    config = config || {}
    this.ident = config.ident || 'meuitem' + Ext.id()

    Ext.applyIf(config, {
        title: _('idimage_action_assign')
        , id: this.ident
        , width: 700
        , labelAlign: 'left'
        , labelWidth: 180
        // , autoHeight: true
        , maxHeight: 450
        , height: 450
        , url: idimage.config.connector_url
        , action: idimage.config.actions.product_creation
        , baseParams: {
            action: idimage.config.actions.product_creation,
            steps: true
        }
        /* ,  buttons: [
               {
                   text: 'Создать товары',
                   cls: 'primary-button',
                   scope: this,
                   handler: this.submit
               },
               {
                   text: 'Закрыть',
                   handler: function () {
                       Ext.getCmp('idimage-grid-closes').windows.assignCategories.hide().getEl().remove();
                       //Ext.getCmp('idimage-grid-closes').windows.assignCategories.hide().getEl().remove();
                       Ext.getCmp('idimage-grid-closes').windows.assignCategories = null
                       // Ext.getCmp('idimage-grid-closes').windows.assignCategories.close()
                   }
               }
           ]*/
        , fields: [
            {
                xtype: 'idimage-tree-modal-categories',
                id: 'idimage-tree-modal-categorys-assign-window',
                categories: 'idimage-categories-ids',
                baseParams: {
                    action: 'settings/category/getcategorynodes',
                    steps: true
                    , currentResource: MODx.request.id || 0
                    , currentAction: MODx.request.a || 0
                    , contextKey: idimage.config.context
                    //, contextKey: idimage.store.get('context')
                }
            },
            {
                xtype: 'hidden', name: 'categorys', id: 'idimage-categorys-ids'
            },
            {
                xtype: 'hidden', name: 'categories', id: 'idimage-categories-ids'
            }
        ]
        , keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    })
    idimage.window.AssignCategorys.superclass.constructor.call(this, config)
}
Ext.extend(idimage.window.AssignCategorys, MODx.Window)
Ext.reg('idimage-window-categorys-assign', idimage.window.AssignCategorys)

idimage.tree.ModalCategories = function (config) {
    config = config || {}

    Ext.applyIf(config, {
        url: idimage.config.connector_url
        , id: 'idimage-modal-categories-tree'
        , title: ''
        , anchor: '100%'
        , rootVisible: false
        , autoLoad: false
        , expandFirst: true
        , enableDD: false
        , autoHeight: true
        , maxHeight: 350
        , height: 350
        , ddGroup: 'modx-treedrop-dd'
        , remoteToolbar: false
        , action: 'mgr/system/category/getcategorynodes'
        , tbarCfg: {id: config.id ? config.id + '-tbar' : 'modx-tree-resource-tbar'}
        , listeners: {
            checkchange: function (node, checked) {
                var checkedNodes = this.getChecked()
                var categories = []

                for (var i = 0; i < checkedNodes.length; i++) {
                    categories.push(checkedNodes[i].attributes.pk)
                }

                var catField = Ext.getCmp(this.categories)
                if (!catField) return false
                catField.setValue(Ext.util.JSON.encode(categories))
            }
            , afterrender: function () {
                this.mask = new Ext.LoadMask(this.getEl())
            }
        }
    })
    idimage.tree.ModalCategories.superclass.constructor.call(this, config)
}
Ext.extend(idimage.tree.ModalCategories, MODx.tree.Tree, {
    _showContextMenu: function (n, e) {
        n.select()
        this.cm.activeNode = n
        this.cm.removeAll()
        var m = []
        console.log(2121);
        m.push({
            text: _('directory_refresh'), handler: function () {
                this.refreshNode(this.cm.activeNode.id, true)
            }
        })
        this.addContextMenuItem(m)
        this.cm.showAt(e.xy)
        e.stopEvent()
    }

})
Ext.reg('idimage-tree-modal-categories', idimage.tree.ModalCategories)
