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
use think\facade\Cache;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class Course extends AdminAuth
{

    //课程统计
    public function course_count()
    {
        $cachekey = md5('course_count' . 'course' . 'cm_course_info');
        if (cache($cachekey)) {
            $list = cache($cachekey);
        } else {
            $list = Db::name('course')
                ->alias('a')
                //->where(['b.etime'=>['>','0']])
                ->join('cm_course_info b', 'a.course = b.course and b.etime > 0', 'left')
                ->field('count(b.id) as counts,a.course')
                ->group('course')
                ->select()->toArray();

            $all_counts  = 0;
            $all_finish  = 0;
            $all_reviwe  = 0;
            $all_share   = 0;
            $all_study   = 0;
            foreach ($list as $key => $value) {
                if ($value['course'] == 1) {
                    $field = 'one_status';
                } elseif ($value['course'] == 2) {
                    $field = 'two_status';
                } elseif ($value['course'] == 3) {
                    $field = 'three_status';
                } elseif ($value['course'] == 4) {
                    $field = 'four_status';
                } elseif ($value['course'] == 5) {
                    $field = 'five_status';
                } elseif ($value['course'] == 6) {
                    $field = 'six_status';
                } elseif ($value['course'] == 7) {
                    $field = 'seven_status';
                }

                if ($value['course'] != '合计') {
                    $list[$key]['course'] = 'S' . $value['course'];
                }

                $all_counts += $value['counts'];
                //完成课程人数
                $finish_study = Db::name('course_record')->where($field, '2')->count();
                $all_finish += $finish_study;
                $list[$key]['finish_study'] = $finish_study;
                //总回看次数
                $reviwe_num = Db::name('course_info')->where('etime', '>', '0')->where(['new' => 2, 'course' => $value['course']])->count();
                $all_reviwe += $reviwe_num;
                $list[$key]['reviwe_num'] = $reviwe_num;
                //总转发次数
                $share_num = Db::name('course_info')->where('etime', '>', '0')->where(['share' => 1, 'course' => $value['course']])->count();
                $all_share += $share_num;
                $list[$key]['share_num'] = $share_num;
                //总学习时长
                $study_time = Db::name('course_info')->where('etime', '>', '0')->where(['course' => $value['course']])->sum('ltime');
                $all_study += $study_time;
                $list[$key]['study_time'] = diff_time($study_time) ?: '0';
                //平均学习时长
                if ($value['counts']) {
                    $list[$key]['average_time'] = diff_time(intval($study_time / $value['counts'])) ?: '0';;
                } else {
                    $list[$key]['average_time'] = diff_time(intval($study_time)) ?: '0';;
                }

                if ($value['course'] == '合计') {
                    $list[$key]['counts']       = $all_counts;
                    $list[$key]['finish_study'] = $all_finish;
                    $list[$key]['reviwe_num']   = $all_reviwe;
                    $list[$key]['share_num']    = $all_share;
                    $list[$key]['study_time']   = diff_time($all_study);
                    if ($all_counts) {
                        $list[$key]['average_time'] = diff_time(intval($all_study / $all_counts));
                    } else {
                        $list[$key]['average_time'] = diff_time(intval($all_study));
                    }
                }
            }
            cache($cachekey, $list, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $list];
        return json($data);
    }

    //导出课程统计
    public function excel_course_count()
    {
        $list = Db::name('course')
            ->alias('a')
            //->where(['b.etime'=>['>','0']])
            ->join('cm_course_info b', 'a.course = b.course and b.etime > 0', 'left')
            ->field('count(b.id) as counts,a.course')
            ->group('course')
            ->select()->toArray();

        $all_counts  = 0;
        $all_finish  = 0;
        $all_reviwe  = 0;
        $all_share   = 0;
        $all_study   = 0;
        foreach ($list as $key => $value) {
            if ($value['course'] == 1) {
                $field = 'one_status';
            } elseif ($value['course'] == 2) {
                $field = 'two_status';
            } elseif ($value['course'] == 3) {
                $field = 'three_status';
            } elseif ($value['course'] == 4) {
                $field = 'four_status';
            } elseif ($value['course'] == 5) {
                $field = 'five_status';
            } elseif ($value['course'] == 6) {
                $field = 'six_status';
            } elseif ($value['course'] == 7) {
                $field = 'seven_status';
            }

            if ($value['course'] != '合计') {
                $list[$key]['course'] = 'S' . $value['course'];
            }

            $all_counts += $value['counts'];
            //完成课程人数
            $finish_study = Db::name('course_record')->where($field, '2')->count();
            $all_finish += $finish_study;
            $list[$key]['finish_study'] = $finish_study;
            //总回看次数
            $reviwe_num = Db::name('course_info')->where('etime', '>', '0')->where(['new' => 2, 'course' => $value['course']])->count();
            $all_reviwe += $reviwe_num;
            $list[$key]['reviwe_num'] = $reviwe_num;
            //总转发次数
            $share_num = Db::name('course_info')->where('etime', '>', '0')->where(['share' => 1, 'course' => $value['course']])->count();
            $all_share += $share_num;
            $list[$key]['share_num'] = $share_num;
            //总学习时长
            $study_time = Db::name('course_info')->where('etime', '>', '0')->where(['course' => $value['course']])->sum('ltime');
            $all_study += $study_time;
            $list[$key]['study_time'] = diff_time($study_time) ?: '0';
            //平均学习时长
            if ($value['counts']) {
                $list[$key]['average_time'] = diff_time(intval($study_time / $value['counts'])) ?: '0';;
            } else {
                $list[$key]['average_time'] = diff_time(intval($study_time)) ?: '0';;
            }

            if ($value['course'] == '合计') {
                $list[$key]['counts']       = $all_counts;
                $list[$key]['finish_study'] = $all_finish;
                $list[$key]['reviwe_num']   = $all_reviwe;
                $list[$key]['share_num']    = $all_share;
                $list[$key]['study_time']   = diff_time($all_study);
                if ($all_counts) {
                    $list[$key]['average_time'] = diff_time(intval($all_study / $all_counts));
                } else {
                    $list[$key]['average_time'] = diff_time(intval($all_study));
                }
            }
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
        $sheet_title = array('课程编号', '总点击次数', '完成课程人数', '总回看次数', '总转发次数', '总学习时长', '平均每次学习时长',);
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(15);
        $PHPSheet->getColumnDimension('B')->setWidth(15);
        $PHPSheet->getColumnDimension('C')->setWidth(15);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(15);
        $PHPSheet->getColumnDimension('F')->setWidth(25);
        $PHPSheet->getColumnDimension('G')->setWidth(20);

        //数据
        foreach ($list as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $PHPSheet->setCellValue('A' . $row, ' ' . $v['course']);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['counts']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['finish_study']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['reviwe_num']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['share_num']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['study_time']);
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['average_time']);
            }
            ob_flush();
            flush();
        }
        $filename = '课程统计' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //课程学习分布
    public function course_study_distribution()
    {
        $cachekey = md5('course_study_distribution' . 'click');
        if (cache($cachekey)) {
            $reArr = cache($cachekey);
        } else {
            $reArr = [];
            $reArr[0][0]['v1'] = 0;
            $reArr[0][0]['p1'] = 0;
            $reArr[0][0]['v2'] = 0;
            $reArr[0][0]['p2'] = 0;
            $reArr[0][0]['v3'] = 0;
            $reArr[0][0]['p3'] = 0;
            $listOne = Db::name('one_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3'])->select()->toArray();
            if ($listOne) {
                foreach ($listOne as $key => $value) {
                    $reArr[0][0]['s'] = 'S1';
                    $reArr[0][0]['v1'] += $value['v1'];
                    $reArr[0][0]['p1'] += $value['p1'];
                    $reArr[0][0]['v2'] += $value['v2'];
                    $reArr[0][0]['p2'] += $value['p2'];
                    $reArr[0][0]['v3'] += $value['v3'];
                    $reArr[0][0]['p3'] += $value['p3'];
                }
            } else {
                $reArr[0][0]['s'] = 'S1';
                $reArr[0][0]['v1'] = 0;
                $reArr[0][0]['p1'] = 0;
                $reArr[0][0]['v2'] = 0;
                $reArr[0][0]['p2'] = 0;
                $reArr[0][0]['v3'] = 0;
                $reArr[0][0]['p3'] = 0;
            }

            $reArr[1][0]['v1'] = 0;
            $reArr[1][0]['p1'] = 0;
            $reArr[1][0]['v2'] = 0;
            $reArr[1][0]['p2'] = 0;
            $reArr[1][0]['v3'] = 0;
            $reArr[1][0]['p3'] = 0;
            $reArr[1][0]['v4'] = 0;
            $listTwo = Db::name('two_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3', 'v4'])->select()->toArray();
            if ($listTwo) {
                foreach ($listTwo as $key => $value) {
                    $reArr[1][0]['s'] = 'S2';
                    $reArr[1][0]['v1'] += $value['v1'];
                    $reArr[1][0]['p1'] += $value['p1'];
                    $reArr[1][0]['v2'] += $value['v2'];
                    $reArr[1][0]['p2'] += $value['p2'];
                    $reArr[1][0]['v3'] += $value['v3'];
                    $reArr[1][0]['p3'] += $value['p3'];
                    $reArr[1][0]['v4'] += $value['v4'];
                }
            } else {
                $reArr[1][0]['s'] = 'S2';
                $reArr[1][0]['v1'] = 0;
                $reArr[1][0]['p1'] = 0;
                $reArr[1][0]['v2'] =  0;
                $reArr[1][0]['p2'] =  0;
                $reArr[1][0]['v3'] = 0;
                $reArr[1][0]['p3'] =  0;
                $reArr[1][0]['v4'] =  0;
            }


            $reArr[2][0]['v1'] = 0;
            $reArr[2][0]['p1'] = 0;
            $reArr[2][0]['v2'] = 0;
            $reArr[2][0]['p2'] = 0;
            $reArr[2][0]['v3'] = 0;
            $reArr[2][0]['p3'] = 0;
            $reArr[2][0]['v4'] = 0;
            $reArr[2][0]['p4'] = 0;
            $listThree = Db::name('three_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3', 'v4', 'p4'])->select()->toArray();
            if ($listThree) {
                foreach ($listThree as $key => $value) {
                    $reArr[2][0]['s'] = 'S3';
                    $reArr[2][0]['v1'] += $value['v1'];
                    $reArr[2][0]['p1'] += $value['p1'];
                    $reArr[2][0]['v2'] += $value['v2'];
                    $reArr[2][0]['p2'] += $value['p2'];
                    $reArr[2][0]['v3'] += $value['v3'];
                    $reArr[2][0]['p3'] += $value['p3'];
                    $reArr[2][0]['v4'] += $value['v4'];
                    $reArr[2][0]['p4'] += $value['p4'];
                }
            } else {
                $reArr[2][0]['s'] = 'S3';
                $reArr[2][0]['v1'] = 0;
                $reArr[2][0]['p1'] = 0;
                $reArr[2][0]['v2'] =  0;
                $reArr[2][0]['p2'] =  0;
                $reArr[2][0]['v3'] = 0;
                $reArr[2][0]['p3'] =  0;
                $reArr[2][0]['v4'] =  0;
                $reArr[2][0]['p4'] =  0;
            }


            $reArr[3][0]['v1'] = 0;
            $reArr[3][0]['p1'] = 0;
            $reArr[3][0]['v2'] = 0;
            $reArr[3][0]['p2'] = 0;
            $reArr[3][0]['v3'] = 0;
            $reArr[3][0]['p3'] = 0;
            $listThree = Db::name('four_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3'])->select()->toArray();
            if ($listThree) {
                foreach ($listThree as $key => $value) {
                    $reArr[3][0]['s'] = 'S4';
                    $reArr[3][0]['v1'] += $value['v1'];
                    $reArr[3][0]['p1'] += $value['p1'];
                    $reArr[3][0]['v2'] += $value['v2'];
                    $reArr[3][0]['p2'] += $value['p2'];
                    $reArr[3][0]['v3'] += $value['v3'];
                    $reArr[3][0]['p3'] += $value['p3'];
                }
            } else {
                $reArr[3][0]['s'] = 'S4';
                $reArr[3][0]['v1'] = 0;
                $reArr[3][0]['p1'] = 0;
                $reArr[3][0]['v2'] =  0;
                $reArr[3][0]['p2'] =  0;
                $reArr[3][0]['v3'] = 0;
                $reArr[3][0]['p3'] =  0;
            }



            $reArr[4][0]['v1'] = 0;
            $reArr[4][0]['p1'] = 0;
            $reArr[4][0]['v2'] = 0;
            $reArr[4][0]['p2'] = 0;
            $reArr[4][0]['v3'] = 0;
            $reArr[4][0]['p3'] = 0;
            $listThree = Db::name('five_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3'])->select()->toArray();
            if ($listThree) {
                foreach ($listThree as $key => $value) {
                    $reArr[4][0]['s'] = 'S5';
                    $reArr[4][0]['v1'] += $value['v1'];
                    $reArr[4][0]['p1'] += $value['p1'];
                    $reArr[4][0]['v2'] += $value['v2'];
                    $reArr[4][0]['p2'] += $value['p2'];
                    $reArr[4][0]['v3'] += $value['v3'];
                    $reArr[4][0]['p3'] += $value['p3'];
                }
            } else {
                $reArr[4][0]['s'] = 'S5';
                $reArr[4][0]['v1'] = 0;
                $reArr[4][0]['p1'] = 0;
                $reArr[4][0]['v2'] =  0;
                $reArr[4][0]['p2'] =  0;
                $reArr[4][0]['v3'] = 0;
                $reArr[4][0]['p3'] =  0;
            }


            $reArr[5][0]['v1'] = 0;
            $reArr[5][0]['p1'] = 0;
            $reArr[5][0]['v2'] = 0;
            $reArr[5][0]['p2'] = 0;
            $reArr[5][0]['v3'] = 0;
            $listThree = Db::name('six_click')->field(['v1', 'p1', 'v2', 'p2', 'v3'])->select()->toArray();
            if ($listThree) {
                foreach ($listThree as $key => $value) {
                    $reArr[5][0]['s'] = 'S6';
                    $reArr[5][0]['v1'] += $value['v1'];
                    $reArr[5][0]['p1'] += $value['p1'];
                    $reArr[5][0]['v2'] += $value['v2'];
                    $reArr[5][0]['p2'] += $value['p2'];
                    $reArr[5][0]['v3'] += $value['v3'];
                }
            } else {
                $reArr[5][0]['s'] = 'S6';
                $reArr[5][0]['v1'] = 0;
                $reArr[5][0]['p1'] = 0;
                $reArr[5][0]['v2'] =  0;
                $reArr[5][0]['p2'] =  0;
                $reArr[5][0]['v3'] = 0;
            }


            $reArr[6][0]['v1'] = 0;
            $reArr[6][0]['p1'] = 0;
            $reArr[6][0]['v2'] = 0;
            $reArr[6][0]['p2'] = 0;
            $listThree = Db::name('seven_click')->field(['v1', 'p1',  'v2', 'p2'])->select()->toArray();
            if ($listThree) {
                foreach ($listThree as $key => $value) {
                    $reArr[6][0]['s'] = 'S7';
                    $reArr[6][0]['v1'] += $value['v1'];
                    $reArr[6][0]['p1'] += $value['p1'];
                    $reArr[6][0]['v2'] += $value['v2'];
                    $reArr[6][0]['p2'] += $value['p2'];
                }
            } else {
                $reArr[6][0]['s'] = 'S7';
                $reArr[6][0]['v1'] = 0;
                $reArr[6][0]['p1'] = 0;
                $reArr[6][0]['v2'] = 0;
                $reArr[6][0]['p2'] =  0;
            }
            cache($cachekey, $reArr, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $reArr];
        return json($data);
    }

    //导出课程学习分布
    public function excel_course_study_distribution()
    {
        $reArr = [];
        $reArr[0][0]['v1'] = 0;
        $reArr[0][0]['p1'] = 0;
        $reArr[0][0]['v2'] = 0;
        $reArr[0][0]['p2'] = 0;
        $reArr[0][0]['v3'] = 0;
        $reArr[0][0]['p3'] = 0;
        $listOne = Db::name('one_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3'])->select()->toArray();
        if ($listOne) {
            foreach ($listOne as $key => $value) {
                $reArr[0][0]['s'] = 'S1';
                $reArr[0][0]['v1'] += $value['v1'];
                $reArr[0][0]['p1'] += $value['p1'];
                $reArr[0][0]['v2'] += $value['v2'];
                $reArr[0][0]['p2'] += $value['p2'];
                $reArr[0][0]['v3'] += $value['v3'];
                $reArr[0][0]['p3'] += $value['p3'];
            }
        } else {
            $reArr[0][0]['s'] = 'S1';
            $reArr[0][0]['v1'] = 0;
            $reArr[0][0]['p1'] = 0;
            $reArr[0][0]['v2'] = 0;
            $reArr[0][0]['p2'] = 0;
            $reArr[0][0]['v3'] = 0;
            $reArr[0][0]['p3'] = 0;
        }

        $reArr[1][0]['v1'] = 0;
        $reArr[1][0]['p1'] = 0;
        $reArr[1][0]['v2'] = 0;
        $reArr[1][0]['p2'] = 0;
        $reArr[1][0]['v3'] = 0;
        $reArr[1][0]['p3'] = 0;
        $reArr[1][0]['v4'] = 0;
        $listTwo = Db::name('two_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3', 'v4'])->select()->toArray();
        if ($listTwo) {
            foreach ($listTwo as $key => $value) {
                $reArr[1][0]['s'] = 'S2';
                $reArr[1][0]['v1'] += $value['v1'];
                $reArr[1][0]['p1'] += $value['p1'];
                $reArr[1][0]['v2'] += $value['v2'];
                $reArr[1][0]['p2'] += $value['p2'];
                $reArr[1][0]['v3'] += $value['v3'];
                $reArr[1][0]['p3'] += $value['p3'];
                $reArr[1][0]['v4'] += $value['v4'];
            }
        } else {
            $reArr[1][0]['s'] = 'S2';
            $reArr[1][0]['v1'] = 0;
            $reArr[1][0]['p1'] = 0;
            $reArr[1][0]['v2'] =  0;
            $reArr[1][0]['p2'] =  0;
            $reArr[1][0]['v3'] = 0;
            $reArr[1][0]['p3'] =  0;
            $reArr[1][0]['v4'] =  0;
        }


        $reArr[2][0]['v1'] = 0;
        $reArr[2][0]['p1'] = 0;
        $reArr[2][0]['v2'] = 0;
        $reArr[2][0]['p2'] = 0;
        $reArr[2][0]['v3'] = 0;
        $reArr[2][0]['p3'] = 0;
        $reArr[2][0]['v4'] = 0;
        $reArr[2][0]['p4'] = 0;
        $listThree = Db::name('three_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3', 'v4', 'p4'])->select()->toArray();
        if ($listThree) {
            foreach ($listThree as $key => $value) {
                $reArr[2][0]['s'] = 'S3';
                $reArr[2][0]['v1'] += $value['v1'];
                $reArr[2][0]['p1'] += $value['p1'];
                $reArr[2][0]['v2'] += $value['v2'];
                $reArr[2][0]['p2'] += $value['p2'];
                $reArr[2][0]['v3'] += $value['v3'];
                $reArr[2][0]['p3'] += $value['p3'];
                $reArr[2][0]['v4'] += $value['v4'];
                $reArr[2][0]['p4'] += $value['p4'];
            }
        } else {
            $reArr[2][0]['s'] = 'S3';
            $reArr[2][0]['v1'] = 0;
            $reArr[2][0]['p1'] = 0;
            $reArr[2][0]['v2'] =  0;
            $reArr[2][0]['p2'] =  0;
            $reArr[2][0]['v3'] = 0;
            $reArr[2][0]['p3'] =  0;
            $reArr[2][0]['v4'] =  0;
            $reArr[2][0]['p4'] =  0;
        }


        $reArr[3][0]['v1'] = 0;
        $reArr[3][0]['p1'] = 0;
        $reArr[3][0]['v2'] = 0;
        $reArr[3][0]['p2'] = 0;
        $reArr[3][0]['v3'] = 0;
        $reArr[3][0]['p3'] = 0;
        $listThree = Db::name('four_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3'])->select()->toArray();
        if ($listThree) {
            foreach ($listThree as $key => $value) {
                $reArr[3][0]['s'] = 'S4';
                $reArr[3][0]['v1'] += $value['v1'];
                $reArr[3][0]['p1'] += $value['p1'];
                $reArr[3][0]['v2'] += $value['v2'];
                $reArr[3][0]['p2'] += $value['p2'];
                $reArr[3][0]['v3'] += $value['v3'];
                $reArr[3][0]['p3'] += $value['p3'];
            }
        } else {
            $reArr[3][0]['s'] = 'S4';
            $reArr[3][0]['v1'] = 0;
            $reArr[3][0]['p1'] = 0;
            $reArr[3][0]['v2'] =  0;
            $reArr[3][0]['p2'] =  0;
            $reArr[3][0]['v3'] = 0;
            $reArr[3][0]['p3'] =  0;
        }



        $reArr[4][0]['v1'] = 0;
        $reArr[4][0]['p1'] = 0;
        $reArr[4][0]['v2'] = 0;
        $reArr[4][0]['p2'] = 0;
        $reArr[4][0]['v3'] = 0;
        $reArr[4][0]['p3'] = 0;
        $listThree = Db::name('five_click')->field(['v1', 'p1', 'v2', 'p2', 'v3', 'p3'])->select()->toArray();
        if ($listThree) {
            foreach ($listThree as $key => $value) {
                $reArr[4][0]['s'] = 'S5';
                $reArr[4][0]['v1'] += $value['v1'];
                $reArr[4][0]['p1'] += $value['p1'];
                $reArr[4][0]['v2'] += $value['v2'];
                $reArr[4][0]['p2'] += $value['p2'];
                $reArr[4][0]['v3'] += $value['v3'];
                $reArr[4][0]['p3'] += $value['p3'];
            }
        } else {
            $reArr[4][0]['s'] = 'S5';
            $reArr[4][0]['v1'] = 0;
            $reArr[4][0]['p1'] = 0;
            $reArr[4][0]['v2'] =  0;
            $reArr[4][0]['p2'] =  0;
            $reArr[4][0]['v3'] = 0;
            $reArr[4][0]['p3'] =  0;
        }


        $reArr[5][0]['v1'] = 0;
        $reArr[5][0]['p1'] = 0;
        $reArr[5][0]['v2'] = 0;
        $reArr[5][0]['p2'] = 0;
        $reArr[5][0]['v3'] = 0;
        $listThree = Db::name('six_click')->field(['v1', 'p1', 'v2', 'p2', 'v3'])->select()->toArray();
        if ($listThree) {
            foreach ($listThree as $key => $value) {
                $reArr[5][0]['s'] = 'S6';
                $reArr[5][0]['v1'] += $value['v1'];
                $reArr[5][0]['p1'] += $value['p1'];
                $reArr[5][0]['v2'] += $value['v2'];
                $reArr[5][0]['p2'] += $value['p2'];
                $reArr[5][0]['v3'] += $value['v3'];
            }
        } else {
            $reArr[5][0]['s'] = 'S6';
            $reArr[5][0]['v1'] = 0;
            $reArr[5][0]['p1'] = 0;
            $reArr[5][0]['v2'] =  0;
            $reArr[5][0]['p2'] =  0;
            $reArr[5][0]['v3'] = 0;
        }


        $reArr[6][0]['v1'] = 0;
        $reArr[6][0]['p1'] = 0;
        $reArr[6][0]['v2'] = 0;
        $reArr[6][0]['p2'] = 0;
        $listThree = Db::name('seven_click')->field(['v1', 'p1',  'v2', 'p2'])->select()->toArray();
        if ($listThree) {
            foreach ($listThree as $key => $value) {
                $reArr[6][0]['s'] = 'S7';
                $reArr[6][0]['v1'] += $value['v1'];
                $reArr[6][0]['p1'] += $value['p1'];
                $reArr[6][0]['v2'] += $value['v2'];
                $reArr[6][0]['p2'] += $value['p2'];
            }
        } else {
            $reArr[6][0]['s'] = 'S7';
            $reArr[6][0]['v1'] = 0;
            $reArr[6][0]['p1'] = 0;
            $reArr[6][0]['v2'] = 0;
            $reArr[6][0]['p2'] =  0;
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);
        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        $sheet_title = array('课程编号', '动画1', '交互1', '交互2', '动画2', '动画3', '交互3', '动画4', '交互4');

        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }

        $PHPSheet->getColumnDimension('A')->setWidth(20);
        $PHPSheet->getColumnDimension('B')->setWidth(15);
        $PHPSheet->getColumnDimension('C')->setWidth(15);
        $PHPSheet->getColumnDimension('D')->setWidth(15);
        $PHPSheet->getColumnDimension('E')->setWidth(15);
        $PHPSheet->getColumnDimension('F')->setWidth(15);
        $PHPSheet->getColumnDimension('G')->setWidth(15);
        $PHPSheet->getColumnDimension('H')->setWidth(15);
        $PHPSheet->getColumnDimension('I')->setWidth(15);

        //数据
        foreach ($reArr as $key => $value) {
            $row = $key + 2;
            for ($i = 0; $i < count($letter); $i++) {
                $PHPSheet->getStyle($letter[$i] . $row)->getAlignment()->setWrapText(true);
                $PHPSheet->setCellValue('A' . $row, ' ' . $value[0]['s'])->getStyle('A' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPSheet->setCellValue('B' . $row, ' ' . $value[0]['v1'])->getStyle('B' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $PHPSheet->setCellValue('C' . $row, ' ' . $value[0]['p1'])->getStyle('C' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if (isset($value[0]['v2'])) {
                    $PHPSheet->setCellValue('E' . $row, ' ' . $value[0]['v2'])->getStyle('E' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                $PHPSheet->setCellValue('D' . $row, ' ' . $value[0]['p2'])->getStyle('D' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if (isset($value[0]['v3'])) {
                    $PHPSheet->setCellValue('F' . $row, ' ' . $value[0]['v3'])->getStyle('F' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                if (isset($value[0]['p3'])) {
                    $PHPSheet->setCellValue('G' . $row, ' ' . $value[0]['p3'])->getStyle('G' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                if (isset($value[0]['p4'])) {
                    $PHPSheet->setCellValue('I' . $row, ' ' . $value[0]['p4'])->getStyle('I' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
                if (isset($value[0]['v4'])) {
                    $PHPSheet->setCellValue('H' . $row, ' ' . $value[0]['v4'])->getStyle('H' . $row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
            }
            ob_flush();
            flush();
        }


        $filename = '课程学习分布' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //学习统计
    public function study_count()
    {
        // halt(request()->param());
        $arrpa = request()->param();
        unset($arrpa['token']);
        // $arr1 = [
        //     "number" => "",
        //     "type" => [],
        //     "name" => "",
        //     "phone" => "",
        //     "limit" => 20,
        //     "page" => 1
        // ];
        // if ($arrpa === $arr1) {
        //     $return = Cache::get('study_count_1');
        //     if ($return) {
        //         $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        //         return json($data);
        //     }
        // }
        // $arr2 = [
        //     "number" => "",
        //     "type" => [],
        //     "name" => "",
        //     "phone" => "",
        //     "limit" => 20,
        //     "page" => 2
        // ];
        // if ($arrpa === $arr2) {
        //     $return = Cache::get('study_count_2');
        //     if ($return) {
        //         $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        //         return json($data);
        //     }
        // }
        $where[] = ['id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['type', 'in', $type];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $cachekey = md5($number . $name . $phone . implode(',', $type).$page.$limit . 'user' . 'cm_course' . 'cm_course_info');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')->where($where)->field('open_id,number,name,wx_phone,type')->page($page, $limit)->order('id')->select()->toArray();

            foreach ($list as $key => $value) {
                if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$key]['type_name'] = 'P-患者';
                } elseif ($value['type'] == '2') {
                    $list[$key]['type_name']  = 'H-高危人群';
                } elseif ($value['type'] == '3') {
                    $list[$key]['type_name']  = 'R-缓解期患者';
                } elseif ($value['type'] == '4') {
                    $list[$key]['type_name']  = '高危-分数';
                } elseif ($value['type'] == '5') {
                    $list[$key]['type_name']  = '患者-B1';
                } elseif ($value['type'] == '6') {
                    $list[$key]['type_name'] = '缓解期-B2';
                } elseif ($value['type'] == '7') {
                    $list[$key]['type_name']  = 'P2-患者轻度';
                } elseif ($value['type'] == '8') {
                    $list[$key]['type_name'] = 'P3-患者中度';
                } elseif ($value['type'] == '9') {
                    $list[$key]['type_name'] = 'P4-患者重度';
                } elseif ($value['type'] == '12') {
                    $list[$key]['type_name'] = 'P5-自曝患者';
                } else {
                    $list[$key]['type_name'] = '游客';
                }

                $result = Db::query("SELECT a.course,count(b.id) AS counts FROM `cm_course` `a` LEFT JOIN `cm_course_info` `b` ON `a`.`course` = b.course AND b.open_id = '{$value['open_id']}' AND b.etime > 0 GROUP BY `course`");


                $all_counts  = 0;
                $all_reviwe  = 0;
                $all_study   = 0;
                $study_finish = '是';
                $study_finish_time = '';
                foreach ($result as $k => $v) {
                    if ($v['course'] == 1) {
                        $field = 'one_status';
                    } elseif ($v['course'] == 2) {
                        $field = 'two_status';
                    } elseif ($v['course'] == 3) {
                        $field = 'three_status';
                    } elseif ($v['course'] == 4) {
                        $field = 'four_status';
                    } elseif ($v['course'] == 5) {
                        $field = 'five_status';
                    } elseif ($v['course'] == 6) {
                        $field = 'six_status';
                    } elseif ($v['course'] == 7) {
                        $field = 'seven_status';
                    }
                    if ($v['course'] != '合计') {
                        $result[$k]['course'] = 'S' . $v['course'];
                    }
                    $all_counts += $v['counts'];

                    //总回看次数
                    $reviwe_num = Db::name('course_info')->where('etime', '>', '0')->where(['new' => 2, 'course' => $v['course'], 'open_id' => $value['open_id']])->count();
                    $all_reviwe += $reviwe_num;
                    $result[$k]['reviwe_num'] = $reviwe_num;
                    //总学习时长
                    $study_time = Db::name('course_info')->where('etime', '>', '0')->where(['course' => $v['course'], 'open_id' => $value['open_id']])->sum('ltime');

                    $all_study += $study_time;
                    $result[$k]['study_time'] = diff_time($study_time) ?: '0';
                    //平均学习时长
                    if ($v['counts']) {
                        $result[$k]['average_time'] = diff_time(intval($study_time / $v['counts'])) ?: '0';
                    } else {
                        $result[$k]['average_time'] = diff_time(intval($study_time)) ?: '0';
                    }

                    //判断是否学完该课程
                    $check_finish = Db::name('course_record')->where(['open_id' => $value['open_id']])->find();
                    if ($check_finish[$field] == 2) {
                        $result[$k]['study_finish'] = '是';
                        //学完时间
                        $finish_time = Db::name('course_info')->where(['course' => $v['course'], 'open_id' => $value['open_id'], 'new' => 1])->where('etime', '>', '0')->order('id desc')->field('etime')->find();
                        if ($finish_time['etime']) {
                            $result[$k]['study_finish_time'] = date('Y-m-d H:i', $finish_time['etime']);
                        }
                        if ($field == 'seven_status') {
                            if ($finish_time['etime']) {
                                $study_finish_time = date('Y-m-d H:i', $finish_time['etime']);
                            }
                        }
                    } else {
                        $result[$k]['study_finish'] = '否';
                        $result[$k]['study_finish_time'] = ''; //学完时间
                        $study_finish = '否';
                    }
                    if ($v['course'] == '合计') {
                        $result[$k]['counts']       = $all_counts;
                        $result[$k]['reviwe_num']   = $all_reviwe;
                        $result[$k]['study_time']   = diff_time($all_study);
                        if ($all_counts) {
                            $result[$k]['average_time'] = diff_time(intval($all_study / $all_counts));
                        } else {
                            $result[$k]['average_time'] = diff_time(intval($all_study));
                        }

                        $result[$k]['study_finish'] = $study_finish;
                        $result[$k]['study_finish_time'] = $study_finish_time;
                    }
                }
                $list[$key]['other'] = $result;
            }

            $total = Db::name('user')->where($where)->field('open_id')->count();
            $page_total = ceil($total / $limit);

            $return = [
                'list' => $list,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
            cache($cachekey, $return, 300);
        }
        // if ($arrpa === $arr1) {
        //     Cache::set('study_count_1', $return, 60);
        // }
        // if ($arrpa === $arr2) {
        //     Cache::set('study_count_2', $return, 60);
        // }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    //导出学习统计
    public function excel_study_count()
    {
        $where[] = ['id', '>', 0];
        $number = input('post.number');
        if ($number) {
            $where[] = ['number', 'like', $number . '%'];
        }
        $name = input('post.name');
        if ($name) {
            $where[] = ['name', 'like', $name . '%'];
        }
        $phone = input('post.phone');
        if ($phone) {
            $where[] = ['wx_phone', 'like', $phone . '%'];
        }
        $type = input('post.type/a', array());
        if ($type && !empty($type) && !in_array('10', $type)) {
            $where[] = ['type', 'in', $type];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')->where($where)->field('open_id,number,name,wx_phone,type')->page($page, $limit)->order('id')->select()->toArray();

        foreach ($list as $key => $value) {
            if ($value['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$key]['type_name'] = 'P-患者';
            } elseif ($value['type'] == '2') {
                $list[$key]['type_name']  = 'H-高危人群';
            } elseif ($value['type'] == '3') {
                $list[$key]['type_name']  = 'R-缓解期患者';
            } elseif ($value['type'] == '4') {
                $list[$key]['type_name']  = '高危-分数';
            } elseif ($value['type'] == '5') {
                $list[$key]['type_name']  = '患者-B1';
            } elseif ($value['type'] == '6') {
                $list[$key]['type_name'] = '缓解期-B2';
            } elseif ($value['type'] == '7') {
                $list[$key]['type_name']  = 'P2-患者轻度';
            } elseif ($value['type'] == '8') {
                $list[$key]['type_name'] = 'P3-患者中度';
            } elseif ($value['type'] == '9') {
                $list[$key]['type_name'] = 'P4-患者重度';
            } elseif ($value['type'] == '12') {
                $list[$key]['type_name'] = 'P5-自曝患者';
            } else {
                $list[$key]['type_name'] = '游客';
            }

            $result = Db::query("SELECT a.course,count(b.id) AS counts FROM `cm_course` `a` LEFT JOIN `cm_course_info` `b` ON `a`.`course` = b.course AND b.open_id = '{$value['open_id']}' AND b.etime > 0 GROUP BY `course`");


            $all_counts  = 0;
            $all_reviwe  = 0;
            $all_study   = 0;
            $study_finish = '是';
            $study_finish_time = '';
            foreach ($result as $k => $v) {
                if ($v['course'] == 1) {
                    $field = 'one_status';
                } elseif ($v['course'] == 2) {
                    $field = 'two_status';
                } elseif ($v['course'] == 3) {
                    $field = 'three_status';
                } elseif ($v['course'] == 4) {
                    $field = 'four_status';
                } elseif ($v['course'] == 5) {
                    $field = 'five_status';
                } elseif ($v['course'] == 6) {
                    $field = 'six_status';
                } elseif ($v['course'] == 7) {
                    $field = 'seven_status';
                }
                if ($v['course'] != '合计') {
                    $result[$k]['course'] = 'S' . $v['course'];
                }
                $all_counts += $v['counts'];

                //总回看次数

                $reviwe_num = Db::name('course_info')->where('etime', '>', '0')->where(['new' => 2, 'course' => $v['course']])->count();
                $all_reviwe += $reviwe_num;
                $result[$k]['reviwe_num'] = $reviwe_num;
                //总学习时长
                $study_time = Db::name('course_info')->where('etime', '>', '0')->where(['course' => $v['course']])->sum('ltime');

                $all_study += $study_time;
                $result[$k]['study_time'] = diff_time($study_time) ?: '0';
                //平均学习时长
                if ($v['counts']) {
                    $result[$k]['average_time'] = diff_time(intval($study_time / $v['counts'])) ?: '0';
                } else {
                    $result[$k]['average_time'] = diff_time(intval($study_time)) ?: '0';
                }

                //判断是否学完该课程
                $check_finish = Db::name('course_record')->where(['open_id' => $value['open_id']])->find();
                if ($check_finish[$field] == 2) {
                    $result[$k]['study_finish'] = '是';
                    //学完时间
                    $finish_time = Db::name('course_info')->where(['course' => $v['course'], 'open_id' => $value['open_id'], 'new' => 1])->where('etime', '>', '0')->order('id desc')->field('etime')->find();
                    if ($finish_time['etime']) {
                        $result[$k]['study_finish_time'] = date('Y-m-d H:i', $finish_time['etime']);
                    }
                    if ($field == 'seven_status') {
                        if ($finish_time['etime']) {
                            $study_finish_time = date('Y-m-d H:i', $finish_time['etime']);
                        }
                    }
                } else {
                    $result[$k]['study_finish'] = '否';
                    $result[$k]['study_finish_time'] = ''; //学完时间
                    $study_finish = '否';
                }
                if ($v['course'] == '合计') {
                    $result[$k]['counts']       = $all_counts;
                    $result[$k]['reviwe_num']   = $all_reviwe;
                    $result[$k]['study_time']   = diff_time($all_study);
                    if ($all_counts) {
                        $result[$k]['average_time'] = diff_time(intval($all_study / $all_counts));
                    } else {
                        $result[$k]['average_time'] = diff_time(intval($all_study));
                    }

                    $result[$k]['study_finish'] = $study_finish;
                    $result[$k]['study_finish_time'] = $study_finish_time;
                }
            }
            $list[$key]['other'] = $result;
        }

        // return json($list);
        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '手机号', '用户分类', '课程编号', '总点击次数', '回看次数', '总学习时长', '平均每次学习时长', '是否学完', '学完时间');
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
        $PHPSheet->getColumnDimension('L')->setWidth(15);
        $PHPSheet->getColumnDimension('M')->setWidth(20);

        //数据
        $n = 8;
        foreach ($list as $k => $v) {
            $row = $k + 2;
            $s = 2 + $k * $n;
            $e = 9 + $k * $n;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                if ($j < 6) {
                    $PHPSheet->mergeCells($letter[$j] . $s . ':' . $letter[$j] . $e); //合并单元格
                    $PHPSheet->getStyle($letter[$j] . $s)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $PHPSheet->getStyle($letter[$j] . $s . ':' . $letter[$j] . $e)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }

                $PHPSheet->setCellValue('A' . $s, ' ' . $num);
                $PHPSheet->setCellValue('B' . $s, ' ' . $v['open_id']);
                $PHPSheet->setCellValue('C' . $s, ' ' . $v['number']);
                $PHPSheet->setCellValue('D' . $s, ' ' . $v['name']);
                $PHPSheet->setCellValue('E' . $s, ' ' . $v['wx_phone']);
                $PHPSheet->setCellValue('F' . $s, ' ' . $v['type_name']);
                // dump($v['open_id']);
                $n = 0;
                for ($i = 0; $i < count($v['other']); $i++) {
                    $rows = 2 + count($v['other']) * $k + $i;
                    if ($j > 5) {
                        $PHPSheet->getStyle($letter[$j] . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $PHPSheet->getStyle($letter[$j] . $rows . ':' . $letter[$j] . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    }
                    $PHPSheet->setCellValue('G' . $rows, ' ' . $v['other'][$i]['course']);
                    $PHPSheet->setCellValue('H' . $rows, ' ' . $v['other'][$i]['counts']);
                    $PHPSheet->setCellValue('I' . $rows, ' ' . $v['other'][$i]['reviwe_num']);
                    $PHPSheet->setCellValue('J' . $rows, ' ' . $v['other'][$i]['study_time']);
                    $PHPSheet->setCellValue('K' . $rows, ' ' . $v['other'][$i]['average_time']);
                    $PHPSheet->setCellValue('L' . $rows, ' ' . $v['other'][$i]['study_finish']);
                    $PHPSheet->setCellValue('M' . $rows, ' ' . $v['other'][$i]['study_finish_time']);
                    $n++;
                }
            }
            ob_flush();
            flush();
        }
        $filename = '学习统计' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //学习详情
    public function course_info()
    {
        $where[] = ['b.etime', '>', 0];
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where['b.etime'] = ['<', strtotime($etime)];
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
        $share = input('post.share/a', array());
        if ($share && !in_array('10', $share)) {
            $where[] = ['b.share', 'in', $share];
        }
        $new = input('post.new/a', array());
        if ($new && !in_array('10', $new)) {
            $where[] = ['b.new', 'in', $new];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 50);

        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . implode(',', $course) . implode(',', $share) . implode(',', $new) . $page . $limit . 'user' . 'cm_course_info');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {
            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_course_info b', 'a.open_id = b.open_id')
                ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.stime,b.etime,b.long_time,b.content_start,b.content_end,b.share,b.new')
                //->field('b.id')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();
            foreach ($list as $key => $value) {
                $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
                $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
                $list[$key]['courses'] = 'S' . $value['course'];
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

                if ($value['content_start'] && $value['content_end']) {
                    $ex_content_start = explode('-', $value['content_start']);

                    if ($ex_content_start[1] == 0) { //动画
                        $list[$key]['content_start'] = 'S' . $value['course'] . '-动画' . $ex_content_start[0];
                    } else { //交互
                        $list[$key]['content_start'] = 'S' . $value['course'] . '-交互' . $ex_content_start[0];
                    }

                    $ex_content_end = explode('-', $value['content_end']);
                    if ($ex_content_end[1] == 0) { //动画
                        if ($ex_content_end[0] == 10) {
                            $list[$key]['content_end'] = 'S' . $value['course'] . '-结束';
                        } else {
                            $list[$key]['content_end'] = 'S' . $value['course'] . '-动画' . $ex_content_end[0];
                        }
                    } else { //交互
                        $list[$key]['content_end'] = 'S' . $value['course'] . '-交互' . $ex_content_end[0];
                        // if ($value['course'] == 1) {
                        //     if ($ex_content_end[0] == 10) {
                        //         $list[$key]['content_end'] = 'S' . $value['course'] . '-结束' . $ex_content_end[0];
                        //     } else {
                        //         $list[$key]['content_end'] = 'S' . $value['course'] . '-交互' . $ex_content_end[0];
                        //     }
                        // } elseif ($value['course'] == 2) {

                        // } elseif ($value['course'] == 3) {
                        // } elseif ($value['course'] == 4) {
                        // } elseif ($value['course'] == 5) {
                        // } elseif ($value['course'] == 6) {
                        // } elseif ($value['course'] == 7) {
                        // }
                    }
                }


                if ($value['share'] == 1) {
                    $list[$key]['share_name'] = '是';
                } else {
                    $list[$key]['share_name'] = '否';
                }
                if ($value['new'] == 1) {
                    $list[$key]['new_name'] = '否';
                } else {
                    $list[$key]['new_name'] = '是';
                }
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_course_info b', 'a.open_id = b.open_id')
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

    //导出学习详情
    public function excel_course_info()
    {
        $where[] = ['b.etime', '>', 0];
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['b.etime', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where['b.etime'] = ['<', strtotime($etime)];
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
        $share = input('post.share/a', array());
        if ($share && !in_array('10', $share)) {
            $where[] = ['b.share', 'in', $share];
        }
        $new = input('post.new/a', array());
        if ($new && !in_array('10', $new)) {
            $where[] = ['b.new', 'in', $new];
        }

        $page = input('post.page', 1);
        $limit = input('post.limit', 50);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_course_info b', 'a.open_id = b.open_id')
            ->field('a.open_id,a.number,a.name,a.wx_phone,a.type,b.course,b.stime,b.etime,b.long_time,b.content_start,b.content_end,b.share,b.new')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['stime'] = date('Y-m-d H:i', $value['stime']);
            $list[$key]['etime'] = date('Y-m-d H:i', $value['etime']);
            $list[$key]['courses'] = 'S' . $value['course'];
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


            if ($value['content_start'] && $value['content_end']) {
                $ex_content_start = explode('-', $value['content_start']);

                if ($ex_content_start[1] == 0) { //动画
                    $list[$key]['content_start'] = 'S' . $value['course'] . '-动画' . $ex_content_start[0];
                } else { //交互
                    $list[$key]['content_start'] = 'S' . $value['course'] . '-交互' . $ex_content_start[0];
                }

                $ex_content_end = explode('-', $value['content_end']);
                if ($ex_content_end[1] == 0) { //动画
                    if ($ex_content_end[0] == 10) {
                        $list[$key]['content_end'] = 'S' . $value['course'] . '-结束';
                    } else {
                        $list[$key]['content_end'] = 'S' . $value['course'] . '-动画' . $ex_content_end[0];
                    }
                } else { //交互
                    $list[$key]['content_end'] = 'S' . $value['course'] . '-交互' . $ex_content_end[0];
                    // if ($value['course'] == 1) {
                    //     if ($ex_content_end[0] == 10) {
                    //         $list[$key]['content_end'] = 'S' . $value['course'] . '-结束' . $ex_content_end[0];
                    //     } else {
                    //         $list[$key]['content_end'] = 'S' . $value['course'] . '-交互' . $ex_content_end[0];
                    //     }
                    // } elseif ($value['course'] == 2) {

                    // } elseif ($value['course'] == 3) {
                    // } elseif ($value['course'] == 4) {
                    // } elseif ($value['course'] == 5) {
                    // } elseif ($value['course'] == 6) {
                    // } elseif ($value['course'] == 7) {
                    // }
                }
            }

            if ($value['share'] == 1) {
                $list[$key]['share_name'] = '是';
            } else {
                $list[$key]['share_name'] = '否';
            }
            if ($value['new'] == 1) {
                $list[$key]['new_name'] = '否';
            } else {
                $list[$key]['new_name'] = '是';
            }
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N');
        $sheet_title = array('序号', '用户ID', '编码', '姓名', '微信手机号', '患者分类', '课程编号', '学习开始时间', '学习结束时间', '学习时长', '开始节点', '结束节点', '是否转发', '是否回复');
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
        $PHPSheet->getColumnDimension('H')->setWidth(20);
        $PHPSheet->getColumnDimension('I')->setWidth(20);
        $PHPSheet->getColumnDimension('J')->setWidth(20);
        $PHPSheet->getColumnDimension('K')->setWidth(15);
        $PHPSheet->getColumnDimension('L')->setWidth(15);
        $PHPSheet->getColumnDimension('M')->setWidth(12);
        $PHPSheet->getColumnDimension('N')->setWidth(12);

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
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['courses']);
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['stime']);
                $PHPSheet->setCellValue('I' . $row, ' ' . $v['etime']);
                $PHPSheet->setCellValue('J' . $row, ' ' . $v['long_time']);
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['content_start']);
                $PHPSheet->setCellValue('L' . $row, ' ' . $v['content_end']);
                $PHPSheet->setCellValue('M' . $row, ' ' . $v['share_name']);
                $PHPSheet->setCellValue('N' . $row, ' ' . $v['new_name']);
            }
            ob_flush();
            flush();
        }
        $filename = '学习详情' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
