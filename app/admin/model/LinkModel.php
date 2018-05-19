<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class LinkModel extends Model
{
	 protected $rule = [
        'name'  => 'require',
        'url'  => 'require',
        'lx'  => 'require|gt:0',
    ];
    protected $message = [
        'name.require' => '名称不能为空',
        'url.require' => '链接地址不能为空',
        'lx.gt' => '类型不能为空',
    ];

}