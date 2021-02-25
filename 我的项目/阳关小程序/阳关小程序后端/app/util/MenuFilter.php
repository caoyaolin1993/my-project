<?php
/**
 * 需验证的前台菜单统一维护
 */

namespace app\util;

class MenuFilter {
    public $menu_List = [
            [
                'name'=>'人力资源架构',
                'children_list'=>[
                    [
                        'chi_name'=>'人力组织架构',
                        '_index'=>'00',
                        'rule' =>'admin/Organization/index',
                        'path'=>'/HRStr'
                    ]
                ]
            ],
            [
                'name'=>'项目中心',
                'children_list'=> [
                    [
                        'chi_name'=>'分配DKP',
                        '_index'=>'10',
                        'rule' =>'admin/projectTask/index',
                        'path'=>'/Distribution'
                    ],
                    [
                        'chi_name'=>'独立任务执行',
                        '_index'=>'11',
                        'rule'=>'admin/SelfTask/index',
                        'path'=>'/SelfTask'
                    ],
                    [
                        'chi_name'=>'查看项目',
                        '_index'=>'12',
                        'rule'=>'admin/Project/index',
                        'path'=>'/ShowProject'
                    ],
                    [
                        'chi_name'=>'划设项目预警分',
                        '_index'=>'13',
                        'rule'=>'admin/Project/warning_score_list',
                        'path'=>'/WarningScore'
                    ]
                ]
            ],
            [
                'name'=>'审核中心',
                'children_list'=> [
                    [
                        'chi_name'=>'增量审核',
                        '_index'=>'20',
                        'rule'=>'admin/ProjectTask/titleList',
                        'path'=>'/ExtraScore'
                    ],
                    [
                        'chi_name'=>'比稿类独立任务分配审核',
                        '_index'=>'21',
                        'rule'=>'admin/SelfTask/checkTask',
                        'path'=>'/CheckSelfTask'
                    ]
                ]
            ],
            [
                'name'=>'个人中心',
                'children_list'=> [
                    [
                        'chi_name'=>'个人中心',
                        '_index'=>'30',
                        'rule'=>'admin/Info/index',
                        'path'=>'/PersonalCenter'
                    ],
                    [
                        'chi_name'=>'查看个人DKP',
                        '_index'=>'31',
                        'rule'=>'admin/Info/projectList',
                        'path'=>'/CheckPerson'
                    ],
                    [
                        'chi_name'=>'查看个人独立任务',
                        '_index'=>'32',
                        'rule'=>'admin/Info/SelfTask',
                        'path'=>'/CheckPersonTask'
                    ]
                ]
            ],
            [
                'name'=>'权限管理',
                'children_list'=> [
                    [
                        'chi_name'=>'权限管理',
                        '_index'=>'40',
                        'rule'=>'admin/AuthGroup/index',
                        'path'=>'/PowerManagement'
                    ]
                ]
            ]
        ];
}