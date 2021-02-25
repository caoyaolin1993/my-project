<?php

declare(strict_types=1);

namespace app\cm\controller;

use think\facade\Cache;
use think\facade\Db;

class Timing extends cmPrefix
{
    public function index()
    {
        $dsArr = Db::name('ds')->withoutField('id,kc_time_1')->select()->toArray();
        $dateStr = date('Y-m-d H:i');
        $str1 = substr($dateStr, 11, 4);
        $str2 = substr($dateStr, 0, 15);
        if ($dsArr) {
            foreach ($dsArr as $key => $value) {
                $template_id1 = $value['template_id1'];
                $lx_page = $value['lx_page'];

                $template_id2 = $value['template_id2'];
                $kc_page = $value['kc_page'];
                if ($value['lx_time']) {
                    $lx_str = date('Y-m-d H:i', $value['lx_time']);
                    $sub_str = substr($lx_str, 11, 4); //
                    if ($str1 == $sub_str) {
                        $lockArr = Db::name('lock')->where('open_id', $value['open_id'])->find();

                        $lx_data = "";
                        if ($lockArr['one_etime']) {
                            $lx_data = 1;
                        } else {
                            continue;
                        }
                        if ($lockArr['two_etime']) {
                            $lx_data = 2;
                        }
                        if ($lockArr['three_etime']) {
                            $lx_data = 3;
                        }
                        if ($lockArr['four_etime']) {
                            $lx_data = 4;
                        }
                        if ($lockArr['five_etime']) {
                            $lx_data = 5;
                        }
                        if ($lockArr['six_etime']) {
                            $lx_data = 6;
                        }
                        if ($lockArr['seven_etime']) {
                            $lx_data = 7;
                        }
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id1, $lx_page, 1, $lx_data);
                    }
                }

                if ($value['kc_time_2']) {
                    $kstr = date('Y-m-d H:i', $value['kc_time_2']);
                    $kstr_2 =  substr($kstr, 0, 15);

                    if ($str2 == $kstr_2) {
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id2, $kc_page, 2, 2);
                    }
                }

                if ($value['kc_time_3']) {
                    $kstr = date('Y-m-d H:i', $value['kc_time_3']);
                    $kstr_3 =  substr($kstr, 0, 15);

                    if ($str2 == $kstr_3) {
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id2, $kc_page, 2, 3);
                    }
                }

                if ($value['kc_time_4']) {
                    $kstr = date('Y-m-d H:i', $value['kc_time_4']);
                    $kstr_4 =  substr($kstr, 0, 15);

                    if ($str2 == $kstr_4) {
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id2, $kc_page, 2, 4);
                    }
                }

                if ($value['kc_time_5']) {
                    $kstr = date('Y-m-d H:i', $value['kc_time_5']);
                    $kstr_5 =  substr($kstr, 0, 15);

                    if ($str2 == $kstr_5) {
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id2, $kc_page, 2, 5);
                    }
                }


                if ($value['kc_time_6']) {
                    $kstr = date('Y-m-d H:i', $value['kc_time_6']);
                    $kstr_6 =  substr($kstr, 0, 15);

                    if ($str2 == $kstr_6) {
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id2, $kc_page, 2, 6);
                    }
                }

                if ($value['kc_time_7']) {
                    $kstr = date('Y-m-d H:i', $value['kc_time_7']);
                    $kstr_7 =  substr($kstr, 0, 15);

                    if ($str2 == $kstr_7) {
                        $access_token_use = $this->get_authorizer_access_token();

                        $this->subs_news($access_token_use, $value['public_open_id'], $template_id2, $kc_page, 2, 7);
                    }
                }
            }
        }
    }

    public function get_authorizer_access_token()
    {
        // if (Cache::get('authorizer_access_token')) {
        //     return Cache::get('authorizer_access_token');
        // }

        $obj = new ThirdParty();
        $component_access_token = $obj->get_token();
        $component_appid = 'wx84b9a45b5e839547';   //
        $authorizer_appid = 'wx094eef7bbffe7995';

        // $arr_a = Db::name('refresh_token')->where('id', 1)->find();
        $authorizer_refresh_token = Cache::get('authorizer_refresh_token');
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $component_access_token;

        $data['component_appid'] = $component_appid;
        $data['authorizer_appid'] = $authorizer_appid;
        $data['authorizer_refresh_token'] = $authorizer_refresh_token;

        $da = json_encode($data);
        $arr =  $obj->https_request($url, $da);
        $result = json_decode($arr, true);
        Cache::set('authorizer_access_token', $result['authorizer_access_token'], $result['expires_in']);
        Cache::set('authorizer_refresh_token', $result['authorizer_refresh_token']);
        return $result['authorizer_access_token'];
    }


    public function subs_news($access_token, $public_open_id, $template_id, $page, $type, $type_data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;

        //发送内容
        $data = [];

        //接收者（用户）的 openid 
        $data['touser'] = $public_open_id;

        //所需下发的订阅模板id
        $data['template_id'] = $template_id;

        $data['miniprogram'] = [
            "appid" => "wx6ada410c648a0fa9",
            "pagepath" => $page
        ];

        if ($type == 1) {
            switch ($type_data) {
                case 1:
                    $data['data'] = [
                        'first' => [
                            'value' => '今天的练习做了吗？快去小程序填写吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '走进抑郁：抑郁从何而来'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d',time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => "练习：\n1. 阅读本周课程小结\n2.记录愉快事件，并尝试着去做\n3.如感到紧张不安的时候，请使用放松训练\n坚持练习，就可以逐步调节情绪"
                        ],
                    ];
                    break;
                case 2:
                    $data['data'] = [
                        'first' => [
                            'value' => '今天的练习做了吗？快去小程序填写吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '记录活动，觉察抑郁思维'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d',time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => "练习：\n1.阅读本周课程总结和目标清单\n2.坚持记录一周的活动和心情\n3.坚持记录三栏思维日记\n坚持练习，就可以逐步调节情绪"
                        ],
                    ];

                    break;
                case 3:

                    $data['data'] = [
                        'first' => [
                            'value' => '今天的练习做了吗？快去小程序填写吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '激发活力，小心思维陷阱'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d',time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => "练习：\n1.阅读本周课程总结\n2.完成活动计划\n3.坚持记录四栏思维日记\n坚持练习，就可以逐步调节情绪"
                        ],
                    ];
                    break;
                case 4:
                    $data['data'] = [
                        'first' => [
                            'value' => '今天的练习做了吗？快去小程序填写吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '克服拖延，调整思维误区'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d',time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => "练习：\n1.阅读本周课程小结\n2.完成任务分解小步骤与活动计划\n3.坚持记录自动思维，评估当下想法\n坚持练习，就可以逐步调节情绪"
                        ],
                    ];
                    break;
                case 5:
                    $data['data'] = [
                        'first' => [
                            'value' => '今天的练习做了吗？快去小程序填写吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '解决问题，合理归因'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d',time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => "练习：\n1.阅读课程总结\n2.每天坚持归因练习\n3.完成解决问题各步骤与活动计划\n坚持练习，就可以逐步调节情绪"
                        ],
                    ];
                    break;
                case 6:
                    $data['data'] = [
                        'first' => [
                            'value' => '今天的练习做了吗？快去小程序填写吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '客观地看待自己、他人和世界'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d',time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => "练习：\n1.阅读课程总结\n2.找出内在信念，收集支持新信念的证据\n3.继续本周活动计划，做好活动记录\n坚持练习，就可以逐步调节情绪"
                        ],
                    ];
                    break;
                    // case 7:
                    // break;

            }
        } elseif ($type == 2) {
            switch ($type_data) {
                case 2:
                    $data['data'] = [
                        'first' => [
                            'value' => '你有新的课程解锁了，快去小程序学习吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '记录活动，觉察抑郁思维'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d', time())
                        ],
                        'remark' => [
                            'value' => '每学习完一节课，7天后解锁新的课程'
                        ],
                    ];
                    break;
                case 3:

                    $data['data'] = [
                        'first' => [
                            'value' => '你有新的课程解锁了，快去小程序学习吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '激发活力，小心思维陷阱'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d', time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => '每学习完一节课，7天后解锁新的课程'
                        ],
                    ];
                    break;
                case 4:
                    $data['data'] = [
                        'first' => [
                            'value' => '你有新的课程解锁了，快去小程序学习吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '克服拖延，调整思维误区'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d', time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => '每学习完一节课，7天后解锁新的课程'
                        ],
                    ];
                    break;
                case 5:
                    $data['data'] = [
                        'first' => [
                            'value' => '你有新的课程解锁了，快去小程序学习吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '解决问题，合理归因'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d', time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => '每学习完一节课，7天后解锁新的课程'
                        ],
                    ];
                    break;
                case 6:
                    $data['data'] = [
                        'first' => [
                            'value' => '你有新的课程解锁了，快去小程序学习吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '客观地看待自己、他人和世界'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d', time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => '每学习完一节课，7天后解锁新的课程'
                        ],
                    ];
                    break;
                case 7:
                    $data['data'] = [
                        'first' => [
                            'value' => '你有新的课程解锁了，快去小程序学习吧',
                            'color' => '#bc723c'
                        ],
                        'keyword1' => [
                            'value' => '维持效果，预防复发'
                        ],
                        'keyword2' => [
                            'value' => date('Y-m-d', time())
                        ],
                        // 'keyword3' => [
                        //   'value' => ''
                        // ],
                        'remark' => [
                            'value' => '每学习完一节课，7天后解锁新的课程'
                        ],
                    ];
                    break;
            }
        }


        $this->https_request($url, json_encode($data));
    }

    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
