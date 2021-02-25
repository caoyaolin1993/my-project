<?php
declare (strict_types = 1);
namespace app\admin\controller;
use app\BaseController;
use app\util\ReturnCode;
use app\util\MenuFilter;
use think\facade\Db;
class Menu extends AdminAuth{
    
    public function auth(){
        $admin_id = session('admin_id');
    
        //查询用户拥有的权限
        $group_id = Db::name('cm_auth_group_access')
        ->where('admin_id',$admin_id)
        ->find();
    
        $row = Db::name('cm_auth_group')
        ->field('id,name')
        ->where('id',$group_id['group_id'])
        ->where('status',1)
        ->find();
        
        $admin = Db::name('cm_admin')
        ->field('account')
        ->where('id',$admin_id)
        ->find();
        
        $data = [
            'roles' => [$row['name']],
            'name'  => $admin['account']
        ];
        
        $res = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功','data'=>$data];
        return json($res);
    }

    /* 菜单列表/权限 */
    public function index(){
        $MenuFilter = new MenuFilter();
        $menu_list = $MenuFilter->menu_List;
        $openid = trim(input('get.openid'));
        if(empty($openid)){
            $data = ['code' => ReturnCode::EMPTY_PARAMS, 'msg' => '缺少必要参数openid', 'data' => []];
            return json($data);
        }
        //查询用户拥有的权限
        $group_id = Db::name('auth_group_access')
            ->where('openid',$openid)
            ->find();
        $row = Db::name('auth_group')
            ->field('id,rules')
            ->where('id',$group_id['group_id'])
            ->where('status',1)
            ->find();
        $rule_list = Db::name('auth_rule')
            ->field('rule')
            ->where('id','in',$row['rules'])
            ->select()->toArray();
        $rules = [];
        //转索引数组
        foreach ($rule_list as $key =>$v){
            $rules[$key] = $v['rule'];
        }
        //筛选菜单
        foreach ($menu_list as $k=> &$v){
            foreach ($v['children_list'] as $key => &$val){
                $rule = $val['rule'];
                if(!in_array($rule,$rules)){
                    array_splice($v['children_list'], $key, 1);
                }
            }
            if(empty($v['children_list'])){
                array_splice($menu_list, $k, 1);
            }
        }
        $data = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功', 'data' =>$menu_list];
        return json($data);
    }
    
    /*测试*/
    public function menu(){
        $admin_id = session('admin_id');
    
        //查询用户拥有的权限
        $group_id = Db::name('cm_auth_group_access')
        ->where('admin_id',$admin_id)
        ->find();
    
        $row = Db::name('cm_auth_group')
        ->field('id,rules')
        ->where('id',$group_id['group_id'])
        ->where('status',1)
        ->find();
    
        $data = [];
        $rule_list = Db::name('cm_auth_rule')
        ->field('id,rule,title')
        ->where('parent_id',0)
        ->where('ismenu',1)
        ->where('status',1)
        ->where('id','in',$row['rules'])
        ->order('sort,id')
        ->select()->toArray();
        foreach ($rule_list as $key =>$value){
            $data[$key]['name'] = $value['title'];
            $child = Db::name('cm_auth_rule')->where('parent_id',$value['id'])->count();
            if(!empty($child)){
                $data[$key]['children_list'] = $this->findchild($value['id'],$row['rules']);
            }
        }
        $res = ['code' => ReturnCode::SUCCESS, 'msg' => '操作成功','data'=>$data];
        return json($res);
    
    }
    
    public function findchild($pid,$row) {
        $data = [];
        $list = Db::name('cm_auth_rule')->where('parent_id',$pid)->where('ismenu',1)->where('id','in',$row)->where('status',1)->field('id,url,title')->order('sort,id')->select()->toArray();
        if(!empty($list)){
            foreach ($list as $key =>$value){
                $data[$key]['name'] = $value['title'];
                $data[$key]['url']  = $value['url'];
//                 $data[$key]['rule'] = $value['rule'];
//                 $explode = explode('/', $value['rule']);
//                 $data[$key]['path'] = $explode[count($explode)-1];
            }
        }
        return $data;
    }
}
