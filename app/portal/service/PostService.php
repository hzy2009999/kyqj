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
namespace app\portal\service;

use app\portal\model\PortalPostModel;
use think\Db;

class PostService
{

    public function adminArticleList($filter)
    {
        return $this->adminPostList($filter);
    }

    public function adminPageList($filter)
    {
        return $this->adminPostList($filter, true);
    }

    public function adminPostList($filter, $isPage = false)
    {

        $where = [
            'a.create_time' => ['>=', 0],
            'a.delete_time' => 0
        ];

        $join = [
            ['__USER__ u', 'a.user_id = u.id']
        ];
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
        }else{
            array_push($join, [
            '__AREA__ t', "t.id = a.area_id"
        ]);
        }
        

        $field = "a.*,u.user_login,u.user_nickname,u.user_email,t.name as areaname,length(t.path)-length(replace(t.path,'-','')) as areapath";

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $where['b.category_id'] = ['eq', $category];
            array_push($join, [
                '__PORTAL_CATEGORY_POST__ b', 'a.id = b.post_id'
            ]);
            $field = "a.*,b.id AS post_category_id,b.list_order,b.category_id,u.user_login,u.user_nickname,u.user_email,t.name as areaname,length(t.path)-length(replace(t.path,'-','')) as areapath";
        }

        $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
        $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['a.published_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['a.published_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['a.published_time'] = ['<= time', $endTime];
            }
        }

        $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
        if (!empty($keyword)) {
            $where['a.post_title'] = ['like', "%$keyword%"];
        }

        if ($isPage) {
            $where['a.post_type'] = 2;
        } else {
            $where['a.post_type'] = 1;
        }
        if (!empty($path)) {
           $where['t.path'] =  ["like",$path."%"];
        }
        

        $portalPostModel = new PortalPostModel();
        $articles        = $portalPostModel->alias('a')->field($field)
            ->join($join)
            ->where($where)
            ->order('update_time', 'DESC')
            ->paginate(10);

        return $articles;

    }

    public function publishedArticle($postId, $categoryId = 0)
    {
        $portalPostModel = new PortalPostModel();
        $where=[];
        if (empty($categoryId)) {

            $where = [
                'post.post_type'      => 1,
                // 'post.published_time' => [['< time', time()], ['> time', 0]],
                // 'post.post_status'    => 1,
                'post.delete_time'    => 0,
                'post.id'             => $postId
            ];
        } else {
            $where = [
                'post.post_type'       => 1,
                // 'post.published_time'  => [['< time', time()], ['> time', 0]],
                // 'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => $postId
            ];

           
        }

        $join    = [
            ['__PORTAL_CATEGORY_POST__ relation', 'post.id = relation.post_id'],
            ['__PORTAL_CATEGORY__ c', 'relation.category_id = c.id']
        ];
        $article = $portalPostModel->alias('post')->field('post.post_title,post.published_time,post.more,post.id,post.post_content,c.name as category_name')
            ->join($join)
            ->where($where)
            ->find();

        return $article;
    }

    //上一篇文章
    public function publishedPrevArticle($postId, $categoryId = 0)
    {
        $portalPostModel = new PortalPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'      => 1,
                'post.published_time' => [['< time', time()], ['> time', 0]],
                'post.post_status'    => 1,
                'post.delete_time'    => 0,
                'post.id '            => ['<', $postId]
            ];

            $article = $portalPostModel->alias('post')->field('post.*')
                ->where($where)
                ->order('id', 'DESC')
                ->find();

        } else {
            $where = [
                'post.post_type'       => 1,
                'post.published_time'  => [['< time', time()], ['> time', 0]],
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => ['<', $postId]
            ];

            $join    = [
                ['__PORTAL_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $article = $portalPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->order('id', 'DESC')
                ->find();
        }


        return $article;
    }

    //下一篇文章
    public function publishedNextArticle($postId, $categoryId = 0)
    {
        $portalPostModel = new PortalPostModel();

        if (empty($categoryId)) {

            $where = [
                'post.post_type'      => 1,
                'post.published_time' => [['< time', time()], ['> time', 0]],
                'post.post_status'    => 1,
                'post.delete_time'    => 0,
                'post.id'             => ['>', $postId]
            ];

            $article = $portalPostModel->alias('post')->field('post.*')
                ->where($where)
                ->order('id', 'ASC')
                ->find();
        } else {
            $where = [
                'post.post_type'       => 1,
                'post.published_time'  => [['< time', time()], ['> time', 0]],
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => ['>', $postId]
            ];

            $join    = [
                ['__PORTAL_CATEGORY_POST__ relation', 'post.id = relation.post_id']
            ];
            $article = $portalPostModel->alias('post')->field('post.*')
                ->join($join)
                ->where($where)
                ->order('id', 'ASC')
                ->find();
        }


        return $article;
    }

    public function publishedPage($pageId)
    {

        $where = [
            'post_type'      => 2,
            'published_time' => [['< time', time()], ['> time', 0]],
            'post_status'    => 1,
            'delete_time'    => 0,
            'id'             => $pageId
        ];

        $portalPostModel = new PortalPostModel();
        $page            = $portalPostModel
            ->where($where)
            ->find();

        return $page;
    }

}