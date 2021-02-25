<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\cm\validate\ActivityPlanS4;
use app\cm\validate\PracticeS4AutoThink;
use app\cm\validate\S4ActivityRecord;
use app\cm\validate\TaskDecomposition;
use think\facade\Route;

$vrm = app('validateRuleMessage');
Route::group('Register', function () {
    Route::post('getPublicOpenId', 'Register/getPublicOpenId');
    Route::post('is_attention_pub', 'Register/is_attention_pub');
    Route::post('getOpenId', 'Register/getOpenId');
    Route::post('getInfo', 'Register/getInfo');
    Route::post('getMobile', 'Register/getMobile');
    Route::post('register', 'Register/register');
    Route::post('test', 'Register/test');
    Route::post('getCourseWarnTime', 'Register/getCourseWarnTime');
});

Route::group('Index', function () {
    Route::post('is_get_public_open_id', 'Index/is_get_public_open_id');
    Route::post('selfEvaluation', 'Index/selfEvaluation');
    Route::post('thinking_pattern', 'Index/thinking_pattern');
    Route::post('psychological_elastic', 'Index/psychological_elastic');
    Route::post('close_dingyue', 'Index/close_dingyue');
    Route::post('dingyue', 'Index/dingyue');
    Route::post('kn_tre', 'Index/kn_tre');
    Route::post('next_class_time', 'Index/next_class_time');
    Route::post('upLock', 'Index/upLock');
    Route::post('isLearnS1', 'Index/isLearnS1');
    Route::post('relax_end', 'Index/relax_end');
    Route::post('index', 'Index/index');
    Route::post('course_start', 'Index/course_start');
    Route::post('problem_list', 'Index/problem_list');
    Route::post('share', 'Index/share');
    Route::post('course_end', 'Index/course_end');
    Route::post('feedback', 'Index/feedback');
    Route::post('page_info', 'Index/page_info');
    Route::post('information', 'Index/information');
    Route::post('health', 'Index/health');
    Route::post('self_depression', 'Index/self_depression');
    Route::post('insomnia', 'Index/insomnia');
    Route::post('life_quality', 'Index/life_quality');
    Route::post('depressive_effects', 'Index/depressive_effects');
    Route::post('somatic_symptoms', 'Index/somatic_symptoms');
    Route::post('mood_record', 'Index/mood_record');
    Route::post('mood_record_info', 'Index/mood_record_info');
    Route::post('self_evaluation', 'Index/self_evaluation');
    Route::post('two_self_evaluation', 'Index/two_self_evaluation');
    Route::post('problem_info', 'Index/problem_info');
    Route::post('target_list', 'Index/target_list');
    Route::post('activity_record', 'Index/activity_record');
    Route::post('auto_think', 'Index/auto_think');
    Route::post('activity_record_info', 'Index/activity_record_info');
    Route::post('activity_reply', 'Index/activity_reply');
    Route::post('activity_plan', 'Index/activity_plan');
    Route::post('activity_plan_list', 'Index/activity_plan_list');
    Route::post('user_activity_keys', 'Index/user_activity_keys');
    Route::post('edit_activity_keys', 'Index/edit_activity_keys');
    Route::post('auto_think_info', 'Index/auto_think_info');
    Route::post('identify_misunderstanding', 'Index/identify_misunderstanding');
    Route::post('misunderstanding_ratio', 'Index/misunderstanding_ratio');
});

Route::group('Practice', function () {
    Route::post('pleasure_event_list', 'Practice/pleasure_event_list');
    Route::post('pr_s3_activity_box_save', 'Practice/pr_s3_activity_box_save');
    Route::post('pr_target_list', 'Practice/pr_target_list');
    Route::post('pr_target_one_list', 'Practice/pr_target_one_list');
    Route::post('pr_target_one_edit', 'Practice/pr_target_one_edit');
    Route::post('pr_target_del', 'Practice/pr_target_del');
    Route::post('pr_activity_record_one_list', 'Practice/pr_activity_record_one_list');
    Route::post('pr_activity_record_list', 'Practice/pr_activity_record_list');
    Route::post('pr_activity_record_edit', 'Practice/pr_activity_record_edit');
    Route::post('pr_activity_record_del', 'Practice/pr_activity_record_del');
    Route::post('pr_s2_auto_think_list', 'Practice/pr_s2_auto_think_list');
    Route::post('pr_s2_auto_think_del', 'Practice/pr_s2_auto_think_del');
    Route::post('pr_s2_auto_think_one_list', 'Practice/pr_s2_auto_think_one_list');
    Route::post('pleasure_event_submit', 'Practice/pleasure_event_submit');
    Route::post('pleasure_event_one_list', 'Practice/pleasure_event_one_list');
    Route::post('pleasure_event_edit', 'Practice/pleasure_event_edit');
    Route::post('pleasure_event_del', 'Practice/pleasure_event_del');
    Route::post('pr_s2_auto_think_edit', 'Practice/pr_s2_auto_think_edit');
    Route::post('pr_s3_activity_box_list', 'Practice/pr_s3_activity_box_list');
    Route::post('pr_s3_activity_box_one_list', 'Practice/pr_s3_activity_box_one_list');
    Route::post('pr_s3_activity_box_del', 'Practice/pr_s3_activity_box_del');
    Route::post('pr_s3_activity_box_edit', 'Practice/pr_s3_activity_box_edit');
    Route::post('pr_s3_activity_box_add', 'Practice/pr_s3_activity_box_add');
    Route::post('pr_s3_activity_plan_list', 'Practice/pr_s3_activity_plan_list');
    Route::post('pr_s3_activity_plan_one_list', 'Practice/pr_s3_activity_plan_one_list');
    Route::post('pr_s3_activity_plan_edit', 'Practice/pr_s3_activity_plan_edit');
    Route::post('pr_s3_activity_plan_del', 'Practice/pr_s3_activity_plan_del');
    Route::post('pr_s3_activity_record_one_list', 'Practice/pr_s3_activity_record_one_list');
    Route::post('pr_s3_activity_record_edit', 'Practice/pr_s3_activity_record_edit');
    Route::post('pr_s3_activity_record_del', 'Practice/pr_s3_activity_record_del');
    Route::post('pr_s3_activity_record_add', 'Practice/pr_s3_activity_record_add');
    Route::post('pr_s3_auto_think_list', 'Practice/pr_s3_auto_think_list');
    Route::post('pr_s3_auto_think_one_list', 'Practice/pr_s3_auto_think_one_list');
    Route::post('pr_s3_auto_think_edit', 'Practice/pr_s3_auto_think_edit');
    Route::post('pr_s3_activity_plan_step_list', 'Practice/pr_s3_activity_plan_step_list');
    Route::post('pr_s3_activity_record_list', 'Practice/pr_s3_activity_record_list');
    Route::post('pr_s3_auto_think_del', 'Practice/pr_s3_auto_think_del');
});

Route::group('S4', function () use ($vrm) {
    //->validate(TaskDecomposition::class)
    Route::post('interactionOneSave', 'LessonFour/interactionOneSave');
    Route::post('interactionTwoSave', 'LessonFour/interactionTwoSave')->validate(ActivityPlanS4::class);
    Route::post('interactionThreeRead', 'LessonFour/interactionThreeRead')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('interactionThreeSave', 'LessonFour/interactionThreeSave')->validate($vrm->osntValidate, '', $vrm->osntMessage);
});

Route::group('PracticeS4TaskResolve', function () use ($vrm) {
    Route::post('save', 'PracticeS4TaskResolve/save')->validate(TaskDecomposition::class);
    Route::post('index', 'PracticeS4TaskResolve/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::get('read/:id', 'PracticeS4TaskResolve/read')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS4TaskResolve/update')->validate(TaskDecomposition::class, 'update')->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS4TaskResolve/delete')->pattern(['id' => '\d+']);
});

Route::group('PracticeS4ActivityPlan', function () use ($vrm) {
    Route::post('index', 'PracticeS4ActivityPlan/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('read', 'PracticeS4ActivityPlan/read')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
    Route::post('update', 'PracticeS4ActivityPlan/update')->validate($vrm->stimeOpenIdSourceRequireValidate, '', $vrm->stimeOpenIdSourceRequireMessage);
    Route::post('save', 'PracticeS4ActivityPlan/save')->validate(ActivityPlanS4::class);
    Route::post('delete', 'PracticeS4ActivityPlan/delete')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
});

Route::group('PracticeS4ActivityRecord', function () use ($vrm) {
    Route::post('index', 'PracticeS4ActivityRecord/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('read', 'PracticeS4ActivityRecord/read')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
    Route::post('update', 'PracticeS4ActivityRecord/update')->validate($vrm->stimeOpenIdSourceRequireValidate, '', $vrm->stimeOpenIdSourceRequireMessage);
    Route::post('save', 'PracticeS4ActivityRecord/save')->validate(S4ActivityRecord::class);
    Route::post('delete', 'PracticeS4ActivityRecord/delete')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
});

Route::group('PracticeS4AutoThink', function () use ($vrm) {
    Route::post('save', 'PracticeS4AutoThink/save')->validate(PracticeS4AutoThink::class);
    Route::post('index', 'PracticeS4AutoThink/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::get('read/:id', 'PracticeS4AutoThink/read')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS4AutoThink/update')->validate($vrm->situationRequireValidate, '', $vrm->situationRequireMessage)->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS4AutoThink/delete')->pattern(['id' => '\d+']);
});

Route::group('PracticeS4ThinkThink', function () use ($vrm) {
    Route::post('save', 'PracticeS4ThinkThink/save')->validate($vrm->osntAllValidate, '', $vrm->osntAllMessage);
    Route::get('index/:id', 'PracticeS4ThinkThink/index')->pattern(['id' => '\d+']);
    Route::get('read/:id', 'PracticeS4ThinkThink/read')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS4ThinkThink/update')->validate($vrm->osntAllSecValidate, '', $vrm->osntAllSecMessage)->pattern(['id' => '\d+']);
});


Route::group('S5', function () use ($vrm) {
    //->validate(TaskDecomposition::class)
    Route::post('interactionOneSave', 'LessonFive/interactionOneSave');
    Route::post('interactionTwoSave', 'LessonFive/interactionTwoSave');
    Route::post('interactionThreeSave', 'LessonFive/interactionThreeSave');
});

Route::group('PracticeS5ProblemSolving', function () use ($vrm) {
    Route::post('save', 'PracticeS5ProblemSolving/save')->validate($vrm->PracticeS5ProblemSolvingsave1, '', $vrm->PracticeS5ProblemSolvingsave2);  //->validate(PracticeS4AutoThink::class)
    Route::post('index', 'PracticeS5ProblemSolving/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::get('read/:id', 'PracticeS5ProblemSolving/read')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS5ProblemSolving/update')->validate($vrm->sov, '', $vrm->sov1)->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS5ProblemSolving/delete')->pattern(['id' => '\d+']);
});

Route::group('PracticeS5AttributionPractice', function () use ($vrm) {
    Route::post('save', 'PracticeS5AttributionPractice/save')->validate($vrm->buti, '', $vrm->buti1);  //
    Route::post('index', 'PracticeS5AttributionPractice/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::get('read/:id', 'PracticeS5AttributionPractice/read')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS5AttributionPractice/update')->validate($vrm->bution, '', $vrm->bution1)->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS5AttributionPractice/delete')->pattern(['id' => '\d+']);
});

Route::group('PracticeS5ActivityPlan', function () use ($vrm) {
    Route::post('index', 'PracticeS5ActivityPlan/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('read', 'PracticeS5ActivityPlan/read')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
    Route::post('update', 'PracticeS5ActivityPlan/update')->validate($vrm->stimeOpenIdSourceRequireValidate, '', $vrm->stimeOpenIdSourceRequireMessage);
    Route::post('save', 'PracticeS5ActivityPlan/save')->validate(ActivityPlanS4::class);
    Route::post('delete', 'PracticeS5ActivityPlan/delete')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
});

Route::group('PracticeS5ActivityRecord', function () use ($vrm) {
    Route::post('index', 'PracticeS5ActivityRecord/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('read', 'PracticeS5ActivityRecord/read')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
    Route::post('update', 'PracticeS5ActivityRecord/update')->validate($vrm->stimeOpenIdSourceRequireValidate, '', $vrm->stimeOpenIdSourceRequireMessage);
    Route::post('save', 'PracticeS5ActivityRecord/save')->validate(S4ActivityRecord::class);
    Route::post('delete', 'PracticeS5ActivityRecord/delete')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
});

Route::group('S6', function () use ($vrm) {
    Route::post('interactionOneRead', 'LessonSix/interactionOneRead')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('interactionOneSave', 'LessonSix/interactionOneSave');
    //->validate(ActivityPlanS4::class)  
    Route::post('interactionTwoRead', 'LessonSix/interactionTwoRead');
    Route::post('interactionTwoSave', 'LessonSix/interactionTwoSave');
});

Route::group('ThirdParty', function () use ($vrm) {
    Route::post('index', 'ThirdParty/index');
    Route::get('get_ticket', 'ThirdParty/get_ticket');
    Route::post('callback', 'ThirdParty/callback');
    Route::post('test', 'ThirdParty/test');
    Route::post('get_token', 'ThirdParty/get_token');
    Route::post('get_pre_auth_code', 'ThirdParty/get_pre_auth_code');
    Route::post('get_auth_code', 'ThirdParty/get_auth_code');
    Route::post('geta', 'ThirdParty/geta');
});

Route::group('Timing', function () use ($vrm) {
    Route::post('index', 'Timing/index');
    Route::post('ceshi', 'Timing/ceshi');
});

Route::group('PracticeS6AssessFaith', function () use ($vrm) {
    Route::post('index', 'PracticeS6AssessFaith/index');
    Route::post('save', 'PracticeS6AssessFaith/save')->validate($vrm->faith, '', $vrm->faith1);
    Route::get('read/:id', 'PracticeS6AssessFaith/read')->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS6AssessFaith/delete')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS6AssessFaith/update')->validate($vrm->ssess, '', $vrm->ssess1)->pattern(['id' => '\d+']);
});

Route::group('PracticeS6FoundFaith', function () use ($vrm) {
    Route::post('index', 'PracticeS6FoundFaith/index');
    Route::post('save', 'PracticeS6FoundFaith/save')->validate(PracticeS4AutoThink::class);
    Route::get('read/:id', 'PracticeS6FoundFaith/read')->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS6FoundFaith/delete')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS6FoundFaith/update')->validate($vrm->situationRequireValidate, '', $vrm->situationRequireMessage)->pattern(['id' => '\d+']);
});


Route::group('PracticeS6FoundFaithNext', function () use ($vrm) {
    Route::post('save', 'PracticeS6FoundFaithNext/save')->validate($vrm->vde_a, '', $vrm->msg_a);
    Route::get('read/:id', 'PracticeS6FoundFaithNext/read')->pattern(['id' => '\d+']);
    Route::post('update/:tt_id', 'PracticeS6FoundFaithNext/update')->validate($vrm->vde_b, '', $vrm->msg_b)->pattern(['tt_id' => '\d+']);
});

Route::group('PracticeS6ActivityPlan', function () use ($vrm) {
    Route::post('index', 'PracticeS6ActivityPlan/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('read', 'PracticeS6ActivityPlan/read')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
    Route::post('update', 'PracticeS6ActivityPlan/update')->validate($vrm->stimeOpenIdSourceRequireValidate, '', $vrm->stimeOpenIdSourceRequireMessage);
    Route::post('save', 'PracticeS6ActivityPlan/save')->validate(ActivityPlanS4::class);
    Route::post('delete', 'PracticeS6ActivityPlan/delete')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
});

Route::group('PracticeS6ActivityRecord', function () use ($vrm) {
    Route::post('index', 'PracticeS6ActivityRecord/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('read', 'PracticeS6ActivityRecord/read')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
    Route::post('update', 'PracticeS6ActivityRecord/update')->validate($vrm->stimeOpenIdSourceRequireValidate, '', $vrm->stimeOpenIdSourceRequireMessage);
    Route::post('save', 'PracticeS6ActivityRecord/save')->validate(S4ActivityRecord::class);
    Route::post('delete', 'PracticeS5ActivityRecord/delete')->validate($vrm->stimeOpenIdRequireValidate, '', $vrm->stimeOpenIdRequireMessage);
});

Route::group('S7', function () use ($vrm) { 
    //->validate($vrm->vde_a, '', $vrm->msg_a)
    Route::post('interactionOneSave', 'LessonSeven/interactionOneSave');
    Route::post('interactionTwoSave', 'LessonSeven/interactionTwoSave');
});

Route::group('PracticeS7NewTarget', function () use ($vrm) {
    Route::post('save', 'PracticeS7NewTarget/save')->validate($vrm->tr_a, '', $vrm->tr_b);  //
    Route::post('index', 'PracticeS7NewTarget/index')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::get('read/:id', 'PracticeS7NewTarget/read')->pattern(['id' => '\d+']);
    Route::post('update/:id', 'PracticeS7NewTarget/update')->validate($vrm->newt_a, '', $vrm->newt_b)->pattern(['id' => '\d+']);
    Route::get('delete/:id', 'PracticeS7NewTarget/delete')->pattern(['id' => '\d+']);
});

Route::group('PracticeS7Return', function () use ($vrm) {
    Route::post('sevenEnd', 'PracticeS7Return/sevenEnd')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);  //
    Route::post('clickReturn', 'PracticeS7Return/clickReturn')->validate($vrm->openIdRequireValidate, '', $vrm->openIdRequireMessage);
    Route::post('returnEnd', 'PracticeS7Return/returnEnd');
    Route::post('returnStart', 'PracticeS7Return/returnStart');
});