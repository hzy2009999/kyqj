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
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\Menu;

class MainController extends AdminBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *  后台欢迎页
     */
    public function index()
    {
        // 点击量统计
        $role_area = session('ADMIN_AREA_ID');

        $where['b.yn']=['eq', date("Y-m",time())];
        $where['a.post_status']=['eq', 1];
        if (!empty($role_area)) {
            $where['a.area_id']= ['exp', 'in (select id from gov_area where id='.$role_area.' or parent_id='.$role_area.')'];
            
        }

        $hits_list=Db::name("portal_post")->alias("a")
                ->join("post_hits b","a.id=b.post_id","LEFT")
                ->join("portal_category_post c","a.id=c.post_id","LEFT")
                ->join("portal_category d","c.category_id=d.id","LEFT")->where($where)
                ->field("sum(b.hits) as hits,c.category_id,d.name")
                ->group('c.category_id,d.name')
                ->order('hits desc')
                ->select();

        $area_name = session('ADMIN_AREA_NAME');
        $area_path = session('ADMIN_AREA_PATH');
        $dwxz = session('ADMIN_AREA_DWXZ');
        $cur_area_name='';
        if (!empty($area_path)) {
            $js = substr_count($area_path,'-');
            if($js==2&&$dwxz=="1"){
                $cur_area_name=$area_name.'以及下属村居';
            }else{
                $cur_area_name=$area_name;
            }

        }
        
        $this->assign("cur_area_name", $cur_area_name);

        $this->assign('tjyn', date("Y-m",time()));
        $this->assign("hits_list", $hits_list);

        // 更新情况统计
        $ny=date("Y-m",time());
        $js=0;
        $where=[];
        if (!empty($role_area)) {
            if (!empty($area_path)) {
                $js = substr_count($area_path,'-');
                if($js==2){
                    $where['a.area_id|b.parent_id']=['eq',$role_area];
                }else{
                    $where['a.area_id']=['eq',$role_area];
                }

            }
        }
        if($js>=2){
            $info_list=Db::name("portal_post")->alias("a")
            ->join("area b","a.area_id=b.id")
            ->where("(DATE_FORMAT(FROM_UNIXTIME(a.create_time),'%Y-%m')='$ny' or DATE_FORMAT(FROM_UNIXTIME(a.update_time),'%Y-%m')='$ny')")
            ->where($where)
            ->field("a.area_id,b.name,count(a.id) as sumid")
            ->group("a.area_id,b.name")
            ->order('sumid desc')
            ->select();
        }
        else{
            $zsql=Db::name("portal_post")->alias("a")
            ->join("area b","a.area_id=b.id","LEFT")
            ->where("DATE_FORMAT(FROM_UNIXTIME(a.create_time),'%Y-%m')='$ny' or DATE_FORMAT(FROM_UNIXTIME(a.update_time),'%Y-%m')='$ny'")
            ->field("a.area_id,b.parent_id,count(a.id) as sum_id")
            ->group("a.area_id,b.parent_id")
            ->buildSql();

            $info_list=Db::name("area")->alias("h")
            ->join([$zsql=> 't'],"h.id=t.area_id or h.id=t.parent_id","RIGHT")
            ->where("length(h.path)-length(replace(h.path,'-',''))=2")
            ->where($where)
            ->field("h.id,h.name,sum(t.sum_id) as sumid")
            ->group("h.id,h.name")
            ->order('sumid desc')
            ->select();
        }
        
        

        $this->assign("info_list", $info_list);
        return $this->fetch();
    }

    public function dashboardWidget()
    {
        $dashboardWidgets = [];
        $widgets          = $this->request->param('widgets/a');
        if (!empty($widgets)) {
            foreach ($widgets as $widget) {
                if ($widget['is_system']) {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 1]);
                } else {
                    array_push($dashboardWidgets, ['name' => $widget['name'], 'is_system' => 0]);
                }
            }
        }

        cmf_set_option('admin_dashboard_widgets', $dashboardWidgets, true);

        $this->success('更新成功!');

    }

}
