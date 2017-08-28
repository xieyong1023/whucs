<?php
/**
 * 后台菜单
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 16:22
 */

use Library\Tools\UrlHelper;

return [
    'admin_center' => [
        'name' => '个人中心',
        'url' => UrlHelper::adminUrl('/home/adminCenter'),
        'privilege' => 'admin_center',
        'child' => [
            'admin_login_info' => [
                'name' => '登录信息',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'admin_login_info',
                'child' => null,
            ],

            'admin_info' => [
                'name' => '个人资料',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'admin_info',
                'child' => null,
            ],

            'admin_password' => [
                'name' => '修改密码',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'admin_password',
                'child' => null,
            ],
        ]
    ],

    'system' => [
        'name' => '系统',
        'url' => UrlHelper::adminUrl('/home/system'),
        'privilege' => 'system',
        'child' => [
            'system_state' => [
                'name' => '系统信息',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'system_info',
                'child' => null,
            ],

            'admin_privilege' => [
                'name' => '后台权限管理',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'admin_privilege',
                'child' => null,
            ],

            'admin_user' => [
                'name' => '管理员用户',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'admin_user',
                'child' => null,
            ],

            'admin_group' => [
                'name' => '管理员用户组',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'admin_group',
                'child' => null,
            ],

            'site_config' => [
                'name' => '站点设置',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'site_config',
                'child' => null,
            ],
        ]
    ],

    'content' => [
        'name' => '内容',
        'url' => UrlHelper::adminUrl('/home/content'),
        'privilege' => 'content',
        'child' => [
            'category' => [
                'name' => '栏目管理',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'category',
                'child' => null,
            ],

            'article' => [
                'name' => '文章管理',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'article',
                'child' => null,
            ],

            'comment' => [
                'name' => '评论管理',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'comment',
                'child' => null,
            ],

            'device' => [
                'name' => '设备管理',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'device',
                'child' => null
            ],

        ]
    ],

    'user' => [
        'name' => '用户',
        'url' => UrlHelper::adminUrl('/home/user'),
        'privilege' => 'user',
        'child' => [
            'user_manage' => [
                'name' => '用户管理',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'user_manage',
                'child' => null
            ],

            'user_group' => [
                'name' => '用户组',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'user_group',
                'child' => null
            ],

            'user_privilege' => [
                'name' => '用户权限',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'user_privilege',
                'child' => null
            ],

            'use_log' => [
                'name' => '使用记录',
                'url' => UrlHelper::adminUrl('/'),
                'privilege' => 'use_log',
                'child' => null
            ],
        ]
    ]
];
