/* 自定义函数集 */

function isNum(i) {
    return (parseInt(i + 1) > 0);
}
function setup(arr, mustLogin = false) {
    var a1 = [], a2 = [];
    for (var i in arr) {
        var v = arr[i];
        if (isNum(i)) {
            a1.push(v);
            a2.push('/');
        } else {
            a1.push(i);
            a2.push(v ? v : '/');
        }
    }
    t.nav_titles = a1;
    t.nav_urls = a2;
    t.nav_mustLogin = mustLogin;
}
var m = ElementPlus.ElMessageBox;
function infoMsg(content, callback) {
    return t.dialogs.push({
        status: true,
        canClose: true,
        canCancel: false,
        scene: 'info',
        title: '提示',
        content: content,
        callback_ok: callback,
        callback_cancel: callback
    }) - 1;
}
function succMsg(content, callback) {
    return t.dialogs.push({
        status: true,
        canClose: true,
        canCancel: false,
        scene: 'success',
        title: '成功',
        content: content,
        callback_ok: callback,
        callback_cancel: callback
    }) - 1;
}
function warnMsg(content, callback) {
    return t.dialogs.push({
        status: true,
        canClose: true,
        canCancel: false,
        scene: 'warning',
        title: '警告',
        content: content,
        callback_ok: callback,
        callback_cancel: callback
    }) - 1;
}
function errMsg(content, callback) {
    return t.dialogs.push({
        status: true,
        canClose: true,
        canCancel: false,
        scene: 'danger',
        title: '失败',
        content: content,
        callback_ok: callback,
        callback_cancel: callback
    }) - 1;
}
function confMsg(content, type, callback_ok, callback_cancel, canClose = true) {
    return t.dialogs.push({
        status: true,
        canClose: canClose,
        canCancel: canClose,
        scene: type,
        title: '确认',
        content: content,
        callback_ok: callback_ok,
        callback_cancel: callback_cancel
    }) - 1;
}
function inputMsg(content, inputType, callback_ok, callback_cancel, canClose = true, defaultValue = null, inputOptions = null) {
    return t.dialogs.push({
        status: true,
        canClose: canClose,
        canCancel: canClose,
        scene: 'input',
        title: '输入',
        content: content,
        inputType: inputType,
        value: defaultValue,
        options: inputOptions,
        callback_ok: callback_ok,
        callback_cancel: callback_cancel
    }) - 1;
}
function loadingMsg(content, defaultPercent = 0) {
    return t.dialogs.push({
        status: true,
        canClose: false,
        canCancel: false,
        hideOK: true,
        scene: 'loading',
        title: '加载中',
        content: content,
        inputType: 'loading',
        value: defaultPercent
    }) - 1;
}
function closeLoadingMsg(id) {
    t.dialogs[id].status = false;
}
function editLoadingMsg_title(id, title) {
    t.dialogs[id].title = title;
}
function editLoadingMsg_percent(id, percent) {
    t.dialogs[id].value = percent.toFixed(0);
}
function post(json) {
    /* 
        参数json结构：
        {'name', 'url', 'data', 'file(可选)', 'callback_ok(可选)', 'callback_ok2(可选，填1为自动跳转至参数jumpUrl的URL)', 'jumpUrl(可选)', 'callback_err(可选)', 'callback_err2(可选)', 'succText(可选)', 'errText(可选)', 'hideUI(可选)', 'hideLoadingUI(可选)', 'hideSuccUI(可选)'}
     */
    var callback = json.callback_ok, callback2, succText = json.succText, errText = json.errText, hideUI = json.hideUI, hideLoadingUI = json.hideLoadingUI, hideSuccUI = json.hideSuccUI;
    
    if (getCookie('userToken'))
        json.data.userToken = getCookie('userToken');
    var data = new FormData();
    data.append('json', JSON.stringify(json.data));
    if (json.file)
        data.append('file', json.file);
    
    var sizeLimit = parseInt('<?= c::$UPLOAD_SIZELIMIT ?>');
    // '&&'的特性：第一个条件不行后面就直接不跑了，直接返回false
    if ((json.file && json.file.size > sizeLimit) || JSON.stringify(json.data).length > sizeLimit) {
        if (! hideUI) {
            errMsg('<strong>' + json.name + '失败：</strong>提交数据过大！', function() {
                if (json.callback_err2)
                    json.callback_err2(1, data);
            });
        }
        return;
    }
    
    try {
        var loadingMsgId = 0;
        $.ajax({
            url: json.url,
            type: 'POST',
            dataType: 'json',
            data: data,
            /* 又踩坑了，提交格式为FromData必须要设置下面这两项： */
            contentType: false,
            processData: false,
            xhr: function() {
                // 重写构造xhr方法，设置请求进度回调
                var xhr = new XMLHttpRequest();
                xhr.upload.onprogress = function(data) {
                    var percent = (data.loaded / data.total) * 100;
                    if (! (hideUI || hideLoadingUI))
                        editLoadingMsg_percent(loadingMsgId, percent);
                };
                return xhr;
            },
            success: function(data) {
                if (! (hideUI || hideLoadingUI)) {
                    closeLoadingMsg(loadingMsgId);
                }
                if (data.status == 1) {
                    try { callback(data); } catch (ex) {}
                    
                    if (json.callback_ok2 === 1) {
                        callback2 = function() {
                            document.location.href = json.jumpUrl;
                        };
                        setTimeout(function() {
                            callback2(data);
                        }, 3000);
                    } else {
                        callback2 = json.callback_ok2;
                    }
                    
                    if (! (hideUI || hideSuccUI)) {
                        succMsg('<strong>' + json.name + '成功！</strong>' + data.msg + (succText ? succText : ''), function() {
                            if (callback2)
                                callback2(data);
                        });
                    }
                } else {
                    if (json.callback_err)
                        json.callback_err(1, data);
                    if (! hideUI) {
                        errMsg('<strong>' + json.name + '失败：</strong>' + data.msg + (errText ? errText : ''), function() {
                            if (json.callback_err2)
                                json.callback_err2(1, data);
                        });
                    }
                }
            },
            error: function(xhr) {
                if (! (hideUI || hideLoadingUI)) {
                    closeLoadingMsg(loadingMsgId);
                }
                if (! hideUI) {
                    errMsg('<strong>' + json.name + '失败：</strong>网络请求失败！' + (errText ? errText : ''), function() {
                        if (json.callback_err2)
                            json.callback_err2(0, xhr);
                    });
                }
                if (json.callback_err)
                    json.callback_err(0, xhr);
            }
        });
    } catch (ex) {
        if (! (hideUI || hideLoadingUI)) {
            closeLoadingMsg(loadingMsgId);
        }
        if (! hideUI) {
            errMsg('<strong>' + json.name + '失败：</strong>处理请求出错！' + (errText ? errText : ''), function() {
                if (json.callback_err2)
                    json.callback_err2(0, null);
            });
        }
    }
    if (! (hideUI || hideLoadingUI)) {
        loadingMsgId = loadingMsg(json.name + '请求中……', 0);
    }
}
function selectFile(callback) {
    var input = $('#fileUploader');
    input.click();
    input.change(function() {
        var data = $(this)[0].files[0];
        input.unbind();
        input.val('');
        var sizeLimit = parseInt(`<?= '' . c::$UPLOAD_SIZELIMIT ?>`);
        if (data.size > sizeLimit) {
            errMsg('你选择的文件太大了哦~（大小限制：' + sizeDesc(sizeLimit) + '）');
            return;
        }
        callback(data);
    });
}

var vaptchaObj;
function vaptchaGo(scene, callback_ok, callback_close, callback_loaded) {
    var loadingId = loadingMsg('验证码加载中……');
    vaptchaObj = null;
    vaptcha({
        vid: `<?= s::$VAPTCHA_CONFIG['vid'] ?>`,
        mode: 'invisible',
        scene: scene,
        area: 'auto'
    }).then(function(_vaptchaObj) {
        vaptchaObj = _vaptchaObj;
        vaptchaObj.listen('pass', function() {
            var serverToken = vaptchaObj.getServerToken();
            if (callback_ok) {
                callback_ok({
                    vaptchaData: {
                        server: serverToken.server,
                        token: serverToken.token,
                        scene: scene
                    }
                });
            }
        });
        vaptchaObj.listen('close', function() {
            if (callback_close)
                callback_close();
        });
        vaptchaObj.validate();
        if (callback_loaded)
            callback_loaded();
        closeLoadingMsg(loadingId);
    });
}

/* 现已用dialog独立重写了对话框系统（原来用的是elementplus自带的messagebox），无该BUG了 */
/* 修复el-message的textarea回车无法换行的BUG */
/*
$(document).on('keydown', '.el-message-box__input .el-textarea__inner', function(e) {
    if (e.keyCode == 13) {
        var v = $(this).val();
        $(this).val(v + '\n');
        // 定位滚动条至光标位置
        $(this).blur();
        $(this).focus();
    }
});
*/

/* 单页面设置的行内style处理 */
var styles = $('#body style').each(function() {
    // 注意这里v是jquery对象
    var v = $(this);
    var new_style = $('<style type="text/css" handled></style>');
    new_style.html(v.html());
    new_style.appendTo('head');
    v.remove();
});