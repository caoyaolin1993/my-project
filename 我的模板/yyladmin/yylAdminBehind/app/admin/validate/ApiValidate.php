<?php
/*
 * @Description  : 接口验证器
 * @Author       : https://github.com/skyselang
 * @Date         : 2020-11-24
 * @LastEditTime : 2021-01-15
 */

namespace app\admin\validate;

use think\Validate;
use think\facade\Db;
use app\admin\service\ApiService;

class ApiValidate extends Validate
{
    // 验证规则
    protected $rule = [
        'api_id'     => ['require', 'checkApiId'],
        'api_name'   => ['require', 'checkApiName'],
        'api_method' => ['require'],
        'image_file' => ['require', 'file', 'image', 'fileExt' => 'jpg,png', 'fileSize' => '2097152'],
    ];

    // 错误信息
    protected $message = [
        'api_id.require'      => '缺少参数：接口id',
        'api_name.require'    => '请输入接口名称',
        'api_method.require'  => '请选择请求方式',
        'image_file.require'  => '请选择图片',
        'image_file.file'     => '请选择图片文件',
        'image_file.image'    => '请选择图片格式文件',
        'image_file.fileExt'  => '请选择jpg、png格式图片',
        'image_file.fileSize' => '请选择大小小于2Mb的图片',
    ];

    // 验证场景
    protected $scene = [
        'id'    => ['api_id'],
        'add'   => ['api_name', 'api_method'],
        'edit'  => ['api_id', 'api_name', 'api_method'],
        'dele'  => ['api_id'],
        'image' => ['image_file'],
    ];

    // 验证场景定义：删除
    protected function scenedele()
    {
        return $this->only(['api_id'])
            ->append('api_id', 'checkApiChild');
    }

    // 自定义验证规则：接口是否存在
    protected function checkApiId($value, $rule, $data = [])
    {
        $api_id = $value;

        $api = ApiService::info($api_id);

        if ($api['is_delete'] == 1) {
            return '接口已删除：' . $api_id;
        }

        return true;
    }

    // 自定义验证规则：接口是否已存在
    protected function checkApiName($value, $rule, $data = [])
    {
        $api_id = isset($data['api_id']) ? $data['api_id'] : '';

        if ($api_id) {
            if ($data['api_pid'] == $data['api_id']) {
                return '接口父级不能等于接口本身';
            }
        }

        $api_name = Db::name('api')
            ->field('api_id')
            ->where('api_id', '<>', $api_id)
            ->where('api_pid', '=', $data['api_pid'])
            ->where('api_name', '=', $data['api_name'])
            ->where('is_delete', '=', 0)
            ->find();

        if ($api_name) {
            return '接口名称已存在：' . $data['api_name'];
        }

        if ($data['api_url']) {
            $api_url = Db::name('api')
                ->field('api_id')
                ->where('api_id', '<>', $api_id)
                ->where('api_url', '=', $data['api_url'])
                ->where('api_url', '<>', '')
                ->where('is_delete', '=', 0)
                ->find();

            if ($api_url) {
                return '接口链接已存在：' . $data['api_url'];
            }
        }

        return true;
    }

    // 自定义验证规则：接口是否有子级接口
    protected function checkApiChild($value, $rule, $data = [])
    {
        $api_id = $value;

        $api = Db::name('api')
            ->field('api_id')
            ->where('api_pid', '=', $api_id)
            ->where('is_delete', '=', 0)
            ->find();

        if ($api) {
            return '请删除所有子级接口后再删除';
        }

        return true;
    }
}
