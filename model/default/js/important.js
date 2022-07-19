/* 自定义函数集（置顶，此时Vue等未加载） */

function random(a, b) {
    // 代码来源：https://www.runoob.com/w3cnote/js-random.html
    switch (arguments.length) {
        case 0:
            a = 100000000;
            // 不break则会跳到下一个case。正好用到！
        case 1:
            return parseInt(Math.random() * a + 1, 10);
        case 2:
            return parseInt(Math.random() * (b - a + 1) + a, 10);
    } 
}
function makeAvatarUrl(ud) {
    if (! ud)
        return null;
    return `<?= u('static://user/avatar') ?>/` + ud.id + '.jpg?v=' + ud.avatarVersion;
}
function getUrlParam(name) {
    /* 来源：https://www.jb51.net/article/73896.htm */
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)');
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return unescape(r[2]);
    } else {
        return null;
    }
}
function getCookie(name) {
    return $.cookie(name);
}
function setCookie(name, value) {
    var _value = (value ? value : '');
    $.cookie(name, _value, { path: '/', domain: `<?= c::$COOKIE_MAINDOMAIN ?>` });
}
// 给cookie主站一个特定cookie，用于标识是否主站
setCookie('mainDomain', '1');
function copy(data) {
    /* 参考：https://www.jianshu.com/p/d6d25bd73e2f */
    var aux = document.createElement('input');
    var content = data;
    aux.setAttribute('value', content);
    document.body.appendChild(aux);
    aux.select();
    document.execCommand('copy');
    document.body.removeChild(aux);
}
function copyIt(text) {
    copy(text);
    if (window.t) succMsg('复制成功！');
}
function sizeDesc(size) {
    if (typeof size != 'number') {
        size = 0;
    }
    var sizeTable = [
        [0, 'B'],
        [1024, 'KB'],
        [1024 * 1024, 'MB'],
        [1024 * 1024 * 1024, 'GB'],
        [1024 * 1024 * 1024 * 1024, 'TB'],
        [1024 * 1024 * 1024 * 1024 * 1024, 'PB'],
        [1024 * 1024 * 1024 * 1024 * 1024 * 1024, 'EB']
    ];
    for (var k in sizeTable) {
        var v1 = sizeTable[k][0];
        var pv1, pv2;
        if (sizeTable[k - 1]) {
            pv1 = sizeTable[k - 1][0];
            pv2 = sizeTable[k - 1][1];
        }
        if (size < v1) {
            return '' + (size / (pv1 ? pv1 : 1)).toFixed(2) + ' ' + pv2;
        }
    }
    var lv1 = sizeTable[sizeTable.length - 1][0];
    var lv2 = sizeTable[sizeTable.length - 1][1];
    return '' + (size / lv1).toFixed(2) + ' ' + lv2;
}
function fill0(num, dig, zero) {
    var _num = '' + num, _zero = (zero ? zero : '0');
    var result = _num;
    for (var i = 0; i < (dig ? dig : 2) - _num.length; i ++) {
        result = _zero + result;
    }
    return result;
}
function timeDesc(time) {
    if (! parseInt(time)) {
        return '';
    }
    var date = new Date(parseInt(time));
    var y, m, d, h, i, s;
    y = date.getFullYear();
    m = fill0(date.getMonth() + 1);
    d = fill0(date.getDate());
    h = fill0(date.getHours());
    i = fill0(date.getMinutes());
    s = fill0(date.getSeconds());
    return y + '/' + m + '/' + d + ' ' + h + ':' + i + ':' + s;
}
function inArray(item, arr) {
    for (var k in arr) {
        var v = arr[k];
        if (v === item) {
            return true;
        }
    }
    return false;
}
function isWechatOrQQ() {
    /* 判断是否微信、QQ内置浏览器。代码来源：https://www.cnblogs.com/love314159/p/10790533.html */
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return 1;
    } else if (ua.match(/QQ/i) == 'qq') {
        return 2;
    } else {
        return false;
    }
}