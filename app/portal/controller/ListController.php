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
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalPostModel;
use think\Db;

class ListController extends HomeBaseController
{
    public function index()
    {
        $portalPostModel = new PortalPostModel();
        $id                  = $this->request->param('id', 0, 'intval');
        $areaid = $this->request->param('areaid', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        $area_name="";
        $dwxz=0;
        if($areaid>0){
            $area_result=Db::name("area")->where("id","eq",$areaid)->find();
            if($area_result){
                $area_name=$area_result["name"];
                $dwxz=$area_result["dwxz"];
                

                $cate_list           = $portalPostModel->alias('a')
                ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>$id,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                ->order('a.update_time', 'DESC')->limit(15)->select();
            }
        }else{
            $cate_list           = $portalPostModel->alias('a')
                ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                ->field("a.id,a.post_title,a.create_time,c.dwxz")->where(["a.post_status"=>1,"b.category_id"=>$id])
                ->order('a.update_time', 'DESC')->limit(15)->select();
        }

        
        $category_name=Db::name("portal_category")->where("id","eq",$id)->value("name");
        
       
        // $this->assign('cate_list', $cate_list);
        // $this->assign('page', $cate_list->render());

        $this->assign('dwxz', $dwxz);
        $this->assign('areaid', $areaid);
        $this->assign('pagename', $pagename);
        $this->assign("areaname",$area_name);
        $this->assign("cateid",$id );
        $this->assign("category_name",$category_name );
        return $this->fetch('/list');
    }

}
