<style type="text/css">
    .xnzx_weekly_detail_heihei {
        font-size: 80%;
        color: gray;
    }
    .xnzx_weekly_item {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 20px;
    }
    .xnzx_weekly_item_class {
        color: #6E6E6E;
        font-size: 150%;
        font-weight: bold;
    }
    .xnzx_weekly_item_term {
        margin-left: 10px;
        
        color: skyblue;
        font-size: 150%;
    }
    .xnzx_weekly_item_count {
        color: gray;
        font-size: 80%;
    }
    .xnzx_weekly_article {
        margin-bottom: 20px;
        
        padding: 30px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background-color: #FAFAFA;
    }
    .xnzx_weekly_article_title {
        font-size: 150%;
        font-weight: bold;
        font-family: '楷体';
        text-align: center;
    }
    .xnzx_weekly_article_author {
        margin-top: 10px;
        
        font-size: 125%;
        font-family: '楷体';
        text-align: center;
    }
    .xnzx_weekly_article_content {
        margin-top: 20px;
        
        font-size: 100%;
        white-space: pre-wrap;
        tab-size: 0;
        font-family: '仿宋';
    }
    .xnzx_weekly_article_content_p {
        text-indent: 2rem;
    }
    .xnzx_weekly_article_content_p_sharp {
        font-family: '楷体';
        font-style: italic;
    }
    .xnzx_weekly_article_content_p_sharp_r {
        text-align: right;
    }
    .xnzx_weekly_btnGroup {
        margin: 20px 0;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .xnzx_weekly_article_btns {
        text-align: center;
        color: gray;
    }
    .xnzx_weekly_article_btn:not(:last-child) {
        margin-right: 20px;
    }
    .xnzx_weekly_article_content_subtitle {
        text-indent: 0 !important;
        font-family: '楷体';
        font-size: 125%;
        text-align: center;
    }
    .xnzx_weekly_article_content_subtitle2 {
        /* line-height用无单位数值，则1表示正常行高度，2表示两倍，etc */
        line-height: 1.8;
        padding: 10px;
        background-color: #ddd;
    }
</style>

<div class="box box_ml hcenter">
    <span class="box_title">新宁空间</span>
    <span class="box_graytitle">班级周报 - 详情</span>
    <el-divider></el-divider>
    <el-skeleton :loading="! weeklyDetail" throttle="50" :rows="4" animated>
        <template #default>
            <el-row>
                <el-tag :span="" :type="weeklyDetail.finish ? 'success' : 'info'">
                    {{ weeklyDetail.finish ? '已出版~' : '总编辑中（' + finishProgress(weeklyDetail) + '%）' }}
                </el-tag>
                <el-tag v-if="parseInt(this.weeklyDetail.nameInvisible)" :span="" type="primary" style="margin-left: 10px;">
                    隐藏作者名
                </el-tag>
                <div :span="" style="margin-left: 10px;">
                    <span class="xnzx_weekly_item_class">{{ weeklyDetail.class }}班</span>
                    <span class="xnzx_weekly_item_term">{{ weeklyDetail.term }} 第{{ weeklyDetail.num }}期 {{ weeklyDetail.note }}</span>
                </div>
            </el-row>
            <div style="margin-top: 20px;">
                <div>
                    <span class="xnzx_weekly_item_count">上报作文：{{ weeklyDetail.full_progress }}篇</span>
                    &nbsp;
                    <span v-if="! weeklyDetail.finish" class="xnzx_weekly_item_count">总编辑进度：{{ finishProgressText(weeklyDetail) }}</span>
                </div>
                <div class="group">
                    <el-button size="small" @click="foldPeopleList">{{ isShowPeopleList ? '隐藏详情' : '详情' }}</el-button>
                    <el-button v-if="isAdmin" size="small" @click="editWeekly" plain>修改周报信息</el-button>
                    <el-button v-if="isAdmin" size="small" @click="downloadWeekly" plain>下载文档</el-button>
                    <el-button v-if="isAdmin" size="small" type="danger" @click="deleteWeekly" plain>删除周报</el-button>
                </div>
            </div>
            <el-divider v-show="isShowPeopleList"></el-divider>
            <!-- 这个table用v-if不行，可能是因为按钮控制（可以点很快），省性能懒加载了。v-show用的是display: none; -->
            <!-- 这里必须等数据来了之后才能显示（v-if），不然就会偶尔有莫名其妙的错误 -->
            <el-table v-if="weeklyDetail" v-show="isShowPeopleList" :data="articleList" :stripe="true" max-height="500">
                <el-table-column prop="" label="操作" width="100">
                    <template #default="scope">
                        <el-button v-if="parseInt(scope.row.status)" @click="jumpArticle(scope.row.id)" size="small">查看</el-button>
                    </template>
                </el-table-column>
                <el-table-column prop="" label="标题" width="150">
                    <template #default="scope">
                        <strong>{{ scope.row.title }}</strong>
                    </template>
                </el-table-column>
                <el-table-column prop="" label="作者" width="150">
                    <template #default="scope">
                        <component v-if="nameVisible" :is="scope.row.author[2] ? 'el-link' : 'span'" target="_blank" :href="'<?= u('local://user/ucenter') ?>?uid=' + scope.row.author[2]">
                            {{ scope.row.author[0] }}号 {{ scope.row.author[1] }}
                        </component>
                        <span v-else class="xnzx_weekly_detail_heihei">你猜~</span>
                    </template>
                </el-table-column>
                <el-table-column prop="" label="打稿人" min-width="150">
                    <template #default="scope">
                        <component v-if="nameVisible" :is="scope.row.typist[2] ? 'el-link' : 'span'" target="_blank" :href="'<?= u('local://user/ucenter') ?>?uid=' + scope.row.typist[2]">
                            {{ scope.row.typist[0] }}号 {{ scope.row.typist[1] }}
                        </component>
                        <span v-else class="xnzx_weekly_detail_heihei">你猜~</span>
                    </template>
                </el-table-column>
                <el-table-column prop="" label="状态" min-width="100">
                    <template #default="scope">
                        <el-tag :type="parseInt(scope.row.status) == 2 ? 'success' : (parseInt(scope.row.status) == 1 ? 'primary' : 'info')">
                            {{ parseInt(scope.row.status) == 2 ? '打稿完成' : (parseInt(scope.row.status) == 1 ? '审核中' : '未提交') }}
                        </el-tag>
                    </template>
                </el-table-column>
            </el-table>
        </template>
    </el-skeleton>
    <div v-if="weeklyDetail" class="box_tip" style="text-align: center; font-weight: bold;">
        {{ copyright }}
    </div>
    <el-divider v-if="! weeklyDetail"></el-divider>
    <br/>
    <el-skeleton :loading="! weeklyDetail" throttle="50" :rows="8" animated>
        <template #default>
            <template v-for="(v, k) in articleList" :key="v.id">
                <div v-if="parseInt(v.status)" class="xnzx_weekly_article">
                    <a class="aAwesome2" :name="'article_' + v.id"></a>
                    <div class="xnzx_weekly_article_btns">
                        <el-link class="xnzx_weekly_article_btn" v-if="parseInt(udata.id) && isAdmin && parseInt(v.status)" @click="passArticle(v.id)" size="small">
                            <i class="fas fa-drafting-compass"></i>&nbsp;{{ parseInt(v.status) == 2 ? '取消' : '' }}过审
                        </el-link>
                        <el-link class="xnzx_weekly_article_btn" v-if="parseInt(udata.id) && parseInt(v.status) == 1" @click="editArticle(v.id)" size="small">
                            <i class="fas fa-edit"></i>&nbsp;编辑
                        </el-link>
                    </div>
                    <el-divider></el-divider>
                    <div class="xnzx_weekly_article_title">{{ v.title }}</div>
                    <div v-if="nameVisible" class="xnzx_weekly_article_author">{{ v.author[1] }}</div>
                    <div class="xnzx_weekly_article_content">
                        <p v-if="v.tiji" class="xnzx_weekly_article_content_p xnzx_weekly_article_content_p_sharp">{{ v.tiji }}</p>
                        <p v-if="v.tiji" class="xnzx_weekly_article_content_p xnzx_weekly_article_content_p_sharp xnzx_weekly_article_content_p_sharp_r">——题记</p>
                        <p v-for="v2 in v.content" :key="v2" :class="{ xnzx_weekly_article_content_p: true, xnzx_weekly_article_content_subtitle: v2.startsWith('#') }">
                            <span :class="{ xnzx_weekly_article_content_subtitle2: v2.startsWith('#') }">{{ v2.startsWith('#') ? v2.replace(/#/g, '') : v2 }}</span>
                        </p>
                        <p v-if="v.houji" class="xnzx_weekly_article_content_p xnzx_weekly_article_content_p_sharp">{{ v.houji }}</p>
                        <p v-if="v.houji" class="xnzx_weekly_article_content_p xnzx_weekly_article_content_p_sharp xnzx_weekly_article_content_p_sharp_r">——后记</p>
                    </div>
                </div>
            </template>
        </template>
    </el-skeleton>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                copyright: '版权归本网站及原作者所有，未经本网站或原作者允许不得转载本周报内容！',
                
                pid: getUrlParam('id'),
                
                weeklyDetail: false,
                articleList: false,
                isShowPeopleList: false
            };
        },
        mounted() {
            setup({'新宁空间': '', '班级周报': '<?= u('local://xnzx/weekly') ?>', '详情': ''});
            document.addEventListener('copy', function(e) {
                warnMsg('复制成功。<br/><br/>' + t.copyright);
            });
        },
        methods: {
            getUrlParam: getUrlParam,
            inArray: inArray,
            finishProgress(v) {
                if (v.full_progress == 0)
                    return 0;
                return (parseInt(v.finish_progress) / parseInt(v.full_progress) * 100).toFixed(0);
            },
            finishProgressText(v) {
                if (v.full_progress == 0)
                    return '';
                return parseInt(v.finish_progress) + '/' + parseInt(v.full_progress);
            },
            jumpArticle(id) {
                document.location.hash = '#article_';
                document.location.hash = '#article_' + id;
            },
            getArticleDetailByAuthor(i) {
                // 适配一作者多文章
                var count = 0, item = t.weeklyDetail.authors[i][0];
                for (var k in t.weeklyDetail.authors) {
                    var v = t.weeklyDetail.authors[k][0];
                    if (v == item) {
                        count ++;
                    }
                    if (v == item && k == i) {
                        break;
                    }
                }
                var count2 = 0;
                for (var k in t.articleList) {
                    var v = t.articleList[k];
                    if (v.author[0] == item) {
                        count2 ++;
                        if (count2 == count) {
                            return v;
                        }
                    }
                }
                return false;
            },
            passArticle(id) {
                confMsg('确定要审核/取消过审该稿件吗？', 'warning', function() {
                    post({
                        name: '新宁空间-班级周报过审/取消过审',
                        url: '<?= u('local://api/xnzx/weekly/pass') ?>',
                        data: { id: id }
                    });
                });
            },
            editArticle(id) {
                document.location = '<?= u('local://xnzx/weekly/upload') ?>?edit_pid=' + t.pid + '&edit_id=' + id;
            },
            foldPeopleList() {
                t.isShowPeopleList = (! t.isShowPeopleList);
            },
            editWeekly() {
                document.location = '<?= u('local://xnzx/weekly/upload') ?>?new=1&edit_pid=' + t.pid;
            },
            downloadWeekly() {
                if (isWechatOrQQ()) {
                    errMsg('在微信、QQ内置浏览框无法下载文件哦，请用浏览器打开本网站~');
                    return;
                }
                var next = function(vaptchaData) {
                    window.open('<?= u('local://api/xnzx/weekly/download') ?>?id=' + t.pid + '&userToken=' + getCookie('userToken') + '&vaptcha=' + encodeURIComponent(JSON.stringify(vaptchaData.vaptcha)));
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            },
            deleteWeekly() {
                confMsg('确定要删除当前周报吗？删除后不可恢复！', 'warning', function() {
                    var next = function(vaptchaData) {
                        post({
                            name: '新宁空间-班级周报删除',
                            url: '<?= u('local://api/xnzx/weekly/delete') ?>',
                            data: $.extend({ id: t.pid }, vaptchaData),
                            jumpUrl: '<?= u('local://xnzx/weekly') ?>',
                            callback_ok2: 1
                        });
                    };
                    vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
                });
            }
        },
        computed: {
            isAdmin() {
                return inArray(parseInt(this.udata.id), this.weeklyDetail.adminUids);
            },
            nameVisible() {
                return ((! parseInt(this.weeklyDetail.nameInvisible)) || this.isAdmin);
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://xnzx/weekly') ?>';
        };
        post({
            name: '新宁空间-班级周报数据同步',
            url: '<?= u('local://api/xnzx/weekly/detail') ?>',
            data: { id: t.pid },
            callback_ok: function(data) {
                t.weeklyDetail = data.data.weeklyDetail;
                t.articleList = data.data.articleList;
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