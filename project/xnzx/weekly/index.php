<style type="text/css">
    .xnzx_weekly_item {
        margin-bottom: 10px;
        
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
</style>

<div class="box box_md hcenter">
    <span class="box_title">新宁空间</span>
    <span class="box_graytitle">班级周报</span>
    <div class="group" style="margin-top: 40px;">
        <div class="group_btns">
            <el-button class="btns" type="primary" size="large" @click="goto(0)" plain>
                <i class="fas fa-upload"></i>&emsp;上传电子稿
            </el-button>
            <el-button class="btns" size="large" @click="goto(1)" :disabled="! inArray(parseInt(udata.id), weeklyData.adminUids)" plain>
                <i class="fas fa-plus"></i>&emsp;发起周报
            </el-button>
        </div>
    </div>
    <el-divider></el-divider>
    <el-skeleton :loading="! weeklyList" throttle="100" :rows="5" animated>
        <template #default>
            <div v-for="(v, k) in weeklyList" :key="v.id" class="xnzx_weekly_item">
                <a class="aAwesomeX" :href="'<?= u('local://xnzx/weekly/detail') ?>?id=' + v.id">
                    <el-row>
                        <el-tag :span="" :type="v.finish ? 'success' : 'info'">
                            {{ v.finish ? '已出版~' : '总编辑中（' + finishProgress(v) + '%）' }}
                        </el-tag>
                        <div :span="" style="margin-left: 10px;">
                            <span class="xnzx_weekly_item_class">{{ v.class }}班</span>
                            <span class="xnzx_weekly_item_term">{{ v.term }} 第{{ v.num }}期 {{ v.note }}</span>
                        </div>
                    </el-row>
                    <div style="margin-top: 20px;">
                        <span class="xnzx_weekly_item_count">上报作文：{{ v.full_progress }}篇</span>
                        &nbsp;
                        <span v-if="! v.finish" class="xnzx_weekly_item_count">总编辑进度：{{ finishProgressText(v) }}</span>
                    </div>
                </a>
            </div>
        </template>
    </el-skeleton>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                weeklyList: false,
                weeklyData: false
            };
        },
        mounted() {
            setup({'新宁空间': '', '班级周报': '<?= u('local://xnzx/weekly') ?>', '主页': ''});
        },
        methods: {
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
            goto(i) {
                switch (i) {
                    case 0:
                        document.location.href = '<?= u('local://xnzx/weekly/upload') ?>';
                        break;
                    case 1:
                        document.location.href = '<?= u('local://xnzx/weekly/upload') ?>?new=1';
                        break;
                    default:
                        break;
               } 
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://xnzx') ?>';
        };
        post({
            name: '新宁空间-班级周报数据同步',
            url: '<?= u('local://api/xnzx/weekly') ?>',
            data: {},
            callback_ok: function(data) {
                t.weeklyData = data.data.weeklyData;
                t.weeklyList = data.data.weeklyList;
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