idimage.combo.Search = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        xtype: 'twintrigger',
        ctCls: 'x-field-search',
        allowBlank: true,
        msgTarget: 'under',
        emptyText: _('search'),
        name: 'query',
        triggerAction: 'all',
        clearBtnCls: 'x-field-search-clear',
        searchBtnCls: 'x-field-search-go',
        onTrigger1Click: this._triggerSearch,
        onTrigger2Click: this._triggerClear,
    });
    idimage.combo.Search.superclass.constructor.call(this, config);
    this.on('render', function () {
        this.getEl().addKeyListener(Ext.EventObject.ENTER, function () {
            this._triggerSearch();
        }, this);
    });
    this.addEvents('clear', 'search');
};
Ext.extend(idimage.combo.Search, Ext.form.TwinTriggerField, {

    initComponent: function () {
        Ext.form.TwinTriggerField.superclass.initComponent.call(this);
        this.triggerConfig = {
            tag: 'span',
            cls: 'x-field-search-btns',
            cn: [
                {tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
                {tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
            ]
        };
    },

    _triggerSearch: function () {
        this.fireEvent('search', this);
    },

    _triggerClear: function () {
        this.fireEvent('clear', this);
    },

});
Ext.reg('idimage-combo-search', idimage.combo.Search);
Ext.reg('idimage-field-search', idimage.combo.Search);

/**
 * Filter Active
 * @param config
 * @constructor
 */
idimage.combo.Active = function (config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-idimage-active-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-idimage-active-clear'
            });
        }

        config.initTrigger = function () {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function (t, all, index) {
                t.hide = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'active',
        hiddenName: config.name || 'active',
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['name', 'value'],
        pageSize: 10,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config.connector_url,
        baseParams: {
            action: 'mgr/misc/active/getlist',
            combo: true,
            addall: config.addall || 0
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({value})</small> <b>{name}</b></span>',
            '</div></tpl>', {
                compiled: true
            }),
        cls: 'input-combo-idimage-active',
        clearValue: function () {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function (index) {
            return this.triggers[index];
        },

        onTrigger1Click: function () {
            this.onTriggerClick();
        },

        onTrigger2Click: function () {
            this.clearValue();
        }
    });
    idimage.combo.Active.superclass.constructor.call(this, config);

};
Ext.extend(idimage.combo.Active, MODx.combo.ComboBox);
Ext.reg('idimage-combo-filter-active', idimage.combo.Active);

/**
 * Filter Resource
 * @param config
 * @constructor
 */
idimage.combo.Resource = function (config) {
    config = config || {};

    if (config.custm) {
        config.triggerConfig = [{
            tag: 'div',
            cls: 'x-field-search-btns',
            style: String.format('width: {0}px;', config.clear ? 62 : 31),
            cn: [{
                tag: 'div',
                cls: 'x-form-trigger x-field-idimage-resource-go'
            }]
        }];
        if (config.clear) {
            config.triggerConfig[0].cn.push({
                tag: 'div',
                cls: 'x-form-trigger x-field-idimage-resource-clear'
            });
        }

        config.initTrigger = function () {
            var ts = this.trigger.select('.x-form-trigger', true);
            this.wrap.setStyle('overflow', 'hidden');
            var triggerField = this;
            ts.each(function (t, all, index) {
                t.hide = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = 'none';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                t.show = function () {
                    var w = triggerField.wrap.getWidth();
                    this.dom.style.display = '';
                    triggerField.el.setWidth(w - triggerField.trigger.getWidth());
                };
                var triggerIndex = 'Trigger' + (index + 1);

                if (this['hide' + triggerIndex]) {
                    t.dom.style.display = 'none';
                }
                t.on('click', this['on' + triggerIndex + 'Click'], this, {
                    preventDefault: true
                });
                t.addClassOnOver('x-form-trigger-over');
                t.addClassOnClick('x-form-trigger-click');
            }, this);
            this.triggers = ts.elements;
        };
    }
    Ext.applyIf(config, {
        name: config.name || 'resource',
        hiddenName: config.name || 'resource',
        displayField: 'pagetitle',
        valueField: 'id',
        editable: true,
        fields: ['pagetitle', 'id'],
        pageSize: 10,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config.connector_url,
        baseParams: {
            action: 'mgr/misc/resource/getlist',
            client_status: 1,
            combo: true
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '<small>({id})</small> <b>{pagetitle}</b>',
            '</div></tpl>',
            {
                compiled: true
            }),
        cls: 'input-combo-idimage-resource',
        clearValue: function () {
            if (this.hiddenField) {
                this.hiddenField.value = '';
            }
            this.setRawValue('');
            this.lastSelectionText = '';
            this.applyEmptyText();
            this.value = '';
            this.fireEvent('select', this, null, 0);
        },

        getTrigger: function (index) {
            return this.triggers[index];
        },

        onTrigger1Click: function () {
            this.onTriggerClick();
        },

        onTrigger2Click: function () {
            this.clearValue();
        }
    });
    idimage.combo.Resource.superclass.constructor.call(this, config);

};
Ext.extend(idimage.combo.Resource, MODx.combo.ComboBox);
Ext.reg('idimage-combo-filter-resource', idimage.combo.Resource);

/**
 * Filed Resource
 * @param config
 * @constructor
 */
idimage.combo.Resource = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'resource',
        hiddenName: 'resource',
        displayField: 'pagetitle',
        valueField: 'id',
        editable: true,
        fields: ['id', 'pagetitle'],
        pageSize: 20,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config['connector_url'],
        baseParams: {
            action: 'mgr/system/element/resource/getlist',
            combo: true
        }
    });
    idimage.combo.Resource.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.Resource, MODx.combo.ComboBox);
Ext.reg('idimage-combo-resource', idimage.combo.Resource);

/**
 * Filed Context
 * @param config
 * @constructor
 */
idimage.combo.Context = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'context',
        hiddenName: 'context',
        displayField: 'name',
        valueField: 'key',
        editable: true,
        fields: ['key', 'name'],
        pageSize: 20,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config['connector_url'],
        baseParams: {
            action: 'mgr/system/element/context/getlist',
            combo: true
        }
    });
    idimage.combo.Context.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.Context, MODx.combo.ComboBox);
Ext.reg('idimage-combo-context', idimage.combo.Context);

/**
 * Filed Chunk
 * @param config
 * @constructor
 */
idimage.combo.Chunk = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'chunk',
        hiddenName: config.name || 'chunk',
        displayField: 'name',
        valueField: 'id',
        editable: true,
        fields: ['id', 'name'],
        pageSize: 20,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config['connector_url'],
        baseParams: {
            action: 'mgr/system/element/chunk/getlist',
            mode: 'chunks'
        }
    });
    idimage.combo.Chunk.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.Chunk, MODx.combo.ComboBox);
Ext.reg('idimage-combo-chunk', idimage.combo.Chunk);

/**
 * Filed User
 * @param config
 * @constructor
 */
idimage.combo.User = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'user',
        fieldLabel: config.name || 'createdby',
        hiddenName: config.name || 'createdby',
        displayField: 'fullname',
        valueField: 'id',
        anchor: '99%',
        fields: ['username', 'id', 'fullname'],
        pageSize: 20,
        typeAhead: false,
        editable: true,
        allowBlank: false,
        url: idimage.config['connector_url'],
        baseParams: {
            action: 'mgr/system/user/getlist',
            combo: true,
        },
        tpl: new Ext.XTemplate('\
            <tpl for=".">\
                <div class="x-combo-list-item">\
                    <span>\
                        <small>({id})</small>\
                        <b>{username}</b>\
                        <tpl if="fullname && fullname != username"> - {fullname}</tpl>\
                    </span>\
                </div>\
            </tpl>',
            {compiled: true}
        ),
    });
    idimage.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.User, MODx.combo.ComboBox);
Ext.reg('idimage-combo-user', idimage.combo.User);


idimage.combo.DateTime = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        timePosition: 'right',
        allowBlank: true,
        hiddenFormat: 'Y-m-d H:i:s',
        dateFormat: MODx.config['manager_date_format'],
        timeFormat: MODx.config['manager_time_format'],
        dateWidth: 120,
        timeWidth: 120
    });
    idimage.combo.DateTime.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.DateTime, Ext.ux.form.DateTime);
Ext.reg('idimage-xdatetime', idimage.combo.DateTime);


/**
 * Status
 * @param config
 * @constructor
 */
idimage.combo.Status = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'status',
        hiddenName: 'status',
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['value', 'name'],
        pageSize: 20,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config['connector_url'],
        baseParams: {
            action: 'mgr/misc/status/getlist',
            combo: true,
            addall: true
        }
    });
    idimage.combo.Status.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.Status, MODx.combo.ComboBox);
Ext.reg('idimage-combo-status', idimage.combo.Status);


/**
 * Filed Resource
 * @param config
 * @constructor
 */
idimage.combo.StatusService = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'status_service',
        hiddenName: 'status_service',
        displayField: 'name',
        valueField: 'value',
        editable: true,
        fields: ['value', 'name'],
        pageSize: 20,
        emptyText: _('idimage_combo_select'),
        hideMode: 'offsets',
        url: idimage.config['connector_url'],
        baseParams: {
            action: 'mgr/misc/status/service/getlist',
            combo: true,
            addall: true
        }
    });
    idimage.combo.StatusService.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.StatusService, MODx.combo.ComboBox);
Ext.reg('idimage-combo-status-service', idimage.combo.StatusService);




/**
 * Filed Resource
 * @param config
 * @constructor
 */
idimage.combo.FilterSimilar = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'received',
        hiddenName: 'received',
        baseParams: {
            action: 'mgr/misc/similar/getlist',
            combo: true,
            addall: config.addall || 0
        },
    });
    idimage.combo.FilterSimilar.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.FilterSimilar, idimage.combo.Active);
Ext.reg('idimage-combo-filter-similar', idimage.combo.FilterSimilar);
/**
 * Filed Resource
 * @param config
 * @constructor
 */
idimage.combo.FilterTaskStatus = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'status',
        hiddenName: 'status',
        baseParams: {
            action: 'mgr/misc/task/status/getlist',
            combo: true,
            addall: config.addall || 0
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '{name}</span>',
            '</div></tpl>', {
                compiled: true
            }),
    });
    idimage.combo.FilterTaskStatus.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.FilterTaskStatus, idimage.combo.Active);
Ext.reg('idimage-combo-filter-task-status', idimage.combo.FilterTaskStatus);

/**
 * Filed Operation
 * @param config
 * @constructor
 */
idimage.combo.FilterTaskOperation = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'operation',
        hiddenName: 'operation',
        baseParams: {
            action: 'mgr/misc/operation/getlist',
            combo: true,
            addall: config.addall || 0
        },
        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item">',
            '{name}</span>',
            '</div></tpl>', {
                compiled: true
            }),
    });
    idimage.combo.FilterTaskOperation.superclass.constructor.call(this, config);
};
Ext.extend(idimage.combo.FilterTaskOperation, idimage.combo.Active);
Ext.reg('idimage-combo-filter-task-operation', idimage.combo.FilterTaskOperation);
