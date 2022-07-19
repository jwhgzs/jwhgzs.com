<style type="text/css">
    .forum_topic_item {
        margin-bottom: 15px;
        
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 30px;
    }
    .forum_topic_item_title {
        color: #6E6E6E;
        font-size: 200%;
        font-weight: bold;
    }
    .forum_topic_item_foot {
        font-size: 80%;
        color: gray;
    }
    .forum_topic_item_uinfo {
        margin-bottom: 10px;
    }
    .forum_topic_item_time {
        color: gray;
        font-size: 80%;
    }
</style>

<div class="box box_ml hcenter">
    <span class="box_title">论坛</span>
    <el-divider></el-divider>
    <div class="group">
        <div class="group_btns">
            <el-button type="primary" size="large" class="btns" @click="gotoNew" plain>
                <i class="fas fa-upload"></i>&emsp;发布话题
            </el-button>
        </div>
    </div>
    <el-divider></el-divider>
    <div style="margin-bottom: 15px;">
        <el-check-tag v-for="(v, k) in forumData.classifies" :key="k" class="btns2" :checked="classifyChecked == k" @change="classifyChanged(k)">{{ v }}</el-check-tag>
    </div>
    <div>
        <div v-for="(v, k) in topicList" :key="v.id" class="forum_topic_item">
            <a class="aAwesomeX" :href="'<?= u('local://forum/detail') ?>?id=' + v.id">
                <user-infobox class="forum_topic_item_uinfo" :udata_="v.udata" :single="true"></user-infobox>
                <div class="forum_topic_item_title">{{ v.title }}</div>
                <el-divider></el-divider>
                <div class="forum_topic_item_foot">
                    <div class="forum_topic_item_time">{{ timeDesc(v.postTime) }}</div>
                    <div style="margin-top: 5px;">
                        <i class="fas fa-eye"></i>&nbsp;{{ v.looks }}
                        &nbsp;&nbsp;
                        <i class="fas fa-thumbs-up"></i>&nbsp;{{ v.likes }}
                        &nbsp;&nbsp;
                        <i class="fas fa-comment-dots"></i>&nbsp;{{ v.replys }}
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                forumData: false,
                topicList: false,
                classifyChecked: (getUrlParam('classify') ? parseInt(getUrlParam('classify')) : 0)
            };
        },
        mounted() {
            setup(['论坛', '主页']);
        },
        methods: {
            timeDesc: timeDesc,
            makeAvatarUrl: makeAvatarUrl,
            classifyChanged(id) {
                document.location.href = '?classify=' + id;
            },
            gotoNew() {
                var cid = ((inArray(parseInt(t.classifyChecked), t.forumData.adminClassifies) && (! t.isAdmin())) ? t.forumData.defaultClassify : parseInt(t.classifyChecked));
                document.location.href = '<?= u('local://forum/new') ?>?classify=' + cid;
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://www') ?>';
        };
        post({
            name: '论坛数据同步',
            url: '<?= u('local://api/forum') ?>',
            data: { classify: t.classifyChecked },
            callback_ok: function(data) {
                t.forumData = data.data.forumData;
                t.topicList = data.data.topicList;
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