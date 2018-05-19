<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\validate;

use app\admin\model\RouteModel;
use think\Validate;

class AreaValidate extends Validate
{
    protected $rule = [
        'name'  => 'require',
        'dwxz'	=> 'require|gt:0'
    ];
    protected $message = [
        'name.require' => '地区名称不能为空',
        'dwxz.gt' => '请指定地区性质！'
    ];

}