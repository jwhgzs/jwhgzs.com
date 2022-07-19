<style type="text/css">
    .forum_topic_item {
        margin-bottom: 15px;
        
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 30px;
    }
    .forum_topic_item_nonreply {
        background-color: #F7F7F7;
    }
    .forum_topic_item_title {
        margin-bottom: 25px;
        color: #6E6E6E;
        text-align: center;
        font-size: 250%;
        font-weight: bold;
    }
    .forum_topic_item_content {
        margin-top: 10px;
        padding: 10px;
        border-radius: 10px;
        word-wrap: break-word;
    }
    .forum_topic_item_content img {
        width: 15rem;
        max-width: 50% !important;
    }
    .forum_topic_item_btn {
        font-size: 80% !important;
        margin-left: 10px;
    }
    .forum_topic_item_btns {
        float: right;
        padding: 10px;
        color: gray;
    }
    .forum_topic_item_id {
        position: absolute;
        border-left: none;
        border-top: none;
        margin: 1px;
    }
</style>

<div class="box box_ml hcenter">
    <span class="box_title">论坛</span>
    <span class="box_graytitle">话题详情</span>
    <el-divider></el-divider>
    <div class="group">
        <el-button type="primary" size="large" @click="gotoReply" plain>
            <i class="fas fa-comments"></i>&emsp;发言
        </el-button>
    </div>
    <el-divider></el-divider>
    <div class="forum_topic_item_title">{{ topicTree ? topicTree[0].title : '' }}</div>
    <div>
        <div v-for="(v, k) in topicTree" :key="v.id">
            <el-tag size="small" :type="k == 0 ? 'success' : 'primary'" class="forum_topic_item_id">#{{ v._id }}</el-tag>
            <span class="forum_topic_item_btns">
                <el-link class="forum_topic_item_btn" v-if="k == 0" disabled>
                    <i class="fas fa-eye"></i>&nbsp;{{ v.looks }}
                </el-link>
                <el-link class="forum_topic_item_btn" :type="v.liked ? 'primary' : ''" plain @click="like(v.id)">
                    <i class="fas fa-thumbs-up"></i>&nbsp;{{ v.likes }}
                </el-link>
                <el-link v-if="inArray(parseInt(udata.id), forumData.adminUids)" class="forum_topic_item_btn" @click="editIt(v.id)">
                    <i class="fas fa-edit"></i>&nbsp;编辑
                </el-link>
                <el-link v-if="inArray(parseInt(udata.id), forumData.adminUids)" class="forum_topic_item_btn" type="danger" @click="deleteIt(v.id)">
                    <i class="fas fa-trash-alt"></i>&nbsp;删除
                </el-link>
            </span>
            <div :class="{ forum_topic_item: true, forum_topic_item_nonreply: (k == 0) }">
                <!-- 这里不给100%会导致因为上面float的btns而压缩了宽度 -->
                <user-infobox :udata_="v.udata" :time="v.postTime"></user-infobox>
                <div class="forum_topic_item_content" v-html="v.content"></div>
            </div>
            <el-divider v-if="k == 0"></el-divider>
        </div>
    </div>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                forumData: false,
                topicTree: false
            };
        },
        mounted() {
            setup(['论坛', '详细页']);
        },
        methods: {
            timeDesc: timeDesc,
            inArray: inArray,
            like(id) {
                var next = function(vaptchaData) {
                    post({
                        name: '论坛点赞/取消点赞',
                        url: '<?= u('local://api/forum/like') ?>',
                        data: $.extend({ id: id }, vaptchaData)
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            },
            gotoReply() {
                document.location.href = '<?= u('local://forum/new') ?>?reply=1&pid=' + getUrlParam('id');
            },
            editIt(id) {
                document.location.href = '<?= u('local://forum/new') ?>?edit=1&id=' + id;
            },
            deleteIt(id) {
                confMsg('确定要删除嘛？', 'warning', function() {
                    var jump = function() {
                        if (id == t.topicTree[0].id) {
                            document.location.href = '<?= u('local://forum') ?>';
                        }
                    };
                    var next = function(vaptchaData) {
                        post({
                            name: '论坛删除',
                            url: '<?= u('local://api/forum/delete') ?>',
                            data: $.extend({ id: id }, vaptchaData),
                            callback_ok: jump
                        });
                    };
                    vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
                });
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://forum') ?>';
        };
        post({
            name: '论坛数据同步',
            url: '<?= u('local://api/forum/detail') ?>',
            data: { id: parseInt(getUrlParam('id')) },
            callback_ok: function(data) {
                t.topicTree = data.data.topicTree;
                t.forumData = data.data.forumData;
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