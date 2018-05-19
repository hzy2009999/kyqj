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

class SearchController extends HomeBaseController
{
    public function index()
    {
        $keyword = $this->request->param('keyword');
        $areaid = $this->request->param('areaid', 0, 'intval');
        $dwxz = $this->request->param('dwxz', 0, 'intval');
        $pagename = $this->request->param('pagename', '');
        $ssfs = $this->request->param('ssfs', '');
        $datatime = $this->request->param('datatime', '');
        if (empty($keyword)) {
            $this -> error("关键词不能为空！请重新输入！");
        }

        $this -> assign("keyword", $keyword);
        $this -> assign("areaid", $areaid);
        $this -> assign("dwxz", $dwxz);
        $this -> assign("pagename", $pagename);
        $this -> assign("ssfs", $ssfs);
        $this -> assign("dt", $datatime);
        return $this->fetch('/search');
    }
}
