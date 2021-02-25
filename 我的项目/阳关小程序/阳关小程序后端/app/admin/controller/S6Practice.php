<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\util\ReturnCode;
use app\util\ReturnMsg;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use think\facade\Db;

class S6Practice
{
    //S6-活动安排
    public function s6_activity_arrange()
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
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'S6Practice' . 's6_activity_arrange');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('activity_plan_s6')->where($where)->group('stime')->field('open_id,stime')->page($page, $limit)->select()->toArray();
            if (empty($list)) {
                $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => [
                    'list' => [],
                    'page_total' => 0,
                    'page' => 1,
                    'total' => 0
                ]];
                return json($data);
            }


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
                } elseif ($userArr['type'] == '11') {
                    $userArr['type'] = 'N-普通人群';
                } else {
                    $userArr['type'] = '游客';
                }
                $haarr[$value] = $userArr;
            }

            foreach ($list as $key => $value) {
                $actarr = Db::name('activity_plan_s6')->where(['open_id' => $value['open_id'], 'stime' => $value['stime']])->field('open_id,date,week,activity,stime,etime,ltime')->select()->toArray();
                $uearr = [];
                foreach ($actarr as $key1 => $value1) {
                    $uearr['open_id'] = $value1['open_id'];
                    $uearr['stime'] = $value1['stime'] ? date('Y-m-d H:i', $value1['stime']) : '';
                    $uearr['etime'] = $value1['etime'] ? date('Y-m-d H:i', $value1['etime']) : '';
                    $uearr['ltime'] = $value1['ltime'];
                    $uearr['couse_number'] = 'S6';
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

    //导出S6-活动安排
    public function excel_s6_activity_arrange()
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
        $list = Db::name('activity_plan_s6')->where($where)->group('stime')->page($page, $limit)->field('open_id,stime')->select()->toArray();
        if ($list) {
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
                } elseif ($value['type'] == '7') {
                    $userArr['type'] = 'P2-患者轻度';
                } elseif ($userArr['type'] == '8') {
                    $userArr['type'] = 'P3-患者中度';
                } elseif ($userArr['type'] == '9') {
                    $userArr['type'] = 'P4-患者重度';
                } elseif ($userArr['type'] == '12') {
                    $userArr['type'] = 'P5-自曝患者';
                } elseif ($userArr['type'] == '11') {
                    $userArr['type'] = 'N-普通人群';
                } else {
                    $userArr['type'] = '游客';
                }
                $haarr[$value] = $userArr;
            }

            foreach ($list as $key => $value) {
                $actarr = Db::name('activity_plan_s6')->where(['open_id' => $value['open_id'], 'stime' => $value['stime']])->field('open_id,date,week,activity,stime,etime,ltime')->select()->toArray();
                $uearr = [];
                foreach ($actarr as $key1 => $value1) {
                    $uearr['open_id'] = $value1['open_id'];
                    $uearr['stime'] = $value1['stime'] ? date('Y-m-d H:i', $value1['stime']) : '';
                    $uearr['etime'] = $value1['etime'] ? date('Y-m-d H:i', $value1['etime']) : '';
                    $uearr['ltime'] = $value1['ltime'];
                    $uearr['couse_number'] = 'S6';
                    $uearr['info'][$value1['week']][] = [
                        'time' => date('Y-m-d H:i', $value1['date']),
                        'activity' => $value1['activity']
                    ];
                }

                $wearr[] =  array_merge($haarr[$uearr['open_id']], $uearr);
            }
        } else {
            $wearr = [];
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

    //S6-活动记录
    public function s6_activity_record()
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
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'S6Practice' . 's6_activity_record');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_s6_activity_record b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime')
                ->group('etime')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $key => $value) {
                //查询该时间段下该用户填写的所有活动
                $activity = Db::name('s6_activity_record')
                    ->where(['open_id' => $value['open_id'], 'etime' => $value['etime']])
                    ->field('date,time,activity,pleasure,achievement,week')
                    ->group('date')
                    ->order('date')
                    ->select()->toArray();
                $infos = [];
                foreach ($activity as $k => $v) {
                    $info = Db::name('s6_activity_record')
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
                $list[$key]['course'] = 'S6';
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
                ->join('cm_s6_activity_record b', 'a.open_id = b.open_id')
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

    //导出S6-活动记录
    public function excel_s6_activity_record()
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
            ->join('cm_s6_activity_record b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.stime,b.etime,b.ltime')
            ->group('etime')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();
        foreach ($list as $key => $value) {
            //查询该时间段下该用户填写的所有活动
            $activity = Db::name('s6_activity_record')
                ->where(['open_id' => $value['open_id'], 'etime' => $value['etime']])
                ->field('date,time,activity,pleasure,achievement,week')
                ->group('date')
                ->order('date')
                ->select()->toArray();
            $infos = [];
            foreach ($activity as $k => $v) {
                $info = Db::name('s6_activity_record')
                    ->where(['date' => $v['date']])
                    ->field('date,time,activity,pleasure,achievement,week')
                    ->order('time')
                    ->select()->toArray();
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
            $list[$key]['course'] = 'S6';
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
        $filename = 's6-活动记录' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //发现内在信念
    public function s6_found_faith()
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
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'S6Practice' . 's6_found_faith');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {

            $list_a = Db::name('user')->alias('a')->where($where)->where(['course' => 3, 'new' => 1, 'b.type' => 1])->whereOr('course', 6)->join('cm_auto_thinking b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.id,b.situation,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();
            $last_arr = [];
            foreach ($list_a as $key => $value) {
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $value['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $value['type_name'] = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $value['type_name'] = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $value['type_name'] = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $value['type_name'] = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $value['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $value['type_name'] = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $value['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $value['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $value['type_name'] = 'P5-自曝患者';
                } elseif ($value['type'] == '11') {
                    $value['type_name'] = 'N-普通人群';
                } else {
                    $value['type_name'] = '游客';
                }

                $value['course_number'] = 'S6';
                $value['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';

                $value['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';

                $arr_mood = Db::name('think_mood')->where('at_id', $value['id'])->select()->toArray();

                $arr_think = Db::name('think_think')->where('at_id', $value['id'])->select()->toArray();
                $value['mood'] = $arr_mood;
                $value['think'] = $arr_think;

                $list_b = Db::name('idea')->where('tt_id', $value['id'])->order('id desc')->select()->toArray();
                if ($list_b) {
                    $list_b[0]['idea'] = json_decode($list_b[0]['idea'], true);
                    $idea_a = array_shift($list_b[0]['idea']);
                    $value['idea'] = $idea_a;
                    $value['idea_arr'] = $list_b[0]['idea'];
                    $last_arr[] = $value;
                } else {
                    $value['idea'] = '';
                    $value['idea_arr'] = [];
                    $last_arr[] = $value;
                }
            }

            $total = Db::name('user')->alias('a')->where($where)->where(['course' => 3, 'new' => 1, 'b.type' => 1])->whereOr('course', 6)->join('cm_auto_thinking b', 'a.open_id=b.open_id')->field('a.open_id')->count();

            $page_total = ceil($total / $limit);
            $return = [
                'list' => $last_arr,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $return);
    }

    //导出 发现内在信念
    public function excel_s6_found_faith()
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

        $list_a = Db::name('user')->alias('a')->where($where)->where(['course' => 3, 'new' => 1, 'b.type' => 1])->whereOr('course', 6)->join('cm_auto_thinking b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.id,b.situation,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();
        $last_arr = [];
        foreach ($list_a as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $value['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $value['type_name'] = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $value['type_name'] = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $value['type_name'] = '高危-分数';
            } elseif ($value['type'] == '5') {
                $value['type_name'] = '患者-B1';
            } elseif ($value['type'] == '6') {
                $value['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $value['type_name'] = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $value['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $value['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $value['type_name'] = 'P5-自曝患者';
            } elseif ($value['type'] == '11') {
                $value['type_name'] = 'N-普通人群';
            } else {
                $value['type_name'] = '游客';
            }

            $value['course_number'] = 'S6';
            $value['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';

            $value['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
            $arr_mood = Db::name('think_mood')->where('at_id', $value['id'])->select()->toArray();

            $arr_think = Db::name('think_think')->where('at_id', $value['id'])->select()->toArray();
            $value['mood'] = $arr_mood;
            $value['think'] = $arr_think;

            $list_b = Db::name('idea')->where('tt_id', $value['id'])->order('id desc')->select()->toArray();
            if ($list_b) {
                $list_b[0]['idea'] = json_decode($list_b[0]['idea'], true);
                $idea_a = array_shift($list_b[0]['idea']);
                $value['idea'] = $idea_a;
                $value['idea_arr'] = $list_b[0]['idea'];
                $last_arr[] = $value;
            } else {
                $value['idea'] = '';
                $value['idea_arr'] = [];
                $last_arr[] = $value;
            }
        }

        $PHPExcel = new PHPExcel();
        $PHPSheet = $PHPExcel->getActiveSheet();

        $PHPExcel->setActiveSheetIndex(0);

        $PHPSheet->getRowDimension('1')->setRowHeight(25);

        $PHPSheet->getStyle('A1:Q1')->getFont()->setSize(13)->setBold(true);

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
        $PHPSheet->setCellValue('P1', '想法');
        $PHPSheet->setCellValue('Q1', '箭头向下');

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
        $PHPSheet->getColumnDimension('M')->setWidth(20);
        $PHPSheet->getColumnDimension('N')->setWidth(20);
        $PHPSheet->getColumnDimension('O')->setWidth(20);
        $PHPSheet->getColumnDimension('P')->setWidth(20);
        $PHPSheet->getColumnDimension('Q')->setWidth(20);

        $i = 2;
        $j = 2;
        $k = 2;
        $u = 2;
        foreach ($last_arr as $key => $value) {
            foreach ($value['mood'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('K' . $i, $value_a['mood']);
                $PHPSheet->setCellValue('L' . $i, $value_a['fraction']);
                $i++;
            }
            foreach ($value['think'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('M' . $j, $value_a['think']);
                $PHPSheet->setCellValue('N' . $j, $value_a['fraction']);
                $PHPSheet->setCellValue('O' . $j, $value_a['misunderstanding']);
                $j++;
            }

            foreach ($value['idea_arr'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('Q' . $k, $value_a);
                $k++;
            }

            $i = $j = $k = max($i, $j, $k);

            $max_a = max(count($value['mood']), count($value['think']), count($value['idea_arr']));
            $PHPSheet->setCellValue('A' . $u, $value['open_id'])->mergeCells('A' . $u . ':' . 'A' . ($u + $max_a - 1));

            $PHPSheet->setCellValue('B' . $u, $value['number'])->mergeCells('B' . $u . ':' . 'B' . ($u + $max_a - 1));

            $PHPSheet->setCellValue('C' . $u, $value['name'])->mergeCells('C' . $u . ':' . 'C' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('D' . $u, $value['wx_phone'])->mergeCells('D' . $u . ':' . 'D' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('E' . $u, $value['type_name'])->mergeCells('E' . $u . ':' . 'E' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('F' . $u, $value['course_number'])->mergeCells('F' . $u . ':' . 'F' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('G' . $u, $value['stime'])->mergeCells('G' . $u . ':' . 'G' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('H' . $u, $value['etime'])->mergeCells('H' . $u . ':' . 'H' . ($u + $max_a - 1));

            $PHPSheet->setCellValue('I' . $u, $value['ltime'])->mergeCells('I' . $u . ':' . 'I' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('J' . $u, $value['situation'])->mergeCells('J' . $u . ':' . 'J' . ($u + $max_a - 1));


            $PHPSheet->setCellValue('P' . $u, $value['idea'])->mergeCells('P' . $u . ':' . 'P' . ($u + $max_a - 1));

            $u += $max_a;
        }

        //设置水平居中
        $PHPSheet->getStyle('A1:Q' . ($u - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置垂直居中
        $PHPSheet->getStyle('A1:Q' . ($u - 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $filename = 'S5-归因练习表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //评估内在信念
    public function assess_faith()
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
        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'admin' . 'S6Practice' . 'assess_faith');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list_a = Db::name('user')->alias('a')->where($where)->join('cm_belief b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.original,b.support,b.fresh,b.fresh_support,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

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

                $list_a[$key]['support'] = json_decode($value['support'], true);
                $list_a[$key]['fresh_support'] = json_decode($value['fresh_support'], true);
                $list_a[$key]['course_number'] = 'S6';
                $list_a[$key]['stime'] = $value['stime'] ? date('Y-m-d H:i', $value['stime']) : '';
                $list_a[$key]['etime'] = $value['etime'] ? date('Y-m-d H:i', $value['etime']) : '';
            }

            $total = Db::name('user')->alias('a')->where($where)->join('cm_belief b', 'a.open_id=b.open_id')->field('a.open_id')->count();

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

    //导出评估内在信念
    public function excel_assess_faith()
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

        $list_a = Db::name('user')->alias('a')->where($where)->join('cm_belief b', 'a.open_id=b.open_id')->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.original,b.support,b.fresh,b.fresh_support,b.stime,b.etime,b.ltime')->page($page, $limit)->select()->toArray();

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

            $list_a[$key]['support'] = json_decode($value['support'], true);
            $list_a[$key]['fresh_support'] = json_decode($value['fresh_support'], true);
            $list_a[$key]['course_number'] = 'S6';
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
        $PHPSheet->setCellValue('J1', '旧信念');
        $PHPSheet->setCellValue('K1', '证据');
        $PHPSheet->setCellValue('L1', '新信念');
        $PHPSheet->setCellValue('M1', '证据');

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

        $i = 2;
        $j = 2;
        $k = 2;
        foreach ($list_a as $key => $value) {
            foreach ($value['support'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('K' . $i, $value_a);
                $i++;
            }
            foreach ($value['fresh_support'] as $key_a => $value_a) {
                $PHPSheet->setCellValue('M' . $j, $value_a);
                $j++;
            }
            $i = $j = max($i, $j);
            $max_count = max(count($value['support']), count($value['fresh_support']));

            $PHPSheet->setCellValue('A' . $k, $value['open_id'])->mergeCells('A' . $k . ':' . 'A' . ($k + $max_count - 1));

            $PHPSheet->setCellValue('B' . $k, $value['number'])->mergeCells('B' . $k . ':' . 'B' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('C' . $k, $value['name'])->mergeCells('C' . $k . ':' . 'C' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('D' . $k, $value['wx_phone'])->mergeCells('D' . $k . ':' . 'D' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('E' . $k, $value['type_name'])->mergeCells('E' . $k . ':' . 'E' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('F' . $k, $value['course_number'])->mergeCells('F' . $k . ':' . 'F' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('G' . $k, $value['stime'])->mergeCells('G' . $k . ':' . 'G' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('H' . $k, $value['etime'])->mergeCells('H' . $k . ':' . 'H' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('I' . $k, $value['ltime'])->mergeCells('I' . $k . ':' . 'I' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('J' . $k, $value['original'])->mergeCells('J' . $k . ':' . 'J' . ($k + $max_count - 1));
            $PHPSheet->setCellValue('L' . $k, $value['fresh'])->mergeCells('L' . $k . ':' . 'L' . ($k + $max_count - 1));

            $k += $max_count;
        }

        //设置水平居中
        $PHPSheet->getStyle('A1:M' . ($k - 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置垂直居中
        $PHPSheet->getStyle('A1:M' . ($k - 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $filename = 'S6评估内在信念表' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
