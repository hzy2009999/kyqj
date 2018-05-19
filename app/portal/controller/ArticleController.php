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
use app\portal\service\PostService;
use app\portal\model\PortalPostModel;
use think\Db;

class ArticleController extends HomeBaseController
{
    public function index()
    {

        $portalCategoryModel = new PortalCategoryModel();
        $postService         = new PostService();

        $articleId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('cid', 0, 'intval');
        $article    = $postService->publishedArticle($articleId, $categoryId);

        if (empty($article)) {
            $this->error('文章不存在!',cmf_url("/portal/index"));
        }


        $prevArticle = $postService->publishedPrevArticle($articleId, $categoryId);
        $nextArticle = $postService->publishedNextArticle($articleId, $categoryId);

        $tplName = 'article';
        $category_name='';
        $category_name=$article['category_name'];
        if (empty($categoryId)) {
            $categories = $article['categories'];
            $categoryId=$categories[0]['id'];
            if (count($categories) > 0) {
                $this->assign('category', $categories[0]);
            } else {
                $this->error('文章未指定分类!',cmf_url("/portal/index"));
            }

        } else {
            $category = $portalCategoryModel->where('id', $categoryId)->find();

            if (empty($category)) {
                $this->error('文章不存在!',cmf_url("/portal/index"));
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('portal_post')->where(['id' => $articleId])->setInc('post_hits');

        $exist_tj=Db::name('post_hits')->where(['post_id' => $articleId,'yn'=>date("Y-m",time())])->find();
        if (empty($exist_tj)) {
            $dataa = ['post_id' => $articleId, 'hits' => 1,'yn'=>date("Y-m",time())];
            Db::name('post_hits')->insert($dataa);
        }else{
            Db::name('post_hits')->where(['post_id' => $articleId,'yn'=>date("Y-m",time())])->setInc('hits');
        }
        hook('portal_before_assign_article', $article);

        $this->assign('article', $article);
        $this->assign('prev_article', $prevArticle);
        $this->assign('next_article', $nextArticle);

        $tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];
        $where=[];
        $dwxz=0;
        $areaid = $this->request->param('areaid', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        if($areaid>0){
            $area_result=Db::name("area")->where("id","eq",$areaid)->find();
            if($area_result){
                $area_name=$area_result["name"];
                $dwxz=$area_result["dwxz"];
                $where['c.dwxz']=['eq',$dwxz];
                $where['a.area_id']=['eq',$areaid];
                
                $this->assign("areaname",$area_name);
            }
        }
        $portalPostModel = new PortalPostModel();
        $data_dwgk           = $portalPostModel->alias('a')
        ->join('__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id')
        ->join('__CATEGORY_DWXZ__ c','b.category_id=c.category_id')
        ->field("distinct a.id,a.post_title")->where(["b.category_id"=>$categoryId,"a.post_status"=>1])
        ->where($where)
        ->order('a.update_time', 'DESC')->limit(7)->select();

        $href_dwgk = Db::name("nav_menu")->where("id=2")->find();
        $this->assign("href_dwgk",$href_dwgk);
        $this->assign("data_dwgk",$data_dwgk);
        $this->assign("category_name",$category_name);
        $this->assign("dwxz",$dwxz);
        $this->assign("pagename",$pagename);
        $this->assign("areaid",$areaid);

        return $this->fetch("/$tplName");
    }

    // 文章点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$articleId", 1);

        if ($canLike) {
            Db::name('portal_post')->where(['id' => $articleId])->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }

    public function ldbzlb(){
        $param=$this->request->param();
        $areaid = $this->request->param('areaid', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        $area_name="";
        $dwxz=0;
        if($areaid>0){
            $area_result=Db::name("area")->where("id","eq",$areaid)->find();
            if($area_result){
                $area_name=$area_result["name"];
                $dwxz=$area_result["dwxz"];
                

                $list=Db::name("ldbz")->where(['area_id'=>$areaid])->order('create_time','desc')->paginate(6);
            }
        }
        $dwxz_name="";
        switch ($dwxz) {
            case '1':
            case '3':
                $dwxz_name="党政班子";
                break;
            case '2':
                $dwxz_name="村两委班子";
                break;
            default:
                break;
        }

        $list->appends($param);
        $this->assign('page', $list->render());
        $this->assign('list', $list);
        $this->assign('dwxz_name', $dwxz_name);
        $this->assign("dwxz",$dwxz);
        $this->assign("pagename",$pagename);
        $this->assign("areaid",$areaid);
        $this->assign("areaname",$area_name);
        return $this->fetch(':ldbzlb');
    }

    public function ldbzxx(){
        $param=$this->request->param();
        $id = $this->request->param('id',0, 'intval');
        $areaid = $this->request->param('areaid', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        $area_name="";
        $dwxz=0;
        if($areaid>0){
            $area_result=Db::name("area")->where("id","eq",$areaid)->find();
            if($area_result){
                $area_name=$area_result["name"];
                $dwxz=$area_result["dwxz"];
                

                $result=Db::name("ldbz")->where(['id'=>$id])->find();
            }
        }
        $dwxz_name="";
        switch ($dwxz) {
            case '1':
            case '3':
                $dwxz_name="党镇班子";
                break;
            case '2':
                $dwxz_name="村级班子";
                break;
            default:
                break;
        }

        $this->assign('result', $result);
        $this->assign('dwxz_name', $dwxz_name);
        $this->assign("dwxz",$dwxz);
        $this->assign("pagename",$pagename);
        $this->assign("areaid",$areaid);
        $this->assign("areaname",$area_name);
        return $this->fetch(':ldbzxx');
    }

}
