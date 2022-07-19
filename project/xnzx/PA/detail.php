<style type="text/css">
    .xnzx_people_inf {
        margin-left: 20px;
    }
    .xnzx_people_boy {
        color: #0080FF;
    }
    .xnzx_people_girl {
        color: #FBB9DF;
    }
    .xnzx_people_name {
        margin-right: 10px;
        font-size: 200%;
        font-weight: bold;
    }
    .xnzx_people_property {
        font-size: 135%;
    }
    .xnzx_people_likes {
        margin-left: 10px;
    }
    .xnzx_people_jwh {
        margin-bottom: 6px;
        font-size: 80%;
        color: gray;
    }
    .xnzx_people_teacher {
        color: yellowgreen;
    }
    .xnzx_tip {
        margin: 10px;
        padding: 10px;
        text-align: center;
        border-top: 1px solid #ddd;
    }
    .xnzx_tip_1 {
        color: #409EFF;
    }
    .xnzx_tip_2 {
        color: #67C23A;
    }
</style>

<div class="box box_ml hcenter">
    <span class="box_title">新宁空间</span>
    <span class="box_graytitle">珍贵档案 - 详情</span>
    <el-divider></el-divider>
    <el-row>
        <el-col :span="">
            <el-image class="customAvatar" :src="'<?= u('static://user/PA') ?>/' + peopleDetail.id + '/0.jpg?v=' + peopleDetail.PA_photosVersion"></el-image>
        </el-col>
        <el-col :span="" class="xnzx_people_inf vcenter">
            <span class="xnzx_people_name" :class="{ xnzx_people_name_gray: parseInt(peopleDetail.disabled), xnzx_people_boy: (peopleDetail.sex == 1), xnzx_people_girl: (peopleDetail.sex == 2), xnzx_people_teacher: (peopleDetail.type == 1) }">{{ peopleDetail.name }}</span>
        </el-col>
    </el-row>
    <el-divider>个人信息</el-divider>
    <el-tag v-if="peopleDetail.type == 0" type="info" class="btns2">{{ peopleDetail.year }}-{{ peopleDetail.class }}-{{ peopleDetail.sid }}</el-tag>
    <el-tag v-if="peopleDetail.type == 1" type="info" class="btns2">{{ peopleDetail.year }}-{{ peopleDetail.teacher }}</el-tag>
    <el-tag class="btns2" style="margin-top: 10px;" type="primary">
        <i class="fas fa-thumbs-up"></i>&nbsp;获赞：{{ peopleDetail.PA_likes }}
    </el-tag>
    <el-row>
        <el-col :span="" class="vcenter xnzx_people_jwh">
            九尾狐账号：
        </el-col>
        <el-col :span="">
            <span v-if="udata2 === 0" class="xnzx_people_jwh">ta还没有九尾狐账号哦~</span>
            <user-infobox v-if="udata2 !== 0" :udata_="udata2" :single="true"></user-infobox>
        </el-col>
    </el-row>
    <div class="xnzx_tip">
        <span class="xnzx_tip_1">蓝色外号</span>
        ，
        <span class="xnzx_tip_2">绿色名梗
    </div>
    <!-- 这里曾经被坑过，按vue的推荐加了:key属性后会导致一直热更新，管他数据变没变就是一个劲的刷新…… -->
    <!-- 以后记住了，除非不加:key有问题，就别加了 -->
    <div>
        <template v-for="(v, k) in peopleDetail.PA_properties">
            <el-tag :type="v.type == 0 ? 'primary' : 'success'" class="btns2" size="large">
                <strong class="xnzx_people_property" v-html="v.name + '：'"></strong>
                <li v-for="v in v.detail" v-html="v" class="aAwesomeY"></li>
                <br/>
                <el-link class="xnzx_people_likes" :type="v.liked ? 'primary' : ''" @click="like(v.id)">
                    <i class="fas fa-thumbs-up"></i>&nbsp;{{ v.likes }}
                </el-link>
                <el-link v-if="isAdmin" class="xnzx_people_likes" @click="editProp(v)">
                    <i class="fas fa-edit"></i>&nbsp;编辑
                </el-link>
                <el-link v-if="isAdmin" class="xnzx_people_likes" type="danger" @click="delProp(v.id)">
                    <i class="fas fa-trash-alt"></i>&nbsp;删除
                </el-link>
            </el-tag>
        </template>
        <template v-if="isAdmin">
            <br/>
            <!-- 注意v-for这样（(v, k) in num）用，v值从1开始，k值从0开始 -->
            <el-button v-for="(v, k) in 2" size="small" :type="k == 0 ? 'primary' : 'success'" @click="addProp(k)">
                <i class="fas fa-plus-circle"></i>&nbsp;添加{{ k == 0 ? '外号' : '梗' }}
            </el-button>
        </template>
    </div>
    <el-divider>照片</el-divider>
    <el-carousel v-if="peopleDetail && peopleDetail.PA_photosName.length" :interval="6000" trigger="click" arrow="always" indicator-position="outside" height="300">
        <template v-for="v in peopleDetail.PA_photosName">
            <el-carousel-item>
                <el-button v-if="isAdmin" style="position: absolute; z-index: 26;" size="small" type="danger" @click="delPhotos(v)">
                    <i class="fas fa-trash-alt"></i>&nbsp;删除
                </el-button>
                <el-image style="width: 100%;" fit="contain" :src="buildDefaultImgUrl(v)" :preview-src-list="[buildDefaultImgUrl(v)]" :teleported="true"></el-image>
            </el-carousel-item>
        </template>
    </el-carousel>
    <el-button v-if="isAdmin" size="small" type="primary" @click="uploadPhotos()">
        <i class="fas fa-upload"></i>&nbsp;上传照片
    </el-button>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                peopleDetail: false,
                PAData: false,
                udata2: false
            };
        },
        mounted() {
            setup({'新宁空间': '', '珍贵档案': '<?= u('local://xnzx/PA') ?>', '详情': ''}, true);
        },
        methods: {
            inArray: inArray,
            buildDefaultImgUrl(i) {
                return '<?= u('static://user/PA') ?>/' + t.peopleDetail.id + '/' + i + '.jpg?v=' + t.peopleDetail.PA_photosVersion;
            },
            like(id) {
                var next = function(vaptchaData) {
                    post({
                        name: '新宁空间-珍贵档案点赞',
                        url: '<?= u('local://api/xnzx/PA/like') ?>',
                        data: $.extend({ id: id }, vaptchaData)
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            },
            addProp(type) {
                inputMsg('请输入标题：', 'text', function(name) {
                    inputMsg('请输入内容：', 'textarea', function(detail) {
                        post({
                            name: '新宁空间-珍贵档案外号/梗添加',
                            url: '<?= u('local://api/xnzx/PA/addProp') ?>',
                            data: { pid: t.peopleDetail.id, type: type, name: name, detail: detail }
                        });
                    });
                });
            },
            editProp(data) {
                var ori = data.detail.join('\n');
                inputMsg('请输入标题：', 'text', function(name) {
                    inputMsg('请输入内容：', 'textarea', function(detail) {
                        detail = detail;
                        if (name == data.name && detail == ori) {
                            infoMsg('你好像改都没改哦！~');
                            return;
                        }
                        post({
                            name: '新宁空间-珍贵档案外号/梗修改',
                            url: '<?= u('local://api/xnzx/PA/editProp') ?>',
                            data: { id: data.id, name: name, detail: detail }
                        });
                    }, function() {}, true, ori);
                }, function() {}, true, data.name);
            },
            delProp(id) {
                var next = function(vaptchaData) {
                    post({
                        name: '新宁空间-珍贵档案外号/梗删除',
                        url: '<?= u('local://api/xnzx/PA/delProp') ?>',
                        data: $.extend({ pid: t.peopleDetail.id, id: id }, vaptchaData)
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            },
            uploadPhotos() {
                inputMsg('请选择操作：', 'select', function(mode) {
                    selectFile(function(data) {
                        post({
                            name: '新宁空间-珍贵档案上传照片',
                            url: '<?= u('local://api/xnzx/PA/photosUpload') ?>',
                            data: { id: t.peopleDetail.id, mode: mode },
                            file: data
                        });
                    });
                }, function() {}, true, 0, [
                    {
                        label: '添加',
                        value: 0
                    },
                    {
                        label: '替换头像',
                        value: 1
                    },
                ]);
            },
            delPhotos(name) {
                var next = function(vaptchaData) {
                    post({
                        name: '新宁空间-珍贵档案删除照片',
                        url: '<?= u('local://api/xnzx/PA/photosDel') ?>',
                        data: $.extend({ id: t.peopleDetail.id, name: name }, vaptchaData)
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            }
        },
        computed: {
            isAdmin() {
                return inArray(parseInt(this.udata.id), this.PAData.adminUids);
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://xnzx/PA') ?>';
        };
        post({
            name: '新宁空间-珍贵档案数据同步',
            url: '<?= u('local://api/xnzx/PA/detail') ?>',
            data: { id: getUrlParam('id') },
            callback_ok: function(data) {
                t.peopleDetail = data.data.peopleDetail;
                t.PAData = data.data.PAData;
                if (t.peopleDetail.uid) {
                    post({
                        name: '新宁空间-珍贵档案-用户数据同步',
                        url: '<?= u('local://api/user/data') ?>',
                        data: { uid: t.peopleDetail.uid },
                        callback_ok: function(data) {
                            t.udata2 = data.data.userData;
                        },
                        hideUI: true
                    });
                } else {
                    t.udata2 = 0;
                }
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