<?php
// +----------------------------------------------------------------------
// | ThinkCMF 方案师简介管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CaserController extends AdminbaseController {
	public function index(){				
		$count= D()->field('u.*,r.name')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")
				->where("u.id=ru.user_id and ru.role_id=r.id and r.name='方案师' and u.status=1")
				->count();		
		$page = $this->page($count, 8);	
		$list =  D()->field('u.*,r.name')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")
				->where("u.id=ru.user_id and ru.role_id=r.id and r.name='方案师' and u.status=1")
				->limit($page->firstRow, $page->listRows)
				->order('u.create_time desc')
                ->select();		
		
		$this->assign("page", $page->show('Admin'));		
		$this->assign("list",$list);
		$this->display();
	}
	
	// 简历编辑
	public function edit(){
		$id=  I("get.id",0,'intval');		
		$info = M('users')
            ->where("id='$id'")
            ->find();		
		$this->assign("info",$info);
		$this->display();
	}
	// 简历编辑提交
	public function edit_post(){
		if (IS_POST) {			
			$pdata=I("post.");			
			$article=array();
			if(empty($pdata['resume']))
			{
				$this->ajaxReturn(array('msg'=>"请编辑简历内容",'status'=>1));
			}
			$article['resume'] = strcontentjs(htmlspecialchars_decode($pdata['resume']));	
			$result=M('users')->where("id='".$pdata['id']."'")->save($article);		
			if ($result!==false) {
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'编辑'.$pdata['user_name'].'的简历内容','编辑项目');
				$this->ajaxReturn(array('msg'=>'保存成功！','status'=>0));
			} else {
				$this->ajaxReturn(array('msg'=>'资料未更改','status'=>1));
			}
		}
	}	
}