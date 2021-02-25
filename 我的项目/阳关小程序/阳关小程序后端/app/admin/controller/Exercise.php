<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 14:50
 */

namespace app\admin\controller;

use app\util\Tools;
use app\BaseController;
use app\util\ReturnCode;
use think\facade\Db;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class Exercise extends AdminAuth
{
    //S1-问题清单
    public function problem_list()
    {
        $where[] = ['a.id', '>', 0];;
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_one_course' . 'problem_list');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_one_course b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.milieu,b.mood,b.phy_per,b.action,b.thinking,b.stime,b.etime,b.ltime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                $list[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
                $list[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
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
                ->join('cm_one_course b', 'a.open_id = b.open_id')
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

    //导出S1-问题清单
    public function excel_problem_list()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
            $where['b.course'] = ['in', $course];
        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_one_course b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.milieu,b.mood,b.phy_per,b.action,b.thinking,b.stime,b.etime,b.ltime')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
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

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '开始时间', '结束时间', '时长', '环境/生活变化情境', '情绪', '生理表现', '行为', '思维');
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
        $PHPSheet->getColumnDimension('H')->setWidth(17);
        $PHPSheet->getColumnDimension('I')->setWidth(17);
        $PHPSheet->getColumnDimension('J')->setWidth(18);
        $PHPSheet->getColumnDimension('K')->setWidth(22);
        $PHPSheet->getColumnDimension('L')->setWidth(17);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(15);
        $PHPSheet->getColumnDimension('O')->setWidth(15);

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
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['milieu']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['mood']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['phy_per']);
                $PHPSheet->setCellValue('N' . $row, ' ' . $v['action']);
                $PHPSheet->setCellValue('O' . $row, ' ' . $v['thinking']);
            }
            ob_flush();
            flush();
        }
        $filename = 'S1-问题清单' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S1-愉快事件记录表
    public function pleasure_event_list()
    {
        $where[] = ['a.id', '>', 0];
        $stime = input('post.stime');
        $etime = input('post.etime');

        if ($stime && empty($etime)) {
            $where['b.etime'] = ['>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where['b.etime'] = ['<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where['b.etime'] = ['between', [strtotime($stime), strtotime($etime)]];
        }

        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_record_happy_event' . 'pleasure_event_list');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_record_happy_event b', 'a.open_id=b.open_id')
                ->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.stime,b.etime,b.ltime,b.what_time,b.what_place,b.have_people,b.what_thing,b.pleasure_deg')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();
            foreach ($list as $k => $v) {
                if ($list[$k]['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $value['type'] = 'P-患者';
                } elseif ($list[$k]['type'] == '2') {
                    $list[$k]['type'] = 'H-高危人群';
                } elseif ($list[$k]['type'] == '3') {
                    $list[$k]['type'] = 'R-缓解期患者';
                } elseif ($list[$k]['type'] == '4') {
                    $list[$k]['type'] = '高危-分数';
                } elseif ($list[$k]['type'] == '5') {
                    $list[$k]['type'] = '患者-B1';
                } elseif ($list[$k]['type'] == '6') {
                    $list[$k]['type'] = '缓解期-B2';
                } elseif ($k['type'] == '7') {
                    $list[$k]['type'] = 'P2-患者轻度';
                } elseif ($k['type'] == '8') {
                    $list[$k]['type'] = 'P3-患者中度';
                } elseif ($k['type'] == '9') {
                    $list[$k]['type'] = 'P4-患者重度';
                } elseif ($k['type'] == '12') {
                    $list[$k]['type'] = 'P5-自曝患者';
                } else {
                    $list[$k]['type'] = '游客';
                }

                $list[$k]['stime'] = date('Y-m-d H:i:s', $v['stime']);
                $list[$k]['etime'] = date('Y-m-d H:i:s', $v['etime']);
                $list[$k]['course'] = "S1";
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_record_happy_event b', 'a.open_id=b.open_id')
                ->field('a.open_id')
                ->page($page, $limit)
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

    //导出S1-愉快事件记录表
    public function excel_pleasure_event_list()
    {
        $where[] = ['a.id', '>', 0];
        $stime = input('post.stime');
        $etime = input('post.etime');

        if ($stime && empty($etime)) {
            $where['b.etime'] = ['>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where['b.etime'] = ['<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where['b.etime'] = ['between', [strtotime($stime), strtotime($etime)]];
        }

        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);


        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_record_happy_event b', 'a.open_id=b.open_id')
            ->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.stime,b.etime,b.ltime,b.what_time,b.what_place,b.have_people,b.what_thing,b.pleasure_deg')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        foreach ($list as $k => $v) {
            if ($list[$k]['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$k]['type'] = 'P-患者';
            } elseif ($list[$k]['type'] == '2') {
                $list[$k]['type'] = 'H-高危人群';
            } elseif ($list[$k]['type'] == '3') {
                $list[$k]['type'] = 'R-缓解期患者';
            } elseif ($list[$k]['type'] == '4') {
                $list[$k]['type'] = '高危-分数';
            } elseif ($list[$k]['type'] == '5') {
                $list[$k]['type'] = '患者-B1';
            } elseif ($list[$k]['type'] == '6') {
                $list[$k]['type'] = '缓解期-B2';
            } elseif ($k['type'] == '7') {
                $list[$k]['type'] = 'P2-患者轻度';
            } elseif ($k['type'] == '8') {
                $list[$k]['type'] = 'P3-患者中度';
            } elseif ($k['type'] == '9') {
                $list[$k]['type'] = 'P4-患者重度';
            } elseif ($k['type'] == '12') {
                $list[$k]['type'] = 'P5-自曝患者';
            } else {
                $list[$k]['type'] = '游客';
            }


            $list[$k]['stime'] = date('Y-m-d H:i:s', $v['stime']);
            $list[$k]['etime'] = date('Y-m-d H:i:s', $v['etime']);
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();

        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('1')->setRowHeight(25);

        $PHPSheet->getStyle('A1:N1')->getFont()->setSize(13)->setBold(true);

        $PHPSheet->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码');
        $PHPSheet->setCellValue('C1', '姓名');
        $PHPSheet->setCellValue('D1', '微信手机号');
        $PHPSheet->setCellValue('E1', '患者分类');
        $PHPSheet->setCellValue('F1', '课程编号');
        $PHPSheet->setCellValue('G1', '开始时间');
        $PHPSheet->setCellValue('H1', '结束时间');
        $PHPSheet->setCellValue('I1', '时长');
        $PHPSheet->setCellValue('J1', '时间');
        $PHPSheet->setCellValue('K1', '地点');
        $PHPSheet->setCellValue('L1', '人物');
        $PHPSheet->setCellValue('M1', '事情');
        $PHPSheet->setCellValue('N1', '愉悦程度');


        $PHPSheet->getColumnDimension('A')->setWidth(25);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(12);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(15);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(12);
        $PHPSheet->getColumnDimension('J')->setWidth(15);
        $PHPSheet->getColumnDimension('K')->setWidth(15);
        $PHPSheet->getColumnDimension('L')->setWidth(15);
        $PHPSheet->getColumnDimension('M')->setWidth(25);
        $PHPSheet->getColumnDimension('N')->setWidth(15);

        foreach ($list as $k => $v) {
            $row = $k + 2;
            $PHPSheet->setCellValue('A' . $row, $v['open_id']);
            $PHPSheet->setCellValue('B' . $row, $v['number']);
            $PHPSheet->setCellValue('C' . $row, $v['name']);
            $PHPSheet->setCellValue('D' . $row, $v['wx_phone']);
            $PHPSheet->setCellValue('E' . $row, $v['type']);
            $PHPSheet->setCellValue('F' . $row, 'S1');
            $PHPSheet->setCellValue('G' . $row, $v['stime']);
            $PHPSheet->setCellValue('H' . $row, $v['etime']);
            $PHPSheet->setCellValue('I' . $row, $v['ltime']);
            $PHPSheet->setCellValue('J' . $row, $v['what_time']);
            $PHPSheet->setCellValue('K' . $row, $v['what_place']);
            $PHPSheet->setCellValue('L' . $row, $v['have_people']);
            $PHPSheet->setCellValue('M' . $row, $v['what_thing']);
            $PHPSheet->getStyle('M' . $row)->getAlignment()->setWrapText(true);
            $PHPSheet->setCellValue('N' . $row, $v['pleasure_deg']);
        }

        //设置水平居中
        $PHPSheet->getStyle('A1:N' . (count($list) + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = '愉快事件记录表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
    //S2-目标清单
    public function target_list()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_target_list' . 'target_list');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_target_list b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.problem,b.main_target,b.specific_goals,b.stime,b.etime,b.ltime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                if ($value['stime']) {
                    $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                }
                if ($value['etime']) {
                    $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                }
                $list[$key]['course'] = 'S2';
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
                if ($value['problem']) {
                    $problem_arr = explode('||', $value['problem']);
                    $list[$key]['problem_arr'] = $problem_arr;
                } else {
                    $list[$key]['problem_arr'] = [];
                }

                if ($value['specific_goals']) {
                    $specific_goals_arr = explode('||', $value['specific_goals']);
                    $list[$key]['specific_goals_arr'] = $specific_goals_arr;
                } else {
                    $list[$key]['specific_goals_arr'] = [];
                }
            }
            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_target_list b', 'a.open_id = b.open_id')
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

    //导出S2-目标清单
    public function excel_target_list()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }

        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_target_list b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.problem,b.main_target,b.specific_goals,b.stime,b.etime,b.ltime')
            ->page($page, $limit)
            ->order('a.id,b.etime')
            ->select()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S2';
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
            $problem_arr = explode('||', $value['problem']);
            $list[$key]['problem_arr'] = $problem_arr;
            $list[$key]['problem_count'] = count($problem_arr);

            $specific_goals_arr = explode('||', $value['specific_goals']);
            $list[$key]['specific_goals_arr'] = $specific_goals_arr;
            $list[$key]['specific_goals_count'] = count($specific_goals_arr);
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '开始时间', '结束时间', '时长', '我的问题清单', '排序值', '总目标', '具体目标');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(15);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(22);
        $PHPSheet->getColumnDimension('L')->setWidth(9);
        $PHPSheet->getColumnDimension('M')->setWidth(25);
        $PHPSheet->getColumnDimension('N')->setWidth(22);

        //数据
        $all = 2;
        foreach ($list as $k => $v) {
            $row = $all;
            if ($v['problem_count'] >= $v['specific_goals_count']) {
                $n = $v['problem_count'];
            } else {
                $n = $v['specific_goals_count'];
            }
            $s = $all;
            $all += $n;
            $e = $all - 1;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 10 || $j == 12) {
                    $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                    $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
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
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['main_target']);
                $m = 1;
                for ($i = 0; $i < count($v['problem_arr']); $i++) {
                    $rows = $row + $i;
                    if ($j == 10 || $j == 11) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('K' . $rows, ' ' . $v['problem_arr'][$i]);
                    $PHPSheet->setCellValue('L' . $rows, ' ' . $m);
                    $m++;
                }

                for ($i = 0; $i < count($v['specific_goals_arr']); $i++) {
                    $rowd = $row + $i;
                    if ($j == 13) {
                        $PHPSheet->getStyle($letter[$j] . $rowd)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rowd . ':' . $letter[$j] . $rowd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('N' . $rowd, ' ' . $v['specific_goals_arr'][$i]);
                }
            }
            ob_flush();
            flush();
        }
        $filename = 'S2-目标清单' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S2-活动记录
    public function activity_record()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_activity_record' . 'activity_record');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_activity_record b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime')
                ->group('etime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                //查询该时间段下该用户填写的所有活动
                $activity = Db::name('activity_record')
                    ->where(['open_id' => $value['open_id'], 'etime' => $value['etime']])
                    ->field('date,time,activity,pleasure,achievement,week')
                    ->group('date')
                    ->order('date')
                    ->select()->toArray();
                $infos = [];
                foreach ($activity as $k => $v) {
                    $info = Db::name('activity_record')
                        ->where(['date' => $v['date']])
                        ->field('date,time,activity,pleasure,achievement,week')
                        ->order('time')
                        ->select()->toArray();
                    //dump($info);
                    foreach ($info as $ka => $va) {
                        $infos[$k]['date'] = date('Y/m/d', $va['date']);
                        $infos[$k]['week'] = $va['week'];
                        $infos[$k]['activity-' . $va['time']] = $va['activity'];
                        $infos[$k]['pleasure-' . $va['time']] = $va['pleasure'];
                        $infos[$k]['achievement-' . $va['time']] = $va['achievement'];
                    }
                }
                $list[$key]['info'] = $infos;
                if ($value['stime']) {
                    $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                }
                if ($value['etime']) {
                    $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                }
                $list[$key]['course'] = 'S2';
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
                ->join('cm_activity_record b', 'a.open_id = b.open_id')
                ->field('a.open_id')
                ->group('etime')
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

    //导出S2-活动记录
    public function excel_activity_record()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_activity_record b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime')
            ->group('etime')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        foreach ($list as $key => $value) {
            //查询该时间段下该用户填写的所有活动
            $activity = Db::name('activity_record')
                ->where(['open_id' => $value['open_id'], 'etime' => $value['etime']])
                ->field('date,time,activity,pleasure,achievement,week')
                ->group('date')
                ->order('date')
                ->select()->toArray();
            $infos = [];
            foreach ($activity as $k => $v) {
                $info = Db::name('activity_record')
                    ->where(['date' => $v['date']])
                    ->field('date,time,activity,pleasure,achievement,week')
                    ->order('time')
                    ->select()->toArray();
                //dump($info);
                foreach ($info as $ka => $va) {
                    $infos[$k]['date'] = date('Y/m/d', $va['date']);
                    $infos[$k]['week'] = $va['week'];
                    $infos[$k]['activity-' . $va['time']] = $va['activity'];
                    $infos[$k]['pleasure-' . $va['time']] = $va['pleasure'];
                    $infos[$k]['achievement-' . $va['time']] = $va['achievement'];
                }
            }
            $list[$key]['info'] = $infos;

            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S2';
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
        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '用户分类', '课程编号', '开始时间', '结束时间', '时长', '日期', '星期', '0-1点', '愉悦度0', '成就感0', '1-2点', '愉悦度1', '成就感1', '2-3点', '愉悦度2', '成就感2', '3-4点', '愉悦度3', '成就感3', '4-5点', '愉悦度4', '成就感4', '5-6点', '愉悦度5', '成就感5', '6-7点', '愉悦度6', '成就感6', '7-8点', '愉悦度7', '成就感7', '8-9点', '愉悦度8', '成就感8', '9-10点', '愉悦度9', '成就感9', '10-11点', '愉悦度10', '成就感10', '11-12点', '愉悦度11', '成就感11', '12-13点', '愉悦度12', '成就感12', '13-14点', '愉悦度13', '成就感13', '14-15点', '愉悦度14', '成就感14', '15-16点', '愉悦度15', '成就感15', '16-17点', '愉悦度16', '成就感16', '17-18点', '愉悦度17', '成就感17', '18-19点', '愉悦度18', '成就感18', '19-20点', '愉悦度19', '成就感19', '20-21点', '愉悦度20', '成就感20', '21-22点', '愉悦度21', '成就感21', '22-23点', '愉悦度22', '成就感22', '23-24点', '愉悦度23', '成就感23');
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
        $PHPSheet->getColumnDimension('H')->setWidth(17);
        $PHPSheet->getColumnDimension('I')->setWidth(17);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(12);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(7);
        $PHPSheet->getColumnDimension('O')->setWidth(7);
        $PHPSheet->getColumnDimension('P')->setWidth(15);
        $PHPSheet->getColumnDimension('Q')->setWidth(7);
        $PHPSheet->getColumnDimension('R')->setWidth(7);
        $PHPSheet->getColumnDimension('S')->setWidth(15);
        $PHPSheet->getColumnDimension('T')->setWidth(7);
        $PHPSheet->getColumnDimension('U')->setWidth(7);
        $PHPSheet->getColumnDimension('V')->setWidth(15);
        $PHPSheet->getColumnDimension('W')->setWidth(7);
        $PHPSheet->getColumnDimension('X')->setWidth(7);
        $PHPSheet->getColumnDimension('Y')->setWidth(15);
        $PHPSheet->getColumnDimension('Z')->setWidth(7);
        $PHPSheet->getColumnDimension('AA')->setWidth(7);
        $PHPSheet->getColumnDimension('AB')->setWidth(15);
        $PHPSheet->getColumnDimension('AC')->setWidth(7);
        $PHPSheet->getColumnDimension('AD')->setWidth(7);
        $PHPSheet->getColumnDimension('AE')->setWidth(15);
        $PHPSheet->getColumnDimension('AF')->setWidth(7);
        $PHPSheet->getColumnDimension('AG')->setWidth(7);
        $PHPSheet->getColumnDimension('AH')->setWidth(15);
        $PHPSheet->getColumnDimension('AI')->setWidth(7);
        $PHPSheet->getColumnDimension('AJ')->setWidth(7);
        $PHPSheet->getColumnDimension('AK')->setWidth(15);
        $PHPSheet->getColumnDimension('AL')->setWidth(7);
        $PHPSheet->getColumnDimension('AM')->setWidth(7);
        $PHPSheet->getColumnDimension('AN')->setWidth(15);
        $PHPSheet->getColumnDimension('AO')->setWidth(7);
        $PHPSheet->getColumnDimension('AP')->setWidth(7);
        $PHPSheet->getColumnDimension('AQ')->setWidth(15);
        $PHPSheet->getColumnDimension('AR')->setWidth(7);
        $PHPSheet->getColumnDimension('AS')->setWidth(7);
        $PHPSheet->getColumnDimension('AT')->setWidth(15);
        $PHPSheet->getColumnDimension('AU')->setWidth(7);
        $PHPSheet->getColumnDimension('AV')->setWidth(7);
        $PHPSheet->getColumnDimension('AW')->setWidth(15);
        $PHPSheet->getColumnDimension('AX')->setWidth(7);
        $PHPSheet->getColumnDimension('AY')->setWidth(7);
        $PHPSheet->getColumnDimension('AZ')->setWidth(15);
        $PHPSheet->getColumnDimension('BA')->setWidth(7);
        $PHPSheet->getColumnDimension('BB')->setWidth(7);
        $PHPSheet->getColumnDimension('BC')->setWidth(15);
        $PHPSheet->getColumnDimension('BD')->setWidth(7);
        $PHPSheet->getColumnDimension('BE')->setWidth(7);
        $PHPSheet->getColumnDimension('BF')->setWidth(15);
        $PHPSheet->getColumnDimension('BG')->setWidth(7);
        $PHPSheet->getColumnDimension('BH')->setWidth(7);
        $PHPSheet->getColumnDimension('BI')->setWidth(15);
        $PHPSheet->getColumnDimension('BJ')->setWidth(7);
        $PHPSheet->getColumnDimension('BK')->setWidth(7);
        $PHPSheet->getColumnDimension('BL')->setWidth(15);
        $PHPSheet->getColumnDimension('BM')->setWidth(7);
        $PHPSheet->getColumnDimension('BN')->setWidth(7);
        $PHPSheet->getColumnDimension('BO')->setWidth(15);
        $PHPSheet->getColumnDimension('BP')->setWidth(7);
        $PHPSheet->getColumnDimension('BQ')->setWidth(7);
        $PHPSheet->getColumnDimension('BR')->setWidth(15);
        $PHPSheet->getColumnDimension('BS')->setWidth(7);
        $PHPSheet->getColumnDimension('BT')->setWidth(7);
        $PHPSheet->getColumnDimension('BU')->setWidth(15);
        $PHPSheet->getColumnDimension('BV')->setWidth(7);
        $PHPSheet->getColumnDimension('BW')->setWidth(7);
        $PHPSheet->getColumnDimension('BX')->setWidth(15);
        $PHPSheet->getColumnDimension('BY')->setWidth(7);
        $PHPSheet->getColumnDimension('BZ')->setWidth(7);
        $PHPSheet->getColumnDimension('CA')->setWidth(15);
        $PHPSheet->getColumnDimension('CB')->setWidth(7);
        $PHPSheet->getColumnDimension('CC')->setWidth(7);
        $PHPSheet->getColumnDimension('CD')->setWidth(15);
        $PHPSheet->getColumnDimension('CE')->setWidth(7);
        $PHPSheet->getColumnDimension('CF')->setWidth(7);

        //数据
        $all = 2;
        foreach ($list as $k => $v) {
            $row = $all;
            $n = count($v['info']);

            $s = $all;

            $all += $n;
            $e = $all - 1;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 11) {
                    if ($v['info']) {
                        $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                        $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                }
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
                $m = 1;

                for ($i = 0; $i < count($v['info']); $i++) {
                    $rows = $row + $i;
                    if ($j >= 11) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('K' . $rows, ' ' . $v['info'][$i]['date']);
                    $PHPSheet->setCellValue('L' . $rows, ' ' . $v['info'][$i]['week']);
                    $PHPSheet->setCellValue('M' . $rows, ' ' . $v['info'][$i]['activity-0']);
                    $PHPSheet->setCellValue('N' . $rows, ' ' . $v['info'][$i]['pleasure-0']);
                    $PHPSheet->setCellValue('O' . $rows, ' ' . $v['info'][$i]['achievement-0']);
                    $PHPSheet->setCellValue('P' . $rows, ' ' . $v['info'][$i]['activity-1']);
                    $PHPSheet->setCellValue('Q' . $rows, ' ' . $v['info'][$i]['pleasure-1']);
                    $PHPSheet->setCellValue('R' . $rows, ' ' . $v['info'][$i]['achievement-1']);
                    $PHPSheet->setCellValue('S' . $rows, ' ' . $v['info'][$i]['activity-2']);
                    $PHPSheet->setCellValue('T' . $rows, ' ' . $v['info'][$i]['pleasure-2']);
                    $PHPSheet->setCellValue('U' . $rows, ' ' . $v['info'][$i]['achievement-2']);
                    $PHPSheet->setCellValue('V' . $rows, ' ' . $v['info'][$i]['activity-3']);
                    $PHPSheet->setCellValue('W' . $rows, ' ' . $v['info'][$i]['pleasure-3']);
                    $PHPSheet->setCellValue('X' . $rows, ' ' . $v['info'][$i]['achievement-3']);
                    $PHPSheet->setCellValue('Y' . $rows, ' ' . $v['info'][$i]['activity-4']);
                    $PHPSheet->setCellValue('Z' . $rows, ' ' . $v['info'][$i]['pleasure-4']);
                    $PHPSheet->setCellValue('AA' . $rows, ' ' . $v['info'][$i]['achievement-4']);
                    $PHPSheet->setCellValue('AB' . $rows, ' ' . $v['info'][$i]['activity-5']);
                    $PHPSheet->setCellValue('AC' . $rows, ' ' . $v['info'][$i]['pleasure-5']);
                    $PHPSheet->setCellValue('AD' . $rows, ' ' . $v['info'][$i]['achievement-5']);
                    $PHPSheet->setCellValue('AE' . $rows, ' ' . $v['info'][$i]['activity-6']);
                    $PHPSheet->setCellValue('AF' . $rows, ' ' . $v['info'][$i]['pleasure-6']);
                    $PHPSheet->setCellValue('AG' . $rows, ' ' . $v['info'][$i]['achievement-6']);
                    $PHPSheet->setCellValue('AH' . $rows, ' ' . $v['info'][$i]['activity-7']);
                    $PHPSheet->setCellValue('AI' . $rows, ' ' . $v['info'][$i]['pleasure-7']);
                    $PHPSheet->setCellValue('AJ' . $rows, ' ' . $v['info'][$i]['achievement-7']);
                    $PHPSheet->setCellValue('AK' . $rows, ' ' . $v['info'][$i]['activity-8']);
                    $PHPSheet->setCellValue('AL' . $rows, ' ' . $v['info'][$i]['pleasure-8']);
                    $PHPSheet->setCellValue('AM' . $rows, ' ' . $v['info'][$i]['achievement-8']);
                    $PHPSheet->setCellValue('AN' . $rows, ' ' . $v['info'][$i]['activity-9']);
                    $PHPSheet->setCellValue('AO' . $rows, ' ' . $v['info'][$i]['pleasure-9']);
                    $PHPSheet->setCellValue('AP' . $rows, ' ' . $v['info'][$i]['achievement-9']);
                    $PHPSheet->setCellValue('AQ' . $rows, ' ' . $v['info'][$i]['activity-10']);
                    $PHPSheet->setCellValue('AR' . $rows, ' ' . $v['info'][$i]['pleasure-10']);
                    $PHPSheet->setCellValue('AS' . $rows, ' ' . $v['info'][$i]['achievement-10']);
                    $PHPSheet->setCellValue('AT' . $rows, ' ' . $v['info'][$i]['activity-11']);
                    $PHPSheet->setCellValue('AU' . $rows, ' ' . $v['info'][$i]['pleasure-11']);
                    $PHPSheet->setCellValue('AV' . $rows, ' ' . $v['info'][$i]['achievement-11']);
                    $PHPSheet->setCellValue('AW' . $rows, ' ' . $v['info'][$i]['activity-12']);
                    $PHPSheet->setCellValue('AX' . $rows, ' ' . $v['info'][$i]['pleasure-12']);
                    $PHPSheet->setCellValue('AY' . $rows, ' ' . $v['info'][$i]['achievement-12']);
                    $PHPSheet->setCellValue('AZ' . $rows, ' ' . $v['info'][$i]['activity-13']);
                    $PHPSheet->setCellValue('BA' . $rows, ' ' . $v['info'][$i]['pleasure-13']);
                    $PHPSheet->setCellValue('BB' . $rows, ' ' . $v['info'][$i]['achievement-13']);
                    $PHPSheet->setCellValue('BC' . $rows, ' ' . $v['info'][$i]['activity-14']);
                    $PHPSheet->setCellValue('BD' . $rows, ' ' . $v['info'][$i]['pleasure-14']);
                    $PHPSheet->setCellValue('BE' . $rows, ' ' . $v['info'][$i]['achievement-14']);
                    $PHPSheet->setCellValue('BF' . $rows, ' ' . $v['info'][$i]['activity-15']);
                    $PHPSheet->setCellValue('BG' . $rows, ' ' . $v['info'][$i]['pleasure-15']);
                    $PHPSheet->setCellValue('BH' . $rows, ' ' . $v['info'][$i]['achievement-15']);
                    $PHPSheet->setCellValue('BI' . $rows, ' ' . $v['info'][$i]['activity-16']);
                    $PHPSheet->setCellValue('BJ' . $rows, ' ' . $v['info'][$i]['pleasure-16']);
                    $PHPSheet->setCellValue('BK' . $rows, ' ' . $v['info'][$i]['achievement-16']);
                    $PHPSheet->setCellValue('BL' . $rows, ' ' . $v['info'][$i]['activity-17']);
                    $PHPSheet->setCellValue('BM' . $rows, ' ' . $v['info'][$i]['pleasure-17']);
                    $PHPSheet->setCellValue('BN' . $rows, ' ' . $v['info'][$i]['achievement-17']);
                    $PHPSheet->setCellValue('BO' . $rows, ' ' . $v['info'][$i]['activity-18']);
                    $PHPSheet->setCellValue('BP' . $rows, ' ' . $v['info'][$i]['pleasure-18']);
                    $PHPSheet->setCellValue('BQ' . $rows, ' ' . $v['info'][$i]['achievement-18']);
                    $PHPSheet->setCellValue('BR' . $rows, ' ' . $v['info'][$i]['activity-19']);
                    $PHPSheet->setCellValue('BS' . $rows, ' ' . $v['info'][$i]['pleasure-19']);
                    $PHPSheet->setCellValue('BT' . $rows, ' ' . $v['info'][$i]['achievement-19']);
                    $PHPSheet->setCellValue('BU' . $rows, ' ' . $v['info'][$i]['activity-20']);
                    $PHPSheet->setCellValue('BV' . $rows, ' ' . $v['info'][$i]['pleasure-20']);
                    $PHPSheet->setCellValue('BW' . $rows, ' ' . $v['info'][$i]['achievement-20']);
                    $PHPSheet->setCellValue('BX' . $rows, ' ' . $v['info'][$i]['activity-21']);
                    $PHPSheet->setCellValue('BY' . $rows, ' ' . $v['info'][$i]['pleasure-21']);
                    $PHPSheet->setCellValue('BZ' . $rows, ' ' . $v['info'][$i]['achievement-21']);
                    $PHPSheet->setCellValue('CA' . $rows, ' ' . $v['info'][$i]['activity-22']);
                    $PHPSheet->setCellValue('CB' . $rows, ' ' . $v['info'][$i]['pleasure-22']);
                    $PHPSheet->setCellValue('CC' . $rows, ' ' . $v['info'][$i]['achievement-22']);
                    $PHPSheet->setCellValue('CD' . $rows, ' ' . $v['info'][$i]['activity-23']);
                    $PHPSheet->setCellValue('CE' . $rows, ' ' . $v['info'][$i]['pleasure-23']);
                    $PHPSheet->setCellValue('CF' . $rows, ' ' . $v['info'][$i]['achievement-23']);
                    $m++;
                }
            }
            ob_flush();
            flush();
        }
        $filename = 'S2-目标清单' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S2-自动思维记录
    public function auto_think()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_auto_thinking' . 'auto_think');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->where('course', 2)
                ->join('cm_auto_thinking b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime,b.situation,b.id')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();
            foreach ($list as $key => $value) {
                //查询情绪详情
                $mood = Db::name('think_mood')
                    ->where(['at_id' => $value['id']])
                    ->field('mood,fraction')
                    ->order('id')
                    ->select()->toArray();
                $list[$key]['mood'] = $mood;
                //查询自动思维
                $think = Db::name('think_think')
                    ->where(['at_id' => $value['id']])
                    ->field('think,fraction')
                    ->order('id')
                    ->select()->toArray();
                $list[$key]['think'] = $think;
                if ($value['stime']) {
                    $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                }
                if ($value['etime']) {
                    $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                }
                $list[$key]['course'] = 'S2';
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
                ->where('course', 2)
                ->join('cm_auto_thinking b', 'a.open_id = b.open_id')
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

    //导出S2-自动思维记录表
    public function excel_auto_think()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->where('course', 2)
            ->join('cm_auto_thinking b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime,b.situation,b.id')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        foreach ($list as $key => $value) {
            //查询情绪详情
            $mood = Db::name('think_mood')
                ->where(['at_id' => $value['id']])
                ->field('mood,fraction')
                ->order('id')
                ->select()->toArray();
            $list[$key]['mood'] = $mood;
            //查询自动思维
            $think = Db::name('think_think')
                ->where(['at_id' => $value['id']])
                ->field('think,fraction')
                ->order('id')
                ->select()->toArray();
            $list[$key]['think'] = $think;

            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S2';
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

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '开始时间', '结束时间', '时长', '情境', '情绪', '情绪平分', '自动思维', '思维平分');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(15);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(22);
        $PHPSheet->getColumnDimension('L')->setWidth(20);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(15);

        //数据
        $all = 2;
        foreach ($list as $k => $v) {
            $row = $all;
            if (count($v['mood']) >= count($v['think'])) {
                $n = count($v['mood']);
            } else {
                $n = count($v['think']);
            }
            $s = $all;
            $all += $n;
            $e = $all - 1;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 10 || $j == 12) {
                    if ($v['mood'] && $v['think']) {
                        $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                        $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                }
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
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['situation']);
                $m = 1;
                for ($i = 0; $i < count($v['mood']); $i++) {
                    $rows = $row + $i;
                    if ($j == 10 || $j == 11) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('L' . $rows, ' ' . $v['mood'][$i]['mood']);
                    $PHPSheet->setCellValue('M' . $rows, ' ' . $v['mood'][$i]['fraction']);
                    $m++;
                }

                for ($i = 0; $i < count($v['think']); $i++) {
                    $rowd = $row + $i;
                    if ($j == 13) {
                        $PHPSheet->getStyle($letter[$j] . $rowd)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rowd . ':' . $letter[$j] . $rowd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('N' . $rowd, ' ' . $v['think'][$i]['think']);
                    $PHPSheet->setCellValue('O' . $rowd, ' ' . $v['think'][$i]['fraction']);
                }
            }
            ob_flush();
            flush();
        }
        $filename = 'S2-自动思维记录表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S3-一周回顾
    public function activity_record_answer()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_activity_answer' . 'activity_record_answer');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_activity_answer b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.bad_time,b.bad_do_what,b.good_time,b.good_do_what,b.good_activity,b.want_activity,b.stime,b.etime,b.ltime')
                ->page($page, $limit)
                ->order('a.id,b.etime')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                if ($value['stime']) {
                    $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                }
                if ($value['etime']) {
                    $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                }
                $list[$key]['course'] = 'S3';
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
                if ($value['good_do_what']) {
                    $good_do_what_arr = explode('||', $value['good_do_what']);
                    $list[$key]['good_do_what_arr'] = $good_do_what_arr;
                } else {
                    $list[$key]['good_do_what_arr'] = [];
                }

                if ($value['good_activity']) {
                    $good_activity_arr = explode('||', $value['good_activity']);
                    $list[$key]['good_activity_arr'] = $good_activity_arr;
                } else {
                    $list[$key]['good_activity_arr'] = [];
                }

                if ($value['want_activity']) {
                    $want_activity_arr = explode('||', $value['want_activity']);
                    $list[$key]['want_activity_arr'] = $want_activity_arr;
                } else {
                    $list[$key]['want_activity_arr'] = [];
                }
            }
            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_activity_answer b', 'a.open_id = b.open_id')
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

    //导出S3-一周回顾
    public function excel_activity_record_answer()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_activity_answer b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.bad_time,b.bad_do_what,b.good_time,b.good_do_what,b.good_activity,b.want_activity,b.stime,b.etime,b.ltime')
            ->page($page, $limit)
            ->order('a.id,b.etime')
            ->select()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S3';
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
            $good_do_what_arr = explode('||', $value['good_do_what']);
            $list[$key]['good_do_what_arr'] = $good_do_what_arr;

            $good_activity_arr = explode('||', $value['good_activity']);
            $list[$key]['good_activity_arr'] = $good_activity_arr;

            $want_activity_arr = explode('||', $value['want_activity']);
            $list[$key]['want_activity_arr'] = $want_activity_arr;
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '开始时间', '结束时间', '时长', '1', '2', '3', '4', '5', '6');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(15);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(20);
        $PHPSheet->getColumnDimension('L')->setWidth(20);
        $PHPSheet->getColumnDimension('M')->setWidth(20);
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(20);
        $PHPSheet->getColumnDimension('P')->setWidth(20);

        //数据
        $all = 2;
        foreach ($list as $k => $v) {
            $row = $all;
            if (count($v['good_do_what_arr']) >= count($v['good_activity_arr'])) {
                if (count($v['good_do_what_arr']) >= count($v['want_activity_arr'])) {
                    $n = count($v['good_do_what_arr']);
                } else {
                    $n = count($v['want_activity_arr']);
                }
            } else {
                if (count($v['good_activity_arr']) >= count($v['want_activity_arr'])) {
                    $n = count($v['good_activity_arr']);
                } else {
                    $n = count($v['want_activity_arr']);
                }
            }
            $s = $all;
            $all += $n;
            $e = $all - 1;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 13) {
                    $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                    $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
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
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['bad_time']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['bad_do_what']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['good_time']);

                for ($i = 0; $i < count($v['good_do_what_arr']); $i++) {
                    $rows = $row + $i;
                    if ($j == 13) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('N' . $rows, ' ' . $v['good_do_what_arr'][$i]);
                }

                for ($n = 0; $n < count($v['good_activity_arr']); $n++) {
                    $rowd = $row + $n;
                    if ($j == 14) {
                        $PHPSheet->getStyle($letter[$j] . $rowd)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rowd . ':' . $letter[$j] . $rowd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('O' . $rowd, ' ' . $v['good_activity_arr'][$n]);
                }

                for ($x = 0; $x < count($v['want_activity_arr']); $x++) {
                    $rowg = $row + $x;
                    if ($j == 15) {
                        $PHPSheet->getStyle($letter[$j] . $rowg)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rowg . ':' . $letter[$j] . $rowd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('P' . $rowg, ' ' . $v['want_activity_arr'][$x]);
                }
            }
            ob_flush();
            flush();
        }
        $filename = 'S3-一周回顾' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S3-活动宝箱
    public function activity_keys()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $cachekey = md5($number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_user_activity_keys' . 'activity_keys');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_user_activity_keys b', 'a.open_id = b.open_id')
                ->field('b.open_id,a.number,a.name,a.wx_phone,a.type')
                ->group('b.open_id')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();
            $activity_arr = [];
            foreach ($list as $key => $value) {
                $list[$key]['course'] = 'S3';
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
                //查询该用户的活动
                $activity = Db::name('user_activity_keys')->where(['open_id' => $value['open_id']])->where('activity', '<>', '')->field('activity')->select()->toArray();
                foreach ($activity as $k => $v) {
                    $activity_arr[$k] = $v['activity'];
                }
                $list[$key]['activity_arr'] = implode(';', $activity_arr);
            }
            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_user_activity_keys b', 'a.open_id = b.open_id')
                ->field('b.open_id,a.number,a.name,a.wx_phone,a.type')
                ->group('b.open_id')
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

    //导出S3-活动宝箱
    public function excel_activity_keys()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_user_activity_keys b', 'a.open_id = b.open_id')
            ->field('b.open_id,a.number,a.name,a.wx_phone,a.type')
            ->group('b.open_id')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        $activity_arr = [];
        foreach ($list as $key => $value) {
            $list[$key]['course'] = 'S3';
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
            //查询该用户的活动
            $activity = Db::name('user_activity_keys')->where(['open_id' => $value['open_id']])->where('activity', '<>', '')->field('activity')->select()->toArray();
            foreach ($activity as $k => $v) {
                $activity_arr[$k] = $v['activity'];
            }
            $list[$key]['activity_arr'] = implode(';', $activity_arr);
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '活动宝箱');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(50);

        //数据
        $row = 2;
        foreach ($list as $k => $v) {

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
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['activity_arr']);
            }
            $row++;
            ob_flush();
            flush();
        }
        $filename = 'S3-一周回顾' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S3-活动安排
    public function activity_arrange()
    {
        $where[] = ['id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'activity_plan' . 'activity_arrange');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {

            $list = Db::name('activity_plan')->where($where)->group('stime')->field('open_id,stime')->page($page, $limit)->select()->toArray();

            foreach ($list as $key => $value) {
                $arrli[] = $value['open_id'];
            }

            $aruArr =  array_unique($arrli);

            foreach ($aruArr as $key => $value) {
                $userArr = Db::name('user')->where('open_id', $value)->field('id,name,wx_phone,type,number')->find();
                if ($userArr['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $userArr['type'] = 'P-患者';
                } elseif ($userArr['type'] == '2') {
                    $userArr['type'] = 'H-高危人群';
                } elseif ($userArr['type'] == '3') {
                    $userArr['type'] = 'R-缓解期患者';
                } elseif ($userArr['type'] == '4') {
                    $userArr['type'] = '高危-分数';
                } elseif ($userArr['type'] == '5') {
                    $userArr['type'] = '患者-B1';
                } elseif ($userArr['type'] == '6') {
                    $userArr['type'] = '缓解期-B2';
                } elseif ($userArr['type'] == '7') {
                    $userArr['type'] = 'P2-患者轻度';
                } elseif ($userArr['type'] == '8') {
                    $userArr['type'] = 'P3-患者中度';
                } elseif ($userArr['type'] == '9') {
                    $userArr['type'] = 'P4-患者重度';
                } elseif ($userArr['type'] == '12') {
                    $userArr['type'] = 'P5-自曝患者';
                } else {
                    $userArr['type'] = '游客';
                }
                $haarr[$value] = $userArr;
            }

            foreach ($list as $key => $value) {
                $actarr = Db::name('activity_plan')->where(['open_id' => $value['open_id'], 'stime' => $value['stime']])->field('open_id,date,week,activity,stime,etime,ltime')->select()->toArray();
                $uearr = [];
                foreach ($actarr as $key1 => $value1) {
                    $uearr['open_id'] = $value1['open_id'];
                    $uearr['stime'] = $value1['stime'] ? date('Y-m-d H:i', $value1['stime']) : '';
                    $uearr['etime'] = $value1['etime'] ? date('Y-m-d H:i', $value1['etime']) : '';
                    $uearr['ltime'] = $value1['ltime'];
                    $uearr['couse_number'] = 'S3';
                    $uearr['info'][$value1['week']][] = [
                        'time' => date('Y-m-d H:i', $value1['date']),
                        'activity' => $value1['activity']
                    ];
                }

                $wearr[] =  array_merge($haarr[$uearr['open_id']], $uearr);
            }

            // $list = Db::name('user')->alias('a')->where($where)
            //     ->join('cm_activity_plan b', 'a.open_id=b.open_id')
            //     ->field('a.id,a.open_id,a.name,a.wx_phone,a.type,a.number,b.stime,b.etime,b.ltime,b.week,b.date,b.activity')
            //     ->page($page, $limit)
            //     ->order('a.id')
            //     ->select()->toArray();


            // foreach ($list as $key => $value) {
            //     if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
            //         $value['type'] = '患者';
            //     } elseif ($value['type'] == '2') {
            //         $value['type'] = '高危';
            //     } elseif ($value['type'] == '3') {
            //         $value['type'] = '缓解期';
            //     } elseif ($value['type'] == '4') {
            //         $value['type'] = '高危-分数';
            //     } elseif ($value['type'] == '5') {
            //         $value['type'] = '患者-B1';
            //     } elseif ($value['type'] == '6') {
            //         $value['type'] = '缓解期-B2';
            //     } else {
            //         $value['type'] = '游客';
            //     }
            //     $value['stime'] = date('Y-m-d H:i', $value['stime']);
            //     $value['etime'] = date('Y-m-d H:i', $value['etime']);


            //     $arr[$value['id']][$value['stime']]['open_id'] = $value['open_id'];
            //     $arr[$value['id']][$value['stime']]['name'] = $value['name'];
            //     $arr[$value['id']][$value['stime']]['wx_phone'] = $value['wx_phone'];
            //     $arr[$value['id']][$value['stime']]['number'] = $value['number'];
            //     $arr[$value['id']][$value['stime']]['type'] = $value['type'];
            //     $arr[$value['id']][$value['stime']]['couse_number'] = 'S3';

            //     $arr[$value['id']][$value['stime']]['stime'] = $value['stime'];
            //     $arr[$value['id']][$value['stime']]['etime'] = $value['etime'];
            //     $arr[$value['id']][$value['stime']]['ltime'] = $value['ltime'];

            // $arr[$value['id']]['info'][$value['etime']][$value['week']][] = [
            //     'time' => date('Y-m-d', $value['date']) . ' ' . $value['time'] . ':' . '00',
            //     'activity' => $value['activity']
            // ];

            //     $arr[$value['id']][$value['stime']]['info'][$value['week']][] = [
            //         'time' => date('Y-m-d H:i', $value['date']),
            //         'activity' => $value['activity']
            //     ];
            // }

            // foreach ($arr as $key => $value) {
            //     foreach ($value as $k => $v) {
            //         $rsArr[] = $v;
            //     }
            // }

            $total = count($list);

            // $a = Db::name('course_record')->getLastSql();
            // echo $a;die;
            // dump($total);die;

            $page_total = ceil($total / $limit);

            $return = [
                'list' => $wearr,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //导出S3-活动安排
    public function excel_activity_arrange()
    {
        $where[] = ['id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $list = Db::name('activity_plan')->where($where)->group('stime')->page($page, $limit)->field('open_id,stime')->select()->toArray();

        foreach ($list as $key => $value) {
            $arrli[] = $value['open_id'];
        }

        $aruArr =  array_unique($arrli);

        foreach ($aruArr as $key => $value) {
            $userArr = Db::name('user')->where('open_id', $value)->field('id,name,wx_phone,type,number')->find();
            if ($userArr['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $userArr['type'] = 'P-患者';
            } elseif ($userArr['type'] == '2') {
                $userArr['type'] = 'H-高危人群';
            } elseif ($userArr['type'] == '3') {
                $userArr['type'] = 'R-缓解期患者';
            } elseif ($userArr['type'] == '4') {
                $userArr['type'] = '高危-分数';
            } elseif ($userArr['type'] == '5') {
                $userArr['type'] = '患者-B1';
            } elseif ($userArr['type'] == '6') {
                $userArr['type'] = '缓解期-B2';
            } elseif ($userArr['type'] == '7') {
                $userArr['type'] = 'P2-患者轻度';
            } elseif ($userArr['type'] == '8') {
                $userArr['type'] = 'P3-患者中度';
            } elseif ($userArr['type'] == '9') {
                $userArr['type'] = 'P4-患者重度';
            } elseif ($userArr['type'] == '12') {
                $userArr['type'] = 'P5-自曝患者';
            } else {
                $userArr['type'] = '游客';
            }

            $haarr[$value] = $userArr;
        }

        foreach ($list as $key => $value) {
            $actarr = Db::name('activity_plan')->where(['open_id' => $value['open_id'], 'stime' => $value['stime']])->field('open_id,date,week,activity,stime,etime,ltime')->select()->toArray();
            $uearr = [];
            foreach ($actarr as $key1 => $value1) {
                $uearr['open_id'] = $value1['open_id'];
                $uearr['stime'] = $value1['stime'] ? date('Y-m-d H:i', $value1['stime']) : '';
                $uearr['etime'] = $value1['etime'] ? date('Y-m-d H:i', $value1['etime']) : '';
                $uearr['ltime'] = $value1['ltime'];
                $uearr['couse_number'] = 'S3';
                $uearr['info'][$value1['week']][] = [
                    'time' => date('Y-m-d H:i', $value1['date']),
                    'activity' => $value1['activity']
                ];
            }

            $wearr[] =  array_merge($haarr[$uearr['open_id']], $uearr);
        }

        // $list = Db::name('user')->alias('a')->where($where)
        //     ->join('cm_activity_plan b', 'a.open_id=b.open_id')
        //     ->field('a.id,a.open_id,a.name,a.wx_phone,a.type,a.number,b.stime,b.etime,b.ltime,b.week,b.date,b.activity,b.time')
        //     // ->page($page, $limit)
        //     ->order('a.id')
        //     ->select()->toArray();

        // foreach ($list as $key => $value) {
        //     if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
        //         $value['type'] = '患者';
        //     } elseif ($value['type'] == '2') {
        //         $value['type'] = '高危';
        //     } elseif ($value['type'] == '3') {
        //         $value['type'] = '缓解期';
        //     } elseif ($value['type'] == '4') {
        //         $value['type'] = '高危-分数';
        //     } elseif ($value['type'] == '5') {
        //         $value['type'] = '患者-B1';
        //     } elseif ($value['type'] == '6') {
        //         $value['type'] = '缓解期-B2';
        //     } else {
        //         $value['type'] = '游客';
        //     }
        //     $value['stime'] = date('Y-m-d H:i', $value['stime']);
        //     $value['etime'] = date('Y-m-d H:i', $value['etime']);


        //     $arr[$value['id']][$value['stime']]['open_id'] = $value['open_id'];
        //     $arr[$value['id']][$value['stime']]['name'] = $value['name'];
        //     $arr[$value['id']][$value['stime']]['wx_phone'] = $value['wx_phone'];
        //     $arr[$value['id']][$value['stime']]['number'] = $value['number'];
        //     $arr[$value['id']][$value['stime']]['type'] = $value['type'];
        //     $arr[$value['id']][$value['stime']]['couse_number'] = 'S3';

        //     $arr[$value['id']][$value['stime']]['stime'] = $value['stime'];
        //     $arr[$value['id']][$value['stime']]['etime'] = $value['etime'];
        //     $arr[$value['id']][$value['stime']]['ltime'] = $value['ltime'];

        //     // $arr[$value['id']]['info'][$value['etime']][$value['week']][] = [
        //     //     'time' => date('Y-m-d', $value['date']) . ' ' . $value['time'] . ':' . '00',
        //     //     'activity' => $value['activity']
        //     // ];

        //     $arr[$value['id']][$value['stime']]['info'][$value['week']][] = [
        //         'time' => date('Y-m-d', $value['date']) . ' ' . $value['time'] . ':' . '00',
        //         'activity' => $value['activity']
        //     ];
        // }
        // foreach ($arr as $key => $value) {
        //     foreach ($value as $k => $v) {
        //         $rsArr[] = $v;
        //     }
        // }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();

        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P');
        $sheet_title = array('用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '开始时间', '结束时间', '时长', '周一', '周二', '周三', '周四', '周五', '周六', '周日');


        $PHPSheet->getStyle('A1:W1')->getFont()->setSize(13)->setBold(true);


        $PHPSheet->mergeCells('A1:A2')->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码')->mergeCells('B1:B2');
        $PHPSheet->setCellValue('C1', '姓名')->mergeCells('C1:C2');
        $PHPSheet->setCellValue('D1', '微信手机号')->mergeCells('D1:D2');
        $PHPSheet->setCellValue('E1', '患者分类')->mergeCells('E1:E2');
        $PHPSheet->setCellValue('F1', '课程编号')->mergeCells('F1:F2');
        $PHPSheet->setCellValue('G1', '开始时间')->mergeCells('G1:G2');
        $PHPSheet->setCellValue('H1', '结束时间')->mergeCells('H1:H2');
        $PHPSheet->setCellValue('I1', '时长')->mergeCells('I1:I2');
        $PHPSheet->setCellValue('J1', '周一')->mergeCells('J1:K1');
        $PHPSheet->setCellValue('J2', '时间');
        $PHPSheet->setCellValue('K2', '活动');
        $PHPSheet->setCellValue('L1', '周二')->mergeCells('L1:M1');
        $PHPSheet->setCellValue('L2', '时间');
        $PHPSheet->setCellValue('M2', '活动');
        $PHPSheet->setCellValue('N1', '周三')->mergeCells('N1:O1');
        $PHPSheet->setCellValue('N2', '时间');
        $PHPSheet->setCellValue('O2', '活动');
        $PHPSheet->setCellValue('P1', '周四')->mergeCells('P1:Q1');
        $PHPSheet->setCellValue('P2', '时间');
        $PHPSheet->setCellValue('Q2', '活动');
        $PHPSheet->setCellValue('R1', '周五')->mergeCells('R1:S1');
        $PHPSheet->setCellValue('R2', '时间');
        $PHPSheet->setCellValue('S2', '活动');
        $PHPSheet->setCellValue('T1', '周六')->mergeCells('T1:U1');
        $PHPSheet->setCellValue('T2', '时间');
        $PHPSheet->setCellValue('U2', '活动');
        $PHPSheet->setCellValue('V1', '周日')->mergeCells('V1:W1');
        $PHPSheet->setCellValue('V2', '时间');
        $PHPSheet->setCellValue('W2', '活动');

        $PHPSheet->getColumnDimension('A')->setWidth(25);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(12);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(15);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(20);
        $PHPSheet->getColumnDimension('H')->setWidth(20);
        $PHPSheet->getColumnDimension('I')->setWidth(20);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(15);
        $PHPSheet->getColumnDimension('L')->setWidth(20);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(15);
        $PHPSheet->getColumnDimension('P')->setWidth(20);
        $PHPSheet->getColumnDimension('Q')->setWidth(15);
        $PHPSheet->getColumnDimension('R')->setWidth(20);
        $PHPSheet->getColumnDimension('S')->setWidth(15);
        $PHPSheet->getColumnDimension('T')->setWidth(20);
        $PHPSheet->getColumnDimension('U')->setWidth(15);
        $PHPSheet->getColumnDimension('V')->setWidth(20);
        $PHPSheet->getColumnDimension('W')->setWidth(15);

        $hCount = 0;
        $zCount = 0;
        $row = 3;
        foreach ($wearr as $key => $value) {

            foreach ($value['info'] as $k => $v) {
                if (($hCount + 1) < count($v)) {
                    $hCount = count($v) - 1;
                }


                if ($k == '周一') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('J' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('K' . $row1, $v1['activity']);
                    }
                }
                if ($k == '周二') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('L' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('M' . $row1, $v1['activity']);
                    }
                }
                if ($k == '周三') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('N' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('O' . $row1, $v1['activity']);
                    }
                }
                if ($k == '周四') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('P' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('Q' . $row1, $v1['activity']);
                    }
                }
                if ($k == '周五') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('R' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('S' . $row1, $v1['activity']);
                    }
                }
                if ($k == '周六') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('T' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('U' . $row1, $v1['activity']);
                    }
                }
                if ($k == '周日') {
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $k1;
                        $PHPSheet->setCellValue('V' . $row1, $v1['time']);
                        $PHPSheet->setCellValue('W' . $row1, $v1['activity']);
                    }
                }
            }

            $zCount += $hCount + $key + 1;


            $PHPSheet->setCellValue('A' . $row, $value['open_id'])->mergeCells('A' . $row . ':' . 'A' . ($row + $hCount));
            $PHPSheet->setCellValue('B' . $row, $value['number'])->mergeCells('B' . $row . ':' . 'B' . ($row + $hCount));
            $PHPSheet->setCellValue('C' . $row, $value['name'])->mergeCells('C' . $row . ':' . 'C' . ($row + $hCount));
            $PHPSheet->setCellValue('D' . $row, $value['wx_phone'])->mergeCells('D' . $row . ':' . 'D' . ($row + $hCount));
            $PHPSheet->setCellValue('E' . $row, $value['type'])->mergeCells('E' . $row . ':' . 'E' . ($row + $hCount));
            $PHPSheet->setCellValue('F' . $row, $value['couse_number'])->mergeCells('F' . $row . ':' . 'F' . ($row + $hCount));
            $PHPSheet->setCellValue('G' . $row, $value['stime'])->mergeCells('G' . $row . ':' . 'G' . ($row + $hCount));
            $PHPSheet->setCellValue('H' . $row, $value['etime'])->mergeCells('H' . $row . ':' . 'H' . ($row + $hCount));
            $PHPSheet->setCellValue('I' . $row, $value['ltime'])->mergeCells('I' . $row . ':' . 'I' . ($row + $hCount));

            $row = $row + $hCount + 1;

            ob_flush();
            flush();
        }
        //设置水平居中
        $PHPSheet->getStyle('A1:W' . ($zCount + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置垂直居中
        $PHPSheet->getStyle('A1:W' . ($zCount + 1))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);;

        $filename = '活动安排' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S3-识别误区
    public function identify_myth()
    {

        $where[] = ['a.id', '>', 0];
        $stime = input('post.stime');
        $etime = input('post.etime');

        if ($stime && empty($etime)) {
            $where['b.etime'] = ['>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where['b.etime'] = ['<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where['b.etime'] = ['between', [strtotime($stime), strtotime($etime)]];
        }

        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'Exercise' . 'identify_myth');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {

            $list = Db::name('user')->alias('a')->where($where)->where('course', 3)->join('cm_auto_thinking b', 'a.open_id=b.open_id')->field(['a.id', 'a.open_id', 'a.name', 'a.wx_phone', 'a.type', 'a.number', 'b.id as th_id', 'b.open_id', 'b.course', 'b.situation', 'b.stime', 'b.etime', 'b.ltime'])->page($page, $limit)->select()->toArray();
            foreach ($list as $key => $value) {
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$key]['type'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list[$key]['type'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list[$key]['type'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list[$key]['type'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list[$key]['type'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list[$key]['type'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list[$key]['type'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list[$key]['type'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list[$key]['type'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list[$key]['type'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list[$key]['type'] = 'N-普通人群';
                } else {
                    $list[$key]['type'] = '游客';
                }
                $list[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
                $list[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
                $list[$key]['couse_number'] = 'S3';
                $mood = Db::name('think_mood')->where('at_id', $value['th_id'])->select()->toArray();
                $think = Db::name('think_think')->where('at_id', $value['th_id'])->select()->toArray();
                $list[$key]['info']['mood'] = $mood;
                $list[$key]['info']['think'] = $think;
            }
            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->where('course', 3)
                ->join('cm_auto_thinking b', 'a.open_id=b.open_id')
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

    //导出S3-识别误区
    public function excel_identify_myth()
    {
        $where[] = ['a.id', '>', 0];
        $stime = input('post.stime');
        $etime = input('post.etime');

        if ($stime && empty($etime)) {
            $where['b.etime'] = ['>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where['b.etime'] = ['<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where['b.etime'] = ['between', [strtotime($stime), strtotime($etime)]];
        }

        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $list = Db::name('user')->alias('a')->where($where)->where('course', 3)->join('cm_auto_thinking b', 'a.open_id=b.open_id')->field(['a.id', 'a.open_id', 'a.name', 'a.wx_phone', 'a.type', 'a.number', 'b.id as th_id', 'b.open_id', 'b.course', 'b.situation', 'b.stime', 'b.etime', 'b.ltime'])->page($page, $limit)->select()->toArray();


        foreach ($list as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$key]['type'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list[$key]['type'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list[$key]['type'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list[$key]['type'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list[$key]['type'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list[$key]['type'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list[$key]['type'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list[$key]['type'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list[$key]['type'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list[$key]['type'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list[$key]['type'] = 'N-普通人群';
            } else {
                $list[$key]['type'] = '游客';
            }
            $list[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
            $list[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
            $list[$key]['couse_number'] = 'S3';
            $mood = Db::name('think_mood')->where('at_id', $value['th_id'])->select()->toArray();
            $think = Db::name('think_think')->where('at_id', $value['th_id'])->select()->toArray();
            $list[$key]['info']['mood'] = $mood;
            $list[$key]['info']['think'] = $think;
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();

        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $PHPSheet->getStyle('A1:O1')->getFont()->setSize(13)->setBold(true);

        $PHPSheet->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码');
        $PHPSheet->setCellValue('C1', '姓名');
        $PHPSheet->setCellValue('D1', '微信手机号');
        $PHPSheet->setCellValue('E1', '患者分类');
        $PHPSheet->setCellValue('F1', '课程编号');
        $PHPSheet->setCellValue('G1', '开始时间');
        $PHPSheet->setCellValue('H1', '结束时间');
        $PHPSheet->setCellValue('I1', '时长');
        $PHPSheet->setCellValue('J1', '情境');
        $PHPSheet->setCellValue('K1', '情绪');
        $PHPSheet->setCellValue('L1', '情绪评分');
        $PHPSheet->setCellValue('M1', '自动思维');
        $PHPSheet->setCellValue('N1', '思维评分');
        $PHPSheet->setCellValue('O1', '思维误区');

        $PHPSheet->getColumnDimension('A')->setWidth(25);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(12);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(15);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(12);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(10);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(25);
        $PHPSheet->getColumnDimension('N')->setWidth(12);
        $PHPSheet->getColumnDimension('O')->setWidth(15);

        $hCount = 0;
        $zCount = 0;
        $row = 2;
        foreach ($list as $key => $value) {
            foreach ($value['info'] as $k => $v) {
                if (($hCount + 1) < count($v)) {
                    $hCount = count($v) - 1;
                }

                if ($k == 'mood') {
                    $i = 0;
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $i;
                        $PHPSheet->setCellValue('K' . $row1, $v1['mood']);
                        $PHPSheet->setCellValue('L' . $row1, $v1['fraction']);
                        $i++;
                    }
                }
                if ($k == 'think') {
                    $j = 0;
                    foreach ($v as $k1 => $v1) {
                        $row1 = $row + $j;
                        $PHPSheet->setCellValue('M' . $row1, $v1['think']);
                        $PHPSheet->setCellValue('N' . $row1, $v1['fraction']);
                        $PHPSheet->setCellValue('O' . $row1, $v1['misunderstanding']);
                        $j++;
                    }
                }
            }
            $zCount += $hCount + $key + 1;
            $PHPSheet->setCellValue('A' . $row, $value['open_id'])->mergeCells('A' . $row . ':' . 'A' . ($row + $hCount));
            $PHPSheet->setCellValue('B' . $row, $value['number'])->mergeCells('B' . $row . ':' . 'B' . ($row + $hCount));
            $PHPSheet->setCellValue('C' . $row, $value['name'])->mergeCells('C' . $row . ':' . 'C' . ($row + $hCount));
            $PHPSheet->setCellValue('D' . $row, $value['wx_phone'])->mergeCells('D' . $row . ':' . 'D' . ($row + $hCount));
            $PHPSheet->setCellValue('E' . $row, $value['type'])->mergeCells('E' . $row . ':' . 'E' . ($row + $hCount));
            $PHPSheet->setCellValue('F' . $row, $value['couse_number'])->mergeCells('F' . $row . ':' . 'F' . ($row + $hCount));
            $PHPSheet->setCellValue('G' . $row, $value['stime'])->mergeCells('G' . $row . ':' . 'G' . ($row + $hCount));
            $PHPSheet->setCellValue('H' . $row, $value['etime'])->mergeCells('H' . $row . ':' . 'H' . ($row + $hCount));
            $PHPSheet->setCellValue('I' . $row, $value['ltime'])->mergeCells('I' . $row . ':' . 'I' . ($row + $hCount));
            $PHPSheet->setCellValue('J' . $row, $value['situation'])->mergeCells('J' . $row . ':' . 'J' . ($row + $hCount));
            //自动换行
            $PHPSheet->getStyle('J' . $row)->getAlignment()->setWrapText(TRUE);

            $row = $row + $hCount + 1;
        }

        //设置水平居中
        $PHPSheet->getStyle('A1:O' . ($zCount + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置垂直居中
        $PHPSheet->getStyle('A1:O' . ($zCount + 1))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);



        $filename = '识别误区' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

    }

    //S3误区比例
    public function myth_proportion()
    {
        $where[] = ['a.id', '>', 0];
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
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'Exercise' . 'myth_proportion');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_misunderstanding_ratio b', 'a.open_id=b.open_id')
                ->join('cm_misunderstanding_info c', 'b.id=c.misun_id')
                ->field(['a.id', 'a.open_id', 'a.name', 'a.wx_phone', 'a.type', 'a.number', 'b.id as mi_id', 'b.stime', 'b.etime', 'b.ltime', 'c.think_error', 'c.ratio'])
                ->select()->toArray();

            $arr = [];
            foreach ($list as $key => $value) {
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $value['type'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $value['type'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $value['type'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $value['type'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $value['type'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $value['type'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $value['type'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $value['type'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $value['type'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $value['type'] = 'P5-自曝患者';
                } else {
                    $value['type'] = '游客';
                }
                $value['stime'] = date('Y-m-d H:i', $value['stime']);
                $value['etime'] = date('Y-m-d H:i', $value['etime']);

                $arr[$value['id']]['open_id'] = $value['open_id'];
                $arr[$value['id']]['number'] = $value['number'];
                $arr[$value['id']]['name'] = $value['name'];
                $arr[$value['id']]['wx_phone'] = $value['wx_phone'];
                $arr[$value['id']]['type'] = $value['type'];
                $arr[$value['id']]['couse_number'] = 'S3';

                $arr[$value['id']]['info'][$value['mi_id']]['err'][] = [
                    'think_error' => $value['think_error'],
                    'ratio' => $value['ratio']
                ];

                $arr[$value['id']]['info'][$value['mi_id']]['time'] = [
                    'stime' => $value['stime'],
                    'etime' => $value['etime'],
                    'ltime' => $value['ltime'],
                ];
            }
            $resArr = [];
            foreach ($arr as $k => $v) {
                foreach ($v['info'] as $k1 => $v1) {
                    $resArr[] = [
                        'open_id' => $v['open_id'],
                        'number' => $v['number'],
                        'name' => $v['name'],
                        'wx_phone' => $v['wx_phone'],
                        'type' => $v['type'],
                        'couse_number' => $v['couse_number'],
                        'stime' => $v1['time']['stime'],
                        'etime' => $v1['time']['etime'],
                        'ltime' => $v1['time']['ltime'],
                        'info' => $v1['err'],
                    ];
                }
            }


            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_misunderstanding_ratio b', 'a.open_id=b.open_id')
                ->field('a.open_id')
                ->count();


            $page_total = ceil($total / $limit);
            $return = [
                'list' => $resArr,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //导出S3误区比例
    public function excel_myth_proportion()
    {
        $where[] = ['a.id', '>', 0];
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
            $where[] = ['a.name', 'like', $number . '%'];
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_misunderstanding_ratio b', 'a.open_id=b.open_id')
            ->join('cm_misunderstanding_info c', 'b.id=c.misun_id')
            ->field(['a.id', 'a.open_id', 'a.name', 'a.wx_phone', 'a.type', 'a.number', 'b.id as mi_id', 'b.stime', 'b.etime', 'b.ltime', 'c.think_error', 'c.ratio'])
            ->select()->toArray();


        foreach ($list as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $value['type'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $value['type'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $value['type'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $value['type'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $value['type'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $value['type'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $value['type'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $value['type'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $value['type'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $value['type'] = 'P5-自曝患者';
            } else {
                $value['type'] = '游客';
            }

            $value['stime'] = date('Y-m-d H:i', $value['stime']);
            $value['etime'] = date('Y-m-d H:i', $value['etime']);

            $arr[$value['id']]['open_id'] = $value['open_id'];
            $arr[$value['id']]['number'] = $value['number'];
            $arr[$value['id']]['name'] = $value['name'];
            $arr[$value['id']]['wx_phone'] = $value['wx_phone'];
            $arr[$value['id']]['type'] = $value['type'];
            $arr[$value['id']]['couse_number'] = 'S3';

            $arr[$value['id']]['info'][$value['mi_id']]['err'][] = [
                'think_error' => $value['think_error'],
                'ratio' => $value['ratio']
            ];

            $arr[$value['id']]['info'][$value['mi_id']]['time'] = [
                'stime' => $value['stime'],
                'etime' => $value['etime'],
                'ltime' => $value['ltime'],
            ];
        }

        foreach ($arr as $k => $v) {
            foreach ($v['info'] as $k1 => $v1) {
                $resArr[] = [
                    'open_id' => $v['open_id'],
                    'number' => $v['number'],
                    'name' => $v['name'],
                    'wx_phone' => $v['wx_phone'],
                    'type' => $v['type'],
                    'couse_number' => $v['couse_number'],
                    'stime' => $v1['time']['stime'],
                    'etime' => $v1['time']['etime'],
                    'ltime' => $v1['time']['ltime'],
                    'info' => $v1['err'],
                ];
            }
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();

        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $PHPSheet->getStyle('A1:W1')->getFont()->setSize(13)->setBold(true);


        $PHPSheet->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码');
        $PHPSheet->setCellValue('C1', '姓名');
        $PHPSheet->setCellValue('D1', '微信手机号');
        $PHPSheet->setCellValue('E1', '患者分类');
        $PHPSheet->setCellValue('F1', '课程编号');
        $PHPSheet->setCellValue('G1', '开始时间');
        $PHPSheet->setCellValue('H1', '结束时间');
        $PHPSheet->setCellValue('I1', '时长');
        $PHPSheet->setCellValue('J1', '思维误区');
        $PHPSheet->setCellValue('K1', '比例');

        $PHPSheet->getColumnDimension('A')->setWidth(25);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(12);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(15);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(12);
        $PHPSheet->getColumnDimension('J')->setWidth(15);
        $PHPSheet->getColumnDimension('K')->setWidth(15);
        $hCount = 0;
        $zCount = 0;
        $row = 2;
        foreach ($resArr as $key => $value) {
            $hCount = count($value['info']) - 1;
            $zCount += $hCount + $key + 1;

            $PHPSheet->setCellValue('A' . $row, $value['open_id'])->mergeCells('A' . $row . ':' . 'A' . ($row + $hCount));
            $PHPSheet->setCellValue('B' . $row, $value['number'])->mergeCells('B' . $row . ':' . 'B' . ($row + $hCount));
            $PHPSheet->setCellValue('C' . $row, $value['name'])->mergeCells('C' . $row . ':' . 'C' . ($row + $hCount));
            $PHPSheet->setCellValue('D' . $row, $value['wx_phone'])->mergeCells('D' . $row . ':' . 'D' . ($row + $hCount));
            $PHPSheet->setCellValue('E' . $row, $value['type'])->mergeCells('E' . $row . ':' . 'E' . ($row + $hCount));
            $PHPSheet->setCellValue('F' . $row, $value['couse_number'])->mergeCells('F' . $row . ':' . 'F' . ($row + $hCount));
            $PHPSheet->setCellValue('G' . $row, $value['stime'])->mergeCells('G' . $row . ':' . 'G' . ($row + $hCount));
            $PHPSheet->setCellValue('H' . $row, $value['etime'])->mergeCells('H' . $row . ':' . 'H' . ($row + $hCount));
            $PHPSheet->setCellValue('I' . $row, $value['ltime'])->mergeCells('I' . $row . ':' . 'I' . ($row + $hCount));
            foreach ($value['info'] as $k => $v) {
                $row1 = $row + $k;
                $PHPSheet->setCellValue('J' . $row1, $v['think_error']);
                $PHPSheet->setCellValue('K' . $row1, $v['ratio']);
            }

            //自动换行
            // $PHPSheet->getStyle('J' . $row)->getAlignment()->setWrapText(TRUE);

            $row = $row + $hCount + 1;
        }

        //设置水平居中
        $PHPSheet->getStyle('A1:K' . ($zCount + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置垂直居中
        $PHPSheet->getStyle('A1:K' . ($zCount + 1))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $filename = '误区比例' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
    //S3-自动思维记录
    public function auto_think_s3()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'Exercise' . 'auto_think_s3');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {

            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->where('course', 3)
                ->join('cm_auto_thinking b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime,b.situation,b.id')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();
            foreach ($list as $key => $value) {
                //查询情绪详情
                $mood = Db::name('think_mood')
                    ->where(['at_id' => $value['id']])
                    ->field('mood,fraction')
                    ->order('id')
                    ->select()->toArray();
                $list[$key]['mood'] = $mood;
                //查询自动思维
                $think = Db::name('think_think')
                    ->where(['at_id' => $value['id']])
                    ->field('think,fraction')
                    ->order('id')
                    ->select()->toArray();
                $list[$key]['think'] = $think;
                if ($value['stime']) {
                    $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                }
                if ($value['etime']) {
                    $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                }
                $list[$key]['course'] = 'S3';
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
                ->where('course', 3)
                ->join('cm_auto_thinking b', 'a.open_id = b.open_id')
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

    //导出S3-自动思维记录表
    public function excel_auto_think_s3()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->where('course', 3)
            ->join('cm_auto_thinking b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime,b.situation,b.id')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        foreach ($list as $key => $value) {
            //查询情绪详情
            $mood = Db::name('think_mood')
                ->where(['at_id' => $value['id']])
                ->field('mood,fraction')
                ->order('id')
                ->select()->toArray();
            $list[$key]['mood'] = $mood;
            //查询自动思维
            $think = Db::name('think_think')
                ->where(['at_id' => $value['id']])
                ->field('think,fraction')
                ->order('id')
                ->select()->toArray();
            $list[$key]['think'] = $think;

            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S3';
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

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '开始时间', '结束时间', '时长', '情境', '情绪', '情绪平分', '自动思维', '思维平分');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(7);
        $PHPSheet->getColumnDimension('B')->setWidth(20);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(17);
        $PHPSheet->getColumnDimension('F')->setWidth(12);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(15);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(22);
        $PHPSheet->getColumnDimension('L')->setWidth(20);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(15);

        //数据
        $all = 2;
        foreach ($list as $k => $v) {
            $row = $all;
            if (count($v['mood']) >= count($v['think'])) {
                $n = count($v['mood']);
            } else {
                $n = count($v['think']);
            }
            $s = $all;
            $all += $n;
            $e = $all - 1;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 10 || $j == 12) {
                    if ($v['mood'] && $v['think']) {
                        $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                        $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                }
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
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['situation']);
                $m = 1;
                for ($i = 0; $i < count($v['mood']); $i++) {
                    $rows = $row + $i;
                    if ($j == 10 || $j == 11) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('L' . $rows, ' ' . $v['mood'][$i]['mood']);
                    $PHPSheet->setCellValue('M' . $rows, ' ' . $v['mood'][$i]['fraction']);
                    $m++;
                }

                for ($i = 0; $i < count($v['think']); $i++) {
                    $rowd = $row + $i;
                    if ($j == 13) {
                        $PHPSheet->getStyle($letter[$j] . $rowd)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rowd . ':' . $letter[$j] . $rowd)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('N' . $rowd, ' ' . $v['think'][$i]['think']);
                    $PHPSheet->setCellValue('O' . $rowd, ' ' . $v['think'][$i]['fraction']);
                }
            }
            ob_flush();
            flush();
        }
        $filename = 'S3-自动思维记录表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //S3-活动记录
    public function s3_activity_record()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }
        $page = input('post.page', 1);
        $limit = input('post.limit', 10);
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'Exercise' . 's3_activity_record');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {

            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_s3_activity_record b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime')
                ->group('etime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                //查询该时间段下该用户填写的所有活动
                $activity = Db::name('s3_activity_record')
                    ->where(['open_id' => $value['open_id'], 'etime' => $value['etime']])
                    ->field('date,time,activity,pleasure,achievement,week')
                    ->group('date')
                    ->order('date')
                    ->select()->toArray();
                $infos = [];
                foreach ($activity as $k => $v) {
                    $info = Db::name('s3_activity_record')
                        ->where(['date' => $v['date']])
                        ->field('date,time,activity,pleasure,achievement,week')
                        ->order('time')
                        ->select()->toArray();
                    //dump($info);
                    foreach ($info as $ka => $va) {
                        $infos[$k]['date'] = date('Y/m/d', $va['date']);
                        $infos[$k]['week'] = $va['week'];
                        $infos[$k]['activity-' . $va['time']] = $va['activity'];
                        $infos[$k]['pleasure-' . $va['time']] = $va['pleasure'];
                        $infos[$k]['achievement-' . $va['time']] = $va['achievement'];
                    }
                }
                $list[$key]['info'] = $infos;
                if ($value['stime']) {
                    $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                }
                if ($value['etime']) {
                    $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                }
                $list[$key]['course'] = 'S3';
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
                ->join('cm_s3_activity_record b', 'a.open_id = b.open_id')
                ->field('a.open_id')
                ->group('etime')
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

    //导出s3-活动记录
    public function excel_s3_activity_record()
    {
        $where[] = ['a.id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['a.name', 'like', $number . '%'];
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
        //        $course= input('post.course/a',array());
        //        if($course && !in_array('10',$course)){
        //            $where['b.course'] = ['in',$course];
        //        }
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['b.etime', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['b.etime', 'between', [strtotime($stime), strtotime($etime)]];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_s3_activity_record b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime')
            ->group('etime')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        foreach ($list as $key => $value) {
            //查询该时间段下该用户填写的所有活动
            $activity = Db::name('s3_activity_record')
                ->where(['open_id' => $value['open_id'], 'etime' => $value['etime']])
                ->field('date,time,activity,pleasure,achievement,week')
                ->group('date')
                ->order('date')
                ->select()->toArray();
            $infos = [];
            foreach ($activity as $k => $v) {
                $info = Db::name('s3_activity_record')
                    ->where(['date' => $v['date']])
                    ->field('date,time,activity,pleasure,achievement,week')
                    ->order('time')
                    ->select()->toArray();
                //dump($info);
                foreach ($info as $ka => $va) {
                    $infos[$k]['date'] = date('Y/m/d', $va['date']);
                    $infos[$k]['week'] = $va['week'];
                    $infos[$k]['activity-' . $va['time']] = $va['activity'];
                    $infos[$k]['pleasure-' . $va['time']] = $va['pleasure'];
                    $infos[$k]['achievement-' . $va['time']] = $va['achievement'];
                }
            }
            $list[$key]['info'] = $infos;

            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['course'] = 'S3';
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
        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '用户分类', '课程编号', '开始时间', '结束时间', '时长', '日期', '星期', '0-1点', '愉悦度0', '成就感0', '1-2点', '愉悦度1', '成就感1', '2-3点', '愉悦度2', '成就感2', '3-4点', '愉悦度3', '成就感3', '4-5点', '愉悦度4', '成就感4', '5-6点', '愉悦度5', '成就感5', '6-7点', '愉悦度6', '成就感6', '7-8点', '愉悦度7', '成就感7', '8-9点', '愉悦度8', '成就感8', '9-10点', '愉悦度9', '成就感9', '10-11点', '愉悦度10', '成就感10', '11-12点', '愉悦度11', '成就感11', '12-13点', '愉悦度12', '成就感12', '13-14点', '愉悦度13', '成就感13', '14-15点', '愉悦度14', '成就感14', '15-16点', '愉悦度15', '成就感15', '16-17点', '愉悦度16', '成就感16', '17-18点', '愉悦度17', '成就感17', '18-19点', '愉悦度18', '成就感18', '19-20点', '愉悦度19', '成就感19', '20-21点', '愉悦度20', '成就感20', '21-22点', '愉悦度21', '成就感21', '22-23点', '愉悦度22', '成就感22', '23-24点', '愉悦度23', '成就感23');
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
        $PHPSheet->getColumnDimension('H')->setWidth(17);
        $PHPSheet->getColumnDimension('I')->setWidth(17);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(12);
        $PHPSheet->getColumnDimension('L')->setWidth(12);
        $PHPSheet->getColumnDimension('M')->setWidth(15);
        $PHPSheet->getColumnDimension('N')->setWidth(7);
        $PHPSheet->getColumnDimension('O')->setWidth(7);
        $PHPSheet->getColumnDimension('P')->setWidth(15);
        $PHPSheet->getColumnDimension('Q')->setWidth(7);
        $PHPSheet->getColumnDimension('R')->setWidth(7);
        $PHPSheet->getColumnDimension('S')->setWidth(15);
        $PHPSheet->getColumnDimension('T')->setWidth(7);
        $PHPSheet->getColumnDimension('U')->setWidth(7);
        $PHPSheet->getColumnDimension('V')->setWidth(15);
        $PHPSheet->getColumnDimension('W')->setWidth(7);
        $PHPSheet->getColumnDimension('X')->setWidth(7);
        $PHPSheet->getColumnDimension('Y')->setWidth(15);
        $PHPSheet->getColumnDimension('Z')->setWidth(7);
        $PHPSheet->getColumnDimension('AA')->setWidth(7);
        $PHPSheet->getColumnDimension('AB')->setWidth(15);
        $PHPSheet->getColumnDimension('AC')->setWidth(7);
        $PHPSheet->getColumnDimension('AD')->setWidth(7);
        $PHPSheet->getColumnDimension('AE')->setWidth(15);
        $PHPSheet->getColumnDimension('AF')->setWidth(7);
        $PHPSheet->getColumnDimension('AG')->setWidth(7);
        $PHPSheet->getColumnDimension('AH')->setWidth(15);
        $PHPSheet->getColumnDimension('AI')->setWidth(7);
        $PHPSheet->getColumnDimension('AJ')->setWidth(7);
        $PHPSheet->getColumnDimension('AK')->setWidth(15);
        $PHPSheet->getColumnDimension('AL')->setWidth(7);
        $PHPSheet->getColumnDimension('AM')->setWidth(7);
        $PHPSheet->getColumnDimension('AN')->setWidth(15);
        $PHPSheet->getColumnDimension('AO')->setWidth(7);
        $PHPSheet->getColumnDimension('AP')->setWidth(7);
        $PHPSheet->getColumnDimension('AQ')->setWidth(15);
        $PHPSheet->getColumnDimension('AR')->setWidth(7);
        $PHPSheet->getColumnDimension('AS')->setWidth(7);
        $PHPSheet->getColumnDimension('AT')->setWidth(15);
        $PHPSheet->getColumnDimension('AU')->setWidth(7);
        $PHPSheet->getColumnDimension('AV')->setWidth(7);
        $PHPSheet->getColumnDimension('AW')->setWidth(15);
        $PHPSheet->getColumnDimension('AX')->setWidth(7);
        $PHPSheet->getColumnDimension('AY')->setWidth(7);
        $PHPSheet->getColumnDimension('AZ')->setWidth(15);
        $PHPSheet->getColumnDimension('BA')->setWidth(7);
        $PHPSheet->getColumnDimension('BB')->setWidth(7);
        $PHPSheet->getColumnDimension('BC')->setWidth(15);
        $PHPSheet->getColumnDimension('BD')->setWidth(7);
        $PHPSheet->getColumnDimension('BE')->setWidth(7);
        $PHPSheet->getColumnDimension('BF')->setWidth(15);
        $PHPSheet->getColumnDimension('BG')->setWidth(7);
        $PHPSheet->getColumnDimension('BH')->setWidth(7);
        $PHPSheet->getColumnDimension('BI')->setWidth(15);
        $PHPSheet->getColumnDimension('BJ')->setWidth(7);
        $PHPSheet->getColumnDimension('BK')->setWidth(7);
        $PHPSheet->getColumnDimension('BL')->setWidth(15);
        $PHPSheet->getColumnDimension('BM')->setWidth(7);
        $PHPSheet->getColumnDimension('BN')->setWidth(7);
        $PHPSheet->getColumnDimension('BO')->setWidth(15);
        $PHPSheet->getColumnDimension('BP')->setWidth(7);
        $PHPSheet->getColumnDimension('BQ')->setWidth(7);
        $PHPSheet->getColumnDimension('BR')->setWidth(15);
        $PHPSheet->getColumnDimension('BS')->setWidth(7);
        $PHPSheet->getColumnDimension('BT')->setWidth(7);
        $PHPSheet->getColumnDimension('BU')->setWidth(15);
        $PHPSheet->getColumnDimension('BV')->setWidth(7);
        $PHPSheet->getColumnDimension('BW')->setWidth(7);
        $PHPSheet->getColumnDimension('BX')->setWidth(15);
        $PHPSheet->getColumnDimension('BY')->setWidth(7);
        $PHPSheet->getColumnDimension('BZ')->setWidth(7);
        $PHPSheet->getColumnDimension('CA')->setWidth(15);
        $PHPSheet->getColumnDimension('CB')->setWidth(7);
        $PHPSheet->getColumnDimension('CC')->setWidth(7);
        $PHPSheet->getColumnDimension('CD')->setWidth(15);
        $PHPSheet->getColumnDimension('CE')->setWidth(7);
        $PHPSheet->getColumnDimension('CF')->setWidth(7);

        //数据
        $all = 2;
        foreach ($list as $k => $v) {
            $row = $all;
            $n = count($v['info']);

            $s = $all;

            $all += $n;
            $e = $all - 1;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 11) {
                    if ($v['info']) {
                        $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                        $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                }
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
                $m = 1;

                for ($i = 0; $i < count($v['info']); $i++) {
                    $rows = $row + $i;
                    if ($j >= 11) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('K' . $rows, ' ' . $v['info'][$i]['date']);
                    $PHPSheet->setCellValue('L' . $rows, ' ' . $v['info'][$i]['week']);
                    $PHPSheet->setCellValue('M' . $rows, ' ' . $v['info'][$i]['activity-0']);
                    $PHPSheet->setCellValue('N' . $rows, ' ' . $v['info'][$i]['pleasure-0']);
                    $PHPSheet->setCellValue('O' . $rows, ' ' . $v['info'][$i]['achievement-0']);
                    $PHPSheet->setCellValue('P' . $rows, ' ' . $v['info'][$i]['activity-1']);
                    $PHPSheet->setCellValue('Q' . $rows, ' ' . $v['info'][$i]['pleasure-1']);
                    $PHPSheet->setCellValue('R' . $rows, ' ' . $v['info'][$i]['achievement-1']);
                    $PHPSheet->setCellValue('S' . $rows, ' ' . $v['info'][$i]['activity-2']);
                    $PHPSheet->setCellValue('T' . $rows, ' ' . $v['info'][$i]['pleasure-2']);
                    $PHPSheet->setCellValue('U' . $rows, ' ' . $v['info'][$i]['achievement-2']);
                    $PHPSheet->setCellValue('V' . $rows, ' ' . $v['info'][$i]['activity-3']);
                    $PHPSheet->setCellValue('W' . $rows, ' ' . $v['info'][$i]['pleasure-3']);
                    $PHPSheet->setCellValue('X' . $rows, ' ' . $v['info'][$i]['achievement-3']);
                    $PHPSheet->setCellValue('Y' . $rows, ' ' . $v['info'][$i]['activity-4']);
                    $PHPSheet->setCellValue('Z' . $rows, ' ' . $v['info'][$i]['pleasure-4']);
                    $PHPSheet->setCellValue('AA' . $rows, ' ' . $v['info'][$i]['achievement-4']);
                    $PHPSheet->setCellValue('AB' . $rows, ' ' . $v['info'][$i]['activity-5']);
                    $PHPSheet->setCellValue('AC' . $rows, ' ' . $v['info'][$i]['pleasure-5']);
                    $PHPSheet->setCellValue('AD' . $rows, ' ' . $v['info'][$i]['achievement-5']);
                    $PHPSheet->setCellValue('AE' . $rows, ' ' . $v['info'][$i]['activity-6']);
                    $PHPSheet->setCellValue('AF' . $rows, ' ' . $v['info'][$i]['pleasure-6']);
                    $PHPSheet->setCellValue('AG' . $rows, ' ' . $v['info'][$i]['achievement-6']);
                    $PHPSheet->setCellValue('AH' . $rows, ' ' . $v['info'][$i]['activity-7']);
                    $PHPSheet->setCellValue('AI' . $rows, ' ' . $v['info'][$i]['pleasure-7']);
                    $PHPSheet->setCellValue('AJ' . $rows, ' ' . $v['info'][$i]['achievement-7']);
                    $PHPSheet->setCellValue('AK' . $rows, ' ' . $v['info'][$i]['activity-8']);
                    $PHPSheet->setCellValue('AL' . $rows, ' ' . $v['info'][$i]['pleasure-8']);
                    $PHPSheet->setCellValue('AM' . $rows, ' ' . $v['info'][$i]['achievement-8']);
                    $PHPSheet->setCellValue('AN' . $rows, ' ' . $v['info'][$i]['activity-9']);
                    $PHPSheet->setCellValue('AO' . $rows, ' ' . $v['info'][$i]['pleasure-9']);
                    $PHPSheet->setCellValue('AP' . $rows, ' ' . $v['info'][$i]['achievement-9']);
                    $PHPSheet->setCellValue('AQ' . $rows, ' ' . $v['info'][$i]['activity-10']);
                    $PHPSheet->setCellValue('AR' . $rows, ' ' . $v['info'][$i]['pleasure-10']);
                    $PHPSheet->setCellValue('AS' . $rows, ' ' . $v['info'][$i]['achievement-10']);
                    $PHPSheet->setCellValue('AT' . $rows, ' ' . $v['info'][$i]['activity-11']);
                    $PHPSheet->setCellValue('AU' . $rows, ' ' . $v['info'][$i]['pleasure-11']);
                    $PHPSheet->setCellValue('AV' . $rows, ' ' . $v['info'][$i]['achievement-11']);
                    $PHPSheet->setCellValue('AW' . $rows, ' ' . $v['info'][$i]['activity-12']);
                    $PHPSheet->setCellValue('AX' . $rows, ' ' . $v['info'][$i]['pleasure-12']);
                    $PHPSheet->setCellValue('AY' . $rows, ' ' . $v['info'][$i]['achievement-12']);
                    $PHPSheet->setCellValue('AZ' . $rows, ' ' . $v['info'][$i]['activity-13']);
                    $PHPSheet->setCellValue('BA' . $rows, ' ' . $v['info'][$i]['pleasure-13']);
                    $PHPSheet->setCellValue('BB' . $rows, ' ' . $v['info'][$i]['achievement-13']);
                    $PHPSheet->setCellValue('BC' . $rows, ' ' . $v['info'][$i]['activity-14']);
                    $PHPSheet->setCellValue('BD' . $rows, ' ' . $v['info'][$i]['pleasure-14']);
                    $PHPSheet->setCellValue('BE' . $rows, ' ' . $v['info'][$i]['achievement-14']);
                    $PHPSheet->setCellValue('BF' . $rows, ' ' . $v['info'][$i]['activity-15']);
                    $PHPSheet->setCellValue('BG' . $rows, ' ' . $v['info'][$i]['pleasure-15']);
                    $PHPSheet->setCellValue('BH' . $rows, ' ' . $v['info'][$i]['achievement-15']);
                    $PHPSheet->setCellValue('BI' . $rows, ' ' . $v['info'][$i]['activity-16']);
                    $PHPSheet->setCellValue('BJ' . $rows, ' ' . $v['info'][$i]['pleasure-16']);
                    $PHPSheet->setCellValue('BK' . $rows, ' ' . $v['info'][$i]['achievement-16']);
                    $PHPSheet->setCellValue('BL' . $rows, ' ' . $v['info'][$i]['activity-17']);
                    $PHPSheet->setCellValue('BM' . $rows, ' ' . $v['info'][$i]['pleasure-17']);
                    $PHPSheet->setCellValue('BN' . $rows, ' ' . $v['info'][$i]['achievement-17']);
                    $PHPSheet->setCellValue('BO' . $rows, ' ' . $v['info'][$i]['activity-18']);
                    $PHPSheet->setCellValue('BP' . $rows, ' ' . $v['info'][$i]['pleasure-18']);
                    $PHPSheet->setCellValue('BQ' . $rows, ' ' . $v['info'][$i]['achievement-18']);
                    $PHPSheet->setCellValue('BR' . $rows, ' ' . $v['info'][$i]['activity-19']);
                    $PHPSheet->setCellValue('BS' . $rows, ' ' . $v['info'][$i]['pleasure-19']);
                    $PHPSheet->setCellValue('BT' . $rows, ' ' . $v['info'][$i]['achievement-19']);
                    $PHPSheet->setCellValue('BU' . $rows, ' ' . $v['info'][$i]['activity-20']);
                    $PHPSheet->setCellValue('BV' . $rows, ' ' . $v['info'][$i]['pleasure-20']);
                    $PHPSheet->setCellValue('BW' . $rows, ' ' . $v['info'][$i]['achievement-20']);
                    $PHPSheet->setCellValue('BX' . $rows, ' ' . $v['info'][$i]['activity-21']);
                    $PHPSheet->setCellValue('BY' . $rows, ' ' . $v['info'][$i]['pleasure-21']);
                    $PHPSheet->setCellValue('BZ' . $rows, ' ' . $v['info'][$i]['achievement-21']);
                    $PHPSheet->setCellValue('CA' . $rows, ' ' . $v['info'][$i]['activity-22']);
                    $PHPSheet->setCellValue('CB' . $rows, ' ' . $v['info'][$i]['pleasure-22']);
                    $PHPSheet->setCellValue('CC' . $rows, ' ' . $v['info'][$i]['achievement-22']);
                    $PHPSheet->setCellValue('CD' . $rows, ' ' . $v['info'][$i]['activity-23']);
                    $PHPSheet->setCellValue('CE' . $rows, ' ' . $v['info'][$i]['pleasure-23']);
                    $PHPSheet->setCellValue('CF' . $rows, ' ' . $v['info'][$i]['achievement-23']);
                    $m++;
                }
            }
            ob_flush();
            flush();
        }
        $filename = 'S2-目标清单' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
