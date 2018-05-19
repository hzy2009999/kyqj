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

use app\admin\model\RouteModel;
use think\Model;
use tree\Tree;

class AreaModel extends Model
{

    protected $type = [
        'more' => 'array',
    ];

    /**
     * 生成地区 select树形结构
     * @param int $selectId 需要选中的地区 id
     * @param int $currentCid 需要隐藏的地区 id
     * @return string
     */
    public function AreaTree($selectId = 0, $currentCid = 0,$sid=0)
    {
        $where = [];
        if (!empty($currentCid)) {
            $where['id'] = ['neq', $currentCid];
        }

        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();
        $array_area = $this->where("id=".$selectId)->find();
        $parent_id = 0;
        if ($array_area) {
            $parent_id = $array_area["parent_id"];
        }

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        $newCategories = [];
        foreach ($categories as $item) {
            $item['selected'] = $sid == $item['id'] ? "selected" : "";

            array_push($newCategories, $item);
        }
        $tree->init($newCategories);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree($parent_id, $str,$sid,'','',$selectId);

        return $treeStr;
    }

    /**
     * @param int|array $currentIds
     * @param string $tpl
     * @return string
     */
    public function AreaTableTree($currentIds = 0, $tpl = '',$selectedId=0)
    {
//        if (!empty($currentCid)) {
//            $where['id'] = ['neq', $currentCid];
//        }

        $categories = $this->order("list_order ASC")->select()->toArray();

        $array_area = $this->where("id=".$selectedId)->find();
        $parent_id = 0;
        if ($array_area) {
            $parent_id = $array_area["parent_id"];
        }
        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }

        $newCategories = [];
        foreach ($categories as $item) {
            $item['checked'] = in_array($item['id'], $currentIds) ? "checked" : "";
            $item['url']     = cmf_url('portal/List/index', ['id' => $item['id']]);;
            $item['str_action'] = '<a href="' . url("Area/add", ["parent" => $item['id']]) . '">添加子地区</a>  <a href="' . url("Area/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a>  <a class="js-ajax-delete" href="' . url("Area/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            switch ($item['dwxz']) {
                case '1':
                    $item['str_dwxz']='中心乡镇';
                    break;
                case '2':
                    $item['str_dwxz']='乡村部门';
                    break;
                case '3':
                    $item['str_dwxz']='入驻单位';
                    break;
                default:
                    $item['str_dwxz']='';
                    break;
            }
            $item['style']        = '';
            $item['parentIdNode'] = ($item['parent_id']) ? ' class="child-of-node-' . $item['parent_id'] . '"' : '';
            array_push($newCategories, $item);
        }

        $tree->init($newCategories);

        if (empty($tpl)) {
            $tpl = "<tr id='node-\$id'\$parentIdNode  style='\$style'>
                        <td><input name='list_orders[\$id]' type='text' size='3' value='\$list_order' class='input-order'></td>
                        <td>\$id</td>
                        <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
                        <td>\$str_dwxz</td>
                        <td>\$bz</td>
                        <td>\$str_action</td>
                    </tr>";
        }
        $treeStr = $tree->getTree($parent_id, $tpl,$selectedId,'','',$selectedId);

        return $treeStr;
    }

    /**
     * 添加文章地区
     * @param $data
     * @return bool
     */
    public function addArea($data)
    {
        $result = true;
        self::startTrans();
        try {
            $this->allowField(true)->save($data);
            $id = $this->id;
            if (empty($data['parent_id'])) {

                $this->where( ['id' => $id])->update(['path' => '0-' . $id]);
            } else {
                $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
                $this->where( ['id' => $id])->update(['path' => "$parentPath-$id"]);

            }
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            $result = false;
        }

        return $result;
    }

    public function editArea($data)
    {
        $result = true;

        $id          = intval($data['id']);
        $parentId    = intval($data['parent_id']);
        $oldArea = $this->where('id', $id)->find();

        if (empty($parentId)) {
            $newPath = '0-' . $id;
        } else {
            $parentPath = $this->where('id', intval($data['parent_id']))->value('path');
            if ($parentPath === false) {
                $newPath = false;
            } else {
                $newPath = "$parentPath-$id";
            }
        }

        if (empty($oldArea) || empty($newPath)) {
            $result = false;
        } else {


            $data['path'] = $newPath;

            $this->isUpdate(true)->allowField(true)->save($data, ['id' => $id]);

            $children = $this->field('id,path')->where('path', 'like', "%-$id-%")->select();

            if (!empty($children)) {
                foreach ($children as $child) {
                    $childPath = str_replace($oldArea['path'] . '-', $newPath . '-', $child['path']);
                    $this->isUpdate(true)->save(['path' => $childPath], ['id' => $child['id']]);
                }
            }

            $routeModel = new RouteModel();
            if (!empty($data['alias'])) {
                $routeModel->setRoute($data['alias'], 'portal/List/index', ['id' => $data['id']], 2, 5000);
                $routeModel->setRoute($data['alias'] . '/:id', 'portal/Article/index', ['cid' => $data['id']], 2, 4999);
            } else {
                $routeModel->deleteRoute('portal/List/index', ['id' => $data['id']]);
                $routeModel->deleteRoute('portal/Article/index', ['cid' => $data['id']]);
            }

            $routeModel->getRoutes(true);
        }


        return $result;
    }


}