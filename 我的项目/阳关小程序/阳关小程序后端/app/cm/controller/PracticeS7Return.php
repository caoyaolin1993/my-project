<?php

declare(strict_types=1);

namespace app\cm\controller;

use app\util\ReturnCode;
use app\util\ReturnMsg;
use think\facade\Db;
use think\Request;

class PracticeS7Return
{
    /*第七节结束调用*/
    function sevenEnd(Request $request)
    {
        $open_id = $request->post('open_id');
        $find = Db::name('return')->where('open_id', $open_id)->find();
        if ($find) return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);

        $time = time();

        $oneWeekTime =  $time + 7 * 24 * 3600; //一周时间
        $oneMonthTime = $time + 30 * 24 * 3600;  //一月时间
        $threeMonthTime = $time + 90 * 24 * 3600; //三月时间
        $haltYearTime = $time + 180 * 24 * 3600; //半年
        $yearTime = $time + 365 * 24 * 3600; // 一年

        $data = [
            'open_id' => $open_id,
            'one_week' => $oneWeekTime,
            'one_month' => $oneMonthTime,
            'three_month' => $threeMonthTime,
            'halt_year' => $haltYearTime,
            'year' => $yearTime,
        ];

        Db::name('return')->insert($data);

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    /*点击回访调用*/
    function clickReturn(Request $request)
    {
        $open_id = $request->post('open_id');
        $time = time();

        $find = Db::name('return')->where('open_id', $open_id)->find();
        // 1不显示  2显示
        $data = [
            'one_week' => 1,
            'one_month' => 1,
            'three_month' => 1,
            'halt_year' => 1,
            'year' => 1,
        ];

        if (!$find) return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data);

        if ($time > $find['one_week']) $data['one_week'] = 2;
        if ($time > $find['one_month']) $data['one_month'] = 2;
        if ($time > $find['three_month']) $data['three_month'] = 2;
        if ($time > $find['halt_year']) $data['halt_year'] = 2;
        if ($time > $find['year']) $data['year'] = 2;

        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, $data);
    }

    function returnEnd(Request $request)
    {
       
        $open_id = $request->post('open_id');
        $type = $request->post('type');
        $location = $request->post('location');

        $find = Db::name('return_cut')->where('open_id', $open_id)->find();

        if ($find && $find['location']) {
            $locationArr = json_decode($find['location'], true);
        } else {
            $locationArr = [
                'one_week' => 0,
                'one_month' => 0,
                'three_month' => 0,
                'halt_year' => 0,
                'year' => 0,
            ];
        }

        switch ($type) {
            case 'one_week':
                $locationArr['one_week'] = $location;
                break;
            case 'one_month':
                $locationArr['one_month'] = $location;
                break;
            case 'three_month':
                $locationArr['three_month'] = $location;
                break;
            case 'halt_year':
                $locationArr['halt_year'] = $location;
                break;
            case 'year':
                $locationArr['year'] = $location;
                break;
        }

        $inserArr = json_encode($locationArr);

        if ($find) {
            Db::name('return_cut')->where('open_id', $open_id)->update(['location' => $inserArr, 's_time' => time()]);
        } else {
            Db::name('return_cut')->insert(['open_id' => $open_id, 'location' => $inserArr, 's_time' => time()]);
        }
        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS);
    }

    function returnStart(Request $request)
    {
        $open_id = $request->post('open_id');
        $type = $request->post('type');
        $find =  Db::name('return_cut')->where('open_id', $open_id)->field('location,s_time')->find();
        // $returnArr = 0;
        if (!$find) {
            return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, 0);
        }else{
            if ($find['s_time']+24*60*60<time()) {
                return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS,0);
            }else{
                switch ($type) {
                    case 'one_week':
                        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, json_decode($find['location'],true)['one_week']);
                        break;
                    case 'one_month':
                        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, json_decode($find['location'],true)['one_month']);
                        break;
                    case 'three_month':
                        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, json_decode($find['location'],true)['three_month']);
                        break;
                    case 'halt_year':
                        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, json_decode($find['location'],true)['halt_year']);
                        break;
                    case 'year':
                        return_msg(ReturnCode::SUCCESS, ReturnMsg::SUCCESS, json_decode($find['location'],true)['year']);
                        break;
                }
            }
        }
    }
}
