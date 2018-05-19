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
use app\portal\model\AreaModel;
use think\Db;
use app\admin\model\ThemeModel;


class AreaController extends AdminBaseController
{
    public function index()
    {
        $areaId=0;
        $user_id = cmf_get_current_admin_id();
        $area = Db::name("area")->alias("a")->join("role b","a.id=b.area_id","LEFT")->join("role_user c","b.id=c.role_id","LEFT")->join("user d","c.user_id=d.id","LEFT")->where("d.id=".$user_id)->field("a.*")->find();
        $path = "";
        if ($area) {
            $path = $area["path"];
            $areaId=$area["id"];
        }

        $areaModel = new AreaModel();
        $areaTree        = $areaModel->AreaTableTree(0,'',$areaId);

        $this->assign('area_tree', $areaTree);
        return $this->fetch();
    }

    public function add()
    {
        $parentId            = $this->request->param('parent', 0, 'intval');
        $areaId=0;
         $user_id = cmf_get_current_admin_id();
        $area = Db::name("area")->alias("a")->join("role b","a.id=b.area_id","LEFT")->join("role_user c","b.id=c.role_id","LEFT")->join("user d","c.user_id=d.id","LEFT")->where("d.id=".$user_id)->field("a.*")->find();
        $path = "";
        if ($area) {
            $path = $area["path"];
            $areaId=$area["id"];
        }

        $areaModel = new AreaModel();
        $areaTree      = $areaModel->AreaTree($areaId);

        $this->assign('area_tree', $areaTree);
        return $this->fetch();
    }

    public function addPost()
    {
        $areaModel = new AreaModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'Area');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $areaModel->addArea($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('Area/index'));

    }

    public function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $area = AreaModel::get($id)->toArray();

            $areaModel = new AreaModel();
            $areaTree      = $areaModel->AreaTree($area['parent_id'], $id);

            $this->assign($area);
            $this->assign('area_tree', $areaTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'Area');

        if ($result !== true) {
            $this->error($result);
        }

        $areaModel = new AreaModel();

        $result = $areaModel->editArea($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!', url('Area/index'));
    }

    public function select()
    {
        $ids                 = $this->request->param('ids');
        $selectedIds         = explode(',', $ids);
        $areaModel = new AreaModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;

        $areaTree = $areaModel->AreaTableTree($selectedIds, $tpl);

        $area = $areaModel->select();

        $this->assign('area', $area);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('area_tree', $areaTree);
        return $this->fetch();
    }

    public function listOrder()
    {
        parent::listOrders(Db::name('area'));
        $this->success("排序更新成功！", '');
    }

    public function delete()
    {
        $areaModel = new AreaModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findArea = $areaModel->where('id', $id)->find();

        if (empty($findArea)) {
            $this->error('地区不存在!');
        }
//判断此地区有无子地区（不算被删除的子地区）
        $areaChildrenCount = $areaModel->where(['parent_id' => $id])->count();

        if ($areaChildrenCount > 0) {
            $this->error('此地区有子类无法删除!');
        }

        // $areaPostCount = Db::name('portal_area_post')->where('area_id', $id)->count();

        // if ($areaPostCount > 0) {
        //     $this->error('此地区有文章无法删除!');
        // }

        $result = $areaModel
            ->where('id', $id)
            ->delete();
        if ($result) {
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
