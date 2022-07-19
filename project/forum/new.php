<style type="text/css">
    
</style>

<div class="box box_md hcenter">
    <span class="box_title">论坛</span>
    <span class="box_graytitle">{{ (! formData.pid) ? '发布话题' : (formData.id ? '编辑话题' : '话题发言') }}</span>
    <div style="margin-top: 40px;">
        <div class="box_tip2">
            <span class="spanAwesome" style="padding-bottom: 5px;">提示：你输入的内容实时保存哦，不用担心！</span>
            <el-button size="small" plain @click="clear">清空表单</el-button>
        </div>
        <el-form label-width="8rem" :model="formData" :rules="formVerify" label-position="top">
            <el-form-item v-if="formData.pid && (! formData.id)" label="回复话题ID" prop="pid">
                <el-input type="number" v-model="formData.pid" disabled>
                    <template #prefix>
                        <i class="fas fa-list-ol"></i>
                    </template>
                </el-input>
            </el-form-item>
            <div v-if="formData.id">
                <el-form-item label="话题ID" prop="id">
                    <el-input type="number" v-model="formData.id" disabled>
                        <template #prefix>
                            <i class="fas fa-list-ol"></i>
                        </template>
                    </el-input>
                </el-form-item>
            </div>
            <el-form-item v-if="! formData.pid" label="分类" prop="classify">
                <el-select v-model="formData.classify" placeholder=" 点击选择分类" @change="saveContent">
                    <el-option v-for="(v, k) in forumData.classifies" :key="k" :label="v" :value="k"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item v-if="! formData.pid" label="标题" prop="title">
                <el-input type="text" v-model="formData.title" @input="saveContent">
                    <template #prefix>
                        <i class="fas fa-heading"></i>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item label="" prop="">
                <quill-container title="内容" :form-style="true">
                    <quill v-model="formData.content" title="内容" @input="saveContent"></quill>
                </quill-container>
            </el-form-item>
            <el-divider style="margin-top: 60px;"></el-divider>
            <div style="width: 100%; text-align: center;">
                <el-button type="primary" size="large" style="width: 50%;" @click="doit" plain round>
                    提交
                </el-button>
            </div>
        </el-form>
    </div>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                forumData: false,
                
                formData: {
                    pid: (getUrlParam('pid') ? parseInt(getUrlParam('pid')) : null),
                    id: (getUrlParam('id') ? parseInt(getUrlParam('id')) : null),
                    title: '',
                    classify: (getUrlParam('classify') ? parseInt(getUrlParam('classify')) : null),
                    content: ''
                },
                formVerify: {
                    title: [
                        {
                            required: true,
                            message: '请输入标题~',
                            trigger: 'blur'
                        }
                    ],
                    classify: [
                        {
                            required: true,
                            message: '请选择分类~',
                            trigger: 'blur'
                        }
                    ],
                    content: [
                        {
                            required: true,
                            message: '请输入内容~',
                            trigger: 'blur'
                        }
                    ]
                }
            };
        },
        mounted() {
            setup(['论坛', '发布'], true);
            if (localStorage.getItem('forum_form') && (! (t.formData.pid || t.formData.id))) {
                var d = JSON.parse(localStorage.getItem('forum_form'));
                t.formData.classify = d.classify;
                t.formData.title = d.title;
                t.formData.content = d.content;
            }
            
            if (t.formData.id) {
                post({
                    name: '论坛数据获取',
                    url: '<?= u('local://api/forum/detail') ?>',
                    data: { id: t.formData.id },
                    callback_ok: function(data) {
                        var d = data.data.topicTree[0];
                        if (d.pid) t.formData.pid = d.pid;
                        t.formData.classify = (d.classify ? parseInt(d.classify) : null);
                        t.formData.title = d.title;
                        t.formData.content = d.content;
                    },
                    hideSuccUI: true,
                    errText: '本错误非致命，你可以继续进行操作~'
                });
            }
        },
        methods: {
            saveContent() {
                if (! (t.formData.pid || t.formData.id)) {
                    localStorage.setItem('forum_form', JSON.stringify(t.formData));
                }
            },
            _clear() {
                localStorage.setItem('forum_form', '');
            },
            clear() {
                confMsg('确定要清空已输入的表单内容吗？', 'warning', function() {
                    t._clear();
                    document.location.reload();
                });
            },
            doit() {
                var jump = function() {
                    if (t.formData.pid)
                        document.location.href = '<?= u('local://forum/detail') ?>?id=' + t.formData.pid;
                    else if (t.formData.id)
                        document.location.href = '<?= u('local://forum/detail') ?>?id=' + t.formData.id;
                    else
                        document.location.href = '<?= u('local://forum') ?>?classify=' + t.formData.classify;
                };
                var next = function(vaptchaData) {
                    post({
                        name: '论坛话题发布',
                        url: '<?= u('local://api/forum/new') ?>',
                        data: $.extend(t.formData, vaptchaData),
                        callback_ok: function(data) {
                            t._clear();
                            setTimeout(jump, 3000);
                        },
                        callback_ok2: function(data) {
                            jump();
                        },
                        succText: '即将返回~'
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://forum') ?>';
        };
        post({
            name: '论坛数据同步',
            url: '<?= u('local://api/forum') ?>',
            data: {},
            callback_ok: function(data) {
                t.forumData = data.data.forumData;
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