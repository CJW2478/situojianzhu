<?php
// +----------------------------------------------------------------------
// | ThinkCMF 前台流程图展示
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业务爱好者 <6491803974@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Think\Controller;
class IndexController extends Controller {
    // 流程图展示
    public function index(){

        $article =M('lcarticles')->order('id asc')->select();       
        $this->assign('article',$article);

        // 客服电话
        $mobile=M('options')->where("option_name='mobile_setting'")->getField('option_value');
        $this->assign('mobile',$mobile);
        $this->display();
    }
}