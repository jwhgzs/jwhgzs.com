<style type="text/css">
    .ucenter_avatar {
        margin-right: 10px;
    }
    .ucenter_name {
        margin-left: 15px;
        
        font-size: 200%;
        font-weight: bold;
    }
    .ucenter_main1 {
        margin-right: 25px;
    }
    .ucenter_main2 {
        line-height: calc(120px / 2);
    }
    .ucenter_ske_avatar {
        width: 120px;
        height: 120px;
        margin-right: 30px;
    }
    .ucenter_ske_info {
        margin-top: 20px;
        height: 200px;
    }
    .ucenter_changeAvatar {
        position: absolute;
        width: 120px;
        
        text-align: center;
    }
    .ucenter_changeAvatar_btn {
        margin-top: -20px;
    }
    .ucenter_col1 {
        margin-right: calc(100% / 24);
        margin-bottom: 20px;
    }
    .ucenter_selfintroduce {
        padding: 15px;
        border-radius: 5px;
        background-color: #f5f7fa;
        margin-bottom: 10px;
        
        white-space: pre-wrap;
    }
</style>

<el-row>
    <el-col class="ucenter_col1" :md="11">
        <div class="box">
            <el-skeleton :loading="! ucdata" :rows="4" animated>
                <template #template>
                    <el-skeleton-item variant="circle" class="ucenter_ske_avatar"></el-skeleton-item>
                    <el-skeleton-item variant="h3" class="ucenter_ske_info"></el-skeleton-item>
                </template>
                <template #default>
                    <el-row class="ucenter_main1">
                        <el-col :span="" style="margin-bottom: 30px;">
                            <user-avatar :udata_="ucdata" :size="120"></user-avatar>
                            <div v-if="ownerIsMe" class="ucenter_changeAvatar">
                                <el-button size="large" class="ucenter_changeAvatar_btn" @click="changeAvatar" circle>
                                    <i class="fas fa-redo"></i>
                                </el-button>
                            </div>
                        </el-col>
                        <el-col :span="" class="vcenter" style="margin-bottom: 30px;">
                            <div :class="{ ucenter_name: true, authed_uname: (ucdata.userGroup || ucdata.userAuth) }">{{ ucdata.name }}</div>
                        </el-col>
                    </el-row>
                    <div v-if="ucdata.isMe || ucdata.selfIntroduce" class="ucenter_selfintroduce">
                        <span v-if="ucdata.selfIntroduce">{{ ucdata.selfIntroduce }}</span>
                        <span v-if="! ucdata.selfIntroduce" style="color: gray;">还没有自我介绍哦~</span>
                        <el-button v-if="ownerIsMe" @click="setUdata('selfIntroduce', '自我介绍', 'textarea', ucdata.selfIntroduce)" style="margin-left: 10px;" circle>
                            <i class="fas fa-edit"></i>
                        </el-button>
                    </div>
                    <el-divider></el-divider>
                    <div class="ucenter_main2">
                        <el-tag :type="ucdata.userGroup ? 'success' : ''" class="btns2">{{ ucdata.userGroup ? ucdata.userGroup : '用户' }}</el-tag>
                        <el-tag v-if="ucdata.userClassify" type="success" class="btns2">{{ ucdata.userClassify ? ucdata.userClassify : '用户' }}</el-tag>
                        <template v-if="ucdata.userAuth" v-for="v in ucdata.userAuth.split('\n')">
                            <el-tag class="btns2">{{ v }}</el-tag>
                        </template>
                        <el-tag v-if="ucdata.realName" type="info" class="btns2">
                            <strong>实名认证：</strong>
                            {{ ucdata.realName }}
                        </el-tag>
                    </div>
                </template>
            </el-skeleton>
        </div>
        <div v-if="ownerIsMe" class="box" style="margin-top: 20px;">
            <div class="box_title">账号安全</div>
            <div class="box_graytitle">安全设置</div>
            <div class="group">
                <el-button type="primary" size="large" @click="setUdata('pass', '密码', 'password')" plain>
                    <i class="fas fa-key"></i>&emsp;修改密码
                </el-button>
            </div>
            <div class="box_graytitle">最近登录记录</div>
            <el-table :data="ucdata.loginDetails" :stripe="true" style="margin-top: 20px;" max-height="300px">
                <!-- TNND，被坑两天了，这个el-table-column不能用单标签形式，所以尾部不能用 `/>` 必须用 `></el-table-column>` -->
                <el-table-column prop="id" label="ID"></el-table-column>
                <el-table-column prop="loginTime" label="登录时间" width="200"></el-table-column>
                <el-table-column prop="loginIP" label="登录IP" width="150"></el-table-column>
                <el-table-column prop="onlineTime" label="最后在线时间" width="200"></el-table-column>
                <el-table-column prop="todo" label="操作/说明" min-width="150">
                    <template #default="scope">
                        <el-button v-if="scope.row.tokenActive && (! scope.row.isMe)" type="warning" size="small" @click="dieUserToken(scope.row.id)" plain>强制退出登录</el-button>
                        {{ scope.row.isMe ? '这是本设备哦' : '' }}
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-col>
    <el-col :md="12">
        <div class="box">
            <div class="box_title">个人信息</div>
            <el-skeleton style="margin-top: 20px;" :loading="! ucdata" :rows="6" animated>
                <template #default>
                    <el-descriptions style="margin-top: 20px;" :column="1" direction="vertical" border>
                        <el-descriptions-item>
                            <template #label>
                                <i class="fas fa-id-badge"></i>
                                &nbsp;
                                用户ID
                            </template>
                            #{{ ucdata.id }}
                        </el-descriptions-item>
                        <el-descriptions-item v-if="! ucdata.isMe">
                            <template #label>
                                <i class="fas fa-signal"></i>
                                &nbsp;
                                是否在线
                            </template>
                            <component :is="ucdata.isOnline ? 'el-tag' : 'div'" :type="ucdata.isOnline ? 'success' : 'info'">{{ ucdata.isOnline ? '在线' : '最后在线时间：' + getTimeDesc(ucdata.lastOnlineTime) }}</component>
                        </el-descriptions-item>
                        <el-descriptions-item>
                            <template #label>
                                <i class="fas fa-phone"></i>
                                &nbsp;
                                手机号
                            </template>
                            {{ ucdata.phone }}
                        </el-descriptions-item>
                        <el-descriptions-item>
                            <template #label>
                                <i class="fas fa-user-plus"></i>
                                &nbsp;
                                注册信息
                            </template>
                            <div>
                                <strong>时间：</strong>
                                {{ getTimeDesc(ucdata.signupTime) }}
                            </div>
                            <div>
                                <strong>IP：</strong>
                                {{ ucdata.signupIP }}
                            </div>
                        </el-descriptions-item>
                        <el-descriptions-item>
                            <template #label>
                                <i class="fas fa-user-check"></i>
                                &nbsp;
                                实名认证
                            </template>
                            <span v-if="ucdata.realName">{{ ucdata.realName }}</span>
                            <span class="box_tip3" v-if="(! ucdata.realName) && ucdata.isMe">可以找管理员申请实名认证哦！～</span>
                        </el-descriptions-item>
                    </el-descriptions>
                </template>
            </div>
        </div>
    </el-col>
</el-row>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                ucdata: false,
                isUcenterPage: true,
                pageOwner: (getUrlParam('uid') ? getUrlParam('uid') : '')
            };
        },
        mounted() {
            setup(['用户中心', '个人中心'], true);
        },
        computed: {
            ownerIsMe() {
                if (! t.ucdata) return false;
                return t.ucdata.isMe;
            }
        },
        methods: {
            getTimeDesc: timeDesc,
            changeAvatar() {
                selectFile(function(data) {
                    var next = function(vaptchaData) {
                        post({
                            name: '上传用户头像',
                            url: '<?= u('local://api/user/avatarUpload') ?>',
                            data: vaptchaData,
                            file: data,
                            succText: '稍等一秒新头像就生效啦~'
                        });
                    };
                    vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
                });
            },
            setUdata_doit(key, desc, value) {
                post({
                    name: '设置个人信息（' + desc + '）',
                    url: '<?= u('local://api/user/setUdata') ?>',
                    data: { key: key, value: value }
                });
            },
            setUdata(key, desc, inputType, defaultValue) {
                inputMsg('请输入新的' + desc + '~', inputType, function(value) {
                    if (inputType == 'password') {
                        inputMsg('请重复输入新的' + desc + '，不然设置错了很麻烦哦~', inputType, function(value2) {
                            if (value !== value2) {
                                errMsg('两次输入的' + desc + '不一致呢~');
                                return;
                            }
                            t.setUdata_doit(key, desc, value);
                        });
                        return;
                    }
                    if (t.ucdata[key] === value) {
                        infoMsg('……这不跟之前的一样嘛~');
                        return;
                    }
                    t.setUdata_doit(key, desc, value);
                }, function() {}, true, defaultValue);
            },
            dieUserToken(id) {
                confMsg('确定要将该设备强制退出登录吗？', 'warning', function() {
                    var next = function(vaptchaData) {
                        post({
                            name: '强制设备退出登录',
                            url: '<?= u('local://api/user/userTokenDie') ?>',
                            data: $.extend({ id: id }, vaptchaData)
                        });
                    };
                    vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
                });
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://www') ?>';
        };
        post({
            name: '用户中心数据同步',
            url: '<?= u('local://api/user/data') ?>',
            data: { uid: t.pageOwner },
            callback_ok: function(data) {
                t.ucdata = data.data.userData;
            },
            callback_err: function() {
                setTimeout(jump, 3000);
            },
            callback_err2: function() {
                jump();
            },
            hideSuccUI: true,
            hideLoadingUI: true,
            errText: '即将返回主页~'
        });
    };
</script>