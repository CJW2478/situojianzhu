<?php
// +----------------------------------------------------------------------
// | ThinkCMF 流程图文管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LcarticleController extends AdminbaseController {
	// 文章管理列表
	public function index(){				
		$this->model =M('lcarticles');
		$count=$this->model->count();			
		$page = $this->page($count, 20);			
		$list=$this->model
			->limit($page->firstRow , $page->listRows)
			->order("addtime ASC")
			->select();		
		
		$this->assign("page", $page->show('Admin'));		
		$this->assign("list",$list);
		$this->display();
	}
	
	// 文章编辑
	public function edit(){
		$id=  I("get.id",0,'intval');		
		$info=M('lcarticles')->where("id=$id")->find();
		$this->assign("info",$info);
		$this->display();
	}
	// 客户电话编辑
	public function editmobile(){
			
		$info=M('options')->where("option_name='mobile_setting'")->find();
		$this->assign("info",$info);
		$this->display();
	}
	public function editmobile_post(){
		if (IS_POST) {			
			$pdata=I("post.");			
			if(empty($pdata['mobile']))
			{
				$this->ajaxReturn(array('msg'=>"请编辑客服电话",'status'=>1));
			}
			$pdata['option_value'] =$pdata['mobile'];
			$result=M('options')->where("option_name='mobile_setting'")->save($pdata);
			unset($pdata);				
			if ($result!==false) {
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'编辑客服电话','编辑项目');
				$this->ajaxReturn(array('msg'=>'保存成功！','status'=>0));
			} else {
				$this->ajaxReturn(array('msg'=>'资料未更改','status'=>1));
			}
		}
	}	
	// 文章编辑提交
	public function edit_post(){
		if (IS_POST) {			
			$pdata=I("post.");			
			$article=array();
			if(empty($pdata['content']))
			{
				$this->ajaxReturn(array('msg'=>"请编辑内容",'status'=>1));
			}
			$article['content'] = strcontentjs(htmlspecialchars_decode($pdata['content']));	
			$result=M('lcarticles')->where("id='".$pdata['id']."'")->save($article);
			unset($pdata);				
			if ($result!==false) {
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'编辑流程图文内容','编辑项目');
				$this->ajaxReturn(array('msg'=>'保存成功！','status'=>0));
			} else {
				$this->ajaxReturn(array('msg'=>'资料未更改','status'=>1));
			}
		}
	}	
}