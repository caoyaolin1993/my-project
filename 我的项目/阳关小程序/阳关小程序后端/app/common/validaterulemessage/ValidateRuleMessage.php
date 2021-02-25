<?php

namespace app\common\validaterulemessage;

class ValidateRuleMessage
{
  //interactionTwoSave
  public $interactionTwoSaveValidate = [
    'date' => 'require',
    'activity' => 'require'
  ];

  public $interactionTwoSaveMessage = [
    'date.require' => ['code' => 10001, 'msg' => '日期不能为空'],
    'activity.require' => ['code' => 10002, 'msg' => '活动不能为空']
  ];

  // date  activity  time  pleasure  achievement
  public $dateActivityTimeValidate = [
    'date' => 'require',
    'activity' => 'require',
    'time' => 'require',
    'pleasure' => 'require',
    'achievement' => 'require'
  ];

  public $dateActivityTimeMessage = [
    'date.require' => ['code' => 10001, 'msg' => '日期不能为空'],
    'activity.require' => ['code' => 10002, 'msg' => '活动不能为空'],
    'time.require' => ['code' => 10003, 'msg' => '时间点不能为空'],
    'pleasure.require' => ['code' => 10004, 'msg' => '愉悦度不能为空'],
    'achievement.require' => ['code' => 10005, 'msg' => '成就感不能为空']
  ];

  //interactionThreeRead
  public $openIdRequireValidate = [
    'open_id' => 'require'
  ];

  public $openIdRequireMessage = [
    'open_id.require' => ['code' => 10001, 'msg' => '缺少必要参数/参数错误']
  ];

  //osnt
  public $osntValidate = [
    'open_id'  => 'require',
    'stime'  => 'require|unique:auto_think_s4',
    'new'  => 'require',
    'type'  => 'require',
    'tt_id'  => 'require',
  ];

  public $osntMessage = [
    'open_id.require' => ['code' => 10001, 'msg' => '缺少必要参数/参数错误'],
    'stime.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'new.require' => ['code' => 10003, 'msg' => '缺少必要参数/参数错误'],
    'type.require' => ['code' => 10004, 'msg' => '缺少必要参数/参数错误'],
    'tt_id.require' => ['code' => 10005, 'msg' => '缺少必要参数/参数错误'],
    'stime.unique' => ['code' => 10006, 'msg' => '开始时间已存在'],
  ];
  //stime open_id
  public $stimeOpenIdRequireValidate = [
    'open_id' => 'require',
    'stime' => 'require'
  ];

  public $stimeOpenIdRequireMessage = [
    'open_id.require' => ['code' => 10001, 'msg' => '缺少必要参数/参数错误'],
    'stime.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
  ];

  //stime open_id source
  public $stimeOpenIdSourceRequireValidate =  [
    'open_id' => 'require',
    'stime' => 'require',
    'source' => 'require|array'
  ];

  public $stimeOpenIdSourceRequireMessage = [
    'open_id.require' => [
      'code' => 10001,
      'msg' => '缺少必要参数/参数错误'
    ],
    'stime.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'source.require' => ['code' => 10003, 'msg' => '请填写活动安排'],
    'source.array' => ['code' => 10004, 'msg' => '只能为数组格式'],
  ];

  // mood  fraction
  public $moodFractionRequireValidate = [
    'mood' => 'require',
    'fraction' => 'require',
  ];
  public $moodFractionRequireMessage = [
    'mood.require' => ['code' => 10001, 'msg' => '请填写心情'],
    'fraction.require' => ['code' => 10002, 'msg' => '请填写分数']
  ];

  // think  fraction  misunderstanding
  public $thinkFractionMisRequireValidate = [
    'think' => 'require',
    'fraction' => 'require',
    'misunderstanding' => 'require',
  ];
  public $thinkFractionMisRequireMessage = [
    'think.require' => ['code' => 10001, 'msg' => '请填写思维'],
    'fraction.require' => ['code' => 10002, 'msg' => '请填写分数'],
    'misunderstanding.require' => ['code' => 10003, 'msg' => '请填写思维误区'],
  ];


  public $situationRequireValidate = [
    'mood' => 'require|array',
    'think' => 'require|array',
    'situation' => 'require',
  ];
  public $situationRequireMessage = [
    'mood.require' => ['code' => 10001, 'msg' => '请填写心情'],
    'think.require' => ['code' => 10002, 'msg' => '请填写思维'],
    'situation.require' => ['code' => 10002, 'msg' => '请填写情境'],
    'mood.array' => ['code' => 10002, 'msg' => '格式错误'],
    'think.array' => ['code' => 10002, 'msg' => '格式错误'],
  ];

  public $osntAllValidate = [
    'open_id'  => 'require',
    'stime'  => 'require|unique:auto_think_s4',
    'new'  => 'require',
    'type'  => 'require',
    'tt_id'  => 'require',
    'q5_1'  => 'require',
    'q5_2'  => 'require',
    'q6'  => 'require',
    'q7_1'  => 'require',
    'q7_2'  => 'require',
    'q7_3'  => 'require',
    'q8_1'  => 'require',
    'q8_2'  => 'require',
    'q9'  => 'require',
    'q10'  => 'require|array',

  ];

  public $osntAllMessage = [
    'open_id.require' => ['code' => 10001, 'msg' => '缺少必要参数/参数错误'],
    'stime.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'new.require' => ['code' => 10003, 'msg' => '缺少必要参数/参数错误'],
    'type.require' => ['code' => 10004, 'msg' => '缺少必要参数/参数错误'],
    'tt_id.require' => ['code' => 10005, 'msg' => '缺少必要参数/参数错误'],
    'stime.unique' => ['code' => 10006, 'msg' => '开始时间已存在'],
    'q5_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q5_2.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q6.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q7_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q7_2.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q7_3.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q8_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q8_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q9.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q10.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q10.array' => ['code' => 10002, 'msg' => '格式不正确'],
  ];
  public $osntAllSecValidate = [
    'q5_1'  => 'require',
    'q5_2'  => 'require',
    'q6'  => 'require',
    'q7_1'  => 'require',
    'q7_2'  => 'require',
    'q7_3'  => 'require',
    'q8_1'  => 'require',
    'q8_2'  => 'require',
    'q9'  => 'require',
    'q10'  => 'require|array',
  ];
  public $osntAllSecMessage = [
    'q5_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q5_2.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q6.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q7_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q7_2.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q7_3.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q8_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q8_1.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q9.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q10.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'q10.array' => ['code' => 10002, 'msg' => '格式不正确'],
  ];

  public $PracticeS5ProblemSolvingsave1 = [
    'open_id'  => 'require',
    'stime'  => 'require|unique:auto_think_s4',
    'new'  => 'require',
    'type'  => 'require',
    'problem'  => 'require',
    'target'  => 'require',
    'selected'  => 'require',
    'plan'  => 'require|array',
    'steps'  => 'require|array',
  ];
  public $PracticeS5ProblemSolvingsave2 = [
    'open_id.require' => ['code' => 10001, 'msg' => '缺少必要参数/参数错误'],
    'stime.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'new.require' => ['code' => 10003, 'msg' => '缺少必要参数/参数错误'],
    'type.require' => ['code' => 10004, 'msg' => '缺少必要参数/参数错误'],
    'stime.unique' => ['code' => 10006, 'msg' => '开始时间已存在'],
    'problem.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'target.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'selected.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'plan.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'plan.array' => ['code' => 10002, 'msg' => '格式不正确'],
    'steps.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'steps.array' => ['code' => 10002, 'msg' => '格式不正确'],
  ];

  public $plan = [
    'number' => 'require',
    'possible_solutions' => 'require',
    'benefits' => 'require',
    'bad' => 'require',
  ];

  public $plan1 = [
    'number.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'possible_solutions.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'benefits.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'bad.require' => ['code' => 10002, 'msg' => '请填写完整']
  ];

  public $steps = [
    'steps' => 'require',
    'time' => 'require',
  ];

  public $steps1 = [
    'steps.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'time.require' => ['code' => 10002, 'msg' => '请填写完整'],
  ];

  public $sov = [
    'problem' => 'require',
    'target' => 'require',
    'plan' => 'require|array',
    'selected' => 'require',
    'steps' => 'require|array',
  ];

  public $sov1 = [
    'problem.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'target.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'selected.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'plan.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'steps.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'steps.array' => ['code' => 10002, 'msg' => '格式不正确'],
    'plan.array' => ['code' => 10002, 'msg' => '格式不正确'],
  ];

  public $buti = [
    'open_id' => 'require',
    'things' => 'require',
    'main_reason' => 'require|array',
    'stime' => 'require',
    'new' => 'require',
    'type' => 'require',
  ];

  public $buti1 = [
    'open_id.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'stime.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'new.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'type.require' => ['code' => 10002, 'msg' => '缺少必要参数/参数错误'],
    'things.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'main_reason.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'main_reason.array' => ['code' => 10002, 'msg' => '格式不正确'],
  ];


  public $butio = [
    'direction' => 'require',
    'reason' => 'require',
    'proportion' => 'require',
  ];

  public $butio1 = [
    'direction.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'reason.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'proportion.require' => ['code' => 10002, 'msg' => '请填写完整'],
  ];

  public $bution = [
    'things' => 'require',
    'main_reason' => 'require|array',
  ];

  public $bution1 = [
    'things.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'main_reason.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'main_reason.array' => ['code' => 10002, 'msg' => '格式不正确'],
  ];

  public $inte = [
    'open_id' => 'require',
    'stime' => 'require|unique:belief',
    'new' => 'require',
    'type' => 'require'
  ];

  public $inte1 = [
    'open_id.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'stime.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'new.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'type.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'stime.unique' => ['code' => 10002, 'msg' => '参数错误']
  ];

  public $faith = [
    'open_id' => 'require',
    'original' => 'require',
    'support' => 'require|array',
    'fresh' => 'require',
    'fresh_support' => 'require|array',
    'stime' => 'require|unique:belief',
    'new' => 'require',
    'type' => 'require',
  ];

  public $faith1 = [
    'open_id.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'original.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'support.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'fresh.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'fresh_support.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'stime.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'new.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'type.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'support.array' => ['code' => 10002, 'msg' => '格式错误'],
    'fresh_support.array' => ['code' => 10002, 'msg' => '格式错误'],
    'stime.unique' => ['code' => 10002, 'msg' => 'stime已存在']
  ];
  
  public $ssess = [
    'original' => 'require',
    'support' => 'require|array',
    'fresh' => 'require',
    'fresh_support' => 'require|array',
  ];

  public $ssess1 = [
    'original.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'support.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'fresh.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'fresh_support.require' => ['code' => 10002, 'msg' => '请填写完整'],
    'support.array' => ['code' => 10002, 'msg' => '格式错误'],
    'fresh_support.array' => ['code' => 10002, 'msg' => '格式错误'],
  ];

  public $vde_a = [
    'idea' => 'require|array',
    'tt_id' => 'require'
  ];

  public $msg_a = [
    'idea.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'tt_id.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'idea.array' => ['code' => 10001, 'msg' => '格式不正确']
  ];

  public $vde_b = [
    'idea' => 'require|array',
  ];

  public $msg_b = [
    'idea.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'idea.array' => ['code' => 10001, 'msg' => '格式不正确']
  ];

  public $tr_a = [
    'open_id' => 'require',
    'new_target' => 'require|array',
    'plan' => 'require|array',
    'way' => 'require',
    'stime' => 'require|unique:new_target',
    'new' => 'require',
    'type' => 'require',
  ];

  public $tr_b = [
    'open_id.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'new_target.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'plan.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'way.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'stime.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'new.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'type.require' => ['code' => 10001, 'msg' => '请填写完整'],
    'plan.array' => ['code' => 10001, 'msg' => '格式不正确'],
    'new_target.array' => ['code' => 10001, 'msg' => '格式不正确'],
    'stime.unique' => ['code' => 10001, 'msg' => 'stime已存在'],
  ];

  public $ntr_a = [
    'plan' => 'require',
    'time' => 'require',
    'timelong' => 'require',
    'reward' => 'require',
  ];

  public $ntr_b = [
    'plan.require' => ['code'=>10001,'msg'=>'请填写完整'],
    'time.require' => ['code'=>10001,'msg'=>'请填写完整'],
    'timelong.require' => ['code'=>10001,'msg'=>'请填写完整'],
    'reward.require' => ['code'=>10001,'msg'=>'请填写完整'],
  ];

  public $newt_a = [
    'new_target' => 'require',
    'plan' => 'require|array',
    'way' => 'require',
  ];

  public $newt_b = [
    'new_target.require' => ['code'=>10001,'msg'=>'请填写完整'],
    'plan.require' => ['code'=>10001,'msg'=>'请填写完整'],
    'way.require' => ['code'=>10001,'msg'=>'请填写完整'],
    'plan.array' => ['code'=>10001,'msg'=>'格式不正确'],
  ];
}
