<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;

header('Access-Control-Allow-Origin:*');
// header('Access-Control-Allow-Origin:'.$_SERVER['HTTP_ORIGIN']);
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header("Access-Control-Allow-Methods: POST,GET，OPTIONS");
class Index extends BaseController
{
    public function index()
    {
        $res = Db::name('jieyebaogao')->select()->toArray();

        foreach ($res as $key => $value) {
            $res[$key]['student_original_painting']  = json_decode($value['student_original_painting'], true);
            $res[$key]['war']  = json_decode($value['war'], true);
            $res[$key]['theory']  = json_decode($value['theory'], true);
            $res[$key]['stamina']  = json_decode($value['stamina'], true);
            $res[$key]['mentality']  = json_decode($value['mentality'], true);
            $res[$key]['combining_capacity']  = json_decode($value['combining_capacity'], true);
            $res[$key]['level_rating']  = json_decode($value['level_rating'], true);
            $res[$key]['coach_message']  = json_decode($value['coach_message'], true);
        }

        return json(['code' => 200, 'msg' => '成功', 'data' => $res]);
    }
    public function add()
    {
        $course_name = request()->post('course_name');
        $course_number  = request()->post('course_number');
        $student_number  = request()->post('student_number');
        $student_name = request()->post('student_name');
        $chief_coach  = request()->post('chief_coach');
        $date  = request()->post('date');
        $student_original_painting  =json_decode(request()->post('student_original_painting'),true);
        $war  =json_decode(request()->post('war'),true) ;
        $theory  = json_decode(request()->post('theory'),true);
        $stamina  = json_decode(request()->post('stamina'),true) ;
        $mentality  = json_decode(request()->post('mentality'),true) ;
        $combining_capacity  = json_decode(request()->post('combining_capacity'),true) ;
        $trial_results  = request()->post('trial_results');
        $employment_advice  = request()->post('employment_advice');
        $coach_message  = request()->post('coach_message');

        if($_FILES){
            $handle = new \Verot\Upload\Upload($_FILES['img']);   
          
            if ($handle->uploaded) {
              
                $handle->process(public_path() . 'student');
                if ($handle->processed) {
                    $student_original_painting['a_11'] = 'http://acejk.gkgaming.cn/student/'.$handle->file_src_name;
                    
                    $handle->clean();
                } else {
                    return json(['code'=>-1,'msg'=>'图片上传失败，请重新尝试']);
                }
            }
        }
        

        $war_score = 0;    // 电竞技战术水平得分
        if ($war['a_02'] >= 2400) {
            $war_score = 100;
        } elseif ($war['a_02'] >= 2200 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 100;
        } elseif ($war['a_02'] >= 2200 && $war['a_03']['b_02'] < 12000) {
            $war_score = 90;
        } elseif ($war['a_02'] >= 1800 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 90;
        } elseif ($war['a_02'] >= 1800 && $war['a_03']['b_02'] < 12000) {
            $war_score = 80;
        } elseif ($war['a_02'] >= 1500 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 80;
        } elseif ($war['a_02'] >= 1500 && $war['a_03']['b_02'] < 12000) {
            $war_score = 70;
        } elseif ($war['a_02'] < 1500 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 70;
        } elseif ($war['a_02'] < 1500 && $war['a_03']['b_02'] < 12000) {
            $war_score = 60;
        }

        $training_match_score = 0;    // 训练赛得分
        if ($war['a_04']['b_02'] >= $war['a_04']['b_01'] && $war['a_04']['b_05'] == '无') {
            $training_match_score = 0;
        } elseif ($war['a_04']['b_02'] >= $war['a_04']['b_01'] && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 50;
        } elseif ($war['a_04']['b_02'] >= $war['a_04']['b_01'] && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 20;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 10 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 50;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 10 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 100;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 10 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 70;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 5 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 30;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 5 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 80;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 5 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 50;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) >= 2 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 10;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) >= 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 60;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) >= 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 30;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) < 2 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 0;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) < 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 50;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) < 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 20;
        }

        $theory_score = 0;    //电竞理论知识得分
        $theory_score = $theory['a_01'] + $theory['a_02'] + $theory['a_03'] + $theory['a_04'] + $theory['a_05'];


        $basketball_score = 0;  //篮球得分
        if ($stamina['a_01'] <= 13.2) {
            $basketball_score = 100;
        } elseif ($stamina['a_01'] <= 16.2) {
            $basketball_score = 80;
        } elseif ($stamina['a_01'] <= 21.2) {
            $basketball_score = 60;
        } else {
            $basketball_score = 0;
        }

        $run_score = 0;    // 跑步得分
        $res_run_time = $stamina['a_02']['b_01'] * 60 + $stamina['a_02']['b_02'];

        if ($res_run_time <= 3 * 60 + 40) {
            $run_score = 100;
        } elseif ($res_run_time <= 3 * 60 + 55) {
            $run_score = 80;
        } elseif ($res_run_time <= 4 * 60 + 20) {
            $run_score = 60;
        } else {
            $run_score = 0;
        }

        $push_up_score = 0;   //俯卧撑得分
        if ($stamina['a_03'] >= 18) {
            $push_up_score = 100;
        } elseif ($stamina['a_03'] >= 10) {
            $push_up_score = 80;
        } elseif ($stamina['a_03'] >= 5) {
            $push_up_score = 60;
        } else {
            $push_up_score = 0;
        }

        $apm_score = 0;   // 手速得分       
        if ($stamina['a_04'] >= 250) {
            $apm_score = 100;
        } elseif ($stamina['a_04'] >= 150) {
            $apm_score = 80;
        } elseif ($stamina['a_04'] >= 100) {
            $apm_score = 60;
        } else {
            $apm_score = 0;
        }

        $reaction_score = 0;  //反应得分
        if ($stamina['a_05'] <= 150) {
            $reaction_score = 100;
        } elseif ($stamina['a_05'] <= 200) {
            $reaction_score = 80;
        } elseif ($stamina['a_05'] <= 250) {
            $reaction_score = 60;
        } else {
            $reaction_score = 0;
        }
        
        $mentality_score = 0;   //心理综合素质得分
        $mentality_score = $mentality['a_01'];

        $combining_capacity_score = 0;  // 学员综合素质能力得分
        $combining_capacity_score = $combining_capacity['a_01']['b_02'] + $combining_capacity['a_02']['b_02'] + $combining_capacity['a_03']['b_02'] + $combining_capacity['a_04']['b_02'] + $combining_capacity['a_05']['b_02'] + $combining_capacity['a_06']['b_02'];
        
        $level_rating['a_01'] = round(60 * ((($war_score * 0.8) + ($theory_score * 0.2)) / 100),2);  // 操作水平得分
        $level_rating['a_02'] =round(5 * ((($reaction_score * 0.5) + ($apm_score * 0.5)) / 100),2) ;   //敏捷度得分
        $level_rating['a_03'] = round(10 * ($training_match_score / 100),2);  //指挥力得分
        $level_rating['a_04']  = round(10 * ($theory_score / 100),2);  // 意识水平得分
        $level_rating['a_05'] =round(10 * (($combining_capacity_score * 0.7 + $mentality_score * 0.3) / 100),2) ; //心态能力得分
        $level_rating['a_06'] = round(5 * (($basketball_score * 0.3 + $run_score * 0.4 + $push_up_score * 0.3) / 100),2);  // 体能综合素质得分

        $total_points = $level_rating['a_01'] + $level_rating['a_02'] + $level_rating['a_03'] + $level_rating['a_04'] +  $level_rating['a_05'] + $level_rating['a_06'];

        if (90 <= $total_points && $total_points <= 100) {
            $talent = 'S级';
        } elseif (80 <= $total_points && $total_points < 90) {
            $talent = 'A级';
        } elseif (70 <= $total_points && $total_points < 80) {
            $talent = 'B级';
        } elseif ($total_points < 70) {
            $talent = 'C级';
        }




        $insertData = [
            'course_name' => $course_name,
            'course_number' => $course_number,
            'student_number' => $student_number,
            'student_name' => $student_name,
            'chief_coach' => $chief_coach,
            'date' => $date,
            'student_original_painting' => json_encode($student_original_painting),
            'war' => json_encode($war),
            'theory' => json_encode($theory),
            'stamina' => json_encode($stamina),
            'mentality' => json_encode($mentality),
            'combining_capacity' => json_encode($combining_capacity),
            'level_rating' => json_encode($level_rating),
            'total_points' => $total_points,
            'talent' => $talent,
            'trial_results' => $trial_results,
            'employment_advice' => $employment_advice,
            'coach_message' => json_encode($coach_message)
        ];

        $res = Db::name('jieyebaogao')->insert($insertData);

        if ($res) {
            return json(['code' => 200, 'msg' => '新增成功', 'data' => [
                'level_rating' => $level_rating,
                'total_points' => $total_points,
                'talent' => $talent
            ]]);
        } else {
            return json(['code' => -1, 'msg' => '错误，请重新提交']);
        }
    }
    public function view()
    {
        $id = request()->post('id');

        $find = Db::name('jieyebaogao')->where('id', $id)->find();

        if ($find) {
            foreach ($find as $k => $v) {
                $find[$k]['student_original_painting'] = json_decode($v['student_original_painting'], true);
                $find[$k]['war'] = json_decode($v['war'], true);
                $find[$k]['theory'] = json_decode($v['theory'], true);
                $find[$k]['stamina'] = json_decode($v['stamina'], true);
                $find[$k]['mentality'] = json_decode($v['mentality'], true);
                $find[$k]['combining_capacity'] = json_decode($v['combining_capacity'], true);
                $find[$k]['coach_message'] = json_decode($v['coach_message'], true);
            }

            return json(['code' => 200, 'msg' => '成功', 'data' => $find]);
        } else {
            return json(['code' => 200, 'msg' => '成功', 'data' => []]);
        }
    }
    public function update()
    {
        $id = request()->post('id');
        $course_name = request()->post('course_name');
        $course_number  = request()->post('course_number');
        $student_number  = request()->post('student_number');
        $student_name  = request()->post('student_name');
        $chief_coach  = request()->post('chief_coach');
        $date  = request()->post('date');
        $student_original_painting  =json_decode(request()->post('student_original_painting'),true);
        $war  =json_decode(request()->post('war'),true) ;
        $theory  = json_decode(request()->post('theory'),true);
        $stamina  = json_decode(request()->post('stamina'),true) ;
        $mentality  = json_decode(request()->post('mentality'),true) ;
        $combining_capacity  = json_decode(request()->post('combining_capacity'),true) ;
        $trial_results  = request()->post('trial_results');
        $employment_advice  = request()->post('employment_advice');
        $coach_message  = request()->post('coach_message');

        if($_FILES){
            $handle = new \Verot\Upload\Upload($_FILES['img']);   
            if ($handle->uploaded) {
            
                $handle->process(public_path() . 'student');
                if ($handle->processed) {
                    $student_original_painting['a_11'] = 'http://acejk.gkgaming.cn/student/'.$handle->file_src_name;

                    $handle->clean();
                } else {
                    return json(['code'=>-1,'msg'=>'图片上传失败，请重新尝试']);
                }
            }
        }

        $war_score = 0;    // 电竞技战术水平得分
        if ($war['a_02'] >= 2400) {
            $war_score = 100;
        } elseif ($war['a_02'] >= 2200 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 100;
        } elseif ($war['a_02'] >= 2200 && $war['a_03']['b_02'] < 12000) {
            $war_score = 90;
        } elseif ($war['a_02'] >= 1800 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 90;
        } elseif ($war['a_02'] >= 1800 && $war['a_03']['b_02'] < 12000) {
            $war_score = 80;
        } elseif ($war['a_02'] >= 1500 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 80;
        } elseif ($war['a_02'] >= 1500 && $war['a_03']['b_02'] < 12000) {
            $war_score = 70;
        } elseif ($war['a_02'] < 1500 && $war['a_03']['b_02'] >= 12000) {
            $war_score = 70;
        } elseif ($war['a_02'] < 1500 && $war['a_03']['b_02'] < 12000) {
            $war_score = 60;
        }

        $training_match_score = 0;    // 训练赛得分
        if ($war['a_04']['b_02'] >= $war['a_04']['b_01'] && $war['a_04']['b_05'] == '无') {
            $training_match_score = 0;
        } elseif ($war['a_04']['b_02'] >= $war['a_04']['b_01'] && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 50;
        } elseif ($war['a_04']['b_02'] >= $war['a_04']['b_01'] && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 20;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 10 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 50;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 10 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 100;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 10 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 70;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 5 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 30;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 5 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 80;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) > 5 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 50;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) >= 2 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 10;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) >= 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 60;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) >= 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 30;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) < 2 && $war['a_04']['b_05'] == '无') {
            $training_match_score = 0;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) < 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] > $war['a_04']['b_07']) {
            $training_match_score = 50;
        } elseif (($war['a_04']['b_01'] - $war['a_04']['b_02']) < 2 && $war['a_04']['b_05'] == '有' && $war['a_04']['b_06'] <= $war['a_04']['b_07']) {
            $training_match_score = 20;
        }

        $theory_score = 0;    //电竞理论知识得分
        $theory_score = $theory['a_01'] + $theory['a_02'] + $theory['a_03'] + $theory['a_04'] + $theory['a_05'];


        $basketball_score = 0;  //篮球得分
        if ($stamina['a_01'] <= 13.2) {
            $basketball_score = 100;
        } elseif ($stamina['a_01'] <= 16.2) {
            $basketball_score = 80;
        } elseif ($stamina['a_01'] <= 21.2) {
            $basketball_score = 60;
        } else {
            $basketball_score = 0;
        }

        $run_score = 0;    // 跑步得分
        $res_run_time = $stamina['a_02']['b_01'] * 60 + $stamina['a_02']['b_02'];

        if ($res_run_time <= 3 * 60 + 40) {
            $run_score = 100;
        } elseif ($res_run_time <= 3 * 60 + 55) {
            $run_score = 80;
        } elseif ($res_run_time <= 4 * 60 + 20) {
            $run_score = 60;
        } else {
            $run_score = 0;
        }

        $push_up_score = 0;   //俯卧撑得分
        if ($stamina['a_03'] >= 18) {
            $push_up_score = 100;
        } elseif ($stamina['a_03'] >= 10) {
            $push_up_score = 80;
        } elseif ($stamina['a_03'] >= 5) {
            $push_up_score = 60;
        } else {
            $push_up_score = 0;
        }

        $apm_score = 0;   // 手速得分       
        if ($stamina['a_04'] >= 250) {
            $apm_score = 100;
        } elseif ($stamina['a_04'] >= 150) {
            $apm_score = 80;
        } elseif ($stamina['a_04'] >= 100) {
            $apm_score = 60;
        } else {
            $apm_score = 0;
        }

        $reaction_score = 0;  //反应得分
        if ($stamina['a_05'] <= 150) {
            $reaction_score = 100;
        } elseif ($stamina['a_05'] <= 200) {
            $reaction_score = 80;
        } elseif ($stamina['a_05'] <= 250) {
            $reaction_score = 60;
        } else {
            $reaction_score = 0;
        }

        $mentality_score = 0;   //心理综合素质得分
        $mentality_score = $mentality['a_01'];

        $combining_capacity_score = 0;  // 学员综合素质能力得分
        $combining_capacity_score = $combining_capacity['a_01'] + $combining_capacity['a_02'] + $combining_capacity['a_03'] + $combining_capacity['a_04'] + $combining_capacity['a_05'] + $combining_capacity['a_06'];

        $level_rating['a_01'] = round(60 * ((($war_score * 0.8) + ($theory_score * 0.2)) / 100),2);  // 操作水平得分
        $level_rating['a_02'] =round(5 * ((($reaction_score * 0.5) + ($apm_score * 0.5)) / 100),2) ;   //敏捷度得分
        $level_rating['a_03'] = round(10 * ($training_match_score / 100),2);  //指挥力得分
        $level_rating['a_04']  = round(10 * ($theory_score / 100),2);  // 意识水平得分
        $level_rating['a_05'] =round(10 * (($combining_capacity_score * 0.7 + $mentality_score * 0.3) / 100),2) ; //心态能力得分
        $level_rating['a_06'] = round(5 * (($basketball_score * 0.3 + $run_score * 0.4 + $push_up_score * 0.3) / 100),2);  // 体能综合素质得分

        $total_points = $level_rating['a_01'] + $level_rating['a_02'] + $level_rating['a_03'] + $level_rating['a_04'] +  $level_rating['a_05'] + $level_rating['a_06'];

        if (90 <= $total_points && $total_points <= 100) {
            $talent = 'S级';
        } elseif (80 <= $total_points && $total_points < 90) {
            $talent = 'A级';
        } elseif (70 <= $total_points && $total_points < 80) {
            $talent = 'B级';
        } elseif (70 < $total_points) {
            $talent = 'C级';
        }

        $insertData = [
            'id' => $id,
            'course_name' => $course_name,
            'course_number' => $course_number,
            'student_number' => $student_number,
            'student_name' => $student_name,
            'chief_coach' => $chief_coach,
            'date' => $date,
            'student_original_painting' => json_encode($student_original_painting),
            'war' => json_encode($war),
            'theory' => json_encode($theory),
            'stamina' => json_encode($stamina),
            'mentality' => json_encode($mentality),
            'combining_capacity' => json_encode($combining_capacity),
            'level_rating' => json_encode($level_rating),
            'total_points' => $total_points,
            'talent' => $talent,
            'trial_results' => $trial_results,
            'employment_advice' => $employment_advice,
            'coach_message' => json_encode($coach_message)
        ];

        $res = Db::name('jieyebaogao')->update($insertData);

        if ($res) {
            return json(['code' => 200, 'msg' => '新增成功', 'data' => [
                'level_rating' => $level_rating,
                'total_points' => $total_points,
                'talent' => $talent
            ]]);
        } else {
            return json(['code' => -1, 'msg' => '错误，请重新提交']);
        }
    }
    public function share()
    {
        return json(['code'=>200,'msg'=>'成功','data'=>(new \app\util\WechatShare())->getSignPackage()]);
    }
}