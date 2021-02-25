<?php

/**
 * 路由统一维护
 *
 */

namespace app\util;

class RouteArr
{
    //注册登陆
    public static $Register = [
        'getPublicOpenId' => [
            'cm/Register/getPublicOpenId',
            ['method' => 'post']
        ],
        'is_attention_pub' => [
            'cm/Register/is_attention_pub',
            ['method' => 'post']
        ],
        'getOpenId' => [
            'cm/Register/getOpenId',
            ['method' => 'post']
        ],
        'getInfo' => [
            'cm/Register/getInfo',
            ['method' => 'post']
        ],
        'getMobile'  => [
            'cm/Register/getMobile',
            ['method' => 'post']
        ],
        'register'  => [
            'cm/Register/register',
            ['method' => 'post']
        ],
        'test'  => [
            'cm/Register/test',
            ['method' => 'get']
        ]
    ];

    //首页
    public static $Index = [
        'is_get_public_open_id'        => [
            'cm/Index/is_get_public_open_id',
            ['method' => 'post']
        ],
        'selfEvaluation'        => [
            'cm/Index/selfEvaluation',
            ['method' => 'post']
        ],
        'thinking_pattern'        => [
            'cm/Index/thinking_pattern',
            ['method' => 'post']
        ],
        'psychological_elastic'        => [
            'cm/Index/psychological_elastic',
            ['method' => 'post']
        ],
        'close_dingyue'        => [
            'cm/Index/close_dingyue',
            ['method' => 'post']
        ],
        'dingyue'        => [
            'cm/Index/dingyue',
            ['method' => 'post']
        ],
        'kn_tre'        => [
            'cm/Index/kn_tre',
            ['method' => 'post']
        ],
        'next_class_time'        => [
            'cm/Index/next_class_time',
            ['method' => 'post']
        ],
        'upLock'        => [
            'cm/Index/upLock',
            ['method' => 'post']
        ],
        'isLearnS1'        => [
            'cm/Index/isLearnS1',
            ['method' => 'post']
        ],
        'index'        => [
            'cm/Index/index',
            ['method' => 'post']
        ],
        'course_start'     => [
            'cm/Index/course_start',
            ['method' => 'post']
        ],
        'problem_list'    => [
            'cm/Index/problem_list',
            ['method' => 'post']
        ],
        'share'          => [
            'cm/Index/share',
            ['method' => 'post']
        ],
        'course_end'      => [
            'cm/Index/course_end',
            ['method' => 'post']
        ],
        'feedback'       => [
            'cm/Index/feedback',
            ['method' => 'post']
        ],
        'page_info'      => [
            'cm/Index/page_info',
            ['method' => 'post']
        ],
        'information'      => [
            'cm/Index/information',
            ['method' => 'post']
        ],
        'health'      => [
            'cm/Index/health',
            ['method' => 'post']
        ],
        'self_depression'      => [
            'cm/Index/self_depression',
            ['method' => 'post']
        ],
        'insomnia'      => [
            'cm/Index/insomnia',
            ['method' => 'post']
        ],
        'life_quality'      => [
            'cm/Index/life_quality',
            ['method' => 'post']
        ],
        'depressive_effects'    => [
            'cm/Index/depressive_effects',
            ['method' => 'post']
        ],
        'somatic_symptoms'    => [
            'cm/Index/somatic_symptoms',
            ['method' => 'post']
        ],
        'mood_record'      => [
            'cm/Index/mood_record',
            ['method' => 'post']
        ],
        'mood_record_info'    => [
            'cm/Index/mood_record_info',
            ['method' => 'post']
        ],
        'self_evaluation'    => [
            'cm/Index/self_evaluation',
            ['method' => 'post']
        ],
        'two_self_evaluation'    => [
            'cm/Index/two_self_evaluation',
            ['method' => 'post']
        ],
        'problem_info'    => [
            'cm/Index/problem_info',
            ['method' => 'post']
        ],
        'target_list'    => [
            'cm/Index/target_list',
            ['method' => 'post']
        ],
        'activity_record'    => [
            'cm/Index/activity_record',
            ['method' => 'post']
        ],
        'auto_think'    => [
            'cm/Index/auto_think',
            ['method' => 'post']
        ],
        'activity_record_info'  => [
            'cm/Index/activity_record_info',
            ['method' => 'post']
        ],
        'activity_reply'  => [
            'cm/Index/activity_reply',
            ['method' => 'post']
        ],
        'activity_plan'  => [
            'cm/Index/activity_plan',
            ['method' => 'post']
        ],
        'activity_plan_list'  => [
            'cm/Index/activity_plan_list',
            ['method' => 'post']
        ],
        'user_activity_keys'  => [
            'cm/Index/user_activity_keys',
            ['method' => 'post']
        ],
        'edit_activity_keys'  => [
            'cm/Index/edit_activity_keys',
            ['method' => 'post']
        ],
        'auto_think_info'  => [
            'cm/Index/auto_think_info',
            ['method' => 'post']
        ],
        'identify_misunderstanding'  => [
            'cm/Index/identify_misunderstanding',
            ['method' => 'post']
        ],
        'misunderstanding_ratio'  => [
            'cm/Index/misunderstanding_ratio',
            ['method' => 'post']
        ],
        'dingyue'  => [
            'cm/Index/dingyue',
            ['method' => 'post']
        ],
    ];

    //练习 
    public static $Practice = [
        'pleasure_event_list'        => [
            'cm/Practice/pleasure_event_list',
            ['method' => 'post']
        ],
        'pr_s3_activity_box_save'        => [
            'cm/Practice/pr_s3_activity_box_save',
            ['method' => 'post']
        ],
        'pr_target_list'        => [
            'cm/Practice/pr_target_list',
            ['method' => 'post']
        ],
        'pr_target_one_list'        => [
            'cm/Practice/pr_target_one_list',
            ['method' => 'post']
        ],
        'pr_target_one_edit'        => [
            'cm/Practice/pr_target_one_edit',
            ['method' => 'post']
        ],
        'pr_target_del'        => [
            'cm/Practice/pr_target_del',
            ['method' => 'post']
        ],
        'pr_activity_record_one_list'        => [
            'cm/Practice/pr_activity_record_one_list',
            ['method' => 'post']
        ],
        'pr_activity_record_list'        => [
            'cm/Practice/pr_activity_record_list',
            ['method' => 'post']
        ],
        'pr_activity_record_edit'        => [
            'cm/Practice/pr_activity_record_edit',
            ['method' => 'post']
        ],
        'pr_activity_record_del'        => [
            'cm/Practice/pr_activity_record_del',
            ['method' => 'post']
        ],
        'pr_s2_auto_think_list'        => [
            'cm/Practice/pr_s2_auto_think_list',
            ['method' => 'post']
        ],
        'pr_s2_auto_think_del'        => [
            'cm/Practice/pr_s2_auto_think_del',
            ['method' => 'post']
        ],
        'pr_s2_auto_think_one_list'        => [
            'cm/Practice/pr_s2_auto_think_one_list',
            ['method' => 'post']
        ],
        'pleasure_event_submit'        => [
            'cm/Practice/pleasure_event_submit',
            ['method' => 'post']
        ],

        'pleasure_event_one_list'        => [
            'cm/Practice/pleasure_event_one_list',
            ['method' => 'post']
        ],
        'pleasure_event_edit'        => [
            'cm/Practice/pleasure_event_edit',
            ['method' => 'post']
        ],
        'pleasure_event_del'        => [
            'cm/Practice/pleasure_event_del',
            ['method' => 'post']
        ],
        'pr_s2_auto_think_edit'        => [
            'cm/Practice/pr_s2_auto_think_edit',
            ['method' => 'post']
        ],
        'pr_s3_activity_box_list'        => [
            'cm/Practice/pr_s3_activity_box_list',
            ['method' => 'post']
        ],
        'pr_s3_activity_box_one_list'        => [
            'cm/Practice/pr_s3_activity_box_one_list',
            ['method' => 'post']
        ],
        'pr_s3_activity_box_del'        => [
            'cm/Practice/pr_s3_activity_box_del',
            ['method' => 'post']
        ],
        'pr_s3_activity_box_edit'        => [
            'cm/Practice/pr_s3_activity_box_edit',
            ['method' => 'post']
        ],
        'pr_s3_activity_box_add'        => [
            'cm/Practice/pr_s3_activity_box_add',
            ['method' => 'post']
        ],
        'pr_s3_activity_plan_list'        => [
            'cm/Practice/pr_s3_activity_plan_list',
            ['method' => 'post']
        ],
        'pr_s3_activity_plan_one_list'        => [
            'cm/Practice/pr_s3_activity_plan_one_list',
            ['method' => 'post']
        ],
        'pr_s3_activity_plan_edit'        => [
            'cm/Practice/pr_s3_activity_plan_edit',
            ['method' => 'post']
        ],
        'pr_s3_activity_plan_del'        => [
            'cm/Practice/pr_s3_activity_plan_del',
            ['method' => 'post']
        ],
        'pr_s3_activity_record_one_list'        => [
            'cm/Practice/pr_s3_activity_record_one_list',
            ['method' => 'post']
        ],
        'pr_s3_activity_record_edit'        => [
            'cm/Practice/pr_s3_activity_record_edit',
            ['method' => 'post']
        ],
        'pr_s3_activity_record_del'        => [
            'cm/Practice/pr_s3_activity_record_del',
            ['method' => 'post']
        ],
        'pr_s3_activity_record_add'        => [
            'cm/Practice/pr_s3_activity_record_add',
            ['method' => 'post']
        ],
        'pr_s3_auto_think_list'        => [
            'cm/Practice/pr_s3_auto_think_list',
            ['method' => 'post']
        ],
        'pr_s3_auto_think_one_list'        => [
            'cm/Practice/pr_s3_auto_think_one_list',
            ['method' => 'post']
        ],
        'pr_s3_auto_think_edit'        => [
            'cm/Practice/pr_s3_auto_think_edit',
            ['method' => 'post']
        ],
    ];

    //第四节课
    public static $LessonFour = [
        'interactionOneSave' => [
            'cm/Lesson_four/interactionOneSave',
            ['method' => 'post']
        ]
    ];

    /* admin模块 */

    public static $Account = [
        'index'       => [
            'admin/Account/index',
            ['method' => 'post']
        ],
        'add'         => [
            'admin/Account/add',
            ['method' => 'post']
        ],
        'del'     => [
            'admin/Account/del',
            ['method' => 'post']
        ],
        'inviteCode'  => [
            'admin/Account/inviteCode',
            ['method' => 'post']
        ],
        'info'        => [
            'admin/Account/info',
            ['method' => 'post']
        ],
        'edit'        => [
            'admin/Account/edit',
            ['method' => 'post']
        ],
        'excel'        => [
            'admin/Account/excel',
            ['method' => 'post']
        ],
        'importExecl'        => [
            'admin/Account/importExecl',
            ['method' => 'post']
        ],
    ];



    public static $Login = [
        'login'        => [
            'admin/Login/login',
            ['method' => 'post']
        ],
        'logout'        => [
            'admin/Login/logout',
            ['method' => 'post']
        ]
    ];

    public static $System = [
        'authorList'        => [
            'admin/System/authorList',
            ['method' => 'post']
        ],
        'levelInfo'        => [
            'admin/System/levelInfo',
            ['method' => 'post']
        ],
        'authorAdd'        => [
            'admin/System/authorAdd',
            ['method' => 'post']
        ],
        'authorInfo'        => [
            'admin/System/authorInfo',
            ['method' => 'post']
        ],
        'authorEdit'        => [
            'admin/System/authorEdit',
            ['method' => 'post']
        ],
        'authorDel'        => [
            'admin/System/authorDel',
            ['method' => 'post']
        ],
        'categoryList'        => [
            'admin/System/categoryList',
            ['method' => 'post']
        ],
        'categoryAdd'        => [
            'admin/System/categoryAdd',
            ['method' => 'post']
        ],
        'categoryInfo'        => [
            'admin/System/categoryInfo',
            ['method' => 'post']
        ],
        'roleInfo'        => [
            'admin/System/roleInfo',
            ['method' => 'post']
        ],
        'categoryEdit'        => [
            'admin/System/categoryEdit',
            ['method' => 'post']
        ],
        'categoryDel'        => [
            'admin/System/categoryDel',
            ['method' => 'post']
        ],
        'changePsw'        => [
            'admin/System/changePsw',
            ['method' => 'post']
        ],
    ];


    public static $Sheet = [
        'health'        => [
            'admin/Sheet/health',
            ['method' => 'post']
        ],
        'excel_health'        => [
            'admin/Sheet/excel_health',
            ['method' => 'post']
        ],
        'course_before'        => [
            'admin/Sheet/course_before',
            ['method' => 'post']
        ],
        'excel_course_before'  => [
            'admin/Sheet/excel_course_before',
            ['method' => 'post']
        ],
        'mood'        => [
            'admin/Sheet/mood',
            ['method' => 'post']
        ],
        'excel_mood'        => [
            'admin/Sheet/excel_mood',
            ['method' => 'post']
        ],
        'feedback'        => [
            'admin/Sheet/feedback',
            ['method' => 'post']
        ],
        'excel_feedback'     => [
            'admin/Sheet/excel_feedback',
            ['method' => 'post']
        ],
    ];

    public static $User = [
        'index'        => [
            'admin/User/index',
            ['method' => 'post']
        ],
        'info'        => [
            'admin/User/info',
            ['method' => 'post']
        ],
        'edit'        => [
            'admin/User/edit',
            ['method' => 'post']
        ],
        'excel'        => [
            'admin/User/excel',
            ['method' => 'post']
        ],
    ];

    public static $Course = [
        'course_count'        => [
            'admin/Course/course_count',
            ['method' => 'post']
        ],
        'excel_course_count'   => [
            'admin/Course/excel_course_count',
            ['method' => 'post']
        ],
        'course_study_distribution'   => [
            'admin/Course/course_study_distribution',
            ['method' => 'post']
        ],
        'excel_course_study_distribution'   => [
            'admin/Course/excel_course_study_distribution',
            ['method' => 'post']
        ],
        'study_count'        => [
            'admin/Course/study_count',
            ['method' => 'post']
        ],
        'excel_study_count'    => [
            'admin/Course/excel_study_count',
            ['method' => 'post']
        ],
        'course_info'        => [
            'admin/Course/course_info',
            ['method' => 'post']
        ],
        'excel_course_info'   => [
            'admin/Course/excel_course_info',
            ['method' => 'post']
        ],
    ];


    public static $Exercise = [
        'problem_list'        => [
            'admin/Exercise/problem_list',
            ['method' => 'post']
        ],
        'target_list'   => [
            'admin/Exercise/target_list',
            ['method' => 'post']
        ],
        'excel_problem_list'   => [
            'admin/Exercise/excel_problem_list',
            ['method' => 'post']
        ],
        'excel_target_list'   => [
            'admin/Exercise/excel_target_list',
            ['method' => 'post']
        ],
        'activity_record'   => [
            'admin/Exercise/activity_record',
            ['method' => 'post']
        ],
        'excel_activity_record'   => [
            'admin/Exercise/excel_activity_record',
            ['method' => 'post']
        ],
        'auto_think'   => [
            'admin/Exercise/auto_think',
            ['method' => 'post']
        ],
        'excel_auto_think'   => [
            'admin/Exercise/excel_auto_think',
            ['method' => 'post']
        ],
        'activity_record_answer'   => [
            'admin/Exercise/activity_record_answer',
            ['method' => 'post']
        ],
        'excel_activity_record_answer'   => [
            'admin/Exercise/excel_activity_record_answer',
            ['method' => 'post']
        ],
        'activity_keys'   => [
            'admin/Exercise/activity_keys',
            ['method' => 'post']
        ],
        'excel_activity_keys'   => [
            'admin/Exercise/excel_activity_keys',
            ['method' => 'post']
        ],
        'activity_arrange'   => [
            'admin/Exercise/activity_arrange',
            ['method' => 'post']
        ],
        'identify_myth'   => [
            'admin/Exercise/identify_myth',
            ['method' => 'post']
        ],
        'myth_proportion'   => [
            'admin/Exercise/myth_proportion',
            ['method' => 'post']
        ],
        'excel_activity_arrange'   => [
            'admin/Exercise/excel_activity_arrange',
            ['method' => 'post']
        ],
        'excel_identify_myth'   => [
            'admin/Exercise/excel_identify_myth',
            ['method' => 'post']
        ],
        'excel_myth_proportion'   => [
            'admin/Exercise/excel_myth_proportion',
            ['method' => 'post']
        ],
        'pleasure_event_list'   => [
            'admin/Exercise/pleasure_event_list',
            ['method' => 'post']
        ],
        'excel_pleasure_event_list'   => [
            'admin/Exercise/excel_pleasure_event_list',
            ['method' => 'post']
        ],
    ];


    public static $Relax = [
        'relaxSta'        => [
            'admin/Relax/relaxSta',
            ['method' => 'post']
        ],
        'relaxDet'        => [
            'admin/Relax/relaxDet',
            ['method' => 'post']
        ],
        'excel_relaxSta'        => [
            'admin/Relax/excel_relaxSta',
            ['method' => 'post']
        ],
        'excel_relaxDet'        => [
            'admin/Relax/excel_relaxDet',
            ['method' => 'post']
        ],
    ];
}
