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
use app\portal\model\LdbzModel;
use app\portal\model\AreaModel;
use think\Db;
use app\admin\model\ThemeModel;


class LdbzController extends AdminBaseController
{
    public function index()
    {
        $where =[];
        $param = $this->request->param();
        $ldbzModel = new LdbzModel();

        $xm = empty($param['xm']) ? '' : $param['xm'];
        if (!empty($xm)) {
            $where['xm'] = ['like', "%$xm%"];
        }
        $areaId = $this->request->param('areaid', 0, 'intval');
        $areaModel = new AreaModel();
        

        $join=[];
        $user_id = cmf_get_current_admin_id();
        $area = Db::name("area")->alias("a")->join("role b","a.id=b.area_id","LEFT")->join("role_user c","b.id=c.role_id","LEFT")->join("user d","c.user_id=d.id","LEFT")->where("d.id=".$user_id)->field("a.*")->find();
        $path = "";
        if ($area) {
            $path = $area["path"];
            $areaId=$area["id"];
        }
        $areaTree        = $areaModel->AreaTree($areaId);
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
            // if (!empty($area)&&$area>0) {
            //     $where['t.id']=['eq',$area['id']];
            // }
        }
        

        $field = "a.*,t.name as areaname,length(t.path)-length(replace(t.path,'-','')) as areapath";



        $list=$ldbzModel->alias('a')
        ->join($join)
        ->where($where)->field($field)->order('create_time','desc')->paginate(10);

        $list->appends($param);
        $this->assign('page', $list->render());
        $this->assign('list', $list);
        $this->assign('xm', $xm);
        $this->assign('areaTree', $areaTree);
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function addPost()
    {
        $ldbzModel = new LdbzModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'Ldbz');

        if ($result !== true) {
            $this->error($result);
        }

        

        $result = $ldbzModel->addLdbz($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('Ldbz/index'));

    }

    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $ldbzModel = new LdbzModel();
            $vo=$ldbzModel->where('id',$id)->find();

            $this->assign('vo',$vo);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'Ldbz');

        if ($result !== true) {
            $this->error($result);
        }

        $ldbzModel = new LdbzModel();

        $result = $ldbzModel->editLdbz($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!', url('Ldbz/index'));
    }

    public function delete()
    {
        $ldbzModel = new LdbzModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findLdbz = $ldbzModel->where('id', $id)->find();

        if (empty($findLdbz)) {
            $this->error('领导班子不存在!');
        }

        $result = $ldbzModel
            ->where('id', $id)
            ->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
