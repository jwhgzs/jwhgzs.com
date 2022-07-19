<style type="text/css">
    .xnzx_link {
        width: 100%;
        
        border-radius: 6px;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 0;
        overflow: hidden;
    }
</style>

<div class="box box_md hcenter">
    <div class="box_title">新宁空间</div>
    <el-divider></el-divider>
    <a href="<?= u('local://xnzx/weekly') ?>">
        <img class="xnzx_link" alt="台山市新宁中学 班级周报banner" src="<?= u('static://public/img/link_xnzx_weekly') ?>"/>
    </a>
    <a href="<?= u('local://xnzx/PA') ?>">
        <img class="xnzx_link" alt="台山市新宁中学 珍贵档案banner" src="<?= u('static://public/img/link_xnzx_PA') ?>"/>
    </a>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                
            };
        },
        mounted() {
            setup(['新宁空间', '主页']);
        },
        methods: {
            
        }
    };
</script>