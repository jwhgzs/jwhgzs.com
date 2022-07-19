<style type="text/css">
    .xnzx_people_item {
        margin-bottom: 10px;
        
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 20px;
    }
    .xnzx_people_boy {
        color: #0080FF;
    }
    .xnzx_people_girl {
        color: #FBB9DF;
    }
    .xnzx_people_name {
        font-size: 200%;
        font-weight: bold;
    }
    .xnzx_people_name_gray {
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
    <span class="box_graytitle">珍贵档案</span>
    <el-divider></el-divider>
    <div v-for="(v, k) in classes">
        <el-check-tag v-for="(v2, k2) in v" :checked="classChecked[0] == k && classChecked[1] == v2" @change="classCheck(k, v2)">
            {{ k }}-{{ v2 }}班
        </el-check-tag>
    </div>
    <br/>
    <el-divider>照片</el-divider>
    <el-carousel :interval="6000" trigger="click" arrow="always" indicator-position="outside" height="300">
        <template v-for="(n, v) in <?= '' . c::$XNZX_PA_CONFIG['defaultPhotoNum'] ?>">
            <el-carousel-item v-if="! img404[v]">
                <el-button v-if="isAdmin" style="position: absolute; z-index: 26;" size="small" type="danger" @click="delPhotos(v)">
                    <i class="fas fa-trash-alt"></i>&nbsp;删除
                </el-button>
                <el-image style="width: 100%;" fit="contain" :src="buildDefaultImgUrl(v)" :preview-src-list="[buildDefaultImgUrl(v)]" :teleported="true" @error="img404[v] = true"></el-image>
            </el-carousel-item>
        </template>
    </el-carousel>
    <el-input v-model="searchTag" type="text" size="large" placeholder="找不到你想要的？请输入关键词搜索" @input="peopleList = null">
        <template #prefix>
            <i class="fas fa-search"></i>
        </template>
    </el-input>
    <div class="xnzx_tip">
        <span class="xnzx_tip_1">蓝色外号</span>
        ，
        <span class="xnzx_tip_2">绿色名梗
    </div>
    <el-skeleton :loading="! peopleList" throttle="100" :rows="10" animated>
        <template #default>
            <el-row style="height: 80%; overflow-y: scroll;" justify="space-between">
                <el-col v-for="(v, k) in peopleList" :lg="11" :key="v.id">
                    <div class="xnzx_people_item">
                        <a class="aAwesomeX" :href="'<?= u('local://xnzx/PA/detail') ?>?id=' + v.id">
                            <el-row>
                                <el-col :span="" style="margin-right: 20px;">
                                    <div class="xnzx_people_name" :class="{ xnzx_people_name_gray: parseInt(v.disabled), xnzx_people_boy: (v.sex == 1), xnzx_people_girl: (v.sex == 2), xnzx_people_teacher: (v.type == 1) }">{{ v.name }}</div>
                                    <el-image class="customAvatar" :src="'<?= u('static://user/PA') ?>/' + v.id + '/0.jpg?v=' + v.PA_photosVersion" :lazy="true"></el-image>
                                </el-col>
                                <el-col :span="">
                                    <el-tag effect="plain" v-if="v.type == 0" type="info" size="small" class="btns2">{{ v.year }}-{{ v.class }}-{{ v.sid }}</el-tag>
                                    <el-tag effect="plain" v-if="v.type == 1" type="info" size="small" class="btns2">{{ v.year }}-{{ v.teacher }}</el-tag>
                                    <el-tag effect="plain" type="primary" size="small" class="btns2">
                                        <i class="fas fa-thumbs-up"></i>&nbsp;获赞：{{ v.PA_likes }}
                                    </el-tag>
                                    <el-divider></el-divider>
                                    <div>
                                        <template v-for="(v2, k2) in v.PA_properties">
                                            <el-tag style="font-weight: bold;" :type="v2.type == 0 ? 'primary' : 'success'" class="btns2">
                                                {{ v2.name }}
                                            </el-tag>
                                        </template>
                                    </div>
                                </el-col>
                            </el-row>
                        </a>
                    </div>
                </el-col>
            </el-row>
        </template>
    </el-skeleton>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                classes: JSON.parse(`<?= json_encode(c::$XNZX_PA_CONFIG['classes']) ?>`),
                classChecked: (getUrlParam('year') ? [getUrlParam('year'), getUrlParam('class')] : JSON.parse(`<?
                    $arr = c::$XNZX_PA_CONFIG['classes'];
                    foreach ($arr as $k => $v) {
                        echo json_encode([$k, $arr[$k][0]]);
                        break;
                    }
                ?>`)),
                peopleList: false,
                PAData: false,
                searchTag: '',
                img404: []
            };
        },
        mounted() {
            setup({'新宁空间': '', '珍贵档案': '<?= u('local://xnzx/PA') ?>', '主页': ''}, true);
        },
        methods: {
            inArray: inArray,
            classCheck(k, k2) {
                document.location.href = '?year=' + k +'&class=' + k2;
            },
            buildDefaultImgUrl(i) {
                return '<?= u('static://user/PA') ?>/' + t.classChecked[0] + '_' + t.classChecked[1] + '/' + i + '.jpg?v=' + t.PAData.defaultPhotoVersion;
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://xnzx') ?>';
        };
        post({
            name: '新宁空间-珍贵档案数据同步',
            url: '<?= u('local://api/xnzx/PA') ?>',
            data: { year: t.classChecked[0], 'class': t.classChecked[1], searchTag: t.searchTag },
            callback_ok: function(data) {
                t.peopleList = (((! t.searchTag) || data.data.searchTag == t.searchTag) ? data.data.peopleList : null);
                t.PAData = data.data.PAData;
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