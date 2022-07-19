<?php
    class c {
        public static $ROUTER = [
            // 这儿不用担心域名中的“.”歧义（正则元字符“.”，表示任意一个字符）问题。就算歧义了，你把个什么wwwfakejwhgzs.com解析到我这，nginx就把你拦了，还轮得到我router来拦？
            'www.jwhgzs.com' => [
                0 => '/www/$'
            ],
            'api.jwhgzs.com' => [
                0 => '/api/$',
                1 => 'api'
            ],
            'user.jwhgzs.com' => [
                0 => '/user/$'
            ],
            'admin.jwhgzs.com' => [
                0 => '/admin/$'
            ],
            'forum.jwhgzs.com' => [
                0 => '/forum/$'
            ],
            'xnzx.jwhgzs.com' => [
                0 => '/xnzx/$'
            ],
            '(.*).jwh.su' => [
                0 => '/jwh.su'
            ]
        ];
        
        public static $MAIN_SERVER = 'https://www.jwhgzs.com';
        public static $STATIC_SERVER = 'https://jwh-cos-lovi-1256760407.cos.ap-guangzhou.myqcloud.com';
        public static $COOKIE_MAINDOMAIN = '.jwhgzs.com'; // 旧浏览器若要泛匹配必须域名前面加点，参考：http://www.faqs.org/rfcs/rfc2109.html
        public static $UPLOAD_SIZELIMIT = 1024 * 1024 * 15;
        public static $USER_EDITABLESQLKEYS = [
            'pass', 'selfIntroduce'
        ];
        public static $USER_PUBLICSQLKEYS = [
            'id', 'name', 'userGroup', 'userClassify', 'userAuth', 'realName', 'avatarVersion', 'selfIntroduce'
        ];
        // $ADMIN_UIDS格式：level => uid
        public static $ADMIN_UIDS = [
            1 => [ 3, 27 ],
            100 => [ 1 ]
        ];
        public static $USERINF_LENGTH = [
            'name_min' => 3,
            'name_max' => 10,
            'pass_min' => 8,
            'pass_max' => 32
        ];
        public static $USERTOKEN_EXPTIME = 60 * 60 * 12 * 1000;
        public static $USERONLINE_INTERVALTIME = 5 * 1000;
        public static $JSTHREAD_INTERVAL = 2000;
        public static $USERLOGINDETAILS_LIMIT = 20;
        public static $URLS_TABLE = [
            'local' => [
                'www' => [
                    0 => 'https://www.jwhgzs.com'
                ],
                'api' => [
                    0 => 'https://api.jwhgzs.com'
                ],
                'user' => [
                    0 => 'https://user.jwhgzs.com'
                ],
                'admin' => [
                    0 => 'https://admin.jwhgzs.com'
                ],
                'xnzx' => [
                    0 => 'https://xnzx.jwhgzs.com'
                ],
                'forum' => [
                    0 => 'https://forum.jwhgzs.com'
                ],
                'su' => [
                    0 => 'https://www.jwh.su'
                ],
                'su_short' => [
                    0 => 'https://jwh.su'
                ]
            ],
            'static' => [
                0 => 'https://static.jwhgzs.com',
                'public' => [
                    0 => '/public',
                    'css' => [
                        'element_plus' => '/element_plus/dist/index.css?v=1',
                        'element_plus_extra1' => '/element_plus/theme-chalk/display.css?v=1',
                        'quill_f1' => '/quill/dist/quill.core.css?v=1',
                        'quill_f2' => '/quill/dist/quill.snow.css?v=1',
                        'quill_f3' => '/quill/dist/quill.bubble.css?v=1',
                        'font_awesome' => '/font_awesome/css/all.min.css?v=1'
                    ],
                    'js' => [
                        'jquery' => '/jquery/jquery.min.js?v=1',
                        'jquery_cookie' => '/jquery/jquery.cookie.min.js?v=1',
                        'vue' => '/vue/dist/vue.global.prod.js?v=1',
                        'element_plus' => '/element_plus/dist/index.full.min.js?v=1',
                        'element_plus_cn' => '/element_plus/dist/locale/zh-cn.min.js?v=1',
                        'quill' => '/quill/dist/quill.min.js?v=1',
                        'vaptcha' => '/vaptcha/v3.js?v=1'
                    ],
                    'img' => [
                        0 => '/img',
                        'logo_banner' => '/jwh/logo/banner_blue.svg?v=1',
                        'logo_ico' => '/jwh/logo/logo.ico?v=1',
                        'beian_icon' => '/beian_icon.png?v=1',
                        'juri_logo' => '/jwh/logo/juri.png?v=1',
                        'canva_logo' => '/links_logo/canva.svg?v=1',
                        'tencentcloud_logo' => '/links_logo/tencent-cloud.svg?v=1',
                        'rainyun_logo' => '/links_logo/rainyun.png?v=1',
                        'bt_logo' => '/links_logo/bt.svg?v=1',
                        'vaptcha_logo' => '/links_logo/vaptcha.png?v=1',
                        'quyu_logo' => '/links_logo/quyu.png?v=1',
                        'bkg' => '/bkg.svg?v=1',
                        'link_xnzx' => '/links_banner/xnzx.svg?v=1',
                        'link_forum' => '/links_banner/forum.svg?v=1',
                        'link_su' => '/links_banner/su.svg?v=1',
                        'link_xnzx_weekly' => '/links_banner/xnzx/weekly.svg?v=1',
                        'link_xnzx_PA' => '/links_banner/xnzx/PA.svg?v=1',
                        'su' => [
                            0 => '/su',
                            'bkg' => '/bkg.jpg?v=1'
                        ],
                        'PA' => [
                            0 => '/PA'
                        ]
                    ]
                ],
                'user' => [
                    0 => '/user',
                    'avatar' => '/avatar',
                    'forum' => '/forum',
                    'PA' => '/PA'
                ]
            ],
            'static_user' => [
                0 => '/user'
            ],
            'beian' => [
                'icp' => 'https://beian.miit.gov.cn',
                'gong_an' => 'http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=44078102440947'
            ],
            'sponsors' => [
                'canva' => [
                    0 => 'https://www.canva.com'
                ],
                'tencent-cloud' => [
                    0 => 'https://cloud.tencent.com'
                ],
                'rainyun' => [
                    0 => 'https://www.rainyun.com/?ref=16719'
                ],
                'bt' => [
                    0 => 'https://www.bt.cn/?invite_code=MV96amRiYWI='
                ],
                'vaptcha' => [
                    0 => 'https://www.vaptcha.com/'
                ],
                'quyu' => [
                    0 => 'https://www.quyu.net/'
                ]
            ],
            'vaptcha' => [
                'api' => [
                    'sms' => [
                        0 => 'https://sms.vaptcha.com/send'
                    ]
                ]
            ]
        ];
        
        public static $SEO_INF = [
            'desc' => '九尾狐工作室，数学、编程、魔方高手云集之地~',
            'keys' => '九尾狐,九尾狐工作室,xf,xf工作室,jwh,jwhgzs,xfgzs,台山市,新宁中学,数学,编程,魔方,2020秋届,11班'
        ];
        public static $CONTACT_INF = [
            'qq' => '725058854',
            'email' => 'admin@jwhgzs.com'
        ];
        public static $VAPTCHA_CONFIG = [
            'scenes' => [
                'test' => 0,
                'login' => 1,
                'phoneLogin' => 2,
                'signup' => 3,
                'phoneVerify' => 4,
                'important' => 5,
                'blank' => 6
            ]
        ];
        public static $VAPTCHA_SMS_CONFIG = [
            'templateIds' => [
                'default' => '1'
            ],
            
            'sendLimitPerDay' => 5,
            'verifyCodeExpTime' => 60 * 10 * 1000
        ];
        public static $STATICCS_CONFIG = [
            'root' => '/www/wwwroot/static.jwhgzs.com'
        ];
        
        public static $QUILL_CONFIG = [
            'formatReplaceTable' => [
                'ql-align-center' => 'quill_align_center',
                'ql-align-right' => 'quill_align_right',
                'ql-align-justify' => 'quill_align_justify'
            ]
        ];
        public static $XNZX_WEEKLY_CONFIG = [
            'year' => 2020,
            'contentMaxLength' => 3000,
            'basicFormatter' => [
                ',' => '，',
                ':' => '：',
                ';' => '；',
                '?' => '？',
                '!' => '！',
                '(' => '（',
                ')' => '）',
                '\\' => '、',
                '〝' => '"',
                '〞' => '"',
                '″' => '"',
                '＂' => '"',
                '－' => '—',
                '∽' => '~',
                '<<' => '《',
                '>>' => '》'
            ]
        ];
        public static $XNZX_PA_CONFIG = [
            'classes' => [ 2020 => [ 11 ] ],
            'defaultPhotoNum' => 10,
            'defaultPhotoVersion' => 0,
            'maxSortPeople' => 10
        ];
        public static $FORUM_CONFIG = [
            'classifies' => [ '官方区', '首页区', '意见反馈区', '吹水区'],
            'defaultClassifyId' => 3,
            'adminClassifyIds' => [ 0, 1 ],
            'carouselClassifyId' => 1,
            'oriContentLengthLimit' => 500 * 1024
        ];
    }
    
    require_once __DIR__ . '/secrets.php';
?>