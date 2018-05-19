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
use app\portal\model\PortalCategoryModel;
use app\portal\model\PortalPostModel;
use think\Db;
use app\portal\model\ZxlyModel;
use app\portal\model\AreaModel;
use tree\Tree;

class IndexController extends HomeBaseController
{
    public function index()
    {
        $portalPostModel = new PortalPostModel();
        $areaid = $this->request->param('areaid', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        if($areaid>0){
            $area_result=Db::name("area")->where("id","eq",$areaid)->find();
            if($area_result){
                $area_name=$area_result["name"];
                $dwxz=$area_result["dwxz"];

                $data_dwgk           = $portalPostModel->alias('a')
                ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>1,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                ->order('a.update_time', 'DESC')->limit(7)->select();

                if($dwxz!=2){
                    $data_zwgk           = $portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>2,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->order('a.update_time', 'DESC')->limit(7)->select();

                    $this->assign("data_zwgk",$data_zwgk);
                }

                if($dwxz==3){
                    $data_zcfg           = $portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>9,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->order('a.update_time', 'DESC')->limit(7)->select();

                    $data_zsbmgk=$portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_content,a.create_time,a.more")->where(["a.post_status"=>1,"b.category_id"=>8,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->find();

                    $this->assign("data_zcfg",$data_zcfg);
                    $this->assign("data_zsbmgk",$data_zsbmgk);
                }

                if($dwxz==1){
                    $data_smxzgk=$portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_content,a.create_time,a.more")->where(["a.post_status"=>1,"b.category_id"=>10,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->find();

                    $data_bsgk           = $portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>3,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->order('a.update_time', 'DESC')->limit(7)->select();

                    $this->assign("data_smxzgk",$data_smxzgk);
                    $this->assign("data_bsgk",$data_bsgk);

                }

                if($dwxz==2){

                    $data_cjgk=$portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_content,a.create_time,a.more")->where(["a.post_status"=>1,"b.category_id"=>11,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->find();

                    $data_cwgk           = $portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>6,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->order('a.update_time', 'DESC')->limit(7)->select();

                    $data_caiwgk           = $portalPostModel->alias('a')
                    ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
                    ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
                    ->field("a.id,a.post_title,a.create_time")->where(["a.post_status"=>1,"b.category_id"=>5,"c.dwxz"=>$dwxz,"a.area_id"=>$areaid])
                    ->order('a.update_time', 'DESC')->limit(7)->select();

                    $this->assign("data_cjgk",$data_cjgk);
                    $this->assign("data_cwgk",$data_cwgk);
                    $this->assign("data_caiwgk",$data_caiwgk);
                }

                $ldbz_list= Db::name("ldbz")->where(["area_id"=>$areaid])->limit(7)->select();

                $href_dwgk = Db::name("nav_menu")->where("id=2")->find();
                $href_zwgk = Db::name("nav_menu")->where("id=3")->find();
                $href_zcfg = Db::name("nav_menu")->where("id=5")->find();
                $href_bsgk = Db::name("nav_menu")->where("id=8")->find();
                $href_cwgk = Db::name("nav_menu")->where("id=4")->find();
                $href_caiwgk = Db::name("nav_menu")->where("id=9")->find();

                $this->assign("ldbz_list",$ldbz_list);

                $this->assign("data_dwgk",$data_dwgk);


                $this->assign("areaname",$area_name);
                $this->assign("href_dwgk",$href_dwgk);
                $this->assign("href_zwgk",$href_zwgk);
                $this->assign("href_zcfg",$href_zcfg);
                $this->assign("href_bsgk",$href_bsgk);
                $this->assign("href_cwgk",$href_cwgk);
                $this->assign("href_caiwgk",$href_caiwgk);
                $this->assign("dwxz",$dwxz);
                $this->assign("pagename",$pagename);
                $this->assign("areaid",$areaid);

                if(!empty($pagename)){
                    cmf_clear_cache();
                    return $this->fetch(':'.$pagename);
                }
            }
        }else{
            $articles        = $portalPostModel->where("recommended=1 and post_status=1")->order('update_time', 'DESC')->limit(5)->select();
            $data_dwgk           = $portalPostModel->alias('a')->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')->field("a.id,a.post_title,a.create_time")->where("b.category_id=1 and a.post_status=1")->order('a.update_time', 'DESC')->limit(7)->select();
            $data_zwgk           = $portalPostModel->alias('a')->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')->field("a.id,a.post_title,a.create_time")->where("b.category_id=2 and a.post_status=1")->order('a.update_time', 'DESC')->limit(12)->select();
            $data_cwgk           = $portalPostModel->alias('a')->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')->field("a.id,a.post_title,a.create_time")->where("b.category_id=6 and a.post_status=1")->order('a.update_time', 'DESC')->limit(14)->select();
            $data_flfg           = $portalPostModel->alias('a')->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')->field("a.id,a.post_title,a.create_time")->where("b.category_id=9 and a.post_status=1")->order('a.update_time', 'DESC')->limit(6)->select();
            $data_area_zx            = Db::name("area")->where("parent_id=1")->order("convert(name using gbk)")->limit(14)->select();
            $data_area_sm            = Db::name("area")->where("parent_id=2")->order("convert(name using gbk)")->select();
            $href_dwgk							 = Db::name("nav_menu")->where("id=2")->find();
            $href_zwgk							 = Db::name("nav_menu")->where("id=3")->find();
            $href_cwgk							 = Db::name("nav_menu")->where("id=4")->find();
            $href_flfg							 = Db::name("nav_menu")->where("id=5")->find();
            $this->assign("articles",$articles);
            $this->assign("data_dwgk",$data_dwgk);
            $this->assign("data_zwgk",$data_zwgk);
            $this->assign("data_cwgk",$data_cwgk);
            $this->assign("data_flfg",$data_flfg);
            $this->assign("data_area_zx",$data_area_zx);
            $this->assign("data_area_sm",$data_area_sm);
            $this->assign("href_dwgk",$href_dwgk);
            $this->assign("href_zwgk",$href_zwgk);
            $this->assign("href_cwgk",$href_cwgk);
            $this->assign("href_flfg",$href_flfg);
            $this->assign("areaid",0);
            $this->assign("pagename","");
            $this->assign("dwxz",0);
            cmf_clear_cache();
            return $this->fetch(':index');
        }
    }

    public function message(){
        $areaid = $this->request->param('areaid', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        $dwxz = $this->request->param('dwxz', 0, 'intval');
        if ($areaid>0) {
            $area_result=Db::name("area")->where("id","eq",$areaid)->find();
            if($area_result){
                $area_name=$area_result["name"];
                $this->assign("areaname",$area_name);
            }
        }
        $areaModel = new AreaModel();
        $areaTree      = $areaModel->AreaTree($areaid);
        $this->assign("areaid",$areaid);
        $this->assign('area_tree', $areaTree);
        $this->assign("pagename",$pagename);
        $this->assign("dwxz",$dwxz);
        return $this->fetch(':message');
    }

    public function addPost()
    {
        $rule = [
            'content'  => 'require',
            'xm'  => 'require',
            'sj'  => 'require',
            'area_id'  => 'require',
        ];
        $message = [
            'content.require' => '留言不能为空',
            'xm.require' => '姓名不能为空',
            'sj.require' => '手机不能为空',
            'area_id.require' => '留言部门不能为空',
        ];
        $zxlyModel = new ZxlyModel();

        $data = $this->request->param();
        $result = $this->validate($data, 'Zxly');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $zxlyModel->addZxly($data);

        if ($result === false) {
            $this->error('留言失败!');
        }

        $this->success('留言成功!', cmf_url("/portal/index"));

    }

    public function cjct(){
        $areaid = $this->request->param('areaid', 0, 'intval');
        $area_result=Db::name("area")->where("id","eq",$areaid)->order("convert(name using gbk)")->find();
        $area = Db::name("area")->where("parent_id = ".$area_result["id"])->select();
        $e_html = "";
        $e_html = "<div class=\"form-inline title\" style=\"background-color:#f5f5f5;\"><div class=\"form-group div_left\">".(isset($area_result['name']) && ($area_result['name'] !== '')?$area_result['name']:'')."</div><div class=\"form-group div_right\"><a class=\"more\" style=\"color: #45b1f9 !important;\" target=\"_blank\" href=\"".cmf_url('index',array('areaid'=>$area_result['id'],'pagename'=>'xzsy','dwxz'=>'1'))."\">点此进入镇级页面</a></div></div><div class=\"row\" style=\"margin-left: 0px;margin-right: 0px;\">";
        foreach($area as $key=>$item){
            $e_html .= "<div class=\"col-md-3 col-xs-6 text-center\"><a class=\"btn btn-a btn-xs\" style=\"width: 110px;\" target=\"_blank\" href=\"".cmf_url('index',array('areaid'=>$item['id'],'pagename'=>'cjsy','dwxz'=>'2'))."\">".(isset($item['name']) && ($item['name'] !== '')?$item['name']:'')."</a></div>";
        }
        $e_html .= "</div>";
        echo "$e_html";
    }
    public function areaName(){
        $areaid = $this->request->param('areaid', 0, 'intval');
        $area_result=Db::name("area")->where("id","eq",$areaid)->find();
        if($area_result){
            echo $area_result["name"];
        }else{
            echo "";
        }
    }
}
