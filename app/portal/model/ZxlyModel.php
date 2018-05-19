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
namespace app\portal\model;

use think\Model;

class ZxlyModel extends Model
{
	// 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

	public function addZxly($data)
    {
        $result = true;
        self::startTrans();
        try {
        	$data['ip']=get_client_ip();
            $this->allowField(true)->save($data);
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            $result = false;
        }

        return $result;
    }


}