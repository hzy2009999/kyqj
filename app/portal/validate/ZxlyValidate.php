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

class ZxlyValidate extends Validate
{
    protected $rule = [
        'content'  => 'require',
        'xm'  => 'require',
        'sj'  => 'require',
    ];
    protected $message = [
        'content.require' => '留言不能为空',
        'xm.require' => '姓名不能为空',
        'sj.require' => '手机不能为空',
    ];

}