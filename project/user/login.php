<div class="box box_sm hcenter">
    <span class="box_title" v-html="title"></span>
    <span class="box_graytitle" v-html="grayTitle"></span>
    <el-divider></el-divider>
    <el-form :model="formData" :rules="formVerify" label-position="top">
        <div v-if="type != 1">
            <el-form-item label="用户名" prop="name">
                <el-input type="text" v-model="formData.name">
                    <template #prefix>
                        <i class="fas fa-user"></i>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item label="密码" prop="pass">
                <el-input type="password" :show-password="true" v-model="formData.pass">
                    <template #prefix>
                        <i class="fas fa-key"></i>
                    </template>
                </el-input>
            </el-form-item>
        </div>
        <div v-if="type == 2">
            <el-form-item label="确认密码" prop="pass2">
                <el-input type="password" :show-password="true" v-model="formData.pass2">
                    <template #prefix>
                        <i class="fas fa-key"></i>
                    </template>
                </el-input>
            </el-form-item>
        </div>
        <div v-if="type == 1 || type == 2">
            <el-form-item label="手机号" prop="phone">
                <el-input type="number" style="margin-top: 5px; margin-bottom: 5px;" v-model="formData.phone">
                    <template #prefix>
                        <i class="fas fa-phone"></i>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item label="手机验证码" prop="phoneVerify">
                <el-input type="number" v-model="formData.phoneVerify">
                    <template #prefix>
                        <i class="fas fa-ticket-alt"></i>
                    </template>
                    <template #append>
                        <el-button text bg style="font-size: 80%;" @click="sendPhoneVerify">
                            发送
                        </el-button>
                    </template>
                </el-input>
            </el-form-item>
        </div>
        <div style="margin-top: 60px;">
            <el-button type="primary" :text="type != 0" size="large" round @click="doit(0, type == 0)">
                {{ type == 0 ? '立即登录' : '账号密码登录' }}
            </el-button>
            <el-button type="primary" :text="type != 1" size="large" round @click="doit(1, type == 1)">
                {{ type == 1 ? '立即登录' : '手机号登录' }}
            </el-button>
            <el-button type="primary" :text="type != 2" size="large" round @click="doit(2, type == 2)">
                {{ type == 2 ? '立即注册' : '注册' }}
            </el-button>
            <div class="box_tip">提示：忘记用户名/密码了可以用手机号登录哦！</div>
        </div>
    </el-form>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                isLoginPage: true,
                type: 0,
                title: '登录',
                grayTitle: 'LOGIN',
                isSentFirstPhoneVerify: false,
                
                formVerify: {
                    name: [
                        {
                            required: true,
                            message: '请输入用户名',
                            trigger: 'blur'
                        },
                        {
                            min: <?= c::$USERINF_LENGTH['name_min'] ?>,
                            max: <?= c::$USERINF_LENGTH['name_max'] ?>,
                            message: `用户名长度必须在<?= '' . c::$USERINF_LENGTH['name_min'] ?>~<?= '' . c::$USERINF_LENGTH['name_max'] ?>之间哦~`,
                            trigger: 'blur'
                        }
                    ],
                    pass: [
                        {
                            required: true,
                            message: '请输入密码',
                            trigger: 'blur'
                        },
                        {
                            min: <?= '' . c::$USERINF_LENGTH['pass_min'] ?>,
                            max: <?= '' . c::$USERINF_LENGTH['pass_max'] ?>,
                            message: `密码长度必须在<?= '' . c::$USERINF_LENGTH['pass_min'] ?>~<?= '' . c::$USERINF_LENGTH['pass_max'] ?>之间哦~`,
                            trigger: 'blur'
                        }
                    ],
                    pass2: [
                        {
                            required: true,
                            message: '请重复输入密码',
                            trigger: 'blur'
                        }
                    ],
                    phone: [
                        {
                            required: true,
                            message: '请输入手机号',
                            trigger: 'blur'
                        }
                    ],
                    phoneVerify: [
                        {
                            required: true,
                            message: '请填写手机验证码',
                            trigger: 'blur'
                        }
                    ]
                },
                
                formData: {
                    name: '',
                    pass: '',
                    pass2: '',
                    phone: '',
                    phoneVerify: ''
                }
            };
        },
        mounted() {
            setup(['用户中心', '登录注册']);
            if (getUrlParam('page') == 'phoneLogin') {
                t.type = 1;
                t.title = '登录';
                t.grayTitle = 'LOGIN';
            } else if (getUrlParam('page') == 'signup') {
                t.type = 2;
                t.title = '注册';
                t.grayTitle = 'SIGNUP';
            }
        },
        methods: {
            sendPhoneVerify() {
                var next = function(vaptchaData) {
                    post({
                        name: '发送手机验证码',
                        url: '<?= u('local://api/user/phoneVerifySend') ?>',
                        data: $.extend(t.formData, vaptchaData),
                        callback_ok: function() {
                            t.isSentFirstPhoneVerify = true;
                        }
                    });
                };
                var go = function() {
                    vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['phoneVerify'] ?>, next);
                };
                if (t.isSentFirstPhoneVerify) {
                    confMsg('你刚刚已经发送过一次手机验证码了哦，确定要再次发送嘛？每天你只能发送一定量的手机验证码哦~', 'warning', function() {
                        go();
                    });
                } else {
                    go();
                }
            },
            doit(type, isPost) {
                var jump = function () {
                    var defaultu = '<?= u('local://www') ?>';
                    var ori = getUrlParam('from');
                    document.location.href = ori ? ori : defaultu;
                };
                switch (type) {
                    case 0:
                        if (! isPost) {
                            document.location.href = '?';
                            break;
                        }
                        var next = function(vaptchaData) {
                            post({
                                name: '登录',
                                url: '<?= u('local://api/user/login') ?>',
                                data: $.extend(t.formData, vaptchaData),
                                callback_ok: function(data) {
                                    setCookie('userToken', data.data.userToken);
                                    setTimeout(jump, 3000);
                                },
                                callback_ok2: function(data) {
                                    jump();
                                },
                                succText: '即将跳转~'
                            });
                        };
                        vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['login'] ?>, next);
                        break;
                    case 1:
                        if (! isPost) {
                            document.location.href = '?page=phoneLogin';
                            break;
                        }
                        var next = function(vaptchaData) {
                            post({
                                name: '手机号登录',
                                url: '<?= u('local://api/user/phoneLogin') ?>',
                                data: $.extend(t.formData, vaptchaData),
                                callback_ok: function(data) {
                                    setCookie('userToken', data.data.userToken);
                                    setTimeout(jump, 3000);
                                },
                                callback_ok2: function(data) {
                                    jump();
                                },
                                succText: '即将跳转~'
                            });
                        };
                        vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['phoneLogin'] ?>, next);
                        break;
                    case 2:
                        if (! isPost) {
                            document.location.href = '?page=signup';
                            break;
                        }
                        if (t.formData.pass != t.formData.pass2) {
                            errMsg('两次输入的密码不一致哦~');
                            return;
                        }
                        var next = function(vaptchaData) {
                            post({
                                name: '注册',
                                url: '<?= u('local://api/user/signup') ?>',
                                data: $.extend(t.formData, vaptchaData),
                                callback_ok2: 1,
                                jumpUrl: '?page=login',
                                succText: '即将跳转登录~'
                            });
                        };
                        vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['signup'] ?>, next);
                        break;
                    default:
                        break;
                }
            }
        }
    };
</script>