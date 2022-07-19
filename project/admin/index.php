<style type="text/css">
    .admin_link {
        width: 100%;
        margin: 10px 0 !important;
        --el-button-size: 80px;
        font-size: 150%;
    }
</style>

<div class="box box_md hcenter">
    <div class="box_title">后台管理</div>
    <el-divider></el-divider>
    <el-button class="admin_link btns2" text round bg size="large" @click="jump2('<?= u('local://admin/shortUrl') ?>')">短链接管理</el-button>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                carouselList: []
            };
        },
        mounted() {
            setup(['后台管理', '主页'], true);
        },
        methods: {
            
        }
    };
</script>