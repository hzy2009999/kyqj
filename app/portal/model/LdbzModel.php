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
use think\Db;

class LdbzModel extends Model
{
	// 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;

	public function addLdbz($data)
    {
        $result = true;
        self::startTrans();
        try {
            $user_id=cmf_get_current_admin_id();
            $area = Db::name("area")->alias("a")->join("role b","a.id=b.area_id","LEFT")->join("role_user c","b.id=c.role_id","LEFT")->join("user d","c.user_id=d.id","LEFT")->where("d.id=".$user_id)->field("a.*")->find();
            if ($area) {
                $data["area_id"] = $area["id"];
            }
            $data['user_id'] = $user_id;
            $this->allowField(true)->save($data);
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            $result = false;
        }

        return $result;
    }

    public function editLdbz($data)
    {
        $result = true;

        $id          = intval($data['id']);

        $oldArea = $this->where('id', $id)->find();

        if (empty($oldArea)) {
            $result = false;
        } else {

            $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);
        }


        return $result;
    }

}