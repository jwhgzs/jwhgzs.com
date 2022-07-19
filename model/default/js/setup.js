/* 全局配置区 */

var Component_UserAvatar = {
    methods: { makeAvatarUrl: makeAvatarUrl },
    props: ['udata_', 'size'],
    template: `
        <!-- error事件返回true，即发生错误时自动显示插槽 -->
        <el-avatar class="imgAwesome" :src="makeAvatarUrl(udata_)" :size="size ? size : 42" @error="true">
            <i class="fas fa-user useravatar_erricon"></i>
        </el-avatar>
    `
};
var Component_UserInfobox = {
    components: { 'user-avatar': Component_UserAvatar },
    methods: { makeAvatarUrl: makeAvatarUrl, timeDesc: timeDesc },
    props: ['udata_', 'time', 'single'],
    template: `
        <el-row style="width: 100%;">
            <el-col :span="" style="margin-right: 10px;">
                <a class="aAwesome" target="_blank" :href="'<?= u('local://user/ucenter') ?>?uid=' + udata_.id">
                    <user-avatar :udata_="udata_"></user-avatar>
                </a>
            </el-col>
            <el-col :span="" :class="{ vcenter: (single ? true : false) }">
                <a class="aAwesome" target="_blank" :href="'<?= u('local://user/ucenter') ?>?uid=' + udata_.id">
                    <span v-if="! udata_.name" style="font-size: 80%; color: gray;">
                        <i class="fas fa-spinner rotate"></i>
                        加载中……
                    </span>
                    <span :class="{ userinfobox_uname: true, authed_uname: (udata_.userGroup || udata_.userAuth) }">
                        {{ udata_.name }}
                    </span>
                    <el-tag v-if="udata_.userClassify" style="margin-left: 5px;" type="success" size="small">{{ udata_.userClassify }}</el-tag>
                </a>
                <div v-if="time" class="userinfobox_time">{{ timeDesc(time) }}</div>
            </el-col>
        </el-row>
    `
};
var Component_Quill = {
    data() {
        return {
            id: 'quill_' + random(),
            quill: null,
            toolbar: null
        };
    },
    mounted() {
        var s = this;
        /* quill配置参考：https://www.jianshu.com/p/b237372f15cc */
        s.quill = new Quill('#' + s.id, {
            modules: {
                toolbar: [
                    [{ 'size': ['small', 'large', 'huge', false] }],
                    
                    ['bold', 'italic', 'underline', 'strike'],
                    
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [ 'link', 'image' ],
                    
                    ['clean']
                ]
            },
            theme: 'snow'
        });
        s.quill.on('text-change', function() {
            var ncontent = $('#' + s.id + ' .ql-editor').html();
            s.$emit('update:modelValue', ncontent);
            s.$emit('input');
        });
    },
    watch: {
        modelValue() {
            var s = this;
            // 防止不是用户输入改变quill本身内容触发（自建的v-model导致）的，否则会导致用户输入不了
            var ncontent = $('#' + s.id + ' .ql-editor').html();
            if (s.modelValue == ncontent) return;
            $('#' + s.id + ' .ql-editor').html(s.modelValue);
        }
    },
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: `
        <div :id="id" class="quill"></div>
    `
};
var Component_QuillContainer = {
    components: { 'el-divider': ElementPlus.ElDivider },
    props: ['title', 'formStyle'],
    template: `
        <div :class="{ quill_container: true, quill_container_formstyle: formStyle }">
            <el-divider>{{ title }}</el-divider>
            <slot></slot>
        </div>
    `
};

var jumpIt = function() {
    document.location.href = "<?= u('local://user/login') ?>?from=" + encodeURIComponent(document.location.href);
};
var jump = function() {
    setCookie('userToken', '');
    jumpIt();
};
var buildVueConfig = function() {
    var vueConfig_data = vueConfig.data();
    vueConfig_data = $.extend(vueConfig_data, {
        loopThreadFirstRun: false,
        loopThreadFirstPost: false,
        uploadMultiple_: false,
        uploadAccept_: '',
        uploadSelectOK_: null,
        dialogs: [],
        
        nav_titles: [],
        nav_urls: [],
        nav_mustLogin: false,
        mustLogin: false,
        isLoggedin: getCookie('userToken'),
        isMainDomain: getCookie('mainDomain'),
        udata: false,
        window_w: 0,
        window_h: 0
    });
    vueConfig.data = function() {
        return vueConfig_data;
    };
    
    vueConfig.methods = $.extend(vueConfig.methods, {
        jumpLogin: jumpIt,
        jump(url) {
            document.location.href = url;
        },
        jump2(url) {
            window.open(url);
        },
        logout() {
            confMsg('确定要退出登录咩？', 'warning', function() {
                setCookie('userToken', '');
                t.jumpLogin();
            });
        },
        dialogClose(i, type) {
            if (type == 0) {
                if (t.dialogs[i].callback_cancel) {
                    t.dialogs[i].callback_cancel(t.dialogs[i].value);
                }
            } else if (type == 1) {
                if (t.dialogs[i].callback_ok) {
                    t.dialogs[i].callback_ok(t.dialogs[i].value);
                }
            }
            t.dialogs[i].status = false;
        },
        isAdmin() {
            if (t.udata)
                return inArray(parseInt(t.udata.id), t.udata.adminUids);
            else
                return false;
        }
    });
    
    vueConfig.computed = $.extend(vueConfig.computed, {
        avatarUrl() {
            return makeAvatarUrl();
        }
    });
    
    var readWindowSize = function() {
        t.window_w = window.screen.availWidth;
        t.window_h = window.screen.availHeight;
    };
    var autoAdapt = function() {
        // 这里自适应（tail位置）抄了之前一版（layui版）九尾狐官网的代码 嘿嘿嘿
        var content_el = $('#content'), window_h = t.window_h, header_h = $('#header').height(), tail_h = $('#tail').height();
        var content_h = window_h - header_h - tail_h;
        content_el.attr('style', 'min-height: ' + content_h.toFixed(0) + 'px;');
    };
    var setupCookie = function() {
        if ((! getCookie('userToken')) && t.nav_mustLogin) {
            jump();
        }
    };
    var vueConfig_mounted = vueConfig.mounted;
    vueConfig.created = function() {
        t = this;
    };
    vueConfig.mounted = function() {
        window.onresize = readWindowSize;
        readWindowSize();
        autoAdapt();
        setupCookie();
        
        vueConfig_mounted();
        loopThread();
    };
    
    vueConfig.watch = $.extend(vueConfig.watch, {
        window_h() {
            autoAdapt();
        }
    });
    
    vueConfig.components = {
        'user-avatar': Component_UserAvatar,
        'user-infobox': Component_UserInfobox,
        'quill': Component_Quill,
        'quill-container': Component_QuillContainer
    };
};
var loopThread = function() {
    t.loopThreadFirstPost = false
    t.loopThreadFirstRun = false;
    
    var thread = function() {
        t.loopThreadFirstRun = true;
        if (window.appThread) appThread();
        
        if (! getCookie('userToken')) return;
        
        post({
            name: '用户数据同步',
            url: "<?= u('local://api/user/data') ?>",
            data: {},
            callback_ok: function(data) {
                t.udata = data.data.userData;
                t.loopThreadFirstPost = true;
            },
            callback_err: function() {
                setTimeout(jump, 3000);
            },
            callback_err2: function() {
                jump();
            },
            hideSuccUI: true,
            hideLoadingUI: true,
            errText: '即将跳转登录~'
        });
    };
    var threadIntervalId = null;
    var startThread = function() {
        if (t.nav_mustLogin && (! getCookie('userToken'))) {
            jumpIt();
            return;
        }
        if (threadIntervalId) return;
        threadIntervalId = setInterval(thread, <?= c::$JSTHREAD_INTERVAL ?>);
        thread();
    };
    var stopThread = function() {
        clearInterval(threadIntervalId);
        threadIntervalId = null;
    };
    
    startThread();
    document.addEventListener('visibilitychange', function() {
        var isHidden = document.hidden;
        if (! isHidden) {
            startThread();
        } else {
            stopThread();
        }
    }, false);
};

buildVueConfig();
Vue.createApp(vueConfig)
    .use(ElementPlus, {
            locale: ElementPlusLocaleZhCn
        })
    .mount('#body');