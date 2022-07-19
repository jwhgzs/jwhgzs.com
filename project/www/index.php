<style type="text/css">
    .index_link {
        width: 100%;
        
        border-radius: 6px;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 0;
        overflow: hidden;
    }
    .index_carouselImg {
        width: 100%;
        height: 100%;
    }
    .index_carouselTitle {
        z-index: 2;
        position: absolute;
        width: 100%;
        height: 100%;
        text-align: center;
    }
    .index_carouselTitle_item {
        /* 贼暴力居中&调整位置 */
        position: absolute;
        width: 100%;
        height: 3rem;
        left: 50%;
        top: 100%;
        transform: translate(-50%, -100%);
        height: 3rem;
        line-height: 3rem;
        white-space: nowrap;
        background-color: rgba(255, 255, 255, 0.6);
        font-size: 150%;
        font-weight: bold;
    }
</style>

<div class="box box_md hcenter">
    <!-- 默认一页空白数据bug解决：https://blog.csdn.net/qq_40007006/article/details/120440831 -->
    <el-carousel v-if="carouselList.length > 0" indicator-position="outside" arrow="always">
        <el-carousel-item v-for="(v, k) in carouselList" :key="k" style="text-align: center;">
            <a target="_blank" :href="carouselUrl(v.id)" class="aAwesome">
                <div class="index_carouselTitle">
                    <div class="index_carouselTitle_item">{{ v.title }}</div>
                </div>
                <el-image class="index_carouselImg" :src="v.coverImg" fit="cover"></el-image>
            </a>
        </el-carousel-item>
    </el-carousel>
    <div class="box_title">子站导航</div>
    <el-divider></el-divider>
    <a href="<?= u('local://xnzx') ?>">
        <img class="index_link" alt="新宁空间 banner" src="<?= u('static://public/img/link_xnzx') ?>"/>
    </a>
    <a href="<?= u('local://forum') ?>">
        <img class="index_link" alt="论坛 banner" src="<?= u('static://public/img/link_forum') ?>"/>
    </a>
    <a href="<?= u('local://su') ?>">
        <img class="index_link" alt="jwh.su banner" src="<?= u('static://public/img/link_su') ?>"/>
    </a>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                carouselList: []
            };
        },
        mounted() {
            setup(['主站', '主页']);
        },
        methods: {
            carouselUrl(id) {
                return '<?= u('local://forum') ?>/detail?id=' + id;
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://www') ?>';
        };
        post({
            name: '首页轮播数据同步',
            url: '<?= u('local://api/www') ?>',
            data: {},
            callback_ok: function(data) {
                t.carouselList = data.data.carouselList;
            },
            callback_err: function() {
                setTimeout(jump2, 3000);
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