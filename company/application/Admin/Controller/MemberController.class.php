<?php
// +----------------------------------------------------------------------
// | ThinkCMF 会员管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MemberController extends AdminbaseController {
	// 后台客户管理列表
	public function index(){				
		$this->member =M('account');
		
		$count=$this->member->where("company_id='".session('ADMINID')."'")->count();			
		$page = $this->page($count, 20);			
		$member=$this->member
                ->where("company_id='".session('ADMINID')."' and status=1")
				->limit($page->firstRow , $page->listRows)
				->order("create_time desc")
				->select();
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("member",$member);
		$this->display();
	}
	// 新增
    public function add()
    {
        $this->display();
    } 
    public function add_post()
    {
        if(IS_POST)
        {
            $pdata=I('post.');
            if(empty($pdata['mobile']) || empty($pdata['user_name']) || empty($pdata['user_duty']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请填写完整信息'));
            }else
            {
                if(!preg_match('/^1[345789]{1}\d{9}$/', $pdata['mobile']))
                {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的手机号'));
                }
            }
            $count =M('commonusers')->where("mobile='".$pdata['mobile']."'")->count();
            if($count > 0)
            {
                $this->ajaxReturn(array('status' =>1,'msg'=>'该手机号已注册'));
            }else
            {
                $pdata['password']=sp_password(123456);
                $pdata['create_time'] =time();
                $pdata['company_id'] =session('ADMINID');
                $res=M('account')->add($pdata);
                if($res)
                {
                    $this->ajaxReturn(array('status' =>0,'msg'=>'保存成功'));
                }
            }
        }
    }
    // 编辑
    public function edit()
    {
        $id=I('id','','intval');
        $info =M('account')->find($id);
        $this->assign('info',$info);
        $this->display();
    } 
    //编辑保存 
	public function edit_post()
	{
		if(IS_POST)
		{
			$pdata =I('post.');	
			if(empty($pdata['mobile']) || empty($pdata['user_name']) || empty($pdata['user_duty']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请填写完整信息'));
            }else
            {
                if(!preg_match('/^1[345789]{1}\d{9}$/', $pdata['mobile']))
                {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的手机号'));
                }
            }
            if($pdata['mobile'] !=$pdata['oldmobile'])
            {
                $count =M('account')->where("mobile='".$pdata['mobile']."'")->count();
                if($count > 0)
                {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'该手机号已注册'));
                }
            }
			
			M('account')->where("id='".$pdata['id']."'")->save($pdata);
			$info =M('account')->find($pdata['id']);
			if($info['atype'] == 1)
			{
				M('project')->where("id='".$info['pid']."'")->save(array('mobile'=>$pdata['mobile'],'principal_name'=>$pdata['user_name'],'duty'=>$pdata['user_duty']));
			}
			$this->ajaxReturn(array('status'=>0,'msg'=>'保存成功'));
		}
	}
	
	public function delete()
	{
		$id = I('post.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('account')->where(array("id"=>$id))->setField('status',2);
    		if ($result!==false){
    			$this->ajaxReturn(array('status'=>0,'msg'=>'删除成功'));
    		} else {
    			$this->ajaxReturn(array('status'=>1,'msg'=>'删除失败'));
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
	}	
}