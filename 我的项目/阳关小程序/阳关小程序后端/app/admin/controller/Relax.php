<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\Controller;
use app\util\ReturnCode;
use think\facade\Db;
use PHPExcel;
use PHPExcel_IOFactory;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class Relax extends AdminAuth
{
    //放松统计
    public function relaxSta()
    {

        $cachekey = md5('relaxSta' . 'relax_click');
        if (cache($cachekey)) {
            $rArr = cache($cachekey);
        } else {

            //腹式呼吸法点击次数
            $rsC_1 =  Db::name('relax_click')->where('relax_type', 1)->sum('click_num');

            //腹式呼吸法转发次数
            $rsF_1 =  Db::name('relax_click')->where('relax_type', 1)->sum('forw_num');

            //腹式呼吸法放松总时长

            $toTime = Db::name('relax_tra')->where('relax_type', 1)->sum('ltime');
            $toTime_1 = diff_time($toTime);

            //腹式呼吸法放松平均时长
            if ($rsC_1) {
                $avTime = round(($toTime / $rsC_1));
            } else {
                $avTime = $toTime;
            }
            $avTime_1 = diff_time($avTime);

            //渐进式肌肉放松训练点击次数
            $rsC_2 =  Db::name('relax_click')->where('relax_type', 2)->sum('click_num');

            //渐进式肌肉放松训练转发次数
            $rsF_2 =  Db::name('relax_click')->where('relax_type', 2)->sum('forw_num');

            //渐进式肌肉放松训练放松总时长
            $toTime = Db::name('relax_tra')->where('relax_type', 2)->sum('ltime');

            $toTime_2 = diff_time($toTime);

            //渐进式肌肉放松训练平均总时长
            if ($rsC_2) {
                $avTime = round(($toTime / $rsC_2));
            } else {
                $avTime = $toTime;
            }
            $avTime_2 = diff_time($avTime);

            //音乐放松点击次数
            $rsC_3 =  Db::name('relax_click')->where('relax_type', 3)->sum('click_num');

            //音乐放松转发次数
            $rsF_3 =  Db::name('relax_click')->where('relax_type', 3)->sum('forw_num');

            //音乐放松放松总时长

            $toTime = Db::name('relax_tra')->where('relax_type', 3)->sum('ltime');

            $toTime_3 = diff_time($toTime);

            //音乐放松放松平均时长
            if ($rsC_3) {
                $avTime = round(($toTime / $rsC_3));
            } else {
                $avTime = $toTime;
            }

            $avTime_3 = diff_time($avTime);

            $rArr = [
                [
                    'name' => '腹式呼吸法',
                    'clickCount' => $rsC_1,
                    'zCount' => $rsF_1,
                    'yTime' => $toTime_1,
                    'aTime' => $avTime_1
                ],
                [
                    'name' => '渐进式肌肉放松训练',
                    'clickCount' => $rsC_2,
                    'zCount' => $rsF_2,
                    'yTime' => $toTime_2,
                    'aTime' => $avTime_2
                ],
                [
                    'name' => '音乐放松',
                    'clickCount' => $rsC_3,
                    'zCount' => $rsF_3,
                    'yTime' => $toTime_3,
                    'aTime' => $avTime_3
                ],
            ];
            cache($cachekey, $rArr, 300);
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $rArr];
        return json($data);
    }

    //导出放松统计
    public function excel_relaxSta()
    {
        //腹式呼吸法点击次数
        $rsC_1 =  Db::name('relax_click')->where('relax_type', 1)->sum('click_num');

        //腹式呼吸法转发次数
        $rsF_1 =  Db::name('relax_click')->where('relax_type', 1)->sum('forw_num');

        //腹式呼吸法放松总时长

        $toTime = Db::name('relax_tra')->where('relax_type', 1)->sum('ltime');
        $toTime_1 = diff_time($toTime);

        //腹式呼吸法放松平均时长
        if ($rsC_1) {
            $avTime = round(($toTime / $rsC_1));
        } else {
            $avTime = $toTime;
        }
        $avTime_1 = diff_time($avTime);

        //渐进式肌肉放松训练点击次数
        $rsC_2 =  Db::name('relax_click')->where('relax_type', 2)->sum('click_num');

        //渐进式肌肉放松训练转发次数
        $rsF_2 =  Db::name('relax_click')->where('relax_type', 2)->sum('forw_num');

        //渐进式肌肉放松训练放松总时长
        $toTime = Db::name('relax_tra')->where('relax_type', 2)->sum('ltime');

        $toTime_2 = diff_time($toTime);

        //渐进式肌肉放松训练平均总时长
        if ($rsC_2) {
            $avTime = round(($toTime / $rsC_2));
        } else {
            $avTime = $toTime;
        }
        $avTime_2 = diff_time($avTime);

        //音乐放松点击次数
        $rsC_3 =  Db::name('relax_click')->where('relax_type', 3)->sum('click_num');

        //音乐放松转发次数
        $rsF_3 =  Db::name('relax_click')->where('relax_type', 3)->sum('forw_num');

        //音乐放松放松总时长

        $toTime = Db::name('relax_tra')->where('relax_type', 3)->sum('ltime');

        $toTime_3 = diff_time($toTime);

        //音乐放松放松平均时长
        if ($rsC_3) {
            $avTime = round(($toTime / $rsC_3));
        } else {
            $avTime = $toTime;
        }

        $avTime_3 = diff_time($avTime);

        $rArr = [
            [
                'name' => '腹式呼吸法',
                'clickCount' => $rsC_1,
                'zCount' => $rsF_1,
                'yTime' => $toTime_1,
                'aTime' => $avTime_1
            ],
            [
                'name' => '渐进式肌肉放松训练',
                'clickCount' => $rsC_2,
                'zCount' => $rsF_2,
                'yTime' => $toTime_2,
                'aTime' => $avTime_2
            ],
            [
                'name' => '音乐放松',
                'clickCount' => $rsC_3,
                'zCount' => $rsF_3,
                'yTime' => $toTime_3,
                'aTime' => $avTime_3
            ],
        ];
        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();

        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(25);

        $PHPSheet->getStyle('A1:E1')->getFont()->setSize(13)->setBold(true);

        $PHPSheet->setCellValue('A1', '训练名称');
        $PHPSheet->setCellValue('B1', '点击次数');
        $PHPSheet->setCellValue('C1', '转发次数');
        $PHPSheet->setCellValue('D1', '放松总时长');
        $PHPSheet->setCellValue('E1', '平均时长');


        $PHPSheet->getColumnDimension('A')->setWidth(18);
        $PHPSheet->getColumnDimension('B')->setWidth(12);
        $PHPSheet->getColumnDimension('C')->setWidth(12);
        $PHPSheet->getColumnDimension('D')->setWidth(12);
        $PHPSheet->getColumnDimension('E')->setWidth(12);

        $ar = array('腹式呼吸法', '渐进式肌肉放松训练', '音乐放松');
        foreach ($rArr as $k => $v) {
            $row = 2 + $k;
            $PHPSheet->setCellValue('A' . $row, $ar[$k]);
            $PHPSheet->setCellValue('B' . $row, $v['clickCount']);
            $PHPSheet->setCellValue('C' . $row, $v['zCount']);
            $PHPSheet->setCellValue('D' . $row, $v['yTime']);
            $PHPSheet->setCellValue('E' . $row, $v['aTime']);
        }

        //设置水平居中
        $PHPSheet->getStyle('A1:E4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $filename = '放松统计' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }

    //放松详情
    public function relaxDet()
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user' . 'cm_relax_tra');
        if (cache($cachekey)) {
            $return = cache($cachekey);
        } else {

            $list = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_relax_tra b', 'a.open_id=b.open_id')
                ->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.relax_type,b.stime,b.etime,b.ltime,b.play_etime,b.is_forw')
                ->page($page, $limit)
                ->order('a.id')
                ->select()->toArray();

            foreach ($list as $k => $v) {

                if ($v['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                    $list[$k]['type'] = 'P-患者';
                } elseif ($v['type'] == '2') {
                    $list[$k]['type'] = 'H-高危人群';
                } elseif ($v['type'] == '3') {
                    $list[$k]['type'] = 'R-缓解期患者';
                } elseif ($v['type'] == '4') {
                    $list[$k]['type'] = '高危-分数';
                } elseif ($v['type'] == '5') {
                    $list[$k]['type'] = '患者-B1';
                } elseif ($v['type'] == '6') {
                    $list[$k]['type'] = '缓解期-B2';
                } elseif ($v['type'] == '7') {
                    $list[$k]['type'] = 'P2-患者轻度';
                } elseif ($v['type'] == '8') {
                    $list[$k]['type'] = 'P3-患者中度';
                } elseif ($v['type'] == '9') {
                    $list[$k]['type'] = 'P4-患者重度';
                } elseif ($v['type'] == '12') {
                    $list[$k]['type'] = 'P5-自曝患者';
                } else {
                    $list[$k]['type'] = '游客';
                }

                if ($v['relax_type'] == 1) {
                    $list[$k]['relax_type'] = '腹式呼吸法';
                } elseif ($v['relax_type'] == 2) {
                    $list[$k]['relax_type']  = '渐进式肌肉放松训练';
                } elseif ($v['relax_type'] == 3) {
                    $list[$k]['relax_type']  = '音乐放松';
                }
                $list[$k]['stime'] = date('Y-m-d H:i:s', $v['stime']);
                $list[$k]['etime'] = date('Y-m-d H:i:s', $v['etime']);
                $list[$k]['ltime'] = diff_time($v['ltime']);
                $list[$k]['play_etime'] = $v['play_etime'] . ' ' . 'min';



                if ($v['is_forw'] == 1) {
                    $list[$k]['is_forw'] = '是';
                } elseif ($v['is_forw'] == 2) {
                    $list[$k]['is_forw']  = '否';
                }
            }

            $total = Db::name('user')
                ->alias('a')
                ->where($where)
                ->join('cm_relax_tra b', 'a.open_id=b.open_id')
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

    //导出放松详情
    public function excel_relaxDet()
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

        $page = input('post.page', 1);
        $limit = input('post.limit', 10);

        $list = Db::name('user')
            ->alias('a')
            ->where($where)
            ->join('cm_relax_tra b', 'a.open_id=b.open_id')
            ->field('a.open_id,a.name,a.wx_phone,a.type,a.number,b.relax_type,b.stime,b.etime,b.ltime,b.play_etime,b.is_forw')
            ->page($page, $limit)
            ->order('a.id')
            ->select()->toArray();

        foreach ($list as $k => $v) {

            if ($v['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
                $list[$k]['type'] = 'P-患者';
            } elseif ($v['type'] == '2') {
                $list[$k]['type'] = 'H-高危人群';
            } elseif ($v['type'] == '3') {
                $list[$k]['type'] = 'R-缓解期患者';
            } elseif ($v['type'] == '4') {
                $list[$k]['type'] = '高危-分数';
            } elseif ($v['type'] == '5') {
                $list[$k]['type'] = '患者-B1';
            } elseif ($v['type'] == '6') {
                $list[$k]['type'] = '缓解期-B2';
            } elseif ($v['type'] == '7') {
                $list[$k]['type'] = 'P2-患者轻度';
            } elseif ($v['type'] == '8') {
                $list[$k]['type'] = 'P3-患者中度';
            } elseif ($v['type'] == '9') {
                $list[$k]['type'] = 'P4-患者重度';
            } elseif ($v['type'] == '12') {
                $list[$k]['type'] = 'P5-自曝患者';
            } else {
                $list[$k]['type'] = '游客';
            }

            if ($v['relax_type'] == 1) {
                $list[$k]['relax_type'] = '腹式呼吸法';
            } elseif ($v['relax_type'] == 2) {
                $list[$k]['relax_type']  = '渐进式肌肉放松训练';
            } elseif ($v['relax_type'] == 3) {
                $list[$k]['relax_type']  = '音乐放松';
            }
            $list[$k]['stime'] = date('Y-m-d H:i:s', $v['stime']);
            $list[$k]['etime'] = date('Y-m-d H:i:s', $v['etime']);
            $list[$k]['ltime'] = diff_time($v['ltime']);
            $list[$k]['play_etime'] = $v['play_etime'] . ' ' . 'min';



            if ($v['is_forw'] == 1) {
                $list[$k]['is_forw'] = '是';
            } elseif ($v['is_forw'] == 2) {
                $list[$k]['is_forw']  = '否';
            }
        }


        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();

        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('1')->setRowHeight(25);

        $PHPSheet->getStyle('A1:K1')->getFont()->setSize(13)->setBold(true);

        $PHPSheet->setCellValue('A1', '用户ID');
        $PHPSheet->setCellValue('B1', '编码');
        $PHPSheet->setCellValue('C1', '姓名');
        $PHPSheet->setCellValue('D1', '微信手机号');
        $PHPSheet->setCellValue('E1', '患者分类');
        $PHPSheet->setCellValue('F1', '训练名称');
        $PHPSheet->setCellValue('G1', '放松开始时间');
        $PHPSheet->setCellValue('H1', '放松结束时间');
        $PHPSheet->setCellValue('I1', '放松时长');
        $PHPSheet->setCellValue('J1', '播放结束点');
        $PHPSheet->setCellValue('K1', '转发');

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

        foreach ($list as $k => $v) {
            $row = $k + 2;
            $PHPSheet->setCellValue('A' . $row, $v['open_id']);
            $PHPSheet->setCellValue('B' . $row, $v['number']);
            $PHPSheet->setCellValue('C' . $row, $v['name']);
            $PHPSheet->setCellValue('D' . $row, $v['wx_phone']);
            $PHPSheet->setCellValue('E' . $row, $v['type']);
            $PHPSheet->setCellValue('F' . $row, $v['relax_type']);
            $PHPSheet->setCellValue('G' . $row, $v['stime']);
            $PHPSheet->setCellValue('H' . $row, $v['etime']);
            $PHPSheet->setCellValue('I' . $row, $v['ltime']);
            $PHPSheet->setCellValue('J' . $row, $v['play_etime']);
            $PHPSheet->setCellValue('K' . $row, $v['is_forw']);
        }


        //设置水平居中
        $PHPSheet->getStyle('A1:K' . (count($list) + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


        $filename = '放松详情' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
