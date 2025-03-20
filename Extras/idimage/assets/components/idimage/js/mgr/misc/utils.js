Ext.Loader.load([
    MODx.config.assets_url + 'components/idimage/js/mgr/misc/strftime-min-1.3.js'
]);

idimage.utils.formatDate = function (string) {
    if (string && string != '0000-00-00 00:00:00' && string != '-1-11-30 00:00:00' && string != 0) {
        var date = /^[0-9]+$/.test(string)
            ? new Date(string * 1000)
            : new Date(string.replace(/(\d+)-(\d+)-(\d+)/, '$2/$3/$1'));
        return date.strftime(idimage.config['date_format']);
    } else {
        return '&nbsp;';
    }
};

idimage.utils.renderBoolean = function (value) {
    return value
        ? String.format('<span class="green">{0}</span>', _('yes'))
        : String.format('<span class="red">{0}</span>', _('no'));
};

idimage.utils.getMenu = function (actions, grid, selected) {
    var menu = [];
    var cls, icon, title, action;

    var has_delete = false;
    for (var i in actions) {
        if (!actions.hasOwnProperty(i)) {
            continue;
        }

        var a = actions[i];
        if (!a['menu']) {
            if (a == '-') {
                menu.push('-');
            }
            continue;
        } else if (menu.length > 0 && !has_delete && (/^remove/i.test(a['action']) || /^delete/i.test(a['action']))) {
            menu.push('-');
            has_delete = true;
        }

        if (selected.length > 1) {
            if (!a['multiple']) {
                continue;
            } else if (typeof (a['multiple']) == 'string') {
                a['title'] = a['multiple'];
            }
        }

        icon = a['icon'] ? a['icon'] : '';
        if (typeof (a['cls']) == 'object') {
            if (typeof (a['cls']['menu']) != 'undefined') {
                icon += ' ' + a['cls']['menu'];
            }
        } else {
            cls = a['cls'] ? a['cls'] : '';
        }
        title = a['title'] ? a['title'] : a['title'];
        action = a['action'] ? grid[a['action']] : '';

        menu.push({
            handler: action,
            text: String.format(
                '<span class="{0}"><i class="x-menu-item-icon {1}"></i>{2}</span>',
                cls, icon, title
            ),
            scope: grid
        });
    }

    return menu;
};

idimage.utils.renderActions = function (value, props, row) {
    var res = [];
    var cls, icon, title, action, item;
    for (var i in row.data.actions) {
        if (!row.data.actions.hasOwnProperty(i)) {
            continue;
        }
        var a = row.data.actions[i];
        if (!a['button']) {
            continue;
        }

        icon = a['icon'] ? a['icon'] : '';
        if (typeof (a['cls']) == 'object') {
            if (typeof (a['cls']['button']) != 'undefined') {
                icon += ' ' + a['cls']['button'];
            }
        } else {
            cls = a['cls'] ? a['cls'] : '';
        }
        action = a['action'] ? a['action'] : '';
        title = a['title'] ? a['title'] : '';

        item = String.format(
            '<li class="{0}"><button class="idimage-btn idimage-btn-default {1}" action="{2}" title="{3}"></button></li>',
            cls, icon, action, title
        );

        res.push(item);
    }

    return String.format(
        '<ul class="idimage-row-actions">{0}</ul>',
        res.join('')
    );
};


idimage.utils.userLink = function (value, id, blank) {
    if (!value) {
        return '';
    } else if (!id) {
        return value;
    }

    return String.format(
        '<a href="?a=security/user/update&id={0}" class="ms2-link" target="{1}">{2}</a>',
        id,
        (blank ? '_blank' : '_self'),
        value
    );
};

idimage.utils.resourceLink = function (value, id, blank) {
    if (!value) {
        return '';
    } else if (!id) {
        return value;
    }

    return String.format(
        '<a href="?a=resource/update&id={0}" class="ms2-link" target="{1}">{2}</a>',
        value,
        (blank ? '_blank' : '_self'),
        value
    );
};
idimage.utils.resourceLinkProduct = function (value, id, r) {
    if (!value) {
        return '';
    } else if (!id) {
        return value;
    }

    var pid = r.data.pid
    var max = r.data.max_scope || '-';
    var min = r.data.min_scope || '-';
    var search = r.data.search_scope || '-';
    var total = r.data.total || '-';

    var ball = String.format(_('idimage_close_ball'), max, min, search, total);

    return String.format(
        '<a href="?a=resource/update&id={0}" class="ms2-link" target="{1}">{2}</a><br>' +
        '<span class="idimage-product-id">Product ID: {3}</span><div class="idimage-ball">' + ball + '</div>',
        pid,
        '_blank',
        value,
        pid
    );
};

idimage.utils.statusClose = function (value) {
    var status = idimage.config.status_map[value]
    return String.format(
        '<span class="idimage-status idimage-status-color-{0}">{0}</span>',
        status
    );
};
idimage.utils.statusTask = function (value, col, row) {
    var msg = row.data.msg || ''
    var can_be_launched = row.data.can_be_launched || ''
    if (msg) {
        var color = value === 'failed' ? 'red' : 'green'
        msg = String.format('<br><span class="idimage-status-msg ' + color + '">{0}</span>', msg);
    }
    return String.format(
        '<span class="idimage-status idimage-status-color-{0}">{0}</span>{1}{2}',
        value,
        msg,
        can_be_launched
    );
};

idimage.utils.jsonDataTags = function (value) {
    v = value ? value.join(", ") : '';
    return v !== '' ? '<span>' + v + '</span>' : '<span class="idimage-gray">---</span>';
};

idimage.utils.jsonDataError = function (value) {
    v = value ? JSON.stringify(value) : '';
    return v !== '' ? '<span class="red">' + v + '</span>' : '<span class="idimage-gray">---</span>';
};

idimage.utils.formatPrice = function (value) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB'
    }).format(value);
};


idimage.utils.renderImage = function (value, row, r) {
    if (Ext.isEmpty(value)) {
        value = idimage.config['default_thumb'];
    } else {
        if (!/\/\//.test(value)) {
            if (!/^\//.test(value)) {
                value = '/' + value;
            }
        }
    }
    var pid = r.data.pid
    return String.format('<a class="idimage-image-link" href="/index.php?id=' + pid + '" target="_blank"><img src="{0}" /></a>', value);
};


idimage.utils.renderImages = function (images) {

    var output = [];
    var out
    var probability
    for (var i = 0; i < images.length; i++) {
        var r = images[i];
        if (r.image) {
            out = String.format('<a class="idimage-image-link" href="/index.php?id={1}" target="_blank"><img src="{0}" /></a>', r.image, r.pid)
            probability = String.format('<div class="idimage-similar-probability" title="{1}">{0}%</div>', r.probability, _('idimage_probability'))
            output.push('<div class="idimage-similar-wrapper"><div class="idimage-similar-image">' + out + '</div>' + probability + '</div>')
        } else {
            output.push('<div class="idimage-similar-wrapper"><div class="idimage-similar-image"><div class="idimage-placeholder">ПОХОЖИЕ</div></div></div>');
        }
        //String.format('<a class="idimage-image-link" href="/index.php?id=' + pid + '" target="_blank"><img src="{0}" /></a>', value)
    }

    return '<div class="idimage-similar-images">' + output.join('') + '</div>';
};


