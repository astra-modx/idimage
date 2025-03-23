idimage.grid.Default = function (config) {
    config = config || {};

    if (typeof (config['multi_select']) != 'undefined' && config['multi_select'] == true) {
        config.sm = new Ext.grid.CheckboxSelectionModel();
    }

    if (!config.multiple) {
        config.multiple = 'item';
    }

    Ext.applyIf(config, {
        url: idimage.config['connector_url'],
        baseParams: {},
        cls: config['cls'] || 'main-wrapper idimage-grid',
        autoHeight: true,
        paging: true,
        remoteSort: true,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: -10,
            getRowClass: function (rec) {
                var cls = [];
                if (rec.data['published'] != undefined && rec.data['published'] == 0) {
                    cls.push('idimage-row-unpublished');
                }
                if (rec.data['active'] != undefined && rec.data['active'] == 0) {
                    cls.push('idimage-row-inactive');
                }
                if (rec.data['deleted'] != undefined && rec.data['deleted'] == 1) {
                    cls.push('idimage-row-deleted');
                }
                if (rec.data['required'] != undefined && rec.data['required'] == 1) {
                    cls.push('idimage-row-required');
                }
                return cls.join(' ');
            }
        },
    });
    idimage.grid.Default.superclass.constructor.call(this, config);

    if (config.enableDragDrop && config.ddAction) {
        this.on('render', function (grid) {
            grid._initDD(config);
        });
    }
};
Ext.extend(idimage.grid.Default, MODx.grid.Grid, {

    getFields: function () {
        return [
            'id', 'actions'
        ];
    },

    getColumns: function () {
        return [{
            header: _('id'),
            dataIndex: 'id',
            width: 35,
            sortable: true,
        }, {
            header: _('idimage_actions'),
            dataIndex: 'actions',
            renderer: idimage.utils.renderActions,
            sortable: false,
            width: 75,
            id: 'actions'
        }];
    },

    getTopBar: function () {
        return ['->', this.getSearchField()];
    },

    getSearchField: function (width) {
        return {
            xtype: 'idimage-field-search',
            width: width || 250,
            listeners: {
                search: {
                    fn: function (field) {
                        this._doSearch(field);
                    }, scope: this
                },
                clear: {
                    fn: function (field) {
                        field.setValue('');
                        this._clearSearch();
                    }, scope: this
                },
            }
        };
    },

    widgetTotal: function (id) {
        return {
            xtype: 'displayfield',
            html: String.format('\
                  <table>\
                      <tr class="top">\
                          <td class="idimage_panel_info">{0} <span id="' + id + '-total_info">0</span></td>\
                      </tr>\
                  </table>',
                _('idimage_form_total'),
            ),
        };
    },
    total: 0,
    getListeners: function () {

        return {
            beforerender: function (grid) {
                var store = grid.getStore()
                store.on('load', function (res) {
                    idImageState()
                    if (res.reader && res.reader['jsonData']) {
                        grid.total = res.reader['jsonData']['total'];
                        var el = document.getElementById(grid.config.id + '-total_info')
                        if (el) {
                            el.innerText = grid.total;
                        }
                    }
                })
            },
        };
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);

        var menu = idimage.utils.getMenu(row.data['actions'], this, ids);

        this.addContextMenuItem(menu);
    },

    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof (row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        } else if (elem.nodeName == 'A' && elem.href.match(/(\?|\&)a=resource/)) {
            if (e.button == 1 || (e.button == 0 && e.ctrlKey == true)) {
                // Bypass
            } else if (elem.target && elem.target == '_blank') {
                // Bypass
            } else {
                e.preventDefault();
                MODx.loadPage('', elem.href);
            }
        }
        return this.processEvent('click', e);
    },

    refresh: function () {
        this.getStore().reload();
        if (this.config['enableDragDrop'] == true) {
            this.getSelectionModel().clearSelections(true);
        }
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    },

    _initDD: function (config) {
        var grid = this;
        var el = grid.getEl();

        new Ext.dd.DropTarget(el, {
            ddGroup: grid.ddGroup,
            notifyDrop: function (dd, e, data) {
                var store = grid.getStore();
                var target = store.getAt(dd.getDragData(e).rowIndex).id;
                var sources = [];
                if (data.selections.length < 1 || data.selections[0].id == target) {
                    return false;
                }
                for (var i in data.selections) {
                    if (!data.selections.hasOwnProperty(i)) {
                        continue;
                    }
                    var row = data.selections[i];
                    sources.push(row.id);
                }

                el.mask(_('loading'), 'x-mask-loading');
                MODx.Ajax.request({
                    url: config.url,
                    params: {
                        action: config.ddAction,
                        sources: Ext.util.JSON.encode(sources),
                        target: target,
                    },
                    listeners: {
                        success: {
                            fn: function () {
                                el.unmask();
                                grid.refresh();
                                if (typeof (grid.reloadTree) == 'function') {
                                    sources.push(target);
                                    grid.reloadTree(sources);
                                }
                            }, scope: grid
                        },
                        failure: {
                            fn: function () {
                                el.unmask();
                            }, scope: grid
                        },
                    }
                });
            },
        });
    },

    _loadStore: function () {
        this.store = new Ext.data.JsonStore({
            url: this.config.url,
            baseParams: this.config.baseParams || {action: this.config.action || 'getList'},
            fields: this.config.fields,
            root: 'results',
            totalProperty: 'total',
            remoteSort: this.config.remoteSort || false,
            storeId: this.config.storeId || Ext.id(),
            autoDestroy: true,
            listeners: {
                load: function (store, rows, data) {
                    store.sortInfo = {
                        field: data.params['sort'] || 'id',
                        direction: data.params['dir'] || 'ASC',
                    };
                    Ext.getCmp('modx-content').doLayout();
                }
            }
        });
    },


    _filterByCombo: function (cb) {
        this.getStore().baseParams[cb.name] = cb.value;
        this.getBottomToolbar().changePage(1);
    },

    action: function (method) {
        var ids = this._getSelectedIds()
        if (!ids.length) {
            return false
        }

        var grid = this;
        Ext.Msg.confirm(_('idimage_action_title'), _('idimage_action_confirm'), function (e) {

            if (e == 'yes') {
                idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
                MODx.Ajax.request({
                    url: grid.config.url,
                    params: {
                        action: 'mgr/' + grid.config.multiple + '/multiple',
                        method: method,
                        ids: Ext.util.JSON.encode(ids),
                    },
                    listeners: {
                        success: {
                            fn: function () {
                                grid.refresh()
                                idimage.progress.hide()
                            }, scope: this
                        },
                        failure: {
                            fn: function (r) {
                                MODx.msg.alert(_('error'), r.message);
                                idimage.progress.hide()
                                grid.refresh()
                            }, scope: this
                        }
                    }
                })
            }
        });

    },

    actionMenu: function (action, icon, one, cls) {
        var lex = action.replace(/\//g, '_'); // Заменяет все слеши


        var k = 'idimage_actions_' + lex;

        var label = _(k);

        if (label === undefined) {
            console.warn('lexicon not found: ' + k);
        }

        label = label === undefined ? action : label;

        icon = icon !== undefined ? '<i class="icon ' + icon + '"></i>&nbsp;' : '';

        var handlerFunction = () => this.actions(action);
        if (one !== true) {
            handlerFunction = () => this.actionsProgress(action);
        }

        var clsd = 'idimage-context-menu';
        if (cls) {
            clsd = clsd + ' ' + cls;
        }

        return {
            cls: clsd,
            text: icon + ' ' + label,
            handler: handlerFunction,
            scope: this
        };
    },


    actions: function (name) {

        var grid = this;
        Ext.Msg.confirm(_('idimage_actions_confirm_title'), _('idimage_actions_confirm_text'), function (e) {

            if (e == 'yes') {

                idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
                MODx.Ajax.request({
                    url: grid.config.url,
                    params: {
                        action: 'mgr/actions/' + name,
                    },
                    listeners: {
                        success: {
                            fn: function () {
                                idimage.progress.hide()
                                grid.refresh()
                            }, scope: this
                        },
                        failure: {
                            fn: function (r) {
                                idimage.progress.hide()
                                MODx.msg.alert(_('error'), r.message);
                                grid.refresh()
                            }, scope: this
                        }
                    }
                })
            }
        });

    },

    totalRecords: 0,
    iterations: null,
    iterationNext: 0,
    iterationPrevTotal: 0,
    progress: null,

    actionsCall: function (controller) {
        controller = controller.replace('mgr/actions/', '');

        if (this.iterations && this.iterations[this.iterationNext] && this.iterations[this.iterationNext].length > 0) {

            var ids = this.iterations[this.iterationNext];
            delete this.iterations[this.iterationNext]
            this.iterationNext++;
            this.iterationPrevTotal += ids.length;

            this.actionsAjax({
                action: 'mgr/actions/' + controller,
                ids: Ext.util.JSON.encode(ids)
            }, function (grid, response) {
                if (response.success) {
                    idimage.progress.updateText('Обработано ' + grid.iterationPrevTotal + ' из ' + grid.totalRecords)
                    grid.actionsCall(controller)
                }
            })

        } else {
            idimage.progress.hide()
            this.refresh()

            this.actionsProgressNext(controller)
        }
    },

    actionsProgressNext: function (name) {

        var nextTask;
        switch (name) {
            case 'product/task/indexed':
                nextTask = 'task/indexed'
                break;
            case 'product/task/upload':
                nextTask = 'task/upload'
                break;
            default:
                break;
        }

        if (nextTask) {
            this.actionsProgress(nextTask)
            /* Ext.MessageBox.show({
                 title: title,
                 msg: desc,
                 width: 500,
                 buttons: {
                     yes: _('idimage_process_task_btn_yes'),
                     cancel: _('idimage_process_task_btn_cancel'),
                 },
                 fn: function (e) {

                     // Обработает только выбранные записи со страницы
                     if (e == 'yes') {

                         return false
                     }

                     // Будут обрабатываться все найденные ресрурсы с учетом фильтров
                     if (e == 'no') {
                         console.log('Отмена')
                     }
                 },
                 icon: Ext.MessageBox.QUESTION
             })*/
        }
    },

    actionsProgress: function (controller) {

        var grid = this;
        // var ids = this._getSelectedIds();

        //var total = ids.length;

        var text = _('idimage_actions_confirm_text')

        /* if (!total) {
             total = this.total
         }*/
        var lex = controller.replace(/\//g, '_'); // Заменяет все слеши

        var label = _('idimage_actions_' + lex);

        text += '<span class="idimage_actions_window_info">' + _('actions') + ': <b>' + label + '</b></span>'


        /*  if (controller !== 'indexed/products') {
              if (total > 0) {
                  text += '<span class="idimage_actions_window_info">' + _('idimage_actions_selected_records') + ': <b>' + total + '</b></span>'
              }
          }*/

        var desc_key = 'idimage_actions_' + lex + '_desc';
        console.log(desc_key);
        var desc = _(desc_key);
        if (desc !== undefined) {
            text += '<span class="idimage_actions_window_info">' + desc + '</span>'
        }

        Ext.Msg.confirm(_('idimage_actions_confirm_title'), text, function (e) {

            if (e == 'yes') {

                var params = {
                    action: 'mgr/actions/' + controller,
                    steps: true
                }

                /* if (total > 0) {
                     params.ids = Ext.util.JSON.encode(ids)
                 }*/
                idimage.progress = Ext.MessageBox.wait('', _('please_wait'))
                grid.actionsAjax(params,
                    function (grid, response) {
                        if (response.success) {

                            grid.totalRecords = response.object.total
                            grid.iterationPrevTotal = 0;
                            grid.iterationNext = 0;
                            grid.iterations = response.object.iterations;

                            if (grid.totalRecords === 0) {
                                MODx.msg.alert(_('success'), 'Изменений не найдено')
                            } else {
                                idimage.progress.updateText('В обработке 0 из ' + grid.totalRecords)

                                grid.actionsCall(controller)
                            }

                        }
                    })

            }
        });
    },
    actionsAjax: function (params, callback) {
        //this.actions('upload')

        MODx.Ajax.request({
            url: this.config.url,
            params: params,
            listeners: {
                success: {
                    fn: function (response) {
                        callback(this, response);
                        //this.refresh()
                    }, scope: this
                },
                failure: {
                    fn: function (r) {
                        idImageState()
                        MODx.msg.alert(_('error'), r.message);
                        this.refresh()
                    }, scope: this
                }
            }
        })
    },

});
Ext.reg('idimage-grid-default', idimage.grid.Default);
