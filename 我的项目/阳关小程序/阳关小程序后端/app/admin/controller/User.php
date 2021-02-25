<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 14:50
 */

namespace app\admin\controller;

use app\BaseController;
use app\util\Tools;
use app\util\ReturnCode;
use PHPExcel;
use PHPExcel_IOFactory;
use think\facade\Db;

header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST,OPTIONS');
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Headers:Authorization,token,Content-Type,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
class User extends AdminAuth
{
    /*用户管理*/
    public function index()
    {
        $where = [['id', '>', 0]];
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['first_login_time', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['first_login_time', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['first_login_time', 'between', [strtotime($stime), strtotime($etime)]];
        }
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
        $limit = input('post.limit', 20);

        $cachekey = md5($stime . $etime . $number . $name . $phone . implode(',', $type) . $page . $limit . 'user');

        // if (cache($cachekey)) {
        //     $return = cache($cachekey);
        // } else {
            $list = Db::name('user')
                ->where($where)
                ->page($page, $limit)
                ->order('id')
                ->select()->toArray();
            foreach ($list as $key => $value) {
                $list[$key]['first_login_time'] = date('Y-m-d H:i', $value['first_login_time']);
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

                if($value['type_way'] == '1'){
                    $list[$key]['type_way_name'] = '邀请码';
                }elseif($value['type_way'] == '2'){
                    $list[$key]['type_way_name'] = '资料匹配';
                }elseif($value['type_way'] == '3'){
                    $list[$key]['type_way_name'] = '分数';
                }elseif($value['type_way'] == '4'){
                    $list[$key]['type_way_name'] = 'PHQ9分数';
                }elseif($value['type_way'] == '5'){
                    $list[$key]['type_way_name'] = '问卷标记';
                }elseif($value['type_way'] == '6'){
                    $list[$key]['type_way_name'] = '手动';
                }else{
                    $list[$key]['type_way_name'] = '';
                }
            }

            $total = Db::name('user')->where($where)->count();
            $page_total = ceil($total / $limit);

            $return = [
                'list' => $list,
                'page_total' => $page_total,
                'page' => $page,
                'total' => $total
            ];
        //     cache($cachekey, $return, 300);
        // }

        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $return];
        return json($data);
    }

    /*查找用户信息*/
    public function info()
    {
        $id = input('post.id');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        /* 
        //验证患者编码是否存在
        $info = Db::name('user_code')->where('id', $id)->field('id,number,name,phone,type')->find();
        if (!$info) {
            $data = ['code' => ReturnCode::DB_READ_ERROR, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        if ($info['type'] == '1') { //患者分类：0=游客，1=患者，2=高危，3=缓解期，4=高危-分数，5=患者-B1,6=缓解期-B2
            $info['type_name'] = 'P-患者';
        } elseif ($info['type'] == '2') {
            $info['type_name'] = 'H-高危人群';
        } elseif ($info['type'] == '3') {
            $info['type_name'] = 'R-缓解期患者';
        } elseif ($info['type'] == '4') {
            $info['type_name'] = '高危-分数';
        } elseif ($info['type'] == '5') {
            $info['type_name'] = '患者-B1';
        } elseif ($info['type'] == '6') {
            $info['type_name'] = '缓解期-B2';
        } elseif ($info['type'] == '7') {
            $info['type'] = 'P2-患者轻度';
        } elseif ($info['type'] == '8') {
            $info['type'] = 'P3-患者中度';
        } elseif ($info['type'] == '9') {
            $info['type'] = 'P4-患者重度';
        } elseif ($info['type'] == '12') {
            $info['type'] = 'P5-自曝患者';
        } else {
            $info['type_name'] = '游客';
        }
        */

        $info = Db::name('user')->where('id',$id)->field('type,number,name,phone')->find();



        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功', 'data' => $info];
        return json($data);
    }

    /*修改用户分类*/
    public function edit()
    {
        $id     = input('post.id');
        $number = input('post.number');
        $name   = input('post.name');
        $phone  = input('post.phone');
        $type   = input('post.type');
        if (empty($id)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '参数有误', 'data' => []];
            return json($data);
        }
        if (empty($number)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写患者编码', 'data' => []];
            return json($data);
        }
        if (empty($name)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写姓名', 'data' => []];
            return json($data);
        }
        if (empty($phone)) {
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请填写手机号', 'data' => []];
            return json($data);
        }
        // if (empty($type) ) {
        //     $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '请选择患者分类', 'data' => []];
        //     return json($data);
        // }
        //判断该编号是否存在
        $check_num = Db::name('user')->where('number', $number)->find();
        if ($check_num) {
            $data = ['code' => ReturnCode::DATA_EXISTS, 'msg' => '该编号已被使用', 'data' => []];
            return json($data);
        }

        $check_user_code = Db::name('user_code')->where(['number'=>$number,'name'=>$name,'phone'=>$phone])->find();

        if(!$check_user_code){
            $data = ['code' => ReturnCode::DATA_EXISTS, 'msg' => '该信息编码表不存在', 'data' => []];
            return json($data);
        }

        $res = Db::name('user')->where('id', $id)->update(['type' => $type, 'number' => $number, 'name' => $name, 'phone' => $phone, 'type_way' => '6']);
        if ($res) {
            $data = ['code' => ReturnCode::SUCCESS, 'msg' => '成功'];
        } else {
            $data = ['code' => ReturnCode::DB_SAVE_ERROR, 'msg' => '失败'];
        }
        return json($data);
    }

    //批量导出
    function excel()
    {
        set_time_limit(0);

        $where = [['id', '>', 0]];
        $stime = input('post.stime');
        $etime = input('post.etime');
        if ($stime && empty($etime)) {
            $where[] = ['first_login_time', '>', strtotime($stime)];
        } elseif (empty($stime) && $etime) {
            $where[] = ['first_login_time', '<', strtotime($etime)];
        } elseif ($stime && $etime) {
            $where[] = ['first_login_time', 'between', [strtotime($stime), strtotime($etime)]];
        }
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

        $data = Db::name('user')->where($where)->order('id')->select()->toArray();
        foreach ($data as $key => $value) {
            $data[$key]['first_login_time'] = date('Y-m-d H:i', $value['first_login_time']);
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

            if ($value['type_way'] == '0') { //分类标记方式：1=邀请码，2=资料匹配，3=分数，4=PHQ9分数，5=问卷标记，0=空
                $data[$key]['type_way_name'] = '';
            } elseif ($value['type_way'] == '1' || $value['type_way'] == '7' || $value['type_way'] == '8' || $value['type_way'] == '9' || $value['type_way'] == '12') {
                $data[$key]['type_way_name'] = '邀请码';
            } elseif ($value['type_way'] == '2') {
                $data[$key]['type_way_name'] = '资料匹配';
            } elseif ($value['type_way'] == '3') {
                $data[$key]['type_way_name'] = '分数';
            } elseif ($value['type_way'] == '4') {
                $data[$key]['type_way_name'] = 'PHQ9分数';
            } elseif ($value['type_way'] == '5') {
                $data[$key]['type_way_name'] = '问卷标记';
            } elseif ($value['type_way'] == '6') {
                $data[$key]['type_way_name'] = '手动标记';
            }
        }

        $PHPExcel = new PHPExcel(); //实例化phpexcel
        $PHPSheet = $PHPExcel->getActiveSheet();
        // 操作第一个工作表
        $PHPExcel->setActiveSheetIndex(0);
        $PHPSheet->getRowDimension('2')->setRowHeight(28);

        $letter = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K');
        $sheet_title = array('序号', '用户ID', '昵称', '微信手机', '用户分类', '分类标记方式', '邀请码', '编码', '姓名', '手机号', '首次登录时间');
        for ($i = 0; $i < count($letter); $i++) {
            $PHPSheet->setCellValue($letter[$i] . '1', $sheet_title[$i]);
            $PHPSheet->getStyle($letter[$i] . '1')->getFont()->setSize(13)->setBold(true);
            //设置单元格内容水平居中
            $PHPSheet->getStyle($letter[$i] . '1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $PHPSheet->getColumnDimension('A')->setWidth(15);
        $PHPSheet->getColumnDimension('B')->setWidth(25);
        $PHPSheet->getColumnDimension('C')->setWidth(20);
        $PHPSheet->getColumnDimension('D')->setWidth(16);
        $PHPSheet->getColumnDimension('E')->setWidth(20);
        $PHPSheet->getColumnDimension('F')->setWidth(20);
        $PHPSheet->getColumnDimension('G')->setWidth(16);
        $PHPSheet->getColumnDimension('H')->setWidth(20);
        $PHPSheet->getColumnDimension('I')->setWidth(15);
        $PHPSheet->getColumnDimension('J')->setWidth(16);
        $PHPSheet->getColumnDimension('K')->setWidth(18);
        //数据
        foreach ($data as $k => $v) {
            $row = $k + 2;
            for ($j = 0; $j < count($letter); $j++) {
                $PHPSheet->getStyle($letter[$j] . $row)->getAlignment()->setWrapText(true);
                $num = $k + 1;
                $PHPSheet->setCellValue('A' . $row, ' ' . $num);
                $PHPSheet->setCellValue('B' . $row, ' ' . $v['open_id']);
                $PHPSheet->setCellValue('C' . $row, ' ' . $v['wx_nickname']);
                $PHPSheet->setCellValue('D' . $row, ' ' . $v['wx_phone']);
                $PHPSheet->setCellValue('E' . $row, ' ' . $v['type_name']);
                $PHPSheet->setCellValue('F' . $row, ' ' . $v['type_way_name']);
                $PHPSheet->setCellValue('G' . $row, ' ' . $v['code']);
                $PHPSheet->setCellValue('H' . $row, ' ' . $v['number']);
                $PHPSheet->setCellValue('I' . $row, ' ' . $v['name']);
                $PHPSheet->setCellValue('J' . $row, ' ' . $v['phone']);
                $PHPSheet->setCellValue('K' . $row, ' ' . $v['first_login_time']);
            }
            ob_flush();
            flush();
        }
        $filename = '用户管理' . date('Ymd');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel, "Excel2007"); //创建生成的格式
        header("Content-Disposition: attachment;filename=$filename.xlsx"); //下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件
    }
}
