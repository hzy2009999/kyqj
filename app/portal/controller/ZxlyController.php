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
namespace app\portal\controller;

use app\admin\model\RouteModel;
use cmf\controller\AdminBaseController;
use app\portal\model\ZxlyModel;
use think\Db;
use app\admin\model\ThemeModel;


class ZxlyController extends AdminBaseController
{
    public function index()
    {
        $where=[];
        $join=[];
        $param = $this->request->param();
        $zxlyModel = new ZxlyModel();
        $areaId = $this->request->param('areaid', 0, 'intval');
        $user_id = cmf_get_current_admin_id();
        $area = Db::name("area")->alias("a")->join("role b","a.id=b.area_id","LEFT")->join("role_user c","b.id=c.role_id","LEFT")->join("user d","c.user_id=d.id","LEFT")->where("d.id=".$user_id)->field("a.*")->find();
        $path = "";
        if ($area) {
            $path = $area["path"];
        }
        if($user_id==1){
            array_push($join, [
                '__AREA__ t', "t.id = a.area_id",'LEFT'
            ]);
            if (!empty($areaId)&&$areaId>0) {
                $cur_path=Db::name("area")->where("id=".$areaId)->find();
                if ($cur_path){
                    $where['t.path'] =  ["like",$cur_path["path"]."%"];
                }
                
            }
        }else{
            array_push($join, [
                '__AREA__ t', "t.id = a.area_id"
            ]);
            if (!empty($path)) {
                $where['t.path'] =  ["like",$path."%"];
            }
        }
        $field = "a.*,t.name as areaname,length(t.path)-length(replace(t.path,'-','')) as areapath";
        $list=$zxlyModel->alias('a')
        ->join($join)->field($field)->where($where)->order('create_time','desc')->paginate(10);
        $list->appends($param);
        $this->assign('page', $list->render());
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        $zxlyModel = new ZxlyModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'Zxly');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $zxlyModel->addZxly($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('Zxly/index'));

    }

    public function delete()
    {
        $zxlyModel = new ZxlyModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findZxly = $zxlyModel->where('id', $id)->find();

        if (empty($findZxly)) {
            $this->error('留言不存在!');
        }

        $result = $zxlyModel
            ->where('id', $id)
            ->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
