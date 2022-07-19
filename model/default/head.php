<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="description" content="<?= c::$SEO_INF['desc'] ?>"/>
        <meta name="keywords" content="<?= c::$SEO_INF['keys'] ?>"/>
        
        <link rel="stylesheet" href="<?= u('static://public/css/element_plus') ?>"/>
        <link rel="stylesheet" href="<?= u('static://public/css/element_plus_extra1') ?>"/>
        <link rel="stylesheet" href="<?= u('static://public/css/quill_f1') ?>"/>
        <link rel="stylesheet" href="<?= u('static://public/css/quill_f2') ?>"/>
        <link rel="stylesheet" href="<?= u('static://public/css/quill_f3') ?>"/>
        <link rel="stylesheet" href="<?= u('static://public/css/font_awesome') ?>"/>
        <link rel="icon" type="image/x-icon" href="<?= u('static://public/img/logo_ico') ?>"/>
        
        <script type="text/javascript" src="<?= u('static://public/js/jquery') ?>"></script>
        <script type="text/javascript" src="<?= u('static://public/js/jquery_cookie') ?>"></script>

        <script type="text/javascript">
            <? i('./js/important.js'); ?>
        </script>
        
        <style type="text/css">
            <? i('./head.php'); ?>
            <? i('./css/index.css'); ?>
            <? i('./css/components.css'); ?>
            <? i('./css/quill.css'); ?>
        </style>
    </head>
    <body id="body">
        <title>九尾狐工作室 | {{ nav_titles[0] }}</title>
        <div id="header">
            <div id="nav" class="nav">
                <div id="mainnav" class="mainnav">
                    <a href="<?= u('local://www') ?>">
                        <img src="<?= u('static://public/img/logo_banner') ?>" alt="九尾狐logo" class="logo_banner"/>
                    </a>
                </div>
                <div id="subnav" class="subnav">
                    <div v-if="udata" class="nav_userinfo">
                        <user-avatar :udata_="udata"></user-avatar>
                        <span :class="{ nav_userinfo_name: true, authed_uname: (udata.userGroup || udata.userAuth) }">{{ udata.name }}</span>
                    </div>
                    <el-breadcrumb class="nav_breadcrumb">
                        <!-- 这里最后一项自动加的css是elementplus自己给的 -->
                        <el-breadcrumb-item v-for="(v, k) in nav_titles">
                            <!-- 最后一个项不给链接 -->
                            <a :href="nav_titles[k + 1] ? nav_urls[k] : null">
                                {{ v }}
                            </a>
                        </el-breadcrumb-item>
                    </el-breadcrumb>
                    <el-menu class="nav_menu" mode="horizontal" :ellipsis="false">
                        <template v-if="isMainDomain">
                            <el-menu-item v-if="! isLoggedin" index="1">
                                <el-button :disabled="isLoginPage" text @click="jumpLogin">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span class="nav_a_span">登录 / 注册</span>
                                </el-button>
                            </el-menu-item>
                            <el-menu-item v-if="isLoggedin" index="2">
                                <el-button :disabled="isUcenterPage && (isUcenterPage ? ownerIsMe : true)" text @click="jump('<?= u('local://user/ucenter') ?>')">
                                    <i class="fas fa-user"></i>
                                    <span class="nav_a_span">我的</span>
                                </el-button>
                            </el-menu-item>
                            <el-menu-item v-if="isLoggedin" index="3">
                                <el-button text @click="logout" href="javascript:;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="nav_a_span">退出</span>
                                </el-button>
                            </el-menu-item>
                        </template>
                        <template v-if="! isMainDomain">
                            <el-menu-item index="1">
                                <el-button text @click="jump('<?= u('local://www') ?>')">
                                    <i class="fas fa-undo"></i>
                                    <span class="nav_a_span">返回主站</span>
                                </el-button>
                            </el-menu-item>
                        </template>
                    </el-menu>
                </div>
            </div>
        </div>
        
        <!-- 占位div，让出nav的位（nav是fixed）  -->
        <div id="block" class="block"></div>
        
        <div id="content">
            <el-image src="<?= u('static://public/img/bkg') ?>" class="bkg" fit="cover"></el-image>