<?php
// +----------------------------------------------------------------------
// | ThinkCMF 施工图阶段板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class StageController extends AdminbaseController {
	// 沟通记录文件
	public function index(){				
		
		$project_id =I('project_id','','intval');
		if($project_id)
		{
			$info=M('project')->find($project_id);	
		}
		else
		{
			$info=D()->field('p.*')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."company c")->where("c.id=p.company_id and c.id='".session('ADMINID')."'")->order('p.create_time desc')->find();
		}
		// 相关的项目
		$list=D()->field('p.*')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."company c")->where("c.id=p.company_id and c.id='".session('ADMINID')."'")->order('p.create_time desc')->select();

		$count =M('uploadgg_data')->where("project_id='".$info['id']."' and type=1")->count();
		$page =$this->page($count,8);
		$files =M('uploadgg_data')->where("project_id='".$info['id']."' and type=1")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
		
		foreach ($files as $key => $value) {
			if($value['atype'] == 2)
			{
				$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
			}else
			{
				$files[$key]['user_name'] =$info['principal_name'];
			}			
		}
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['id'] =$pdata['solution_id'];
			unset($pdata['solution_id']);
			if($_FILES['filename']['name'] !='')
			{
				$pdata['mfilename'] =$_FILES['filename']['name'];
				$pdata['murldata'] =uploadOne($_FILES['filename'],'message');
				if($pdata['murldata'])
				{
					$pdata['murldata']='./company/'.$pdata['murldata'];
				}
			}
			$pdata['adminid2'] =session('ADMINID');
			$pdata['mtime'] =time();
			$res=M('uploadgg_data')->save($pdata);			
			redirect(U('Stage/index',array('project_id'=>$pdata['project_id'])));						
		}

		// 平面图确认 未标识
		$pictcount=M('uploadpic_data')->where("project_id='".$info['id']."' and status=1")->count();
		$piccount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='uploadpic_data'")->getField('number');
		$pcount =$pictcount-$piccount;
		$this->assign('pcount',$pcount);

		$this->assign('files',$files);
		$this->assign('info',$info);
		$this->assign('project_id',$info['id']);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST)); 
		$this->assign("page", $page->show('Admin'));
		$this->display();
	}
	// 平面图
	public function indexlist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);	
		// 专业相关的项目
		$list=D()->field('p.*')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."company c")->where("c.id=p.company_id and c.id='".session('ADMINID')."'")->order('p.create_time desc')->select();
		// 平面图信息
		$count =M('uploadpic_data')->where("project_id='".$project_id."' and status=1")->count();
		$page =$this->page($count,8);
		$piclist =M('uploadpic_data')->where("project_id='".$project_id."' and status=1")->order('createtime desc')->limit($page->firstRow , $page->listRows)->select();
		foreach ($piclist as $key => $value) {
			$piclist[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
		}
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['id'] =$pdata['solution_id'];
			$pdata['recontent'] =$pdata['message'];
			unset($pdata['solution_id']);
			unset($pdata['message']);
			if($_FILES['filename']['name'] !='')
			{
				$pdata['refilename'] =$_FILES['filename']['name'];
				$pdata['reurldata'] =uploadOne($_FILES['filename'],'message');
				if($pdata['reurldata'])
				{
					$pdata['reurldata']='./company/'.$pdata['reurldata'];
				}
			}
			$res=M('uploadpic_data')->save($pdata);
			
			redirect(U('Stage/indexlist',array('project_id'=>$pdata['project_id'])));						
		}
		if($info['id'] > 0)
		{
			addreadlog(session('ADMINID'),$info['id'],2,'uploadpic_data',C('DB_PREFIX').'uploadpic_data');
		}
		$this->assign("page", $page->show('Admin'));
		$this->assign('files',$piclist);
		$this->assign('info',$info);
		$this->assign('project_id',$info['id']);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
		
	}	
	
	// 各专业人员基本信息
	public function allzylist()
	{

		$project_id =I('project_id','','intval');
		$info=M('project')->find($project_id);	
		$list=D()->field('p.*')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."company c")->where("c.id=p.company_id and c.id='".session('ADMINID')."'")->order('p.create_time desc')->select();
		// 项目相关的各专业人员
		$zylist =D()->field('u.user_name,u.mobile,u.user_duty,u.qq_no,u.wx_no,s.role_name')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."users u")->where("s.by_distribution=u.id and s.project_id='$project_id' and s.status=1")->select();
		$this->assign('info',$info);
		$this->assign('zyall',$zylist);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	// ajax 上传沟通记录文件
	public function uploadmessage()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['adminid'] =session('ADMINID');
			$pdata['atype']=1;
			$pdata['status2']=1;
			$pdata['createtime'] =time();
			$lastid=M('uploadgg_data')->add($pdata);
			if($lastid)
			{
				$username =M('users')->where("id='".$pdata['adminid']."'")->getField('user_name');
				$this->ajaxReturn(array('status'=>0,'url'=>U($pdata['url'],array('project_id'=>$pdata['project_id']))));
			}else
			{
				$this->ajaxReturn(array('status'=>1));
			}
		}
	}
	
	// 获取反馈信息
	public function getggmesage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('uploadgg_data')->find($id);
			$content =htmlspecialchars_decode($info['message']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfilename'],'urldata'=>$info['murldata'],'url'=>U('Admin/Stage/downloadgg',array('id'=>$id))));
		}
	}	
	// 下载沟通记录反馈
	public function downloadgg()
	{
		$id=I('id','','intval');
		$info =M('uploadgg_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['murldata'];
        $url_file =str_replace('\company','',$url_file);
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['mfilename'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	
	// 下载平面图文件
	public function download()
	{
		$id=I('id','','intval');
		$info =M('uploadpic_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
        $url_file =str_replace('\company','',$url_file);
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['filename'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	// 获取反馈信息
	public function getpmmessage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('uploadpic_data')->find($id);
			$content =htmlspecialchars_decode($info['recontent']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['refilename'],'urldata'=>$info['reurldata'],'url'=>U('Admin/Stage/download2',array('id'=>$id))));
		}
	}
	// 下载平面图反馈意见
	public function download2()
	{
		$id=I('id','','intval');
		$info =M('uploadpic_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['reurldata'];
        $url_file =str_replace('\company','',$url_file);
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['refilename'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	// 下载记录文件
	public function downloadmessage()
	{
		$id=I('id','','intval');
		$info =M('uploadgg_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
        if($info['type'] !=2)
        {
        	$url_file =str_replace('\company','',$url_file);
        }        
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['filename'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	
}