        </div>
        
        <div id="tail" class="tail">
            <!-- 当年的页尾已不再是页尾，当年的备案已不再回。也许一波三折，才算是真正的人生吧。
                ——记2022年2月27日由于生活资金紧张问题，被迫注销备案，台山市九尾狐网络有限公司即将不复存在…… -->
            <!--
                <div>
                    Copyright &copy; 2020-<?= date('Y') ?> jwhgzs.com 台山市九尾狐网络有限公司 版权所有
                </div>
                <div class="tail_div">
                    <el-link href="<?= u('beian://icp') ?>" target="_blank" type="primary">粤ICP备2021007196号</el-link>
                    &nbsp;
                    <el-link href="<?= u('beian://gong_an') ?>" target="_blank" type="primary">
                        <img alt="公安备案图标" class="imgAwesome" src="<?= u('static://public/img/beian_icon') ?>"/>
                        粤公网安备44078102440947号
                    </el-link>
                </div>
                <div class="tail_div">
                    联系我们：QQ群：725058854，邮箱：admin@jwhgzs.com
                </div>
            -->
            <div>
                Copyright &copy; 2020-<?= date('Y') ?> jwhgzs.com 谭镇洋 版权所有
            </div>
            <el-divider></el-divider>
            <div class="tail_div">
                联系我们：QQ群：<?= c::$CONTACT_INF['qq'] ?>，邮箱：<?= c::$CONTACT_INF['email'] ?>
            </div>
            <div class="tail_div">
                <el-link class="tail_link" href="<?= u('sponsors://canva') ?>" target="_blank" type="primary">
                    <img src="<?= u('static://public/img/canva_logo') ?>" alt="Canva logo" class="tail_linklogo"/>
                </el-link>
                <el-link class="tail_link" href="<?= u('sponsors://bt') ?>" target="_blank" type="primary">
                    <img src="<?= u('static://public/img/bt_logo') ?>" alt="宝塔 logo" class="tail_linklogo"/>
                </el-link>
                <el-link class="tail_link" href="<?= u('sponsors://tencent-cloud') ?>" target="_blank" type="primary">
                    <img src="<?= u('static://public/img/tencentcloud_logo') ?>" alt="腾讯云 logo" class="tail_linklogo"/>
                </el-link>
                <!--
                <el-link class="tail_link" href="<?= u('sponsors://rainyun') ?>" target="_blank" type="primary">
                    <img src="<?= u('static://public/img/rainyun_logo') ?>" alt="雨云 logo" class="tail_linklogo"/>
                </el-link>
                -->
                <el-link class="tail_link" href="<?= u('sponsors://vaptcha') ?>" target="_blank" type="primary">
                    <img src="<?= u('static://public/img/vaptcha_logo') ?>" alt="Vaptcha logo" class="tail_linklogo"/>
                </el-link>
                <el-link class="tail_link" href="<?= u('sponsors://quyu') ?>" target="_blank" type="primary">
                    <img src="<?= u('static://public/img/quyu_logo') ?>" alt="Vaptcha logo" class="tail_linklogo"/>
                </el-link>
            </div>
        </div>
        
        <el-dialog v-for="(v, k) in dialogs" :key="k" v-model="v.status" :close-on-click-modal="v.canClose" :close-on-press-escape="v.canClose" :show-close="v.canClose" @close="v.callback_cancel ? v.callback_cancel : function() {}" destroy-on-close>
            <template #title>
                <div style="font-size: 125%;">
                    <i v-if="v.scene == 'success'" class="fas fa-check-circle" style="color: #67C23A;"></i>
                    <i v-if="v.scene == 'warning'" class="fas fa-exclamation-triangle" style="color: #E6A23C;"></i>
                    <i v-if="v.scene == 'info'" class="fas fa-info-circle" style="color: #909399;"></i>
                    <i v-if="v.scene == 'danger'" class="fas fa-times-circle" style="color: #F56C6C;"></i>
                    <i v-if="v.scene == 'input'" class="fas fa-italic" style="color: #909399;"></i>
                    <i v-if="v.scene == 'loading'" class="fas fa-spinner rotate" style="color: #909399;"></i>
                    <span v-html="v.title" style="margin-left: 15px;"></span>
                </div>
            </template>
            <template #footer>
                <el-button v-if="v.canCancel" type="primary" @click="dialogClose(k, 0)" plain>取消</el-button>
                <el-button v-if="! v.hideOK" type="primary" @click="dialogClose(k, 1)">确定</el-button>
            </template>
            
            <div v-html="v.content" style="margin-bottom: 10px;"></div>
            <template v-if="v.inputType == 'textarea'">
                <el-input v-model="v.value" type="textarea" :autosize="{ minRows: 3, maxRows: 6 }"></el-input>
            </template>
            <template v-if="v.inputType == 'text'">
                <el-input v-model="v.value" type="text"></el-input>
            </template>
            <template v-if="v.inputType == 'number'">
                <el-input v-model="v.value" type="number"></el-input>
            </template>
            <template v-if="v.inputType == 'password'">
                <el-input v-model="v.value" type="password" show-password></el-input>
            </template>
            <template v-if="v.inputType == 'select'">
                <el-select v-model="v.value">
                    <el-option v-for="v2 in v.options" :label="v2.label" :value="v2.value"></el-option>
                </el-select>
            </template>
            <template v-if="v.inputType == 'loading'">
                <el-progress :percentage="v.value >= 0 ? v.value : 100" :indeterminate="v.value < 0" :duration="1">
                    {{ v.value < 0 ? '' : v.value + '%' }}
                </el-progress>
            </template>
        </el-dialog>
        <input v-show="false" id="fileUploader" type="file"/>
        
        <script type="text/javascript" src="<?= u('static://public/js/vue') ?>"></script>
        <script type="text/javascript" src="<?= u('static://public/js/element_plus') ?>"></script>
        <script type="text/javascript" src="<?= u('static://public/js/element_plus_cn') ?>"></script>
        <script type="text/javascript" src="<?= u('static://public/js/quill') ?>"></script>
        <script type="text/javascript" src="<?= u('static://public/js/vaptcha') ?>"></script>
        <script type="text/javascript">
            <? i('./js/funcs.js'); ?>
            <? i('./js/setup.js'); ?>
        </script>
    <body/>
<html/>