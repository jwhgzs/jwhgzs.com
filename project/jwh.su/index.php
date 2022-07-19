<style type="text/css">
    .su_text {
        border: none;
        margin-bottom: 30px;
        width: 50rem;
        max-width: 80%;
        z-index: 2;
        padding: 20px;
        
        background-color: rgba(0, 0, 0, 0.5);
        font-size: 170%;
        color: white;
        text-align: center;
        word-break: break-all;
    }
    .su_text2 {
        margin-top: 20px;
        font-size: 50%;
        text-align: right;
    }
    .su_tip {
        font-size: 50%;
        margin-bottom: 10px;
    }
    .su_item {
        max-width: 100%;
    }
</style>

<el-image src="<?= u('static://public/img/su/bkg') ?>" class="bkg" fit="cover"></el-image>
<div class="box hcenter su_text">
    本域名jwh.su为保留的苏联国别域名。
</div>
<div class="box hcenter su_text">
    “谁不为苏联解体而惋惜，他就没有良心；<br/>谁要是想恢复过去的苏联，他就没有头脑！”
    <div class="su_text2">
        ——俄罗斯总统 普京
    </div>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                
            };
        },
        mounted() {
            setup(['jwh.su', '主页']);
            
            <?
                // 短链接系统
                if ($__urlData['url'] != '/index.php') {
                    $data = sql_query1('SELECT * FROM shortUrl WHERE tag LIKE ? ORDER BY id DESC LIMIT 1', [substr($__url, 1)]);
                    $url = $data['url'];
                    if ($data) {
                        echo <<<CODE
                            document.location.href = '$url';
CODE;
                    }
                }
            ?>
        },
        methods: {
            
        }
    };
</script>