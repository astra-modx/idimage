idimage.window.DefaultComboExt = function (config) {
    config = config || {}
    Ext.applyIf(config, {
        title: '',
    })
    idimage.window.DefaultComboExt.superclass.constructor.call(this, config)
    this.on('hide', function () {
        var w = this
        window.setTimeout(function () {
            w.close()
        }, 200)
    })
}
Ext.extend(idimage.window.DefaultComboExt, MODx.Window, {
    progress: true,
    cyclicQuery: function ($this) {

        var total = idimage.listRecords.length

        var ix, j, temparray, chunk = idimage.config.max_records_processed
        for (ix = 0, j = idimage.listRecords.length; ix < j; ix += chunk) {
            if (!idimage.listRecords.hasOwnProperty(ix)) {
                continue
            }

            if (ix === idimage.recordCount) {
                temparray = mspre.listRecords.slice(ix, ix + chunk)
                idimage.message_wait = _('mspre_treated_resources') + ' ' + mspre.recordCount + ' ' + _('mspre_from') + ' ' + total + ''
                idimage.recordCount = mspre.recordCount + temparray.length

                idimage.formExt.setValues({
                    ids: [Ext.util.JSON.encode(temparray)]
                })

                if (idimage.listRecords.length !== idimage.recordCount) {
                    $this.submit($this.cyclicQuery)
                } else {
                    $this.submit()
                }
                return true
            }
        }
    },
    isProgress: function ($this) {
        var $thisProgress = this;

        if (!idimage.offsetCyclic) {
            if (this.progress) {

                idimage.formExt = this.fp.getForm()
                var values = mspre.formExt.getValues()
                idimage.recordCount = 0
                idimage.listRecords = Ext.util.JSON.decode(values.ids)
                idimage.disableRefresh = true
                idimage.offsetCyclic = true

                // Режим эксперт
                if (idimage.config.mode_expert) {
                    var total_all = Ext.get('idimage-panel-info-total_info').dom.innerText
                    var total_selected = idimage.listRecords.length
                    Ext.MessageBox.show({
                        title: _('warning'),
                        msg: _('idimage_expert_mode_confirm') + idimage.config.max_records_processed_all,
                        width: 500,
                        buttons: {
                            yes: _('idimage_process_btn_yes') + ' (' + _('idimage_process_total') + ' ' + total_selected + ')',
                            no: _('idimage_process_btn_no') + ' (' + _('idimage_process_all_total') + ' ' + total_all + ')',
                            cancel: _('idimage_process_btn_cancel'),
                        },
                        fn: function (e) {

                            // Обработает только выбранные записи со страницы
                            if (e == 'yes') {
                                $this.cyclicQuery($this)
                                return false
                            }


                            // Будут обрабатываться все найденные ресрурсы с учетом фильтров
                            if (e == 'no') {

                                var grid = Ext.getCmp(idimage.config.grid_id)
                                var baseParamsCyclic = {cyclic: true, limit: idimage.config.max_records_processed_all}
                                for (var keyParam in grid.baseParams) {
                                    if (grid.baseParams.hasOwnProperty(keyParam)) {
                                        baseParamsCyclic[keyParam] = grid.baseParams[keyParam]
                                    }
                                }


                                MODx.Ajax.request({
                                    url: idimage.config.connector_url,
                                    params: baseParamsCyclic,
                                    listeners: {
                                        success: {
                                            fn: function (r) {
                                                if (r.success && r.total !== 0) {
                                                    idimage.listRecords = r.results
                                                    //idimage.listRecords = Ext.util.JSON.encode(r.results)
                                                    $thisProgress.cyclicQuery($this)
                                                    return false
                                                }
                                            },
                                            scope: this
                                        },
                                        failure: {
                                            fn: function (response) {
                                                MODx.msg.alert(_('idimage_error'), response.message)
                                            },
                                            scope: this
                                        }
                                    }
                                })

                            }
                        },
                        icon: Ext.MessageBox.QUESTION
                    })
                } else {
                    this.cyclicQuery($this)
                }

                return true
            }
        }

        return false
    },
    submit: function (callback) {

        var $this = this

        close = close === false ? false : true
        var f = this.fp.getForm()
        if (f.isValid() && this.fireEvent('beforeSubmit', f.getValues())) {

            var elem = Ext.getCmp(this.id)
            if (!elem.isProgress($this)) {

                f.submit({
                    //waitMsg: _('saving')
                    waitMsg: idimage.message_wait
                    , submitEmptyText: this.config.submitEmptyText !== false
                    , scope: this
                    , failure: function (frm, a) {
                        if (this.fireEvent('failure', {f: frm, a: a})) {
                            MODx.form.Handler.errorExt(a.result, frm)
                        }
                        this.doLayout()
                    }
                    , success: function (frm, a) {

                        if (typeof callback === 'function') {
                            callback($this)
                        } else {
                            if (this.config.success) {
                                Ext.callback(this.config.success, this.config.scope || this, [frm, a])
                            }
                            this.fireEvent('success', {f: frm, a: a})
                            if (close) {
                                this.config.closeAction !== 'close' ? this.hide() : this.close()
                            }
                            this.doLayout()

                            // Сбрасываем
                            idimage.offsetCyclic = false
                            idimage.disableRefresh = false
                        }
                    }
                })

            }
        }
    }
})
Ext.reg('idimage-cyclic', idimage.window.DefaultComboExt)
