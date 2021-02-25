<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\util\ReturnCode;
use app\util\ReturnMsg;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use think\facade\Db;

class S7Practice
{
    // 方法掌握程度
    public function method_mastery()
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
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'S7Practice' . 'method_mastery');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list_a = Db::name('user')->alias('a')->where($where)->join('cm_master_degree b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.technology,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

            foreach ($list_a as $key => $value) {
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list_a[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list_a[$key]['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list_a[$key]['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list_a[$key]['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list_a[$key]['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list_a[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list_a[$key]['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list_a[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list_a[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list_a[$key]['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list_a[$key]['type_name'] = 'N-普通人群';
                } else {
                    $list_a[$key]['type_name'] = '游客';
                }
                $list_a[$key]['technology'] = json_decode($value['technology'], true);
                $list_a[$key]['course_number'] = 'S7';
                $list_a[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
                $list_a[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
            }

            $list_a = Db::name('user')->alias('a')->where($where)->join('cm_master_degree b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.technology,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

            foreach ($list_a as $key => $value) {
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list_a[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list_a[$key]['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list_a[$key]['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list_a[$key]['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list_a[$key]['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list_a[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list_a[$key]['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list_a[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list_a[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list_a[$key]['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $list_a[$key]['type_name'] = 'N-普通人群';
                } else {
                    $list_a[$key]['type_name'] = '游客';
                }
                $list_a[$key]['technology'] = json_decode($value['technology'], true);
                $list_a[$key]['course_number'] = 'S7';
                $list_a[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
                $list_a[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
            }
            $total = Db::name('user')->alias('a')->where($where)->join('cm_master_degree b', 'a.open_id=b.open_id')->field('a.open_id')->page($page, $limit)->count();
            $page_total = ceil($total / $limit);
            $return = [
                'list' => $list_a,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $return);
    }

    //导出方法掌握程度
    public function excel_method_mastery()
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
        $list_a = Db::name('user')->alias('a')->where($where)->join('cm_master_degree b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.technology,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

        foreach ($list_a as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list_a[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list_a[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list_a[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list_a[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list_a[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list_a[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list_a[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list_a[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list_a[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list_a[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list_a[$key]['type_name'] = 'N-普通人群';
            } else {
                $list_a[$key]['type_name'] = '游客';
            }
            $list_a[$key]['technology'] = json_decode($value['technology'], true);
            $list_a[$key]['course_number'] = 'S7';
            $list_a[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
            $list_a[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
        }

        $PHPExcel = new PHPExcel;
        $PHPSheet = $PHPExcel->getActiveSheet();

        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('1')->setRowHeight(25);

        $PHPSheet->getStyle('A1:L1')->getFont()->setSize(13)->setBold(true);

        $PHPSheet->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码');
        $PHPSheet->setCellValue('C1', '姓名');
        $PHPSheet->setCellValue('D1', '微信手机号');
        $PHPSheet->setCellValue('E1', '患者分类');
        $PHPSheet->setCellValue('F1', '课程编号');
        $PHPSheet->setCellValue('G1', '开始时间');
        $PHPSheet->setCellValue('H1', '结束时间');
        $PHPSheet->setCellValue('I1', '时长');
        $PHPSheet->setCellValue('J1', '技术');
        $PHPSheet->setCellValue('K1', '掌握度');
        $PHPSheet->setCellValue('L1', '有效度');

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
        $i = 2;
        $j = 2;
        foreach ($list_a as $key => $value) {
            foreach ($value['technology'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('J' . $i, $value_a['technology']);
                $PHPSheet->setCellValue('K' . $i, $value_a['master']);
                $PHPSheet->setCellValue('L' . $i, $value_a['effective']);
                $i++;
            }
            $max_count = count($value['technology']);
            $PHPSheet->setCellValue('A' . $j, $value['open_id'])->mergeCells('A' . $j . ':' . 'A' . ($j + $max_count - 1));

            $PHPSheet->setCellValue('B' . $j, $value['number'])->mergeCells('B' . $j . ':' . 'B' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('C' . $j, $value['name'])->mergeCells('C' . $j . ':' . 'C' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('D' . $j, $value['wx_phone'])->mergeCells('D' . $j . ':' . 'D' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('E' . $j, $value['type_name'])->mergeCells('E' . $j . ':' . 'E' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('F' . $j, $value['course_number'])->mergeCells('F' . $j . ':' . 'F' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('G' . $j, $value['stime'])->mergeCells('G' . $j . ':' . 'G' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('H' . $j, $value['etime'])->mergeCells('H' . $j . ':' . 'H' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('I' . $j, $value['ltime'])->mergeCells('I' . $j . ':' . 'I' . ($j + $max_count - 1));
            $j += $max_count;
        }
        //设置水平居中
        $PHPSheet->getStyle('A1:L' . ($j - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置垂直居中
        $PHPSheet->getStyle('A1:L' . ($j - 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $filename = 'S6-方法掌握程度表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //我的新目标
    public function new_target()
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

        $list_a = Db::name('user')->alias('a')->where($where)->join('cm_new_target b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.new_target,b.plan,b.way,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

        foreach ($list_a as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list_a[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list_a[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list_a[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list_a[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list_a[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list_a[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list_a[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list_a[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list_a[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list_a[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list_a[$key]['type_name'] = 'N-普通人群';
            } else {
                $list_a[$key]['type_name'] = '游客';
            }

            $list_a[$key]['plan'] = json_decode($value['plan'], true);
            $list_a[$key]['new_target'] = json_decode($value['new_target'],true);
            $list_a[$key]['course_number'] = 'S7';

            $list_a[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
            $list_a[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
        }

        $total = Db::name('user')->alias('a')->where($where)->join('cm_new_target b', 'a.open_id=b.open_id')->field('a.open_id')->page($page, $limit)->count();
        $page_total = ceil($total / $limit);
        $return = [
            'list' => $list_a,
            'page_total' => $page_total,
            'page' => $page,
            'total' => $total
        ];

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $return);
    }

    //导出我的新目标
    public function excel_new_target()
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

        $list_a = Db::name('user')->alias('a')->where($where)->join('cm_new_target b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.new_target,b.plan,b.way,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

        foreach ($list_a as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list_a[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list_a[$key]['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list_a[$key]['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list_a[$key]['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list_a[$key]['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list_a[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list_a[$key]['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list_a[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list_a[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list_a[$key]['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $list_a[$key]['type_name'] = 'N-普通人群';
            } else {
                $list_a[$key]['type_name'] = '游客';
            }

            $list_a[$key]['plan'] = json_decode($value['plan'], true);

            $list_a[$key]['course_number'] = 'S7';

            $list_a[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
            $list_a[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
        }

        $PHPExcel = new PHPExcel;
        $PHPSheet = $PHPExcel->getActiveSheet();

        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('1')->setRowHeight(25);

        $PHPSheet->getStyle('A1:M1')->getFont()->setSize(13)->setBold(true);

        $PHPSheet->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码');
        $PHPSheet->setCellValue('C1', '姓名');
        $PHPSheet->setCellValue('D1', '微信手机号');
        $PHPSheet->setCellValue('E1', '患者分类');
        $PHPSheet->setCellValue('F1', '课程编号');
        $PHPSheet->setCellValue('G1', '开始时间');
        $PHPSheet->setCellValue('H1', '结束时间');
        $PHPSheet->setCellValue('I1', '时长');
        $PHPSheet->setCellValue('J1', '新目标');
        $PHPSheet->setCellValue('K1', '具体计划');
        $PHPSheet->setCellValue('L1', '预计开始的时间');
        $PHPSheet->setCellValue('M1', '完成时限');
        $PHPSheet->setCellValue('N1', '完成之后的奖励');
        $PHPSheet->setCellValue('O1', '可用的方法');

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
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(20);


        $i = 2;
        $j = 2;
        foreach ($list_a as $key => $value) {
            foreach ($value['plan'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('K' . $i, $value_a['plan']);
                $PHPSheet->setCellValue('L' . $i, $value_a['time']);
                $PHPSheet->setCellValue('M' . $i, $value_a['timelong']);
                $PHPSheet->setCellValue('N' . $i, $value_a['reward']);
                $i++;
            }
            $max_count = count($value['plan']);
            $PHPSheet->setCellValue('A' . $j, $value['open_id'])->mergeCells('A' . $j . ':' . 'A' . ($j + $max_count - 1));

            $PHPSheet->setCellValue('B' . $j, $value['number'])->mergeCells('B' . $j . ':' . 'B' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('C' . $j, $value['name'])->mergeCells('C' . $j . ':' . 'C' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('D' . $j, $value['wx_phone'])->mergeCells('D' . $j . ':' . 'D' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('E' . $j, $value['type_name'])->mergeCells('E' . $j . ':' . 'E' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('F' . $j, $value['course_number'])->mergeCells('F' . $j . ':' . 'F' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('G' . $j, $value['stime'])->mergeCells('G' . $j . ':' . 'G' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('H' . $j, $value['etime'])->mergeCells('H' . $j . ':' . 'H' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('I' . $j, $value['ltime'])->mergeCells('I' . $j . ':' . 'I' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('J' . $j, $value['new_target'])->mergeCells('J' . $j . ':' . 'J' . ($j + $max_count - 1));
            $PHPSheet->setCellValue('O' . $j, $value['way'])->mergeCells('O' . $j . ':' . 'O' . ($j + $max_count - 1));

            $j += $max_count;
        }
        //设置水平居中
        $PHPSheet->getStyle('A1:O' . ($j - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置垂直居中
        $PHPSheet->getStyle('A1:O' . ($j - 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $filename = 'S7-我的新目标表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
