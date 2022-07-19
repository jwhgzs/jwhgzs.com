<style type="text/css">
    
</style>

<div class="box box_ml hcenter">
    <div class="box_title">后台管理</div>
    <div class="box_graytitle">短链接管理</div>
    <el-divider></el-divider>
    <div class="group">
        <el-button type="primary" size="large" @click="add" plain>
            <i class="fas fa-plus"></i>&emsp;新建短链
        </el-button>
    </div>
    <el-table :data="urlList" max-height="500">
        <el-table-column prop="id" label="ID"></el-table-column>
        <el-table-column prop="" label="短链接" width="200">
            <template #default="scope">
                {{ makeShortUrl(scope.row) }}
            </template>
        </el-table-column>
        <el-table-column prop="url" label="原链接" width="150">
            <template #default="scope">
                <el-button size="small" text @click="jump2(scope.row.url)">跳转</el-button>
                <el-button size="small" text @click="copy(scope.row.url)">复制</el-button>
            </template>
        </el-table-column>
        <el-table-column prop="note" label="备注" width="200"></el-table-column>
        <el-table-column prop="" label="操作" width="150">
            <template #default="scope">
                <el-button size="small" plain @click="edit(scope.row.id, scope.row.url, scope.row.tag, scope.row.note)">编辑</el-button>
                <el-button size="small" type="danger" plain @click="del(scope.row.id)">删除</el-button>
            </template>
        </el-table-column>
    </el-table>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                urlList: []
            };
        },
        mounted() {
            setup(['后台管理', '短链接管理'], true);
        },
        methods: {
            copy: copyIt,
            makeShortUrl(a) {
                return '<?= u('local://su_short') ?>/' + a.tag;
            },
            add() {
                inputMsg('请输入原链接：', 'text', function(url) {
                    inputMsg('请输入短链接标签（不填则随机分配）：', 'text', function(tag) {
                        inputMsg('请输入短链接备注（选填）：', 'text', function(note) {
                            post({
                                name: '后台管理-短链接管理 新建短链',
                                url: '<?= u('local://api/admin/shortUrl/add') ?>',
                                data: { url: url, tag: tag, note: note }
                            });
                        }, function() {}, true);
                    }, function() {}, true);
                }, function() {}, true);
            },
            edit(id, d1, d2, d3) {
                inputMsg('请输入原链接：', 'text', function(url) {
                    inputMsg('请输入短链接标签（不填则随机分配）：', 'text', function(tag) {
                        inputMsg('请输入短链接备注（选填）：', 'text', function(note) {
                            if (url == d1 && tag == d2 && note == d3) {
                                infoMsg('你玩我呢~没改呀~');
                                return;
                            }
                            post({
                                name: '后台管理-短链接管理 修改短链',
                                url: '<?= u('local://api/admin/shortUrl/edit') ?>',
                                data: { id: id, url: url, tag: tag, note: note }
                            });
                        }, function() {}, true, d3);
                    }, function() {}, true, d2);
                }, function() {}, true, d1);
            },
            del(id) {
                var next = function(vaptchaData) {
                    post({
                        name: '后台管理-短链接管理 删除短链',
                        url: '<?= u('local://api/admin/shortUrl/del') ?>',
                        data: $.extend({ id: id }, vaptchaData)
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://admin') ?>';
        };
        post({
            name: '后台管理-短链接管理 数据同步',
            url: '<?= u('local://api/admin/shortUrl') ?>',
            data: {},
            callback_ok: function(data) {
                t.urlList = data.data.urlList;
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