<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use app\util\ReturnCode;
use think\facade\Db;
use PHPExcel;
use PHPExcel_IOFactory;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class Sheet extends AdminAuth
{
    //健康信息问卷表
    public function health()
    {
        $where[] = ['a.id', '>', 0];;
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . implode(',', $course) . $page . $limit);

        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_user_info b', 'a.open_id = b.openid')
                ->join('cm_user_health_info c', 'b.openid = c.openid and b.course = c.course', 'left')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.name as b_name,b.sex,b.phone,b.idcard,b.birthday,b.age,b.nation,b.education,b.job,b.birthplace,b.census,b.live_time,b.reason,b.housing_situation,b.living_situation,b.parent_live,b.contacts,b.marriage,b.marriage_status,b.income,b.stime as b_stime,b.etime as b_etime,b.ltime,c.height,c.weight,c.smoke,c.smoke_age,c.smoke_date,c.smoke_amount,c.drink,c.drink_age,c.drink_day,c.drink_bad_time,c.drink_more,c.confirmed_disease,c.pregnancy_amount,c.childbirth_amount,c.sleep_time,c.sleep_quality,c.sleeping_pills,c.used_drug,c.want_suicide,c.attempt_suicide,c.suicide_plan,c.one_attempt_suicide,c.depression,c.exercise_count,c.exercise_duration,c.stime as c_stime,c.etime as c_etime,c.time')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                if ($value['b_stime']) {
                    $list[$key]['b_stime'] = date('Y-m-d H:i', $value['b_stime']);
                }
                if ($value['b_etime']) {
                    $list[$key]['b_etime'] = date('Y-m-d H:i', $value['b_etime']);
                }
                if ($value['c_stime']) {
                    $list[$key]['c_stime'] = date('Y-m-d H:i', $value['c_stime']);
                }
                if ($value['c_etime']) {
                    $list[$key]['c_etime'] = date('Y-m-d H:i', $value['c_etime']);
                }
                $list[$key]['course'] = 'S1';
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list[$key]['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list[$key]['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list[$key]['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list[$key]['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list[$key]['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list[$key]['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list[$key]['type_name'] = 'N-普通人群';
                } else {
                    $list[$key]['type_name'] = '游客';
                }
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_user_info b', 'a.open_id = b.openid')
                ->join('cm_user_health_info c', 'b.openid = c.openid', 'left')
                ->field('a.open_id')
                ->count();
            $page_total = ceil($total / $limit);

            $return = [
                'list' => $list,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //导出健康信息问卷表
    public function excel_health()
    {
        $where[] = ['a.id', '>', 0];;
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);


        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_user_info b', 'a.open_id = b.openid')
            ->join('cm_user_health_info c', 'b.openid = c.openid and b.course = c.course', 'left')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.name as b_name,b.sex,b.phone,b.idcard,b.birthday,b.age,b.nation,b.education,b.job,b.birthplace,b.census,b.live_time,b.reason,b.housing_situation,b.living_situation,b.parent_live,b.contacts,b.marriage,b.marriage_status,b.income,b.stime as b_stime,b.etime as b_etime,b.ltime,c.height,c.weight,c.smoke,c.smoke_age,c.smoke_date,c.smoke_amount,c.drink,c.drink_age,c.drink_day,c.drink_bad_time,c.drink_more,c.confirmed_disease,c.pregnancy_amount,c.childbirth_amount,c.sleep_time,c.sleep_quality,c.sleeping_pills,c.used_drug,c.want_suicide,c.attempt_suicide,c.suicide_plan,c.one_attempt_suicide,c.depression,c.exercise_count,c.exercise_duration,c.stime as c_stime,c.etime as c_etime,c.time')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();


        foreach ($list as $key => $value) {
            $list[$key]['b_stime'] = $value['b_stime'] ? date('Y-m-d H:i', $value['b_stime']) : '';
            $list[$key]['b_etime'] = $value['b_etime'] ? date('Y-m-d H:i', $value['b_etime']) : '';
            $list[$key]['c_stime'] = $value['c_stime'] ? date('Y-m-d H:i', $value['c_stime']) : '';
            $list[$key]['c_etime'] = $value['c_etime'] ? date('Y-m-d H:i', $value['c_etime']) : '';
            $list[$key]['course'] = 'S1';
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list[$key]['type_name'] = 'N-普通人群';
            } else {
                $list[$key]['type_name'] = '游客';
            }
        } //dump($list);die;

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '用户分类', '课程编号', 'A01', 'A02', 'A03', 'A04', 'A05', 'A05a', 'A06', 'A07', 'A08', 'A09', 'A10', 'A11', 'A11a', 'A12', 'A13', 'A14', 'A15', 'A16', 'A16a', 'A17', 'A开始时间', 'A结束时间', 'A填写时长', 'B01', 'B02', 'B03', 'B03a', 'B03b', 'B03c', 'B04', 'B04a', 'B04b', 'B04c', 'B04d', 'B05', 'B06', 'B06a', 'B07', 'B08', 'B09', 'B09a', 'B10', 'B11', 'B12', 'B13', 'B14', 'B15', 'B15a', 'B开始时间', 'B结束时间', 'B填写时长');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(22);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(16);
        $PHPSheet->getColumnDimension('I')->setWidth(12);
        $PHPSheet->getColumnDimension('J')->setWidth(12);
        $PHPSheet->getColumnDimension('K')->setWidth(12);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(15);
        $PHPSheet->getColumnDimension('O')->setWidth(15);
        $PHPSheet->getColumnDimension('P')->setWidth(15);
        $PHPSheet->getColumnDimension('Q')->setWidth(13);
        $PHPSheet->getColumnDimension('R')->setWidth(13);
        $PHPSheet->getColumnDimension('S')->setWidth(13);
        $PHPSheet->getColumnDimension('T')->setWidth(13);
        $PHPSheet->getColumnDimension('U')->setWidth(13);
        $PHPSheet->getColumnDimension('V')->setWidth(13);
        $PHPSheet->getColumnDimension('W')->setWidth(13);
        $PHPSheet->getColumnDimension('X')->setWidth(15);
        $PHPSheet->getColumnDimension('Y')->setWidth(13);
        $PHPSheet->getColumnDimension('Z')->setWidth(13);
        $PHPSheet->getColumnDimension('AA')->setWidth(20);
        $PHPSheet->getColumnDimension('AB')->setWidth(20);
        $PHPSheet->getColumnDimension('AC')->setWidth(20);
        $PHPSheet->getColumnDimension('AD')->setWidth(13);
        $PHPSheet->getColumnDimension('AE')->setWidth(13);
        $PHPSheet->getColumnDimension('AF')->setWidth(15);
        $PHPSheet->getColumnDimension('AG')->setWidth(15);
        $PHPSheet->getColumnDimension('AH')->setWidth(13);
        $PHPSheet->getColumnDimension('AI')->setWidth(13);
        $PHPSheet->getColumnDimension('AJ')->setWidth(13);
        $PHPSheet->getColumnDimension('AK')->setWidth(15);
        $PHPSheet->getColumnDimension('AL')->setWidth(15);
        $PHPSheet->getColumnDimension('AM')->setWidth(13);
        $PHPSheet->getColumnDimension('AN')->setWidth(13);
        $PHPSheet->getColumnDimension('AO')->setWidth(13);
        $PHPSheet->getColumnDimension('AP')->setWidth(15);
        $PHPSheet->getColumnDimension('AQ')->setWidth(15);
        $PHPSheet->getColumnDimension('AR')->setWidth(13);
        $PHPSheet->getColumnDimension('AS')->setWidth(13);
        $PHPSheet->getColumnDimension('AT')->setWidth(13);
        $PHPSheet->getColumnDimension('AU')->setWidth(15);
        $PHPSheet->getColumnDimension('AV')->setWidth(15);
        $PHPSheet->getColumnDimension('AW')->setWidth(13);
        $PHPSheet->getColumnDimension('AX')->setWidth(13);
        $PHPSheet->getColumnDimension('AY')->setWidth(13);
        $PHPSheet->getColumnDimension('AZ')->setWidth(13);
        $PHPSheet->getColumnDimension('BA')->setWidth(15);
        $PHPSheet->getColumnDimension('BB')->setWidth(15);
        $PHPSheet->getColumnDimension('BC')->setWidth(13);
        $PHPSheet->getColumnDimension('BD')->setWidth(20);
        $PHPSheet->getColumnDimension('BE')->setWidth(20);
        $PHPSheet->getColumnDimension('BF')->setWidth(20);

        //数据
        foreach ($list as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                $PHPSheet->setCellValue('A' . $row, ' ' . $num);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['open_id']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['number']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['name']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['wx_phone']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['type_name']);
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['course']);
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['b_name']);
                $PHPSheet->setCellValue('I' . $row, ' ' . $v['sex']);
                $PHPSheet->setCellValue('J' . $row, ' ' . $v['phone']);
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['idcard']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['birthday']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['age']);
                $PHPSheet->setCellValue('N' . $row, ' ' . $v['nation']);
                $PHPSheet->setCellValue('O' . $row, ' ' . $v['education']);
                $PHPSheet->setCellValue('P' . $row, ' ' . $v['job']);
                $PHPSheet->setCellValue('Q' . $row, ' ' . $v['birthplace']);
                $PHPSheet->setCellValue('R' . $row, ' ' . $v['census']);
                $PHPSheet->setCellValue('S' . $row, ' ' . $v['live_time']);
                $PHPSheet->setCellValue('T' . $row, ' ' . $v['reason']);
                $PHPSheet->setCellValue('U' . $row, ' ' . $v['housing_situation']);
                $PHPSheet->setCellValue('V' . $row, ' ' . $v['living_situation']);
                $PHPSheet->setCellValue('W' . $row, ' ' . $v['parent_live']);
                $PHPSheet->setCellValue('X' . $row, ' ' . $v['contacts']);
                $PHPSheet->setCellValue('Y' . $row, ' ' . $v['marriage']);
                $PHPSheet->setCellValue('Z' . $row, ' ' . $v['marriage_status']);
                $PHPSheet->setCellValue('AA' . $row, ' ' . $v['income']);
                $PHPSheet->setCellValue('AB' . $row, ' ' . $v['b_stime']);
                $PHPSheet->setCellValue('AC' . $row, ' ' . $v['b_etime']);
                $PHPSheet->setCellValue('AD' . $row, ' ' . $v['ltime']);
                $PHPSheet->setCellValue('AE' . $row, ' ' . $v['height']);
                $PHPSheet->setCellValue('AF' . $row, ' ' . $v['weight']);
                $PHPSheet->setCellValue('AG' . $row, ' ' . $v['smoke']);
                $PHPSheet->setCellValue('AH' . $row, ' ' . $v['smoke_age']);
                $PHPSheet->setCellValue('AI' . $row, ' ' . $v['smoke_date']);
                $PHPSheet->setCellValue('AJ' . $row, ' ' . $v['smoke_amount']);
                $PHPSheet->setCellValue('AK' . $row, ' ' . $v['drink']);
                $PHPSheet->setCellValue('AL' . $row, ' ' . $v['drink_age']);
                $PHPSheet->setCellValue('AM' . $row, ' ' . $v['drink_day']);
                $PHPSheet->setCellValue('AN' . $row, ' ' . $v['drink_bad_time']);
                $PHPSheet->setCellValue('AO' . $row, ' ' . $v['drink_more']);
                $PHPSheet->setCellValue('AP' . $row, ' ' . $v['confirmed_disease']);
                $PHPSheet->setCellValue('AQ' . $row, ' ' . $v['pregnancy_amount']);
                $PHPSheet->setCellValue('AR' . $row, ' ' . $v['childbirth_amount']);
                $PHPSheet->setCellValue('AS' . $row, ' ' . $v['sleep_time']);
                $PHPSheet->setCellValue('AT' . $row, ' ' . $v['sleep_quality']);
                $PHPSheet->setCellValue('AU' . $row, ' ' . $v['sleeping_pills']);
                $PHPSheet->setCellValue('AV' . $row, ' ' . $v['used_drug']);
                $PHPSheet->setCellValue('AW' . $row, ' ' . $v['want_suicide']);
                $PHPSheet->setCellValue('AX' . $row, ' ' . $v['attempt_suicide']);
                $PHPSheet->setCellValue('AY' . $row, ' ' . $v['suicide_plan']);
                $PHPSheet->setCellValue('AZ' . $row, ' ' . $v['one_attempt_suicide']);
                $PHPSheet->setCellValue('BA' . $row, ' ' . $v['depression']);
                $PHPSheet->setCellValue('BB' . $row, ' ' . $v['exercise_count']);
                $PHPSheet->setCellValue('BC' . $row, ' ' . $v['exercise_duration']);
                $PHPSheet->setCellValue('BD' . $row, ' ' . $v['c_stime']);
                $PHPSheet->setCellValue('BE' . $row, ' ' . $v['c_etime']);
                $PHPSheet->setCellValue('BF' . $row, ' ' . $v['time']);
            }
            ob_flush();
            flush();
        }
        $filename = '信息健康' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //课前记录问卷表
    public function course_before()
    {
        $where[] = ['a.id', '>', 0];;
        $search = [];
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
            $search[] = ['course', 'in', $course];
        }
        $CP = input('post.CP/a', array());
        if ($CP && !in_array('10', $CP)) {
            $cp_arr = [];
            if (in_array('1', $CP)) {
                $cp_arr[] = 0;
                $cp_arr[] = 1;
                $cp_arr[] = 2;
                $cp_arr[] = 3;
                $cp_arr[] = 4;
            }
            if (in_array('2', $CP)) {
                $cp_arr[] = 5;
                $cp_arr[] = 6;
                $cp_arr[] = 7;
                $cp_arr[] = 8;
                $cp_arr[] = 9;
            }
            if (in_array('3', $CP)) {
                $cp_arr[] = 10;
                $cp_arr[] = 11;
                $cp_arr[] = 12;
                $cp_arr[] = 13;
                $cp_arr[] = 14;
            }
            if (in_array('4', $CP)) {
                $cp_arr[] = 15;
                $cp_arr[] = 16;
                $cp_arr[] = 17;
                $cp_arr[] = 18;
                $cp_arr[] = 19;
            }
            if (in_array('5', $CP)) {
                $cp_arr[] = 20;
                $cp_arr[] = 21;
                $cp_arr[] = 22;
                $cp_arr[] = 23;
                $cp_arr[] = 24;
                $cp_arr[] = 25;
                $cp_arr[] = 26;
                $cp_arr[] = 27;
            }
            $where[] = ['b.CP', 'in', $cp_arr];
        }
        $DP = input('post.DP/a', array());
        if ($DP && !in_array('10', $DP)) {
            $dp_arr = [];
            if (in_array('1', $DP)) {
                $dp_arr[] = 0;
                $dp_arr[] = 1;
                $dp_arr[] = 2;
                $dp_arr[] = 3;
                $dp_arr[] = 4;
            }
            if (in_array('2', $DP)) {
                $dp_arr[] = 5;
                $dp_arr[] = 6;
                $dp_arr[] = 7;
                $dp_arr[] = 8;
                $dp_arr[] = 9;
            }
            if (in_array('3', $DP)) {
                $dp_arr[] = 10;
                $dp_arr[] = 11;
                $dp_arr[] = 12;
                $dp_arr[] = 13;
                $dp_arr[] = 14;
            }
            if (in_array('4', $DP)) {
                $dp_arr[] = 15;
                $dp_arr[] = 16;
                $dp_arr[] = 17;
                $dp_arr[] = 18;
                $dp_arr[] = 19;
                $dp_arr[] = 20;
                $dp_arr[] = 21;
            }
            $where[] = ['c.DP', 'in', $dp_arr];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);


        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . implode(',', $course) . implode(',', $CP) . implode(',', $DP) . $page . $limit . 'user' . 'cm_depression_info' . 'cm_anxiety_info');

        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_depression_info b', 'a.open_id = b.openid')
                ->join('cm_anxiety_info c', 'b.id = c.dep_id', 'left')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.C01,b.C02,b.C03,b.C04,b.C05,b.C06,b.C07,b.C08,b.C09,b.CP,b.stime as b_stime,b.etime as b_etime,b.ltime as b_ltime,b.state,c.D01,c.D02,c.D03,c.D04,c.D05,c.D06,c.D07,c.DP,c.stime as c_stime,c.etime as c_etime,c.time as c_ltime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                $list[$key]['b_stime'] = date('Y-m-d H:i', $value['b_stime']);
                $list[$key]['b_etime'] = date('Y-m-d H:i', $value['b_etime']);
                $list[$key]['c_stime'] = date('Y-m-d H:i', $value['c_stime']);
                $list[$key]['c_etime'] = date('Y-m-d H:i', $value['c_etime']);

                if (in_array($value['course'], [1, 2, 3, 4, 5, 6, 7])) $list[$key]['course']  = 'S' . $value['course'];
                if ($value['course'] == 'w1') $list[$key]['course']  = 'E-1周';
                if ($value['course'] == 'm1') $list[$key]['course']  = 'E-1月';
                if ($value['course'] == 'm3') $list[$key]['course']  = 'E-3月';
                if ($value['course'] == 'm6') $list[$key]['course']  = 'E-6月';
                if ($value['course'] == 'y1') $list[$key]['course']  = 'E-1年';

                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list[$key]['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list[$key]['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list[$key]['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list[$key]['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list[$key]['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list[$key]['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list[$key]['type_name'] = 'N-普通人群';
                } else {
                    $list[$key]['type_name'] = '游客';
                }
                $search[] = ['openid', '=', $value['open_id']];
                //判断是否是第一次该课程第一次填写
                if ($value['state'] == '1') { //该课程第一次填写
                    //查询失眠测评
                    $insomnia = Db::name('insomnia_info')->where($search)->field('E01a,E01b,E01c,E02,E03,E04,E05,EP,stime,etime,ltime')->find();
                    if ($insomnia) {
                        $list[$key]['E01a']  = $insomnia['E01a'];
                        $list[$key]['E01b']  = $insomnia['E01b'];
                        $list[$key]['E01c']  = $insomnia['E01c'];
                        $list[$key]['E02']   = $insomnia['E02'];
                        $list[$key]['E03']   = $insomnia['E03'];
                        $list[$key]['E04']   = $insomnia['E04'];
                        $list[$key]['E05']   = $insomnia['E05'];
                        $list[$key]['EP']    = $insomnia['EP'];
                        $list[$key]['e_stime'] = date('Y-m-d H:i', $insomnia['stime']);
                        $list[$key]['e_etime'] = date('Y-m-d H:i', $insomnia['etime']);
                        $list[$key]['e_ltime'] = $insomnia['ltime'];
                    } else {
                        $list[$key]['E01a']  = '';
                        $list[$key]['E01b']  = '';
                        $list[$key]['E01c']  = '';
                        $list[$key]['E02']   = '';
                        $list[$key]['E03']   = '';
                        $list[$key]['E04']   = '';
                        $list[$key]['E05']   = '';
                        $list[$key]['EP']    = '';
                        $list[$key]['e_stime'] = '';
                        $list[$key]['e_etime'] = '';
                        $list[$key]['e_ltime'] = '';
                    }
                    //查询WHO-5生活质量测评记录
                    $life_quality = Db::name('life_quality_info')->where($search)->field('F01,F02,F03,F04,F05,FP,stime,etime,ltime')->find();
                    if ($life_quality) {
                        $list[$key]['F01']     = $life_quality['F01'];
                        $list[$key]['F02']     = $life_quality['F02'];
                        $list[$key]['F03']     = $life_quality['F03'];
                        $list[$key]['F04']     = $life_quality['F04'];
                        $list[$key]['F05']     = $life_quality['F05'];
                        $list[$key]['FP']      = $life_quality['FP'];
                        $list[$key]['f_stime'] = date('Y-m-d H:i', $life_quality['stime']);
                        $list[$key]['f_etime'] = date('Y-m-d H:i', $life_quality['etime']);
                        $list[$key]['f_ltime'] = $life_quality['ltime'];
                    } else {
                        $list[$key]['F01']     = '';
                        $list[$key]['F02']     = '';
                        $list[$key]['F03']     = '';
                        $list[$key]['F04']     = '';
                        $list[$key]['F05']     = '';
                        $list[$key]['FP']      = '';
                        $list[$key]['f_stime'] = '';
                        $list[$key]['f_etime'] = '';
                        $list[$key]['f_ltime'] = '';
                    }
                    //查询失眠测评
                    $depressive_effects = Db::name('depressive_effects')->where($search)->field('G01,G02,G03,G04,G05,G06,GP,stime,etime,ltime')->find();
                    if ($depressive_effects) {
                        $list[$key]['G01']     = $depressive_effects['G01'];
                        $list[$key]['G02']     = $depressive_effects['G02'];
                        $list[$key]['G03']     = $depressive_effects['G03'];
                        $list[$key]['G04']     = $depressive_effects['G04'];
                        $list[$key]['G05']     = $depressive_effects['G05'];
                        $list[$key]['G06']     = $depressive_effects['G06'];
                        $list[$key]['GP']      = $depressive_effects['GP'];
                        $list[$key]['g_stime'] = date('Y-m-d H:i', $depressive_effects['stime']);
                        $list[$key]['g_etime'] = date('Y-m-d H:i', $depressive_effects['etime']);
                        $list[$key]['g_ltime'] = $depressive_effects['ltime'];
                    } else {
                        $list[$key]['G01']     = '';
                        $list[$key]['G02']     = '';
                        $list[$key]['G03']     = '';
                        $list[$key]['G04']     = '';
                        $list[$key]['G05']     = '';
                        $list[$key]['G06']     = '';
                        $list[$key]['GP']      = '';
                        $list[$key]['g_stime'] = '';
                        $list[$key]['g_etime'] = '';
                        $list[$key]['g_ltime'] = '';
                    }
                    //查询失眠测评
                    $somatic_symptoms = Db::name('somatic_symptoms')->where($search)->withoutField(['id', 'openid', 'course'])->find();
                    if ($somatic_symptoms) {
                        $list[$key]['H01']     = $somatic_symptoms['H01'];
                        $list[$key]['H02']     = $somatic_symptoms['H02'];
                        $list[$key]['H03']     = $somatic_symptoms['H03'];
                        $list[$key]['H04']     = $somatic_symptoms['H04'];
                        $list[$key]['H05']     = $somatic_symptoms['H05'];
                        $list[$key]['H06']     = $somatic_symptoms['H06'];
                        $list[$key]['H07']     = $somatic_symptoms['H07'];
                        $list[$key]['H08']     = $somatic_symptoms['H08'];
                        $list[$key]['H09']     = $somatic_symptoms['H09'];
                        $list[$key]['H10']     = $somatic_symptoms['H10'];
                        $list[$key]['H11']     = $somatic_symptoms['H11'];
                        $list[$key]['H12']     = $somatic_symptoms['H12'];
                        $list[$key]['H13']     = $somatic_symptoms['H13'];
                        $list[$key]['H14']     = $somatic_symptoms['H14'];
                        $list[$key]['H15']     = $somatic_symptoms['H15'];
                        $list[$key]['H16']     = $somatic_symptoms['H16'];
                        $list[$key]['H17']     = $somatic_symptoms['H17'];
                        $list[$key]['H18']     = $somatic_symptoms['H18'];
                        $list[$key]['H19']     = $somatic_symptoms['H19'];
                        $list[$key]['H20']     = $somatic_symptoms['H20'];
                        $list[$key]['H21']     = $somatic_symptoms['H21'];
                        $list[$key]['H22']     = $somatic_symptoms['H22'];
                        $list[$key]['H23']     = $somatic_symptoms['H23'];
                        $list[$key]['H24']     = $somatic_symptoms['H24'];
                        $list[$key]['H25']     = $somatic_symptoms['H25'];
                        $list[$key]['H26']     = $somatic_symptoms['H26'];
                        $list[$key]['H27']     = $somatic_symptoms['H27'];
                        $list[$key]['H28']     = $somatic_symptoms['H28'];
                        $list[$key]['HP']      = $somatic_symptoms['HP'];
                        $list[$key]['h_stime'] = date('Y-m-d H:i', $somatic_symptoms['stime']);
                        $list[$key]['h_etime'] = date('Y-m-d H:i', $somatic_symptoms['etime']);
                        $list[$key]['h_ltime'] = $somatic_symptoms['ltime'];
                    } else {
                        $list[$key]['H01']     = '';
                        $list[$key]['H02']     = '';
                        $list[$key]['H03']     = '';
                        $list[$key]['H04']     = '';
                        $list[$key]['H05']     = '';
                        $list[$key]['H06']     = '';
                        $list[$key]['H07']     = '';
                        $list[$key]['H08']     = '';
                        $list[$key]['H09']     = '';
                        $list[$key]['H10']     = '';
                        $list[$key]['H11']     = '';
                        $list[$key]['H12']     = '';
                        $list[$key]['H13']     = '';
                        $list[$key]['H14']     = '';
                        $list[$key]['H15']     = '';
                        $list[$key]['H16']     = '';
                        $list[$key]['H17']     = '';
                        $list[$key]['H18']     = '';
                        $list[$key]['H19']     = '';
                        $list[$key]['H20']     = '';
                        $list[$key]['H21']     = '';
                        $list[$key]['H22']     = '';
                        $list[$key]['H23']     = '';
                        $list[$key]['H24']     = '';
                        $list[$key]['H25']     = '';
                        $list[$key]['H26']     = '';
                        $list[$key]['H27']     = '';
                        $list[$key]['H28']     = '';
                        $list[$key]['HP']      = '';
                        $list[$key]['h_stime'] = '';
                        $list[$key]['h_etime'] = '';
                        $list[$key]['h_ltime'] = '';
                    }

                    //查心理弹性量
                    $psychological_elastic = Db::name('psychological_elastic')->where('open_id', $value['open_id'])->withoutField('id,open_id,course')->find();
                    if ($psychological_elastic) {
                        $list[$key]['I01']     = $psychological_elastic['I01'];
                        $list[$key]['I02']     = $psychological_elastic['I02'];
                        $list[$key]['I03']     = $psychological_elastic['I03'];
                        $list[$key]['I04']     = $psychological_elastic['I04'];
                        $list[$key]['I05']     = $psychological_elastic['I05'];
                        $list[$key]['I06']     = $psychological_elastic['I06'];
                        $list[$key]['I07']     = $psychological_elastic['I07'];
                        $list[$key]['I08']     = $psychological_elastic['I08'];
                        $list[$key]['I09']     = $psychological_elastic['I09'];
                        $list[$key]['I10']     = $psychological_elastic['I10'];
                        $list[$key]['I11']     = $psychological_elastic['I11'];
                        $list[$key]['I12']     = $psychological_elastic['I12'];
                        $list[$key]['I13']     = $psychological_elastic['I13'];
                        $list[$key]['I14']     = $psychological_elastic['I14'];
                        $list[$key]['I15']     = $psychological_elastic['I15'];
                        $list[$key]['I16']     = $psychological_elastic['I16'];
                        $list[$key]['I17']     = $psychological_elastic['I17'];
                        $list[$key]['I18']     = $psychological_elastic['I18'];
                        $list[$key]['I19']     = $psychological_elastic['I19'];
                        $list[$key]['I20']     = $psychological_elastic['I20'];
                        $list[$key]['I21']     = $psychological_elastic['I21'];
                        $list[$key]['I22']     = $psychological_elastic['I22'];
                        $list[$key]['I23']     = $psychological_elastic['I23'];
                        $list[$key]['I24']     = $psychological_elastic['I24'];
                        $list[$key]['I25']     = $psychological_elastic['I25'];
                        $list[$key]['IP']      = $psychological_elastic['IP'];
                        $list[$key]['tough']      = $psychological_elastic['tough'];
                        $list[$key]['power']      = $psychological_elastic['power'];
                        $list[$key]['optimistic']      = $psychological_elastic['optimistic'];
                        $list[$key]['i_stime'] = date('Y-m-d H:i', $psychological_elastic['stime']);
                        $list[$key]['i_etime'] = date('Y-m-d H:i', $psychological_elastic['etime']);
                        $list[$key]['i_ltime'] = $psychological_elastic['ltime'];
                    } else {
                        $list[$key]['I01']     = '';
                        $list[$key]['I02']     = '';
                        $list[$key]['I03']     = '';
                        $list[$key]['I04']     = '';
                        $list[$key]['I05']     = '';
                        $list[$key]['I06']     = '';
                        $list[$key]['I07']     = '';
                        $list[$key]['I08']     = '';
                        $list[$key]['I09']     = '';
                        $list[$key]['I10']     = '';
                        $list[$key]['I11']     = '';
                        $list[$key]['I12']     = '';
                        $list[$key]['I13']     = '';
                        $list[$key]['I14']     = '';
                        $list[$key]['I15']     = '';
                        $list[$key]['I16']     = '';
                        $list[$key]['I17']     = '';
                        $list[$key]['I18']     = '';
                        $list[$key]['I19']     = '';
                        $list[$key]['I20']     = '';
                        $list[$key]['I21']     = '';
                        $list[$key]['I22']     = '';
                        $list[$key]['I23']     = '';
                        $list[$key]['I24']     = '';
                        $list[$key]['I25']     = '';
                        $list[$key]['IP']      = '';
                        $list[$key]['tough']      = '';
                        $list[$key]['power']      = '';
                        $list[$key]['optimistic']      = '';
                        $list[$key]['i_stime'] = '';
                        $list[$key]['i_etime'] = '';
                        $list[$key]['i_ltime'] = '';
                    }
                    //抑郁思维模式评估
                    $thinking_pattern = Db::name('thinking_pattern')->where('open_id', $value['open_id'])->withoutField('id,open_id,course')->find();
                    if ($thinking_pattern) {
                        $list[$key]['J01']     = $thinking_pattern['J01'];
                        $list[$key]['J02']     = $thinking_pattern['J02'];
                        $list[$key]['J03']     = $thinking_pattern['J03'];
                        $list[$key]['J04']     = $thinking_pattern['J04'];
                        $list[$key]['J05']     = $thinking_pattern['J05'];
                        $list[$key]['J06']     = $thinking_pattern['J06'];
                        $list[$key]['J07']     = $thinking_pattern['J07'];
                        $list[$key]['J08']     = $thinking_pattern['J08'];
                        $list[$key]['J09']     = $thinking_pattern['J09'];
                        $list[$key]['J10']     = $thinking_pattern['J10'];
                        $list[$key]['J11']     = $thinking_pattern['J11'];
                        $list[$key]['J12']     = $thinking_pattern['J12'];
                        $list[$key]['J13']     = $thinking_pattern['J13'];
                        $list[$key]['J14']     = $thinking_pattern['J14'];
                        $list[$key]['J15']     = $thinking_pattern['J15'];
                        $list[$key]['J16']     = $thinking_pattern['J16'];
                        $list[$key]['J17']     = $thinking_pattern['J17'];
                        $list[$key]['J18']     = $thinking_pattern['J18'];
                        $list[$key]['J19']     = $thinking_pattern['J19'];
                        $list[$key]['J20']     = $thinking_pattern['J20'];
                        $list[$key]['J21']     = $thinking_pattern['J21'];
                        $list[$key]['J22']     = $thinking_pattern['J22'];
                        $list[$key]['J23']     = $thinking_pattern['J23'];
                        $list[$key]['J24']     = $thinking_pattern['J24'];
                        $list[$key]['J25']     = $thinking_pattern['J25'];
                        $list[$key]['J26']     = $thinking_pattern['J26'];
                        $list[$key]['J27']     = $thinking_pattern['J27'];
                        $list[$key]['J28']     = $thinking_pattern['J28'];
                        $list[$key]['J29']     = $thinking_pattern['J29'];
                        $list[$key]['J30']     = $thinking_pattern['J30'];
                        $list[$key]['JP']      = $thinking_pattern['JP'];
                        $list[$key]['individual']      = $thinking_pattern['individual'];
                        $list[$key]['negative']      = $thinking_pattern['negative'];
                        $list[$key]['self_confidence']      = $thinking_pattern['self_confidence'];
                        $list[$key]['Helpless']      = $thinking_pattern['Helpless'];
                        $list[$key]['j_stime'] = date('Y-m-d H:i', $thinking_pattern['stime']);
                        $list[$key]['j_etime'] = date('Y-m-d H:i', $thinking_pattern['etime']);
                        $list[$key]['j_ltime'] = $thinking_pattern['ltime'];
                    } else {
                        $list[$key]['I01']     = '';
                        $list[$key]['J02']     = '';
                        $list[$key]['J03']     = '';
                        $list[$key]['J04']     = '';
                        $list[$key]['J05']     = '';
                        $list[$key]['J06']     = '';
                        $list[$key]['J07']     = '';
                        $list[$key]['J08']     = '';
                        $list[$key]['J09']     = '';
                        $list[$key]['J10']     = '';
                        $list[$key]['J11']     = '';
                        $list[$key]['J12']     = '';
                        $list[$key]['J13']     = '';
                        $list[$key]['J14']     = '';
                        $list[$key]['J15']     = '';
                        $list[$key]['J16']     = '';
                        $list[$key]['J17']     = '';
                        $list[$key]['J18']     = '';
                        $list[$key]['J19']     = '';
                        $list[$key]['J20']     = '';
                        $list[$key]['J21']     = '';
                        $list[$key]['J22']     = '';
                        $list[$key]['J23']     = '';
                        $list[$key]['J24']     = '';
                        $list[$key]['J25']     = '';
                        $list[$key]['J26']     = '';
                        $list[$key]['J27']     = '';
                        $list[$key]['J28']     = '';
                        $list[$key]['J29']     = '';
                        $list[$key]['J30']     = '';
                        $list[$key]['JP']      = '';
                        $list[$key]['individual']      = '';
                        $list[$key]['negative']      = '';
                        $list[$key]['self_confidence']      = '';
                        $list[$key]['Helpless']      = '';
                        $list[$key]['j_stime'] = '';
                        $list[$key]['j_etime'] = '';
                        $list[$key]['j_ltime'] = '';
                    }
                } else {
                    $list[$key]['E01a']  = '';
                    $list[$key]['E01b']  = '';
                    $list[$key]['E01c']  = '';
                    $list[$key]['E02']   = '';
                    $list[$key]['E03']   = '';
                    $list[$key]['E04']   = '';
                    $list[$key]['E05']   = '';
                    $list[$key]['EP']    = '';
                    $list[$key]['e_stime'] = '';
                    $list[$key]['e_etime'] = '';
                    $list[$key]['e_ltime'] = '';
                    //
                    $list[$key]['F01']     = '';
                    $list[$key]['F02']     = '';
                    $list[$key]['F03']     = '';
                    $list[$key]['F04']     = '';
                    $list[$key]['F05']     = '';
                    $list[$key]['FP']      = '';
                    $list[$key]['f_stime'] = '';
                    $list[$key]['f_etime'] = '';
                    $list[$key]['f_ltime'] = '';
                    //
                    $list[$key]['G01']     = '';
                    $list[$key]['G02']     = '';
                    $list[$key]['G03']     = '';
                    $list[$key]['G04']     = '';
                    $list[$key]['G05']     = '';
                    $list[$key]['G06']     = '';
                    $list[$key]['GP']      = '';
                    $list[$key]['g_stime'] = '';
                    $list[$key]['g_etime'] = '';
                    $list[$key]['g_ltime'] = '';
                    //
                    $list[$key]['H01']     = '';
                    $list[$key]['H02']     = '';
                    $list[$key]['H03']     = '';
                    $list[$key]['H04']     = '';
                    $list[$key]['H05']     = '';
                    $list[$key]['H06']     = '';
                    $list[$key]['H07']     = '';
                    $list[$key]['H08']     = '';
                    $list[$key]['H09']     = '';
                    $list[$key]['H10']     = '';
                    $list[$key]['H11']     = '';
                    $list[$key]['H12']     = '';
                    $list[$key]['H13']     = '';
                    $list[$key]['H14']     = '';
                    $list[$key]['H15']     = '';
                    $list[$key]['H16']     = '';
                    $list[$key]['H17']     = '';
                    $list[$key]['H18']     = '';
                    $list[$key]['H19']     = '';
                    $list[$key]['H20']     = '';
                    $list[$key]['H21']     = '';
                    $list[$key]['H22']     = '';
                    $list[$key]['H23']     = '';
                    $list[$key]['H24']     = '';
                    $list[$key]['H25']     = '';
                    $list[$key]['H26']     = '';
                    $list[$key]['H27']     = '';
                    $list[$key]['H28']     = '';
                    $list[$key]['HP']      = '';
                    $list[$key]['h_stime'] = '';
                    $list[$key]['h_etime'] = '';
                    $list[$key]['h_ltime'] = '';
                    //
                    $list[$key]['I01']     = '';
                    $list[$key]['I02']     = '';
                    $list[$key]['I03']     = '';
                    $list[$key]['I04']     = '';
                    $list[$key]['I05']     = '';
                    $list[$key]['I06']     = '';
                    $list[$key]['I07']     = '';
                    $list[$key]['I08']     = '';
                    $list[$key]['I09']     = '';
                    $list[$key]['I10']     = '';
                    $list[$key]['I11']     = '';
                    $list[$key]['I12']     = '';
                    $list[$key]['I13']     = '';
                    $list[$key]['I14']     = '';
                    $list[$key]['I15']     = '';
                    $list[$key]['I16']     = '';
                    $list[$key]['I17']     = '';
                    $list[$key]['I18']     = '';
                    $list[$key]['I19']     = '';
                    $list[$key]['I20']     = '';
                    $list[$key]['I21']     = '';
                    $list[$key]['I22']     = '';
                    $list[$key]['I23']     = '';
                    $list[$key]['I24']     = '';
                    $list[$key]['I25']     = '';
                    $list[$key]['IP']      = '';
                    $list[$key]['tough']      = '';
                    $list[$key]['power']      = '';
                    $list[$key]['optimistic']      = '';
                    $list[$key]['i_stime'] = '';
                    $list[$key]['i_etime'] = '';
                    $list[$key]['i_ltime'] = '';
                    //
                    $list[$key]['J01']     = '';
                    $list[$key]['J02']     = '';
                    $list[$key]['J03']     = '';
                    $list[$key]['J04']     = '';
                    $list[$key]['J05']     = '';
                    $list[$key]['J06']     = '';
                    $list[$key]['J07']     = '';
                    $list[$key]['J08']     = '';
                    $list[$key]['J09']     = '';
                    $list[$key]['J10']     = '';
                    $list[$key]['J11']     = '';
                    $list[$key]['J12']     = '';
                    $list[$key]['J13']     = '';
                    $list[$key]['J14']     = '';
                    $list[$key]['J15']     = '';
                    $list[$key]['J16']     = '';
                    $list[$key]['J17']     = '';
                    $list[$key]['J18']     = '';
                    $list[$key]['J19']     = '';
                    $list[$key]['J20']     = '';
                    $list[$key]['J21']     = '';
                    $list[$key]['J22']     = '';
                    $list[$key]['J23']     = '';
                    $list[$key]['J24']     = '';
                    $list[$key]['J25']     = '';
                    $list[$key]['J26']     = '';
                    $list[$key]['J27']     = '';
                    $list[$key]['J28']     = '';
                    $list[$key]['J29']     = '';
                    $list[$key]['J30']     = '';
                    $list[$key]['JP']      = '';
                    $list[$key]['individual']      = '';
                    $list[$key]['negative']      = '';
                    $list[$key]['self_confidence']      = '';
                    $list[$key]['Helpless']      = '';
                    $list[$key]['j_stime'] = '';
                    $list[$key]['j_etime'] = '';
                    $list[$key]['j_ltime'] = '';
                }
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_depression_info b', 'a.open_id = b.openid')
                ->join('cm_anxiety_info c', 'b.id = c.dep_id', 'left')
                ->field('a.open_id')
                ->count();
            $page_total = ceil($total / $limit);

            $return = [
                'list' => $list,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //导出课前问卷
    public function excel_course_before()
    {
        $where[] = ['a.id', '>', 0];;
        $search = [];
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
            $search[] = ['course', 'in', $course];
        }
        $CP = input('post.CP/a', array());
        if ($CP && !in_array('10', $CP)) {
            $cp_arr = [];
            if (in_array('1', $CP)) {
                $cp_arr[] = 0;
                $cp_arr[] = 1;
                $cp_arr[] = 2;
                $cp_arr[] = 3;
                $cp_arr[] = 4;
            }
            if (in_array('2', $CP)) {
                $cp_arr[] = 5;
                $cp_arr[] = 6;
                $cp_arr[] = 7;
                $cp_arr[] = 8;
                $cp_arr[] = 9;
            }
            if (in_array('3', $CP)) {
                $cp_arr[] = 10;
                $cp_arr[] = 11;
                $cp_arr[] = 12;
                $cp_arr[] = 13;
                $cp_arr[] = 14;
            }
            if (in_array('4', $CP)) {
                $cp_arr[] = 15;
                $cp_arr[] = 16;
                $cp_arr[] = 17;
                $cp_arr[] = 18;
                $cp_arr[] = 19;
            }
            if (in_array('5', $CP)) {
                $cp_arr[] = 20;
                $cp_arr[] = 21;
                $cp_arr[] = 22;
                $cp_arr[] = 23;
                $cp_arr[] = 24;
                $cp_arr[] = 25;
                $cp_arr[] = 26;
                $cp_arr[] = 27;
            }
            $where[] = ['b.CP', 'in', $cp_arr];
        }
        $DP = input('post.DP/a', array());
        if ($DP && !in_array('10', $DP)) {
            $dp_arr = [];
            if (in_array('1', $DP)) {
                $dp_arr[] = 0;
                $dp_arr[] = 1;
                $dp_arr[] = 2;
                $dp_arr[] = 3;
                $dp_arr[] = 4;
            }
            if (in_array('2', $DP)) {
                $dp_arr[] = 5;
                $dp_arr[] = 6;
                $dp_arr[] = 7;
                $dp_arr[] = 8;
                $dp_arr[] = 9;
            }
            if (in_array('3', $DP)) {
                $dp_arr[] = 10;
                $dp_arr[] = 11;
                $dp_arr[] = 12;
                $dp_arr[] = 13;
                $dp_arr[] = 14;
            }
            if (in_array('4', $DP)) {
                $dp_arr[] = 15;
                $dp_arr[] = 16;
                $dp_arr[] = 17;
                $dp_arr[] = 18;
                $dp_arr[] = 19;
                $dp_arr[] = 20;
                $dp_arr[] = 21;
            }
            $where[] = ['c.DP', 'in', $dp_arr];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_depression_info b', 'a.open_id = b.openid')
            ->join('cm_anxiety_info c', 'b.id = c.dep_id', 'left')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.C01,b.C02,b.C03,b.C04,b.C05,b.C06,b.C07,b.C08,b.C09,b.CP,b.stime as b_stime,b.etime as b_etime,b.ltime as b_ltime,b.state,c.D01,c.D02,c.D03,c.D04,c.D05,c.D06,c.D07,c.DP,c.stime as c_stime,c.etime as c_etime,c.time as c_ltime')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['b_stime'] = date('Y-m-d H:i', $value['b_stime']);
            $list[$key]['b_etime'] = date('Y-m-d H:i', $value['b_etime']);
            $list[$key]['c_stime'] = date('Y-m-d H:i', $value['c_stime']);
            $list[$key]['c_etime'] = date('Y-m-d H:i', $value['c_etime']);
            $list[$key]['course']  = 'S' . $value['course'];
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list[$key]['type_name'] = 'N-普通人群';
            } else {
                $list[$key]['type_name'] = '游客';
            }
            $search['openid'] = $value['open_id'];
            //判断是否是第一次该课程第一次填写
            if ($value['state'] == '1') { //该课程第一次填写
                //查询失眠测评
                $insomnia = Db::name('insomnia_info')->where($search)->field('E01a,E01b,E01c,E02,E03,E04,E05,EP,stime,etime,ltime')->find();
                if ($insomnia) {
                    $list[$key]['E01a']  = $insomnia['E01a'];
                    $list[$key]['E01b']  = $insomnia['E01b'];
                    $list[$key]['E01c']  = $insomnia['E01c'];
                    $list[$key]['E02']   = $insomnia['E02'];
                    $list[$key]['E03']   = $insomnia['E03'];
                    $list[$key]['E04']   = $insomnia['E04'];
                    $list[$key]['E05']   = $insomnia['E05'];
                    $list[$key]['EP']    = $insomnia['EP'];
                    $list[$key]['e_stime'] = date('Y-m-d H:i', $insomnia['stime']);
                    $list[$key]['e_etime'] = date('Y-m-d H:i', $insomnia['etime']);
                    $list[$key]['e_ltime'] = $insomnia['ltime'];
                } else {
                    $list[$key]['E01a']  = '';
                    $list[$key]['E01b']  = '';
                    $list[$key]['E01c']  = '';
                    $list[$key]['E02']   = '';
                    $list[$key]['E03']   = '';
                    $list[$key]['E04']   = '';
                    $list[$key]['E05']   = '';
                    $list[$key]['EP']    = '';
                    $list[$key]['e_stime'] = '';
                    $list[$key]['e_etime'] = '';
                    $list[$key]['e_ltime'] = '';
                }
                //查询WHO-5生活质量测评记录
                $life_quality = Db::name('life_quality_info')->where($search)->field('F01,F02,F03,F04,F05,FP,stime,etime,ltime')->find();
                if ($life_quality) {
                    $list[$key]['F01']     = $life_quality['F01'];
                    $list[$key]['F02']     = $life_quality['F02'];
                    $list[$key]['F03']     = $life_quality['F03'];
                    $list[$key]['F04']     = $life_quality['F04'];
                    $list[$key]['F05']     = $life_quality['F05'];
                    $list[$key]['FP']      = $life_quality['FP'];
                    $list[$key]['f_stime'] = date('Y-m-d H:i', $life_quality['stime']);
                    $list[$key]['f_etime'] = date('Y-m-d H:i', $life_quality['etime']);
                    $list[$key]['f_ltime'] = $life_quality['ltime'];
                } else {
                    $list[$key]['F01']     = '';
                    $list[$key]['F02']     = '';
                    $list[$key]['F03']     = '';
                    $list[$key]['F04']     = '';
                    $list[$key]['F05']     = '';
                    $list[$key]['FP']      = '';
                    $list[$key]['f_stime'] = '';
                    $list[$key]['f_etime'] = '';
                    $list[$key]['f_ltime'] = '';
                }
                //查询失眠测评
                $depressive_effects = Db::name('depressive_effects')->where($search)->field('G01,G02,G03,G04,G05,G06,GP,stime,etime,ltime')->find();
                if ($depressive_effects) {
                    $list[$key]['G01']     = $depressive_effects['G01'];
                    $list[$key]['G02']     = $depressive_effects['G02'];
                    $list[$key]['G03']     = $depressive_effects['G03'];
                    $list[$key]['G04']     = $depressive_effects['G04'];
                    $list[$key]['G05']     = $depressive_effects['G05'];
                    $list[$key]['G06']     = $depressive_effects['G06'];
                    $list[$key]['GP']      = $depressive_effects['GP'];
                    $list[$key]['g_stime'] = date('Y-m-d H:i', $depressive_effects['stime']);
                    $list[$key]['g_etime'] = date('Y-m-d H:i', $depressive_effects['etime']);
                    $list[$key]['g_ltime'] = $depressive_effects['ltime'];
                } else {
                    $list[$key]['G01']     = '';
                    $list[$key]['G02']     = '';
                    $list[$key]['G03']     = '';
                    $list[$key]['G04']     = '';
                    $list[$key]['G05']     = '';
                    $list[$key]['G06']     = '';
                    $list[$key]['GP']      = '';
                    $list[$key]['g_stime'] = '';
                    $list[$key]['g_etime'] = '';
                    $list[$key]['g_ltime'] = '';
                }
                //查询失眠测评
                $somatic_symptoms = Db::name('somatic_symptoms')->where($search)->withoutField(['id', 'openid', 'course'])->find();
                if ($somatic_symptoms) {
                    $list[$key]['H01']     = $somatic_symptoms['H01'];
                    $list[$key]['H02']     = $somatic_symptoms['H02'];
                    $list[$key]['H03']     = $somatic_symptoms['H03'];
                    $list[$key]['H04']     = $somatic_symptoms['H04'];
                    $list[$key]['H05']     = $somatic_symptoms['H05'];
                    $list[$key]['H06']     = $somatic_symptoms['H06'];
                    $list[$key]['H07']     = $somatic_symptoms['H07'];
                    $list[$key]['H08']     = $somatic_symptoms['H08'];
                    $list[$key]['H09']     = $somatic_symptoms['H09'];
                    $list[$key]['H10']     = $somatic_symptoms['H10'];
                    $list[$key]['H11']     = $somatic_symptoms['H11'];
                    $list[$key]['H12']     = $somatic_symptoms['H12'];
                    $list[$key]['H13']     = $somatic_symptoms['H13'];
                    $list[$key]['H14']     = $somatic_symptoms['H14'];
                    $list[$key]['H15']     = $somatic_symptoms['H15'];
                    $list[$key]['H16']     = $somatic_symptoms['H16'];
                    $list[$key]['H17']     = $somatic_symptoms['H17'];
                    $list[$key]['H18']     = $somatic_symptoms['H18'];
                    $list[$key]['H19']     = $somatic_symptoms['H19'];
                    $list[$key]['H20']     = $somatic_symptoms['H20'];
                    $list[$key]['H21']     = $somatic_symptoms['H21'];
                    $list[$key]['H22']     = $somatic_symptoms['H22'];
                    $list[$key]['H23']     = $somatic_symptoms['H23'];
                    $list[$key]['H24']     = $somatic_symptoms['H24'];
                    $list[$key]['H25']     = $somatic_symptoms['H25'];
                    $list[$key]['H26']     = $somatic_symptoms['H26'];
                    $list[$key]['H27']     = $somatic_symptoms['H27'];
                    $list[$key]['H28']     = $somatic_symptoms['H28'];
                    $list[$key]['HP']      = $somatic_symptoms['HP'];
                    $list[$key]['h_stime'] = date('Y-m-d H:i', $somatic_symptoms['stime']);
                    $list[$key]['h_etime'] = date('Y-m-d H:i', $somatic_symptoms['etime']);
                    $list[$key]['h_ltime'] = $somatic_symptoms['ltime'];
                } else {
                    $list[$key]['H01']     = '';
                    $list[$key]['H02']     = '';
                    $list[$key]['H03']     = '';
                    $list[$key]['H04']     = '';
                    $list[$key]['H05']     = '';
                    $list[$key]['H06']     = '';
                    $list[$key]['H07']     = '';
                    $list[$key]['H08']     = '';
                    $list[$key]['H09']     = '';
                    $list[$key]['H10']     = '';
                    $list[$key]['H11']     = '';
                    $list[$key]['H12']     = '';
                    $list[$key]['H13']     = '';
                    $list[$key]['H14']     = '';
                    $list[$key]['H15']     = '';
                    $list[$key]['H16']     = '';
                    $list[$key]['H17']     = '';
                    $list[$key]['H18']     = '';
                    $list[$key]['H19']     = '';
                    $list[$key]['H20']     = '';
                    $list[$key]['H21']     = '';
                    $list[$key]['H22']     = '';
                    $list[$key]['H23']     = '';
                    $list[$key]['H24']     = '';
                    $list[$key]['H25']     = '';
                    $list[$key]['H26']     = '';
                    $list[$key]['H27']     = '';
                    $list[$key]['H28']     = '';
                    $list[$key]['HP']      = '';
                    $list[$key]['h_stime'] = '';
                    $list[$key]['h_etime'] = '';
                    $list[$key]['h_ltime'] = '';
                }

                //查心理弹性量
                $psychological_elastic = Db::name('psychological_elastic')->where('open_id', $value['open_id'])->withoutField('id,open_id,course')->find();
                if ($psychological_elastic) {
                    $list[$key]['I01']     = $psychological_elastic['I01'];
                    $list[$key]['I02']     = $psychological_elastic['I02'];
                    $list[$key]['I03']     = $psychological_elastic['I03'];
                    $list[$key]['I04']     = $psychological_elastic['I04'];
                    $list[$key]['I05']     = $psychological_elastic['I05'];
                    $list[$key]['I06']     = $psychological_elastic['I06'];
                    $list[$key]['I07']     = $psychological_elastic['I07'];
                    $list[$key]['I08']     = $psychological_elastic['I08'];
                    $list[$key]['I09']     = $psychological_elastic['I09'];
                    $list[$key]['I10']     = $psychological_elastic['I10'];
                    $list[$key]['I11']     = $psychological_elastic['I11'];
                    $list[$key]['I12']     = $psychological_elastic['I12'];
                    $list[$key]['I13']     = $psychological_elastic['I13'];
                    $list[$key]['I14']     = $psychological_elastic['I14'];
                    $list[$key]['I15']     = $psychological_elastic['I15'];
                    $list[$key]['I16']     = $psychological_elastic['I16'];
                    $list[$key]['I17']     = $psychological_elastic['I17'];
                    $list[$key]['I18']     = $psychological_elastic['I18'];
                    $list[$key]['I19']     = $psychological_elastic['I19'];
                    $list[$key]['I20']     = $psychological_elastic['I20'];
                    $list[$key]['I21']     = $psychological_elastic['I21'];
                    $list[$key]['I22']     = $psychological_elastic['I22'];
                    $list[$key]['I23']     = $psychological_elastic['I23'];
                    $list[$key]['I24']     = $psychological_elastic['I24'];
                    $list[$key]['I25']     = $psychological_elastic['I25'];
                    $list[$key]['IP']      = $psychological_elastic['IP'];
                    $list[$key]['tough']      = $psychological_elastic['tough'];
                    $list[$key]['power']      = $psychological_elastic['power'];
                    $list[$key]['optimistic']      = $psychological_elastic['optimistic'];
                    $list[$key]['i_stime'] = date('Y-m-d H:i', $psychological_elastic['stime']);
                    $list[$key]['i_etime'] = date('Y-m-d H:i', $psychological_elastic['etime']);
                    $list[$key]['i_ltime'] = $psychological_elastic['ltime'];
                } else {
                    $list[$key]['I01']     = '';
                    $list[$key]['I02']     = '';
                    $list[$key]['I03']     = '';
                    $list[$key]['I04']     = '';
                    $list[$key]['I05']     = '';
                    $list[$key]['I06']     = '';
                    $list[$key]['I07']     = '';
                    $list[$key]['I08']     = '';
                    $list[$key]['I09']     = '';
                    $list[$key]['I10']     = '';
                    $list[$key]['I11']     = '';
                    $list[$key]['I12']     = '';
                    $list[$key]['I13']     = '';
                    $list[$key]['I14']     = '';
                    $list[$key]['I15']     = '';
                    $list[$key]['I16']     = '';
                    $list[$key]['I17']     = '';
                    $list[$key]['I18']     = '';
                    $list[$key]['I19']     = '';
                    $list[$key]['I20']     = '';
                    $list[$key]['I21']     = '';
                    $list[$key]['I22']     = '';
                    $list[$key]['I23']     = '';
                    $list[$key]['I24']     = '';
                    $list[$key]['I25']     = '';
                    $list[$key]['IP']      = '';
                    $list[$key]['tough']      = '';
                    $list[$key]['power']      = '';
                    $list[$key]['optimistic']      = '';
                    $list[$key]['i_stime'] = '';
                    $list[$key]['i_etime'] = '';
                    $list[$key]['i_ltime'] = '';
                }
                //抑郁思维模式评估
                $thinking_pattern = Db::name('thinking_pattern')->where('open_id', $value['open_id'])->withoutField('id,open_id,course')->find();
                if ($thinking_pattern) {
                    $list[$key]['J01']     = $thinking_pattern['J01'];
                    $list[$key]['J02']     = $thinking_pattern['J02'];
                    $list[$key]['J03']     = $thinking_pattern['J03'];
                    $list[$key]['J04']     = $thinking_pattern['J04'];
                    $list[$key]['J05']     = $thinking_pattern['J05'];
                    $list[$key]['J06']     = $thinking_pattern['J06'];
                    $list[$key]['J07']     = $thinking_pattern['J07'];
                    $list[$key]['J08']     = $thinking_pattern['J08'];
                    $list[$key]['J09']     = $thinking_pattern['J09'];
                    $list[$key]['J10']     = $thinking_pattern['J10'];
                    $list[$key]['J11']     = $thinking_pattern['J11'];
                    $list[$key]['J12']     = $thinking_pattern['J12'];
                    $list[$key]['J13']     = $thinking_pattern['J13'];
                    $list[$key]['J14']     = $thinking_pattern['J14'];
                    $list[$key]['J15']     = $thinking_pattern['J15'];
                    $list[$key]['J16']     = $thinking_pattern['J16'];
                    $list[$key]['J17']     = $thinking_pattern['J17'];
                    $list[$key]['J18']     = $thinking_pattern['J18'];
                    $list[$key]['J19']     = $thinking_pattern['J19'];
                    $list[$key]['J20']     = $thinking_pattern['J20'];
                    $list[$key]['J21']     = $thinking_pattern['J21'];
                    $list[$key]['J22']     = $thinking_pattern['J22'];
                    $list[$key]['J23']     = $thinking_pattern['J23'];
                    $list[$key]['J24']     = $thinking_pattern['J24'];
                    $list[$key]['J25']     = $thinking_pattern['J25'];
                    $list[$key]['J26']     = $thinking_pattern['J26'];
                    $list[$key]['J27']     = $thinking_pattern['J27'];
                    $list[$key]['J28']     = $thinking_pattern['J28'];
                    $list[$key]['J29']     = $thinking_pattern['J29'];
                    $list[$key]['J30']     = $thinking_pattern['J30'];
                    $list[$key]['JP']      = $thinking_pattern['JP'];
                    $list[$key]['individual']      = $thinking_pattern['individual'];
                    $list[$key]['negative']      = $thinking_pattern['negative'];
                    $list[$key]['self_confidence']      = $thinking_pattern['self_confidence'];
                    $list[$key]['Helpless']      = $thinking_pattern['Helpless'];
                    $list[$key]['j_stime'] = date('Y-m-d H:i', $thinking_pattern['stime']);
                    $list[$key]['j_etime'] = date('Y-m-d H:i', $thinking_pattern['etime']);
                    $list[$key]['j_ltime'] = $thinking_pattern['ltime'];
                } else {
                    $list[$key]['I01']     = '';
                    $list[$key]['J02']     = '';
                    $list[$key]['J03']     = '';
                    $list[$key]['J04']     = '';
                    $list[$key]['J05']     = '';
                    $list[$key]['J06']     = '';
                    $list[$key]['J07']     = '';
                    $list[$key]['J08']     = '';
                    $list[$key]['J09']     = '';
                    $list[$key]['J10']     = '';
                    $list[$key]['J11']     = '';
                    $list[$key]['J12']     = '';
                    $list[$key]['J13']     = '';
                    $list[$key]['J14']     = '';
                    $list[$key]['J15']     = '';
                    $list[$key]['J16']     = '';
                    $list[$key]['J17']     = '';
                    $list[$key]['J18']     = '';
                    $list[$key]['J19']     = '';
                    $list[$key]['J20']     = '';
                    $list[$key]['J21']     = '';
                    $list[$key]['J22']     = '';
                    $list[$key]['J23']     = '';
                    $list[$key]['J24']     = '';
                    $list[$key]['J25']     = '';
                    $list[$key]['J26']     = '';
                    $list[$key]['J27']     = '';
                    $list[$key]['J28']     = '';
                    $list[$key]['J29']     = '';
                    $list[$key]['J30']     = '';
                    $list[$key]['JP']      = '';
                    $list[$key]['individual']      = '';
                    $list[$key]['negative']      = '';
                    $list[$key]['self_confidence']      = '';
                    $list[$key]['Helpless']      = '';
                    $list[$key]['j_stime'] = '';
                    $list[$key]['j_etime'] = '';
                    $list[$key]['j_ltime'] = '';
                }
            } else {
                $list[$key]['E01a']  = '';
                $list[$key]['E01b']  = '';
                $list[$key]['E01c']  = '';
                $list[$key]['E02']   = '';
                $list[$key]['E03']   = '';
                $list[$key]['E04']   = '';
                $list[$key]['E05']   = '';
                $list[$key]['EP']    = '';
                $list[$key]['e_stime'] = '';
                $list[$key]['e_etime'] = '';
                $list[$key]['e_ltime'] = '';
                //
                $list[$key]['F01']     = '';
                $list[$key]['F02']     = '';
                $list[$key]['F03']     = '';
                $list[$key]['F04']     = '';
                $list[$key]['F05']     = '';
                $list[$key]['FP']      = '';
                $list[$key]['f_stime'] = '';
                $list[$key]['f_etime'] = '';
                $list[$key]['f_ltime'] = '';
                //
                $list[$key]['G01']     = '';
                $list[$key]['G02']     = '';
                $list[$key]['G03']     = '';
                $list[$key]['G04']     = '';
                $list[$key]['G05']     = '';
                $list[$key]['G06']     = '';
                $list[$key]['GP']      = '';
                $list[$key]['g_stime'] = '';
                $list[$key]['g_etime'] = '';
                $list[$key]['g_ltime'] = '';
                //
                $list[$key]['H01']     = '';
                $list[$key]['H02']     = '';
                $list[$key]['H03']     = '';
                $list[$key]['H04']     = '';
                $list[$key]['H05']     = '';
                $list[$key]['H06']     = '';
                $list[$key]['H07']     = '';
                $list[$key]['H08']     = '';
                $list[$key]['H09']     = '';
                $list[$key]['H10']     = '';
                $list[$key]['H11']     = '';
                $list[$key]['H12']     = '';
                $list[$key]['H13']     = '';
                $list[$key]['H14']     = '';
                $list[$key]['H15']     = '';
                $list[$key]['H16']     = '';
                $list[$key]['H17']     = '';
                $list[$key]['H18']     = '';
                $list[$key]['H19']     = '';
                $list[$key]['H20']     = '';
                $list[$key]['H21']     = '';
                $list[$key]['H22']     = '';
                $list[$key]['H23']     = '';
                $list[$key]['H24']     = '';
                $list[$key]['H25']     = '';
                $list[$key]['H26']     = '';
                $list[$key]['H27']     = '';
                $list[$key]['H28']     = '';
                $list[$key]['HP']      = '';
                $list[$key]['h_stime'] = '';
                $list[$key]['h_etime'] = '';
                $list[$key]['h_ltime'] = '';
                //
                $list[$key]['I01']     = '';
                $list[$key]['I02']     = '';
                $list[$key]['I03']     = '';
                $list[$key]['I04']     = '';
                $list[$key]['I05']     = '';
                $list[$key]['I06']     = '';
                $list[$key]['I07']     = '';
                $list[$key]['I08']     = '';
                $list[$key]['I09']     = '';
                $list[$key]['I10']     = '';
                $list[$key]['I11']     = '';
                $list[$key]['I12']     = '';
                $list[$key]['I13']     = '';
                $list[$key]['I14']     = '';
                $list[$key]['I15']     = '';
                $list[$key]['I16']     = '';
                $list[$key]['I17']     = '';
                $list[$key]['I18']     = '';
                $list[$key]['I19']     = '';
                $list[$key]['I20']     = '';
                $list[$key]['I21']     = '';
                $list[$key]['I22']     = '';
                $list[$key]['I23']     = '';
                $list[$key]['I24']     = '';
                $list[$key]['I25']     = '';
                $list[$key]['IP']      = '';
                $list[$key]['tough']      = '';
                $list[$key]['power']      = '';
                $list[$key]['optimistic']      = '';
                $list[$key]['i_stime'] = '';
                $list[$key]['i_etime'] = '';
                $list[$key]['i_ltime'] = '';
                //
                $list[$key]['J01']     = '';
                $list[$key]['J02']     = '';
                $list[$key]['J03']     = '';
                $list[$key]['J04']     = '';
                $list[$key]['J05']     = '';
                $list[$key]['J06']     = '';
                $list[$key]['J07']     = '';
                $list[$key]['J08']     = '';
                $list[$key]['J09']     = '';
                $list[$key]['J10']     = '';
                $list[$key]['J11']     = '';
                $list[$key]['J12']     = '';
                $list[$key]['J13']     = '';
                $list[$key]['J14']     = '';
                $list[$key]['J15']     = '';
                $list[$key]['J16']     = '';
                $list[$key]['J17']     = '';
                $list[$key]['J18']     = '';
                $list[$key]['J19']     = '';
                $list[$key]['J20']     = '';
                $list[$key]['J21']     = '';
                $list[$key]['J22']     = '';
                $list[$key]['J23']     = '';
                $list[$key]['J24']     = '';
                $list[$key]['J25']     = '';
                $list[$key]['J26']     = '';
                $list[$key]['J27']     = '';
                $list[$key]['J28']     = '';
                $list[$key]['J29']     = '';
                $list[$key]['J30']     = '';
                $list[$key]['JP']      = '';
                $list[$key]['individual']      = '';
                $list[$key]['negative']      = '';
                $list[$key]['self_confidence']      = '';
                $list[$key]['Helpless']      = '';
                $list[$key]['j_stime'] = '';
                $list[$key]['j_etime'] = '';
                $list[$key]['j_ltime'] = '';
            }
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '用户分类', '课程编号', 'C01', 'C02', 'C03', 'C04', 'C05', 'C06', 'C07', 'C08', 'C09', 'CP', 'C开始时间', 'C结束时间', 'C填写时间', 'D01', 'D02', 'D03', 'D04', 'D05', 'D开始时间', 'D结束时间', 'D填写时长', 'E01a', 'E01b', 'E01c', 'E02', 'E03', 'E04', 'E05', 'EP', 'E开始时间', 'E结束时间', 'E填写时长', 'F01', 'F02', 'F03', 'F04', 'F05', 'FP', 'F开始时间', 'F结束时间', 'F填写时长', 'G01', 'G02', 'G03', 'G04', 'G05', 'G06', 'GP', 'G开始时间', 'G结束时间', 'G填写时长', 'H01', 'H02', 'H03', 'H04', 'H05', 'H06', 'H07', 'H08', 'H09', 'H10', 'H11', 'H12', 'H13', 'H14', 'H15', 'H16', 'H17', 'H18', 'H19', 'H20', 'H21', 'H22', 'H23', 'H24', 'H25', 'H26', 'H27', 'H28', 'HP', 'H开始时间', 'H结束时间', 'G填写时长', 'I01', 'I02', 'I03', 'I04', 'I05', 'I06', 'I07', 'I08', 'I09', 'I10', 'I11', 'I12', 'I13', 'I14', 'I15', 'I16', 'I17', 'I18', 'I19', 'I20', 'I21', 'I22', 'I23', 'I24', 'I25', 'IP', 'tough', 'power', 'optimistic', 'I开始时间', 'I结束时间', 'I填写时长', 'J01', 'J02', 'J03', 'J04', 'J05', 'J06', 'J07', 'J08', 'J09', 'J10', 'J11', 'J12', 'J13', 'J14', 'J15', 'J16', 'J17', 'J18', 'J19', 'J20', 'J21', 'J22', 'J23', 'J24', 'J25', 'J26', 'J27', 'J28', 'J29', 'J30', 'JP', 'individual', 'negative', 'self_confidence', 'Helpless', 'J开始时间', 'J结束时间', 'G填写时长');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(22);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(16);
        $PHPSheet->getColumnDimension('I')->setWidth(12);
        $PHPSheet->getColumnDimension('J')->setWidth(12);
        $PHPSheet->getColumnDimension('K')->setWidth(12);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(15);
        $PHPSheet->getColumnDimension('O')->setWidth(15);
        $PHPSheet->getColumnDimension('P')->setWidth(15);
        $PHPSheet->getColumnDimension('Q')->setWidth(13);
        $PHPSheet->getColumnDimension('R')->setWidth(13);
        $PHPSheet->getColumnDimension('S')->setWidth(13);
        $PHPSheet->getColumnDimension('T')->setWidth(13);
        $PHPSheet->getColumnDimension('U')->setWidth(13);
        $PHPSheet->getColumnDimension('V')->setWidth(13);
        $PHPSheet->getColumnDimension('W')->setWidth(13);
        $PHPSheet->getColumnDimension('X')->setWidth(15);
        $PHPSheet->getColumnDimension('Y')->setWidth(13);
        $PHPSheet->getColumnDimension('Z')->setWidth(13);
        $PHPSheet->getColumnDimension('AA')->setWidth(20);
        $PHPSheet->getColumnDimension('AB')->setWidth(20);
        $PHPSheet->getColumnDimension('AC')->setWidth(20);
        $PHPSheet->getColumnDimension('AD')->setWidth(13);
        $PHPSheet->getColumnDimension('AE')->setWidth(13);
        $PHPSheet->getColumnDimension('AF')->setWidth(15);
        $PHPSheet->getColumnDimension('AG')->setWidth(15);
        $PHPSheet->getColumnDimension('AH')->setWidth(13);
        $PHPSheet->getColumnDimension('AI')->setWidth(13);
        $PHPSheet->getColumnDimension('AJ')->setWidth(13);
        $PHPSheet->getColumnDimension('AK')->setWidth(15);
        $PHPSheet->getColumnDimension('AL')->setWidth(15);
        $PHPSheet->getColumnDimension('AM')->setWidth(13);
        $PHPSheet->getColumnDimension('AN')->setWidth(13);
        $PHPSheet->getColumnDimension('AO')->setWidth(13);
        $PHPSheet->getColumnDimension('AP')->setWidth(15);
        $PHPSheet->getColumnDimension('AQ')->setWidth(15);
        $PHPSheet->getColumnDimension('AR')->setWidth(13);
        $PHPSheet->getColumnDimension('AS')->setWidth(13);
        $PHPSheet->getColumnDimension('AT')->setWidth(13);
        $PHPSheet->getColumnDimension('AU')->setWidth(15);
        $PHPSheet->getColumnDimension('AV')->setWidth(15);
        $PHPSheet->getColumnDimension('AW')->setWidth(13);
        $PHPSheet->getColumnDimension('AX')->setWidth(13);
        $PHPSheet->getColumnDimension('AY')->setWidth(13);
        $PHPSheet->getColumnDimension('AZ')->setWidth(13);
        $PHPSheet->getColumnDimension('BA')->setWidth(15);
        $PHPSheet->getColumnDimension('BB')->setWidth(15);
        $PHPSheet->getColumnDimension('BC')->setWidth(13);
        $PHPSheet->getColumnDimension('BD')->setWidth(20);
        $PHPSheet->getColumnDimension('BE')->setWidth(20);
        $PHPSheet->getColumnDimension('BF')->setWidth(20);
        $PHPSheet->getColumnDimension('BG')->setWidth(15);
        $PHPSheet->getColumnDimension('BH')->setWidth(16);
        $PHPSheet->getColumnDimension('BI')->setWidth(12);
        $PHPSheet->getColumnDimension('BJ')->setWidth(12);
        $PHPSheet->getColumnDimension('BK')->setWidth(12);
        $PHPSheet->getColumnDimension('BL')->setWidth(12);
        $PHPSheet->getColumnDimension('BM')->setWidth(15);
        $PHPSheet->getColumnDimension('BN')->setWidth(15);
        $PHPSheet->getColumnDimension('BO')->setWidth(15);
        $PHPSheet->getColumnDimension('BP')->setWidth(15);
        $PHPSheet->getColumnDimension('BQ')->setWidth(13);
        $PHPSheet->getColumnDimension('BR')->setWidth(13);
        $PHPSheet->getColumnDimension('BS')->setWidth(13);
        $PHPSheet->getColumnDimension('BT')->setWidth(13);
        $PHPSheet->getColumnDimension('BU')->setWidth(13);
        $PHPSheet->getColumnDimension('BV')->setWidth(13);
        $PHPSheet->getColumnDimension('BW')->setWidth(13);
        $PHPSheet->getColumnDimension('BX')->setWidth(15);
        $PHPSheet->getColumnDimension('BY')->setWidth(13);
        $PHPSheet->getColumnDimension('BZ')->setWidth(13);
        $PHPSheet->getColumnDimension('CA')->setWidth(7);
        $PHPSheet->getColumnDimension('CB')->setWidth(20);
        $PHPSheet->getColumnDimension('CC')->setWidth(22);
        $PHPSheet->getColumnDimension('CD')->setWidth(15);
        $PHPSheet->getColumnDimension('CE')->setWidth(17);
        $PHPSheet->getColumnDimension('CF')->setWidth(12);
        $PHPSheet->getColumnDimension('CG')->setWidth(15);
        $PHPSheet->getColumnDimension('CH')->setWidth(16);
        $PHPSheet->getColumnDimension('CI')->setWidth(12);
        $PHPSheet->getColumnDimension('CJ')->setWidth(12);
        $PHPSheet->getColumnDimension('CK')->setWidth(12);
        $PHPSheet->getColumnDimension('CL')->setWidth(12);
        $PHPSheet->getColumnDimension('CM')->setWidth(15);
        $PHPSheet->getColumnDimension('CN')->setWidth(15);
        $PHPSheet->getColumnDimension('CO')->setWidth(15);
        $PHPSheet->getColumnDimension('CP')->setWidth(15);
        $PHPSheet->getColumnDimension('CQ')->setWidth(15);
        $PHPSheet->getColumnDimension('CR')->setWidth(15);
        $PHPSheet->getColumnDimension('CS')->setWidth(15);
        $PHPSheet->getColumnDimension('CT')->setWidth(15);
        $PHPSheet->getColumnDimension('CU')->setWidth(15);
        $PHPSheet->getColumnDimension('CV')->setWidth(15);
        $PHPSheet->getColumnDimension('CW')->setWidth(15);
        $PHPSheet->getColumnDimension('CX')->setWidth(15);
        $PHPSheet->getColumnDimension('CY')->setWidth(15);
        $PHPSheet->getColumnDimension('CZ')->setWidth(15);

        $PHPSheet->getColumnDimension('DA')->setWidth(20);
        $PHPSheet->getColumnDimension('DB')->setWidth(20);
        $PHPSheet->getColumnDimension('DC')->setWidth(20);
        $PHPSheet->getColumnDimension('DD')->setWidth(13);
        $PHPSheet->getColumnDimension('DE')->setWidth(13);
        $PHPSheet->getColumnDimension('DF')->setWidth(15);
        $PHPSheet->getColumnDimension('DG')->setWidth(15);
        $PHPSheet->getColumnDimension('DH')->setWidth(13);
        $PHPSheet->getColumnDimension('DI')->setWidth(13);
        $PHPSheet->getColumnDimension('DJ')->setWidth(13);
        $PHPSheet->getColumnDimension('DK')->setWidth(15);
        $PHPSheet->getColumnDimension('DL')->setWidth(15);
        $PHPSheet->getColumnDimension('DM')->setWidth(13);
        $PHPSheet->getColumnDimension('DN')->setWidth(13);
        $PHPSheet->getColumnDimension('DO')->setWidth(13);
        $PHPSheet->getColumnDimension('DP')->setWidth(15);
        $PHPSheet->getColumnDimension('DQ')->setWidth(15);
        $PHPSheet->getColumnDimension('DR')->setWidth(13);
        $PHPSheet->getColumnDimension('DS')->setWidth(13);
        $PHPSheet->getColumnDimension('DT')->setWidth(13);
        $PHPSheet->getColumnDimension('DU')->setWidth(15);
        $PHPSheet->getColumnDimension('DV')->setWidth(15);
        $PHPSheet->getColumnDimension('DW')->setWidth(13);
        $PHPSheet->getColumnDimension('DX')->setWidth(13);
        $PHPSheet->getColumnDimension('DY')->setWidth(13);
        $PHPSheet->getColumnDimension('DZ')->setWidth(13);

        $PHPSheet->getColumnDimension('EA')->setWidth(20);
        $PHPSheet->getColumnDimension('EB')->setWidth(20);
        $PHPSheet->getColumnDimension('EC')->setWidth(20);
        $PHPSheet->getColumnDimension('ED')->setWidth(13);
        $PHPSheet->getColumnDimension('EE')->setWidth(13);
        $PHPSheet->getColumnDimension('EF')->setWidth(15);
        $PHPSheet->getColumnDimension('EG')->setWidth(15);
        $PHPSheet->getColumnDimension('EH')->setWidth(13);
        $PHPSheet->getColumnDimension('EI')->setWidth(13);
        $PHPSheet->getColumnDimension('EJ')->setWidth(13);
        $PHPSheet->getColumnDimension('EK')->setWidth(15);
        $PHPSheet->getColumnDimension('EL')->setWidth(15);
        $PHPSheet->getColumnDimension('EM')->setWidth(13);
        $PHPSheet->getColumnDimension('EN')->setWidth(13);
        $PHPSheet->getColumnDimension('EO')->setWidth(13);
        $PHPSheet->getColumnDimension('EP')->setWidth(15);
        $PHPSheet->getColumnDimension('EQ')->setWidth(15);
        $PHPSheet->getColumnDimension('ER')->setWidth(13);
        $PHPSheet->getColumnDimension('ES')->setWidth(13);
        $PHPSheet->getColumnDimension('ET')->setWidth(13);
        $PHPSheet->getColumnDimension('EU')->setWidth(15);
        $PHPSheet->getColumnDimension('EV')->setWidth(15);
        $PHPSheet->getColumnDimension('EW')->setWidth(13);
        $PHPSheet->getColumnDimension('EX')->setWidth(13);
        $PHPSheet->getColumnDimension('EY')->setWidth(13);
        $PHPSheet->getColumnDimension('EZ')->setWidth(13);

        $PHPSheet->getColumnDimension('FA')->setWidth(20);
        $PHPSheet->getColumnDimension('FB')->setWidth(20);
        $PHPSheet->getColumnDimension('FC')->setWidth(20);
        $PHPSheet->getColumnDimension('FD')->setWidth(13);





        //数据
        foreach ($list as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                $PHPSheet->setCellValue('A' . $row, ' ' . $num);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['open_id']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['number']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['name']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['wx_phone']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['type_name']);
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['course']);
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['C01']);
                $PHPSheet->setCellValue('I' . $row, ' ' . $v['C02']);
                $PHPSheet->setCellValue('J' . $row, ' ' . $v['C03']);
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['C04']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['C05']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['C06']);
                $PHPSheet->setCellValue('N' . $row, ' ' . $v['C07']);
                $PHPSheet->setCellValue('O' . $row, ' ' . $v['C08']);
                $PHPSheet->setCellValue('P' . $row, ' ' . $v['C09']);
                $PHPSheet->setCellValue('Q' . $row, ' ' . $v['CP']);
                $PHPSheet->setCellValue('R' . $row, ' ' . $v['b_stime']);
                $PHPSheet->setCellValue('S' . $row, ' ' . $v['b_etime']);
                $PHPSheet->setCellValue('T' . $row, ' ' . $v['b_ltime']);
                $PHPSheet->setCellValue('U' . $row, ' ' . $v['D01']);
                $PHPSheet->setCellValue('V' . $row, ' ' . $v['D02']);
                $PHPSheet->setCellValue('W' . $row, ' ' . $v['D03']);
                $PHPSheet->setCellValue('X' . $row, ' ' . $v['D04']);
                $PHPSheet->setCellValue('Y' . $row, ' ' . $v['D05']);
                $PHPSheet->setCellValue('Z' . $row, ' ' . $v['D06']);
                $PHPSheet->setCellValue('AA' . $row, ' ' . $v['D07']);
                $PHPSheet->setCellValue('AB' . $row, ' ' . $v['DP']);
                $PHPSheet->setCellValue('AC' . $row, ' ' . $v['c_stime']);
                $PHPSheet->setCellValue('AD' . $row, ' ' . $v['c_etime']);
                $PHPSheet->setCellValue('AE' . $row, ' ' . $v['c_ltime']);
                $PHPSheet->setCellValue('AF' . $row, ' ' . $v['E01a']);
                $PHPSheet->setCellValue('AG' . $row, ' ' . $v['E01b']);
                $PHPSheet->setCellValue('AH' . $row, ' ' . $v['E01c']);
                $PHPSheet->setCellValue('AI' . $row, ' ' . $v['E02']);
                $PHPSheet->setCellValue('AJ' . $row, ' ' . $v['E03']);
                $PHPSheet->setCellValue('AK' . $row, ' ' . $v['E04']);
                $PHPSheet->setCellValue('AL' . $row, ' ' . $v['E05']);
                $PHPSheet->setCellValue('AM' . $row, ' ' . $v['EP']);
                $PHPSheet->setCellValue('AN' . $row, ' ' . $v['e_stime']);
                $PHPSheet->setCellValue('AO' . $row, ' ' . $v['e_etime']);
                $PHPSheet->setCellValue('AP' . $row, ' ' . $v['e_ltime']);
                $PHPSheet->setCellValue('AQ' . $row, ' ' . $v['F01']);
                $PHPSheet->setCellValue('AR' . $row, ' ' . $v['F02']);
                $PHPSheet->setCellValue('AS' . $row, ' ' . $v['F03']);
                $PHPSheet->setCellValue('AT' . $row, ' ' . $v['F04']);
                $PHPSheet->setCellValue('AU' . $row, ' ' . $v['F05']);
                $PHPSheet->setCellValue('AV' . $row, ' ' . $v['FP']);
                $PHPSheet->setCellValue('AW' . $row, ' ' . $v['f_stime']);
                $PHPSheet->setCellValue('AX' . $row, ' ' . $v['f_etime']);
                $PHPSheet->setCellValue('AY' . $row, ' ' . $v['f_ltime']);
                $PHPSheet->setCellValue('AZ' . $row, ' ' . $v['G01']);
                $PHPSheet->setCellValue('BA' . $row, ' ' . $v['G02']);
                $PHPSheet->setCellValue('BB' . $row, ' ' . $v['G03']);
                $PHPSheet->setCellValue('BC' . $row, ' ' . $v['G04']);
                $PHPSheet->setCellValue('BD' . $row, ' ' . $v['G05']);
                $PHPSheet->setCellValue('BE' . $row, ' ' . $v['G06']);
                $PHPSheet->setCellValue('BF' . $row, ' ' . $v['GP']);
                $PHPSheet->setCellValue('BG' . $row, ' ' . $v['g_stime']);
                $PHPSheet->setCellValue('BH' . $row, ' ' . $v['g_etime']);
                $PHPSheet->setCellValue('BI' . $row, ' ' . $v['g_ltime']);
                $PHPSheet->setCellValue('BJ' . $row, ' ' . $v['H01']);
                $PHPSheet->setCellValue('BK' . $row, ' ' . $v['H02']);
                $PHPSheet->setCellValue('BL' . $row, ' ' . $v['H03']);
                $PHPSheet->setCellValue('BM' . $row, ' ' . $v['H04']);
                $PHPSheet->setCellValue('BN' . $row, ' ' . $v['H05']);
                $PHPSheet->setCellValue('BO' . $row, ' ' . $v['H06']);
                $PHPSheet->setCellValue('BP' . $row, ' ' . $v['H07']);
                $PHPSheet->setCellValue('BQ' . $row, ' ' . $v['H08']);
                $PHPSheet->setCellValue('BR' . $row, ' ' . $v['H09']);
                $PHPSheet->setCellValue('BS' . $row, ' ' . $v['H10']);
                $PHPSheet->setCellValue('BT' . $row, ' ' . $v['H11']);
                $PHPSheet->setCellValue('BU' . $row, ' ' . $v['H12']);
                $PHPSheet->setCellValue('BV' . $row, ' ' . $v['H13']);
                $PHPSheet->setCellValue('BW' . $row, ' ' . $v['H14']);
                $PHPSheet->setCellValue('BX' . $row, ' ' . $v['H15']);
                $PHPSheet->setCellValue('BY' . $row, ' ' . $v['H16']);
                $PHPSheet->setCellValue('BZ' . $row, ' ' . $v['H17']);
                $PHPSheet->setCellValue('CA' . $row, ' ' . $v['H18']);
                $PHPSheet->setCellValue('CB' . $row, ' ' . $v['H19']);
                $PHPSheet->setCellValue('CC' . $row, ' ' . $v['H20']);
                $PHPSheet->setCellValue('CD' . $row, ' ' . $v['H21']);
                $PHPSheet->setCellValue('CE' . $row, ' ' . $v['H22']);
                $PHPSheet->setCellValue('CF' . $row, ' ' . $v['H23']);
                $PHPSheet->setCellValue('CG' . $row, ' ' . $v['H24']);
                $PHPSheet->setCellValue('CH' . $row, ' ' . $v['H25']);
                $PHPSheet->setCellValue('CI' . $row, ' ' . $v['H26']);
                $PHPSheet->setCellValue('CJ' . $row, ' ' . $v['H27']);
                $PHPSheet->setCellValue('CK' . $row, ' ' . $v['H28']);
                $PHPSheet->setCellValue('CL' . $row, ' ' . $v['HP']);
                $PHPSheet->setCellValue('CM' . $row, ' ' . $v['h_stime']);
                $PHPSheet->setCellValue('CN' . $row, ' ' . $v['h_etime']);
                $PHPSheet->setCellValue('CO' . $row, ' ' . $v['h_ltime']);
                $PHPSheet->setCellValue('CP' . $row, ' ' . $v['I01']);
                $PHPSheet->setCellValue('CQ' . $row, ' ' . $v['I02']);
                $PHPSheet->setCellValue('CR' . $row, ' ' . $v['I03']);
                $PHPSheet->setCellValue('CS' . $row, ' ' . $v['I04']);
                $PHPSheet->setCellValue('CT' . $row, ' ' . $v['I05']);
                $PHPSheet->setCellValue('CU' . $row, ' ' . $v['I06']);
                $PHPSheet->setCellValue('CV' . $row, ' ' . $v['I07']);
                $PHPSheet->setCellValue('CW' . $row, ' ' . $v['I08']);
                $PHPSheet->setCellValue('CX' . $row, ' ' . $v['I09']);
                $PHPSheet->setCellValue('CY' . $row, ' ' . $v['I10']);
                $PHPSheet->setCellValue('CZ' . $row, ' ' . $v['I11']);
                $PHPSheet->setCellValue('DA' . $row, ' ' . $v['I12']);
                $PHPSheet->setCellValue('DB' . $row, ' ' . $v['I13']);
                $PHPSheet->setCellValue('DC' . $row, ' ' . $v['I14']);
                $PHPSheet->setCellValue('DD' . $row, ' ' . $v['I15']);
                $PHPSheet->setCellValue('DE' . $row, ' ' . $v['I16']);
                $PHPSheet->setCellValue('DF' . $row, ' ' . $v['I17']);
                $PHPSheet->setCellValue('DG' . $row, ' ' . $v['I18']);
                $PHPSheet->setCellValue('DH' . $row, ' ' . $v['I19']);
                $PHPSheet->setCellValue('DI' . $row, ' ' . $v['I20']);
                $PHPSheet->setCellValue('DJ' . $row, ' ' . $v['I21']);
                $PHPSheet->setCellValue('DK' . $row, ' ' . $v['I22']);
                $PHPSheet->setCellValue('DL' . $row, ' ' . $v['I23']);
                $PHPSheet->setCellValue('DM' . $row, ' ' . $v['I24']);
                $PHPSheet->setCellValue('DN' . $row, ' ' . $v['I25']);
                $PHPSheet->setCellValue('DO' . $row, ' ' . $v['IP']);
                $PHPSheet->setCellValue('DP' . $row, ' ' . $v['tough']);
                $PHPSheet->setCellValue('DQ' . $row, ' ' . $v['power']);
                $PHPSheet->setCellValue('DR' . $row, ' ' . $v['optimistic']);
                $PHPSheet->setCellValue('DS' . $row, ' ' . $v['i_stime']);
                $PHPSheet->setCellValue('DT' . $row, ' ' . $v['i_etime']);
                $PHPSheet->setCellValue('DU' . $row, ' ' . $v['i_ltime']);
                $PHPSheet->setCellValue('DV' . $row, ' ' . $v['J01']);
                $PHPSheet->setCellValue('DW' . $row, ' ' . $v['J02']);
                $PHPSheet->setCellValue('DX' . $row, ' ' . $v['J03']);
                $PHPSheet->setCellValue('DY' . $row, ' ' . $v['J04']);
                $PHPSheet->setCellValue('DZ' . $row, ' ' . $v['J05']);
                $PHPSheet->setCellValue('EA' . $row, ' ' . $v['J06']);
                $PHPSheet->setCellValue('EB' . $row, ' ' . $v['J07']);
                $PHPSheet->setCellValue('EC' . $row, ' ' . $v['J08']);
                $PHPSheet->setCellValue('ED' . $row, ' ' . $v['J09']);
                $PHPSheet->setCellValue('EE' . $row, ' ' . $v['J10']);
                $PHPSheet->setCellValue('EF' . $row, ' ' . $v['J11']);
                $PHPSheet->setCellValue('EG' . $row, ' ' . $v['J12']);
                $PHPSheet->setCellValue('EH' . $row, ' ' . $v['J13']);
                $PHPSheet->setCellValue('EI' . $row, ' ' . $v['J14']);
                $PHPSheet->setCellValue('EJ' . $row, ' ' . $v['J15']);
                $PHPSheet->setCellValue('EK' . $row, ' ' . $v['J16']);
                $PHPSheet->setCellValue('EL' . $row, ' ' . $v['J17']);
                $PHPSheet->setCellValue('EM' . $row, ' ' . $v['J18']);
                $PHPSheet->setCellValue('EN' . $row, ' ' . $v['J19']);
                $PHPSheet->setCellValue('EO' . $row, ' ' . $v['J20']);
                $PHPSheet->setCellValue('EP' . $row, ' ' . $v['J21']);
                $PHPSheet->setCellValue('EQ' . $row, ' ' . $v['J22']);
                $PHPSheet->setCellValue('ER' . $row, ' ' . $v['J23']);
                $PHPSheet->setCellValue('ES' . $row, ' ' . $v['J24']);
                $PHPSheet->setCellValue('ET' . $row, ' ' . $v['J25']);
                $PHPSheet->setCellValue('EU' . $row, ' ' . $v['J26']);
                $PHPSheet->setCellValue('EV' . $row, ' ' . $v['J27']);
                $PHPSheet->setCellValue('EW' . $row, ' ' . $v['J28']);
                $PHPSheet->setCellValue('EX' . $row, ' ' . $v['J29']);
                $PHPSheet->setCellValue('EY' . $row, ' ' . $v['J30']);
                $PHPSheet->setCellValue('EZ' . $row, ' ' . $v['JP']);
                $PHPSheet->setCellValue('FA' . $row, ' ' . $v['individual']);
                $PHPSheet->setCellValue('FB' . $row, ' ' . $v['negative']);
                $PHPSheet->setCellValue('FC' . $row, ' ' . $v['self_confidence']);
                $PHPSheet->setCellValue('FD' . $row, ' ' . $v['Helpless']);
            }
            ob_flush();
            flush();
        }
        $filename = '课前问卷' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //心情记录问卷表
    public function mood()
    {
        $where[] = ['a.id', '>', 0];;
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
        }
        $CP = input('post.CP/a', array());
        if ($CP && !in_array('10', $CP)) {
            $cp_arr = [];
            if (in_array('1', $CP)) {
                $cp_arr[] = 0;
                $cp_arr[] = 1;
                $cp_arr[] = 2;
                $cp_arr[] = 3;
                $cp_arr[] = 4;
            }
            if (in_array('2', $CP)) {
                $cp_arr[] = 5;
                $cp_arr[] = 6;
                $cp_arr[] = 7;
                $cp_arr[] = 8;
                $cp_arr[] = 9;
            }
            if (in_array('3', $CP)) {
                $cp_arr[] = 10;
                $cp_arr[] = 11;
                $cp_arr[] = 12;
                $cp_arr[] = 13;
                $cp_arr[] = 14;
            }
            if (in_array('4', $CP)) {
                $cp_arr[] = 15;
                $cp_arr[] = 16;
                $cp_arr[] = 17;
                $cp_arr[] = 18;
                $cp_arr[] = 19;
            }
            if (in_array('5', $CP)) {
                $cp_arr[] = 20;
                $cp_arr[] = 21;
                $cp_arr[] = 22;
                $cp_arr[] = 23;
                $cp_arr[] = 24;
                $cp_arr[] = 25;
                $cp_arr[] = 26;
                $cp_arr[] = 27;
            }
            $where[] = ['b.CP', 'in', $cp_arr];
        }
        $DP = input('post.DP/a', array());
        if ($DP && !in_array('10', $DP)) {
            $dp_arr = [];
            if (in_array('1', $DP)) {
                $dp_arr[] = 0;
                $dp_arr[] = 1;
                $dp_arr[] = 2;
                $dp_arr[] = 3;
                $dp_arr[] = 4;
            }
            if (in_array('2', $DP)) {
                $dp_arr[] = 5;
                $dp_arr[] = 6;
                $dp_arr[] = 7;
                $dp_arr[] = 8;
                $dp_arr[] = 9;
            }
            if (in_array('3', $DP)) {
                $dp_arr[] = 10;
                $dp_arr[] = 11;
                $dp_arr[] = 12;
                $dp_arr[] = 13;
                $dp_arr[] = 14;
            }
            if (in_array('4', $DP)) {
                $dp_arr[] = 15;
                $dp_arr[] = 16;
                $dp_arr[] = 17;
                $dp_arr[] = 18;
                $dp_arr[] = 19;
                $dp_arr[] = 20;
                $dp_arr[] = 21;
            }
            $where[] = ['c.DP', 'in', $dp_arr];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 20);

        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . implode(',', $course) . implode(',', $CP) . implode(',', $DP) . $page . $limit . 'cm_depression_info' . 'cm_anxiety_info');

        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_depression_info b', 'a.open_id = b.openid')
                ->join('cm_anxiety_info c', 'b.id = c.dep_id', 'left')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.C01,b.C02,b.C03,b.C04,b.C05,b.C06,b.C07,b.C08,b.C09,b.CP,b.stime,b.ltime as b_ltime,c.D01,c.D02,c.D03,c.D04,c.D05,c.D06,c.D07,c.DP,c.etime,c.time as c_ltime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                $list[$key]['ltime']   = timediff($value['etime'], $value['stime']);
                $list[$key]['b_stime'] = date('Y-m-d H:i', $value['stime']);
                $list[$key]['c_etime'] = date('Y-m-d H:i', $value['etime']);
                $list[$key]['course'] = 'S' . $value['course'];
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list[$key]['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list[$key]['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list[$key]['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list[$key]['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list[$key]['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list[$key]['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list[$key]['type_name'] = 'N-普通人群';
                } else {
                    $list[$key]['type_name'] = '游客';
                }
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_depression_info b', 'a.open_id = b.openid')
                ->join('cm_anxiety_info c', 'b.id = c.dep_id', 'left')
                ->field('a.open_id')
                ->count();
            $page_total = ceil($total / $limit);

            $return = [
                'list' => $list,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //导出心情记录问卷表
    public function excel_mood()
    {
        $where[] = ['a.id', '>', 0];;
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
        }
        $CP = input('post.CP/a', array());
        if ($CP && !in_array('10', $CP)) {
            $cp_arr = [];
            if (in_array('1', $CP)) {
                $cp_arr[] = 0;
                $cp_arr[] = 1;
                $cp_arr[] = 2;
                $cp_arr[] = 3;
                $cp_arr[] = 4;
            }
            if (in_array('2', $CP)) {
                $cp_arr[] = 5;
                $cp_arr[] = 6;
                $cp_arr[] = 7;
                $cp_arr[] = 8;
                $cp_arr[] = 9;
            }
            if (in_array('3', $CP)) {
                $cp_arr[] = 10;
                $cp_arr[] = 11;
                $cp_arr[] = 12;
                $cp_arr[] = 13;
                $cp_arr[] = 14;
            }
            if (in_array('4', $CP)) {
                $cp_arr[] = 15;
                $cp_arr[] = 16;
                $cp_arr[] = 17;
                $cp_arr[] = 18;
                $cp_arr[] = 19;
            }
            if (in_array('5', $CP)) {
                $cp_arr[] = 20;
                $cp_arr[] = 21;
                $cp_arr[] = 22;
                $cp_arr[] = 23;
                $cp_arr[] = 24;
                $cp_arr[] = 25;
                $cp_arr[] = 26;
                $cp_arr[] = 27;
            }
            $where[] = ['b.CP', 'in', $cp_arr];
        }
        $DP = input('post.DP/a', array());
        if ($DP && !in_array('10', $DP)) {
            $dp_arr = [];
            if (in_array('1', $DP)) {
                $dp_arr[] = 0;
                $dp_arr[] = 1;
                $dp_arr[] = 2;
                $dp_arr[] = 3;
                $dp_arr[] = 4;
            }
            if (in_array('2', $DP)) {
                $dp_arr[] = 5;
                $dp_arr[] = 6;
                $dp_arr[] = 7;
                $dp_arr[] = 8;
                $dp_arr[] = 9;
            }
            if (in_array('3', $DP)) {
                $dp_arr[] = 10;
                $dp_arr[] = 11;
                $dp_arr[] = 12;
                $dp_arr[] = 13;
                $dp_arr[] = 14;
            }
            if (in_array('4', $DP)) {
                $dp_arr[] = 15;
                $dp_arr[] = 16;
                $dp_arr[] = 17;
                $dp_arr[] = 18;
                $dp_arr[] = 19;
                $dp_arr[] = 20;
                $dp_arr[] = 21;
            }
            $where[] = ['c.DP', 'in', $dp_arr];
        }

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_depression_info b', 'a.open_id = b.openid')
            ->join('cm_anxiety_info c', 'b.id = c.dep_id', 'left')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.C01,b.C02,b.C03,b.C04,b.C05,b.C06,b.C07,b.C08,b.C09,b.CP,b.stime,b.ltime as b_ltime,c.D01,c.D02,c.D03,c.D04,c.D05,c.D06,c.D07,c.DP,c.etime,c.time as c_ltime')
            ->order('a.id')
            ->select()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['ltime']   = timediff($value['etime'], $value['stime']);
            $list[$key]['b_stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['c_etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S' . $value['course'];
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list[$key]['type_name'] = 'N-普通人群';
            } else {
                $list[$key]['type_name'] = '游客';
            }
        } //dump($list);die;

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '用户分类', '课程编号', 'C01', 'C02', 'C03', 'C04', 'C05', 'C06', 'C07', 'C08', 'C09', 'CP', 'D01', 'D02', 'D03', 'D04', 'D05', 'D06', 'D07', 'DP');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(22);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(20);
        $PHPSheet->getColumnDimension('I')->setWidth(20);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(12);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(15);
        $PHPSheet->getColumnDimension('O')->setWidth(15);
        $PHPSheet->getColumnDimension('P')->setWidth(15);
        $PHPSheet->getColumnDimension('Q')->setWidth(13);
        $PHPSheet->getColumnDimension('R')->setWidth(13);
        $PHPSheet->getColumnDimension('S')->setWidth(13);
        $PHPSheet->getColumnDimension('T')->setWidth(13);
        $PHPSheet->getColumnDimension('U')->setWidth(13);
        $PHPSheet->getColumnDimension('V')->setWidth(13);
        $PHPSheet->getColumnDimension('W')->setWidth(13);
        $PHPSheet->getColumnDimension('X')->setWidth(15);
        $PHPSheet->getColumnDimension('Y')->setWidth(13);
        $PHPSheet->getColumnDimension('Z')->setWidth(13);
        $PHPSheet->getColumnDimension('AA')->setWidth(15);
        $PHPSheet->getColumnDimension('AB')->setWidth(15);

        //数据
        foreach ($list as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                $PHPSheet->setCellValue('A' . $row, ' ' . $num);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['open_id']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['number']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['name']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['wx_phone']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['type_name']);
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['course']);
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['stime']);
                $PHPSheet->setCellValue('I' . $row, ' ' . $v['etime']);
                $PHPSheet->setCellValue('J' . $row, ' ' . $v['ltime']);
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['C01']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['C02']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['C03']);
                $PHPSheet->setCellValue('N' . $row, ' ' . $v['C04']);
                $PHPSheet->setCellValue('O' . $row, ' ' . $v['C05']);
                $PHPSheet->setCellValue('P' . $row, ' ' . $v['C06']);
                $PHPSheet->setCellValue('Q' . $row, ' ' . $v['C07']);
                $PHPSheet->setCellValue('R' . $row, ' ' . $v['C08']);
                $PHPSheet->setCellValue('S' . $row, ' ' . $v['C09']);
                $PHPSheet->setCellValue('T' . $row, ' ' . $v['CP']);
                $PHPSheet->setCellValue('U' . $row, ' ' . $v['D01']);
                $PHPSheet->setCellValue('V' . $row, ' ' . $v['D02']);
                $PHPSheet->setCellValue('W' . $row, ' ' . $v['D03']);
                $PHPSheet->setCellValue('X' . $row, ' ' . $v['D04']);
                $PHPSheet->setCellValue('Y' . $row, ' ' . $v['D05']);
                $PHPSheet->setCellValue('Z' . $row, ' ' . $v['D06']);
                $PHPSheet->setCellValue('AA' . $row, ' ' . $v['D07']);
                $PHPSheet->setCellValue('AB' . $row, ' ' . $v['DP']);
            }
            ob_flush();
            flush();
        }
        $filename = '心情记录' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //课后反馈信息表
    public function feedback()
    {
        $where[] = ['a.id', '>', 0];;
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . implode(',', $course) . $page . $limit . 'user' . 'cm_feedback');

        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_feedback b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.stime,b.etime,b.ltime,b.Q1,b.Q2,b.Q3,b.Q4,b.Q5,b.Q6,b.question')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();
            foreach ($list as $key => $value) {
                $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                $list[$key]['course'] = 'S' . $value['course'];
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list[$key]['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list[$key]['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list[$key]['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list[$key]['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list[$key]['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list[$key]['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list[$key]['type_name'] = 'N-普通人群';
                } else {
                    $list[$key]['type_name'] = '游客';
                }
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_feedback b', 'a.open_id = b.open_id')
                ->field('a.open_id')
                ->count();
            $page_total = ceil($total / $limit);

            $return = [
                'list' => $list,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //批量导出课后反馈
    function excel_feedback()
    {
        set_time_limit(0);

        $where[] = ['a.id', '>', 0];;
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['a.name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['a.wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['a.type', 'in', $type];
        }
        $course = input('post.course/a', array());
        if ($course && !in_array('10', $course)) {
            $where[] = ['b.course', 'in', $course];
        }

        $data = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_feedback b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.stime,b.etime,b.ltime,b.Q1,b.Q2,b.Q3,b.Q4,b.Q5,b.Q6,b.question')
            ->order('a.id')
            ->select()->toArray();
        foreach ($data as $key => $value) {
            $data[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $data[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $data[$key]['course'] = 'S' . $value['course'];
            $data[$key]['class'] = '课后反馈问卷';
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $data[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $data[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $data[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $data[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $data[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $data[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $data[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $data[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $data[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $data[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $data[$key]['type_name'] = 'N-普通人群';
            } else {
                $data[$key]['type_name'] = '游客';
            }
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '用户分类', '课程编码', '问卷分类', '问卷填写开始时间', '问卷填写结束时间', '问卷填写时长', 'Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', '建议');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(22);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(16);
        $PHPSheet->getColumnDimension('I')->setWidth(23);
        $PHPSheet->getColumnDimension('J')->setWidth(23);
        $PHPSheet->getColumnDimension('K')->setWidth(20);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(15);
        $PHPSheet->getColumnDimension('O')->setWidth(15);
        $PHPSheet->getColumnDimension('P')->setWidth(15);
        $PHPSheet->getColumnDimension('Q')->setWidth(13);
        $PHPSheet->getColumnDimension('R')->setWidth(40);

        //数据
        foreach ($data as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                $PHPSheet->setCellValue('A' . $row, ' ' . $num);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['open_id']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['number']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['name']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['wx_phone']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['type_name']);
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['course']);
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['class']);
                $PHPSheet->setCellValue('I' . $row, ' ' . $v['stime']);
                $PHPSheet->setCellValue('J' . $row, ' ' . $v['etime']);
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['ltime']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['Q1']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['Q2']);
                $PHPSheet->setCellValue('N' . $row, ' ' . $v['Q3']);
                $PHPSheet->setCellValue('O' . $row, ' ' . $v['Q4']);
                $PHPSheet->setCellValue('P' . $row, ' ' . $v['Q5']);
                $PHPSheet->setCellValue('Q' . $row, ' ' . $v['Q6']);
                $PHPSheet->setCellValue('R' . $row, ' ' . $v['question']);
            }
            ob_flush();
            flush();
        }
        $filename = '课后反馈' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
