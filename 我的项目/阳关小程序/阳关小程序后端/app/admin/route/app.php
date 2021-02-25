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
use think\facade\Route;

Route::group('Account', function () {
    Route::post('index', 'Account/index');
    Route::post('add', 'Account/add');
    Route::post('del', 'Account/del');
    Route::post('inviteCode', 'Account/inviteCode');
    Route::post('info', 'Account/info');
    Route::post('edit', 'Account/edit');
    Route::post('excel', 'Account/excel');
    Route::post('importExecl', 'Account/importExecl');
});

Route::group('Login', function () {
    Route::post('login', 'Login/login');
    Route::post('logout', 'Login/logout');
});

Route::group('System', function () {
    Route::post('authorList', 'System/authorList');
    Route::post('levelInfo', 'System/levelInfo');
    Route::post('authorAdd', 'System/authorAdd');
    Route::post('authorInfo', 'System/authorInfo');
    Route::post('authorEdit', 'System/authorEdit');
    Route::post('authorDel', 'System/authorDel');
    Route::post('categoryList', 'System/categoryList');
    Route::post('categoryAdd', 'System/categoryAdd');
    Route::post('categoryInfo', 'System/categoryInfo');
    Route::post('roleInfo', 'System/roleInfo');
    Route::post('categoryEdit', 'System/categoryEdit');
    Route::post('categoryDel', 'System/categoryDel');
    Route::post('changePsw', 'System/changePsw');
});

Route::group('Sheet', function () {
    Route::post('health', 'Sheet/health');
    Route::post('excel_health', 'Sheet/excel_health');
    Route::post('course_before', 'Sheet/course_before');
    Route::post('excel_course_before', 'Sheet/excel_course_before');
    Route::post('mood', 'Sheet/mood');
    Route::post('excel_mood', 'Sheet/excel_mood');
    Route::post('feedback', 'Sheet/feedback');
    Route::post('excel_feedback', 'Sheet/excel_feedback');
});

Route::group('User', function () {
    Route::post('index', 'User/index');
    Route::post('info', 'User/info');
    Route::post('edit', 'User/edit');
    Route::post('excel', 'User/excel');
});

Route::group('Course', function () {
    Route::post('course_count', 'Course/course_count');
    Route::post('excel_course_count', 'Course/excel_course_count');
    Route::post('course_study_distribution', 'Course/course_study_distribution');
    Route::post('excel_course_study_distribution', 'Course/excel_course_study_distribution');
    Route::post('study_count', 'Course/study_count');
    Route::post('excel_study_count', 'Course/excel_study_count');
    Route::post('course_info', 'Course/course_info');
    Route::post('excel_course_info', 'Course/excel_course_info');
});

Route::group('Exercise', function () {
    Route::post('problem_list', 'Exercise/problem_list');
    Route::post('target_list', 'Exercise/target_list');
    Route::post('excel_problem_list', 'Exercise/excel_problem_list');
    Route::post('excel_target_list', 'Exercise/excel_target_list');
    Route::post('activity_record', 'Exercise/activity_record');
    Route::post('excel_activity_record', 'Exercise/excel_activity_record');
    Route::post('auto_think', 'Exercise/auto_think');
    Route::post('excel_auto_think', 'Exercise/excel_auto_think');
    Route::post('activity_record_answer', 'Exercise/activity_record_answer');
    Route::post('excel_activity_record_answer', 'Exercise/excel_activity_record_answer');
    Route::post('activity_keys', 'Exercise/activity_keys');
    Route::post('excel_activity_keys', 'Exercise/excel_activity_keys');
    Route::post('activity_arrange', 'Exercise/activity_arrange');
    Route::post('identify_myth', 'Exercise/identify_myth');
    Route::post('myth_proportion', 'Exercise/myth_proportion');
    Route::post('excel_activity_arrange', 'Exercise/excel_activity_arrange');
    Route::post('excel_identify_myth', 'Exercise/excel_identify_myth');
    Route::post('excel_myth_proportion', 'Exercise/excel_myth_proportion');
    Route::post('pleasure_event_list', 'Exercise/pleasure_event_list');
    Route::post('excel_pleasure_event_list', 'Exercise/excel_pleasure_event_list');
    Route::post('auto_think_s3', 'Exercise/auto_think_s3');
    Route::post('excel_auto_think_s3', 'Exercise/excel_auto_think_s3');
    Route::post('s3_activity_record', 'Exercise/s3_activity_record');
    Route::post('excel_s3_activity_record', 'Exercise/excel_s3_activity_record');
});

Route::group('Relax', function () {
    Route::post('relaxSta', 'Relax/relaxSta');
    Route::post('relaxDet', 'Relax/relaxDet');
    Route::post('excel_relaxSta', 'Relax/excel_relaxSta');
    Route::post('excel_relaxDet', 'Relax/excel_relaxDet');
});

Route::group('S4Practice', function () {
    Route::post('taskDecomposition', 'S4Practice/taskDecomposition');
    Route::post('excel_taskDecomposition', 'S4Practice/excel_taskDecomposition');
    Route::post('s4_activity_arrange', 'S4Practice/s4_activity_arrange');
    Route::post('excel_s4_activity_arrange', 'S4Practice/excel_s4_activity_arrange');
    Route::post('s4_activity_record', 'S4Practice/s4_activity_record');
    Route::post('excel_s4_activity_record', 'S4Practice/excel_s4_activity_record');
    Route::post('auto_think_s4', 'S4Practice/auto_think_s4');
    Route::post('excel_auto_think_s4', 'S4Practice/excel_auto_think_s4');
});

Route::group('S5Practice', function () {
    Route::post('attributionPractice', 'S5Practice/attributionPractice');
    Route::post('excel_attributionPractice', 'S5Practice/excel_attributionPractice');
    Route::post('s5_activity_arrange', 'S5Practice/s5_activity_arrange');
    Route::post('excel_s5_activity_arrange', 'S5Practice/excel_s5_activity_arrange');
    Route::post('s5_activity_record', 'S5Practice/s5_activity_record');
    Route::post('excel_s5_activity_record', 'S5Practice/excel_s5_activity_record');
    Route::post('problemSolving', 'S5Practice/problemSolving');
    Route::post('excel_problemSolving', 'S5Practice/excel_problemSolving');
});

Route::group('S6Practice', function () {
    Route::post('s6_activity_arrange', 'S6Practice/s6_activity_arrange');
    Route::post('excel_s6_activity_arrange', 'S6Practice/excel_s6_activity_arrange');
    Route::post('s6_activity_record', 'S6Practice/s6_activity_record');
    Route::post('excel_s6_activity_record', 'S6Practice/excel_s6_activity_record');
    Route::post('s6_found_faith', 'S6Practice/s6_found_faith');
    Route::post('excel_s6_found_faith', 'S6Practice/excel_s6_found_faith');
    Route::post('assess_faith', 'S6Practice/assess_faith');
    Route::post('excel_assess_faith', 'S6Practice/excel_assess_faith');
});

Route::group('S7Practice', function () {
    Route::post('method_mastery', 'S7Practice/method_mastery');
    Route::get('excel_method_mastery', 'S7Practice/excel_method_mastery');
    Route::post('new_target', 'S7Practice/new_target');
    Route::get('excel_new_target', 'S7Practice/excel_new_target');
});
