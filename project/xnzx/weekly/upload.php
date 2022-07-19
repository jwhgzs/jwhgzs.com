<style type="text/css">
    
</style>

<div class="box box_md hcenter">
    <span class="box_title">新宁空间</span>
    <span class="box_graytitle">
        班级周报 - {{ getUrlParam('new') ? (formData.editPid ? '修改周报信息' : '发起周报') : (formData.editPid && formData.editId ? '修改电子稿' : '上传电子稿') }}
    </span>
    <div style="margin-top: 40px;">
        <div v-if="! (getUrlParam('new') || formData.editPid)" class="box_tip2">
            <span class="spanAwesome" style="padding-bottom: 5px;">提示：你输入的内容实时保存哦，不用担心！</span>
            <el-button size="small" plain @click="clear">清空表单</el-button>
        </div>
        <el-divider v-if="getUrlParam('new') || formData.editPid"></el-divider>
        <div v-if="! getUrlParam('new')">
            <el-form label-width="8rem" :model="formData" :rules="formVerify" label-position="top">
                <div v-if="! formData.editPid">
                    <el-form-item label="周报" prop="pid">
                        <el-select v-model="formData.pid" placeholder=" 点击选择周报" @change="resetPeople(0); saveContent()">
                            <template #prefix>
                                <i class="fas fa-calendar-alt"></i>
                            </template>
                            <template v-for="(v, k) in weeklyList" :key="v.id">
                                <el-option v-if="! v.finish" :label="genWeeklyTitle(v)" :value="v.id"></el-option>
                            </template>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="打稿人（你）" prop="typist">
                        <el-select v-model="formData.typist" placeholder=" 点击选择打稿人（你）" no-data-text="请先选择周报" @change="resetPeople(0, 1); saveContent()">
                            <template #prefix>
                                <i class="fas fa-keyboard"></i>
                            </template>
                            <template v-for="(v, k) in peopleList.typists" :key="k">
                                <el-option :label="v[0] + '号 ' + v[1]" :value="v[0]"></el-option>
                            </template>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="稿件作者" prop="author">
                        <el-select v-model="formData.author" placeholder=" 点击选择稿件作者" no-data-text="请先选择打稿人" @change="saveContent">
                            <template #prefix>
                                <i class="fas fa-user-edit"></i>
                            </template>
                            <template v-for="(v, k) in peopleList.authors" :key="k">
                                <el-option v-if="isTypistMatchAuthor(getweeklyListById(formData.pid), formData.typist, v[0])" :label="v[0] + '号 ' + v[1]" :value="v[0]"></el-option>
                            </template>
                        </el-select>
                    </el-form-item>
                </div>
                <div v-if="formData.editPid && formData.editId">
                    <el-form-item label="修改稿件ID" prop="editId">
                        <el-input type="number" v-model="formData.editId" disabled>
                            <template #prefix>
                                <i class="fas fa-list-ol"></i>
                            </template>
                        </el-input>
                    </el-form-item>
                </div>
                <div v-if="(! formData.editPid) || (formData.editPid && formData.editId)">
                    <el-divider>稿件内容</el-divider>
                    <el-form-item label="标题" prop="title">
                        <el-input type="text" v-model="formData.title" @input="saveContent">
                            <template #prefix>
                                <i class="fas fa-heading"></i>
                            </template>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="题记（选填）" prop="tiji">
                        <el-input type="textarea" v-model="formData.tiji" :rows="2" @input="saveContent"></el-input>
                    </el-form-item>
                    <el-form-item label="正文" prop="content">
                        <span class="box_tip3">如有小标题请在小标题前加上井号“#”哦！</span>
                        <el-input type="textarea" v-model="formData.content" :rows="20" maxlength="<?= c::$XNZX_WEEKLY_CONFIG['contentMaxLength'] ?>" show-word-limit @input="saveContent"></el-input>
                    </el-form-item>
                    <el-form-item label="后记（选填）" prop="houji">
                        <el-input type="textarea" v-model="formData.houji" :rows="2" @input="saveContent"></el-input>
                    </el-form-item>
                </div>
                <el-divider style="margin-top: 60px;"></el-divider>
                <div style="width: 100%; text-align: center;">
                    <el-button type="primary" size="large" style="width: 50%;" @click="doit(0)" plain round>
                        提交
                    </el-button>
                </div>
            </el-form>
        </div>
        <div v-if="getUrlParam('new')">
            <el-form :model="formData" :rules="formVerify" label-position="top">
                <div v-if="formData.editPid && (! formData.editId)">
                    <el-form-item label="修改周报ID" prop="editPid">
                        <el-input type="number" v-model="formData.editPid" disabled>
                            <template #prefix>
                                <i class="fas fa-list-ol"></i>
                            </template>
                        </el-input>
                    </el-form-item>
                    <el-divider></el-divider>
                </div>
                <el-form-item label="班级" prop="class_">
                    <el-select v-model="formData.class_" :disabled="formData.editPid && (! formData.editId)" placeholder=" 点击选择班级" @change="resetPeople(1)">
                        <template #prefix>
                            <i class="fas fa-graduation-cap"></i>
                        </template>
                        <template v-for="(v, k) in weeklyData.classes" :key="k">
                            <el-option :label="v + '班'" :value="v"></el-option>
                        </template>
                    </el-select>
                </el-form-item>
                <el-form-item label="学期" prop="term">
                    <el-input type="text" v-model="formData.term">
                        <template #prefix>
                            <i class="fas fa-calendar-week"></i>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item label="期数" prop="num">
                    <el-input type="number" v-model="formData.num">
                        <template #prefix>
                            <i class="fas fa-list-ol"></i>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item label="备注" prop="note">
                    <el-input type="text" v-model="formData.note">
                        <template #prefix>
                            <i class="fas fa-sticky-note"></i>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item label="人员安排" prop="">
                    <div class="group" style="margin: 0 !important;">
                        <div>
                            <el-select v-model="tmpData.author" style="margin-right: 10px;" placeholder=" 点击选择稿件作者" no-data-text="请选择正确的班级">
                                <template #prefix>
                                    <i class="fas fa-user-edit"></i>
                                </template>
                                <el-option v-for="(v, k) in weeklyData.students[parseInt(formData.class_)]" :key="k" :label="v[0] + '号 ' + v[1]" :value="v[0]"></el-option>
                            </el-select>
                            <el-select v-model="tmpData.typist" placeholder=" 点击选择打稿人" no-data-text="请选择正确的班级">
                                <template #prefix>
                                    <i class="fas fa-keyboard"></i>
                                </template>
                                <el-option v-for="(v, k) in weeklyData.students[parseInt(formData.class_)]" :key="k" :label="v[0] + '号 ' + v[1]" :value="v[0]"></el-option>
                            </el-select>
                        </div>
                        <!-- margin-bottom: -10px用来消掉btns设置的尾margin（间margin还是有必要的） -->
                        <div style="margin-top: 10px; margin-bottom: -10px;">
                            <el-button class="btns" :disabled="! (tmpData.author && tmpData.typist)" type="primary" plain @click="addPeople()">添加</el-button>
                            <el-button class="btns" plain @click="addPeople2()">批量添加</el-button>
                            <el-button class="btns" type="danger" plain @click="delAllPeople()">清除全部</el-button>
                        </div>
                    </div>
                    <el-table :data="formData.people" max-height="300">
                        <el-table-column prop="" label="稿件作者" min-width="150">
                            <template #default="scope">
                                {{ scope.row[0] + '号 ' + getSnameBySid(scope.row[0], formData.class_) }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="" label="打稿人" min-width="150">
                            <template #default="scope">
                                {{ scope.row[1] + '号 ' + getSnameBySid(scope.row[1], formData.class_) }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="" label="操作">
                            <template #default="scope">
                                <el-button size="small" type="danger" plain @click="deletePeople(scope.$index)">删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>
                <el-form-item label="是否隐藏作者名" prop="nameInvisible">
                    <el-select v-model="formData.nameInvisible" placeholder=" 点击选择是否隐藏作者名">
                        <template #prefix>
                            <i class="fas fa-user-secret"></i>
                        </template>
                        <el-option label="不隐藏" :value="0"></el-option>
                        <el-option label="隐藏" :value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-divider style="margin-top: 60px;"></el-divider>
                <div style="width: 100%; text-align: center;">
                    <el-button type="primary" size="large" style="width: 50%;" @click="doit(1)" plain round>
                        提交
                    </el-button>
                </div>
            </el-form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var vueConfig = {
        data() {
            return {
                weeklyList: false,
                weeklyData: false,
                
                formVerify: {
                    pid: {
                        required: true,
                        message: '请选择周报~',
                        trigger: 'blur'
                    },
                    typist: {
                        required: true,
                        message: '请选择打稿人（你）~',
                        trigger: 'blur'
                    },
                    author: {
                        required: true,
                        message: '请输入稿件作者~',
                        trigger: 'blur'
                    },
                    title: {
                        required: true,
                        message: '请输入稿件标题~',
                        trigger: 'blur'
                    },
                    content: {
                        required: true,
                        message: '请输入稿件内容~',
                        trigger: 'blur'
                    },
                    
                    class_: {
                        required: true,
                        message: '请选择班级~',
                        trigger: 'blur'
                    },
                    term: {
                        required: true,
                        message: '请输入学期~',
                        trigger: 'blur'
                    },
                    num: {
                        required: true,
                        message: '请输入期数~',
                        trigger: 'blur'
                    }
                },
                
                formData: {
                    pid: '',
                    typist: '',
                    author: '',
                    title: '',
                    tiji: '',
                    content: '',
                    
                    class_: '',
                    term: '',
                    num: '',
                    note: '',
                    people: [],
                    nameInvisible: 0,
                    
                    editPid: getUrlParam('edit_pid'),
                    editId: getUrlParam('edit_id')
                },
                tmpData: {
                    author: '',
                    typist: ''
                },
                peopleList: {
                    authors: [],
                    typists: []
                }
            };
        },
        mounted() {
            setup({'新宁空间': '', '班级周报': '<?= u('local://xnzx/weekly') ?>', '上传': ''}, true);
            if (t.formData.editPid) {
                post({
                    name: '新宁空间-班级周报数据获取',
                    url: '<?= u('local://api/xnzx/weekly/detail') ?>',
                    data: { id: t.formData.editPid, selectAll: true },
                    callback_ok: function(data) {
                        if (t.formData.editPid && t.formData.editId) {
                            var l = data.data.articleList, d = [];
                            for (var i in l) {
                                var v = l[i];
                                if (v.id == t.formData.editId) {
                                    d = v;
                                }
                            }
                            t.formData.title = d.title;
                            t.formData.tiji = d.tiji;
                            t.formData.content = d.oriContent;
                            t.formData.houji = d.houji;
                        } else {
                            var d = data.data.weeklyDetail;
                            var d2 = data.data.articleList;
                            t.formData.class_ = d.class_;
                            t.formData.term = d.term;
                            t.formData.num = d.num;
                            t.formData.note = d.note;
                            t.formData.nameInvisible = parseInt(d.nameInvisible);
                            var people = [];
                            for (var i in d2) {
                                var v = d2[i];
                                people.push([parseInt(v.author[0]), parseInt(v.typist[0])]);
                            }
                            t.formData.people = people;
                        }
                    },
                    hideSuccUI: true,
                    errText: '本错误非致命，你可以继续进行操作~'
                });
            } else {
                if (localStorage.getItem('xnzx_weekly_form')) {
                    var d = JSON.parse(localStorage.getItem('xnzx_weekly_form'));
                    t.formData.pid = d.pid;
                    t.formData.title = d.title;
                    t.formData.tiji = d.tiji;
                    t.formData.content = d.content;
                    t.formData.houji = d.houji;
                    setTimeout(function() {
                        t.resetPeople(0);
                        t.formData.author = d.author;
                        t.formData.typist = d.typist;
                    }, 200);
                }
            }
        },
        methods: {
            getUrlParam: getUrlParam,
            inArray: inArray,
            genWeeklyTitle(v) {
                return v.class + '班 ' + v.term + ' 第' + v.num + '期 ' + v.note;
            },
            doit(isNew) {
                var next = function(vaptchaData) {
                    var jump = function() {
                        document.location.href = '<?= u('local://xnzx/weekly') ?>';
                    };
                    var data = $.extend(t.formData, vaptchaData);
                    if (isNew) {
                        data = $.extend(data, { isNew: true });
                    }
                    post({
                        name: '新宁空间-班级周报上传',
                        url: '<?= u('local://api/xnzx/weekly/upload') ?>',
                        data: data,
                        callback_ok: function(data) {
                            t._clear();
                            setTimeout(jump, 3000);
                        },
                        callback_ok2: jump,
                        succText: '即将返回主页~'
                    });
                };
                vaptchaGo(<?= c::$VAPTCHA_CONFIG['scenes']['important'] ?>, next);
            },
            getweeklyListById(id) {
                for (var k in t.weeklyList) {
                    var v = t.weeklyList[k];
                    if (v.id == id) {
                        return v;
                    }
                }
                return false;
            },
            getSnameBySid(sid, class_) {
                var d = t.weeklyData.students[class_];
                for (var k in d) {
                    var v = d[k];
                    if (v[0] == sid)
                        return v[1];
                }
                return false;
            },
            isTypistMatchAuthor(list, typist, author) {
                for (var k in list.typists) {
                    var v = list.typists[k];
                    if (v[0] == typist && list.authors[k][0] == author) {
                        return true;
                    }
                }
                return false;
            },
            saveContent() {
                if (! t.formData.editPid)
                    localStorage.setItem('xnzx_weekly_form', JSON.stringify(t.formData));
            },
            _clear() {
                localStorage.setItem('xnzx_weekly_form', '');
            },
            clear() {
                confMsg('确定要清空已输入的表单内容吗？', 'warning', function() {
                    t._clear();
                    document.location.reload();
                });
            },
            resetPeople(type, isTypistChange) {
                if (type == 0) {
                    if (! isTypistChange) {
                        t.formData.typist = t.formData.author = '';
                        t.peopleList.typists = t.getweeklyListById(t.formData.pid).typists;
                        t.peopleList.authors = t.getweeklyListById(t.formData.pid).authors;
                    } else {
                        t.formData.author = '';
                        t.peopleList.authors = t.getweeklyListById(t.formData.pid).authors;
                    }
                } else if (type == 1) {
                    t.formData.tmpData = {
                        author: '',
                        typist: ''
                    };
                }
            },
            addPeople() {
                t.formData.people.push([ t.tmpData.author, t.tmpData.typist ]);
            },
            addPeople2() {
                if (! t.formData.class_) {
                    errMsg('请先选择班级哦~');
                    return;
                }
                inputMsg('请选择批量添加模式：', 'select', function(value) {
                    switch (value) {
                        case 0:
                            inputMsg('请按照“打稿人#稿件作者”（均为学号）格式填写，一行一个打稿人，多个稿件作者用英文逗号“,”分割~', 'textarea', function(value) {
                                var rows = (value ? value : '').split('\n'), list = [];
                                for (var k in rows) {
                                    var v = rows[k];
                                    if (! v.trim())
                                        continue;
                                    var arr = v.split('#');
                                    var typist = parseInt(arr[0]), authors = arr[1].split(',');
                                    for (var k2 in authors) {
                                        var author = parseInt(authors[k2]);
                                        list.push([ author, typist ]);
                                    }
                                }
                                t.formData.people = list;
                                succMsg('批量添加成功~');
                            });
                            break;
                        case 1:
                            var list = [];
                            var students = t.weeklyData.students[parseInt(t.formData.class_)];
                            for (var k in students) {
                                var v = students[k];
                                list.push([ v[0], v[0] ]);
                            }
                            t.formData.people = list;
                            succMsg('批量添加成功~');
                            break;
                        default:
                            break;
                    }
                }, function() {}, true, '', [
                    {label: '快速1对1添加', value: 0},
                    {label: '全班自打', value: 1},
                ]);
            },
            deletePeople(index) {
                t.formData.people.splice(index, 1);
            },
            delAllPeople() {
                confMsg('确定要清除全部人员安排嘛？', 'warning', function() {
                    t.formData.people = [];
                    succMsg('清除成功~');
                });
            }
        }
    };
    var appThread = function() {
        var jump = function() {
            document.location.href = '<?= u('local://xnzx/weekly') ?>';
        };
        post({
            name: '新宁空间-班级周报数据同步',
            url: '<?= u('local://api/xnzx/weekly') ?>',
            data: {},
            callback_ok: function(data) {
                t.weeklyList = data.data.weeklyList;
                t.weeklyData = data.data.weeklyData;
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