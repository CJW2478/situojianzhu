<?php
// +----------------------------------------------------------------------
// | ThinkCMF 效果图阶段板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CaseschemeController extends AdminbaseController {
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
		// 审核过的方案师基本信息
		$fanganer =D()->field('u.*')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("s.by_distribution=u.id and s.role_name='方案师' and s.status=1 and s.project_id='".$info['id']."'")->order('s.updatetime desc')->find();
		
		$count =M('uploadgg_data')->where("project_id='".$info['id']."' and type = 4 ")->count();
		$page =$this->page($count,8);
		$files =M('uploadgg_data')->where("project_id='".$info['id']."' and type = 4 ")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
		
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
			$pdata['mtime']=time();
			$res=M('uploadgg_data')->save($pdata);
			
			redirect(U('Casescheme/index',array('project_id'=>$pdata['project_id'])));						
		}
		

		// 项目风格选中未标识
		$protcount=M('upload_projectinfo')->where("project_id='".$info['id']."' and type=2")->count();
		$procount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='upload_projectinfo'")->getField('number');
		$pcount =$protcount-$procount;
		$this->assign('pcount',$pcount);
		
		// 效果图方案确认未标识
		$erotcount=M('upload_effect')->where("project_id='".$info['id']."' and (status!=0 or audit_status !=0)")->count();
		$erocount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='upload_effect'")->getField('number');
		$ecount =$erotcount-$erocount;
		$this->assign('ecount',$ecount);
		
		if($info['id']>0){
			addreadlog2(session('ADMINID'),$info['id'],2,'uploadgg12',C('DB_PREFIX').'uploadgg_data');
		}
		$this->assign('files',$files);
		$this->assign('info',$info);
		$this->assign('fanganer',$fanganer);
		$this->assign('project_id',$info['id']);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST)); 
		$this->assign("page", $page->show('Admin'));
		$this->display();
	}
	// 项目风格选中
	public function indexlist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);	
		// 相关的项目
		$list=D()->field('p.*')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."company c")->where("c.id=p.company_id and c.id='".session('ADMINID')."'")->order('p.create_time desc')->select();
		// 审核过的方案师基本信息
		$fanganer =D()->field('u.*')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("s.by_distribution=u.id and s.role_name='方案师' and s.status=1 and s.project_id='".$info['id']."'")->order('s.updatetime desc')->find();

		// 项目风格
		$count =M('upload_projectinfo')->where("project_id='".$project_id."' and type=2")->count();
		$page =$this->page($count,8);
		$piclist =M('upload_projectinfo')->where("project_id='".$project_id."' and type=2")->order('create_time desc')->limit($page->firstRow , $page->listRows)->select();
		foreach ($piclist as $key => $value) {
			$piclist[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
		}
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['id'] =$pdata['solution_id'];
			unset($pdata['solution_id']);
			if($_FILES['filename']['name'] !='')
			{
				$pdata['mfile_name'] =$_FILES['filename']['name'];
				$pdata['mfile_url'] =uploadOne($_FILES['filename'],'message');
				if($pdata['mfile_url'])
				{
					$pdata['mfile_url']='./company/'.$pdata['mfile_url'];
				}
			}
			$pdata['status']=1;
			$res=M('upload_projectinfo')->save($pdata);
			$p=M('project')->find($pdata['project_id']);
			if($p['projectvalue']==11 && $p['projecttype']==2)
			{
				$this->change_stage($pdata['project_id'],12);
			}           

			redirect(U('Casescheme/indexlist',array('project_id'=>$pdata['project_id'])));						
		}
		$count1 =M('upload_projectinfo')->where("project_id='".$project_id."' and status=1")->count();

		// 效果图方案确认未标识
		$erotcount=M('upload_effect')->where("project_id='".$info['id']."' and status=1")->count();
		$erocount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='upload_effect'")->getField('number');
		$ecount =$erotcount-$erocount;
		$this->assign('ecount',$ecount);
		//沟通文件
		$tcount =M('uploadgg_data')->where("project_id='".$info['id']."' and type=4")->count();
		$rocount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='uploadgg12'")->getField('number');
		$e1count =$tcount-$rocount;
		$this->assign('e1count',$e1count);
		
		if($info['id'] > 0)
		{
			addreadlog9(session('ADMINID'),$info['id'],2,'upload_projectinfo',C('DB_PREFIX').'upload_projectinfo');
		}		

		$this->assign('count1',$count1);
		$this->assign("page", $page->show('Admin'));
		$this->assign('files',$piclist);
		$this->assign('fanganer',$fanganer);
		$this->assign('info',$info);
		$this->assign('project_id',$info['id']);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}	
	
	// 效果图方案确认
	public function allzylist()
	{

		$project_id =I('project_id','','intval');
		$info=M('project')->find($project_id);	
		// 相关的项目
		$list=D()->field('p.*')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."company c")->where("c.id=p.company_id and c.id='".session('ADMINID')."'")->order('p.create_time desc')->select();
		// 审核过的方案师基本信息
		$fanganer =D()->field('u.*')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("s.by_distribution=u.id and s.role_name='方案师' and s.status=1 and s.project_id='".$info['id']."'")->order('s.updatetime desc')->find();

		$count =M('upload_effect')->where("project_id='$project_id' and ((is_first=1 and status=0) or (is_first=0))")->count();
		$page =$this->page($count,8);
		$fanganlist =M('upload_effect')->where("project_id='$project_id' and ((is_first=1 and status=0) or (is_first=0))")->order('create_time desc')->limit($page->firstRow,$page->listRows)->select();
		foreach ($fanganlist as $key => $value) {
			$fanganlist[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
		}
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['id'] =$pdata['solution_id'];
			unset($pdata['solution_id']);
			if($_FILES['filename']['name'] !='')
			{
				$pdata['mfile_name'] =$_FILES['filename']['name'];
				$pdata['mfile_url'] =uploadOne($_FILES['filename'],'message');
				if($pdata['mfile_url'])
				{
					$pdata['mfile_url']='./company/'.$pdata['mfile_url'];
				}
			}

			$res=M('upload_effect')->save($pdata);
			$effect_data = M('upload_effect')->where('id='.$pdata['id'])->find();

			$p =M('project')->find($pdata['project_id']);
			if($p['projectvalue']<17 && $p['projecttype']==2)
			{
				$this->change_stage($pdata['project_id'],17);
			}
			redirect(U('Casescheme/allzylist',array('project_id'=>$pdata['project_id'])));						
		}

		// 项目风格选中未标识
		$protcount=M('upload_projectinfo')->where("project_id='".$info['id']."' and type=2")->count();
		$procount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='upload_projectinfo'")->getField('number');
		$pcount =$protcount-$procount;
		$this->assign('pcount',$pcount);

		//沟通文件
		$tcount =M('uploadgg_data')->where("project_id='".$info['id']."' and type in(1,2)")->count();
		$rocount =M('uploadreadlog')->where("adminid='".session('ADMINID')."' and project_id='".$info['id']."' and type=2 and modelname='uploadgg12'")->getField('number');
		$e1count =$tcount-$rocount;
		$this->assign('e1count',$e1count);
		if($info['id'] > 0)
		{
			addreadlog20(session('ADMINID'),$info['id'],2,'upload_effect',C('DB_PREFIX').'upload_effect');
		}
		$this->assign('info',$info);
		$this->assign('fanganer',$fanganer);
		$this->assign('fanganlist',$fanganlist);
		$this->assign("page", $page->show('Admin'));
		$this->assign('project_id',$info['id']);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
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
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfilename'],'urldata'=>$info['murldata'],'url'=>U('Admin/Casescheme/downloadgg',array('id'=>$id))));
		}
	}	
	public function getfamesage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('upload_effect')->find($id);
			$content =htmlspecialchars_decode($info['message']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfile_name'],'urldata'=>$info['mfile_url'],'url'=>U('Admin/Casescheme/downloadfagg',array('id'=>$id))));
		}
	}
	// 获取风格我的反馈
	public function getfenggemesage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('upload_projectinfo')->find($id);
			$content =htmlspecialchars_decode($info['message']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfile_name'],'urldata'=>$info['mfile_url'],'url'=>U('Admin/Casescheme/downloadfgfile',array('id'=>$id))));
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
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['refilename'],'urldata'=>$info['reurldata'],'url'=>U('Admin/Casescheme/download2',array('id'=>$id))));
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
	public function downloadfgfileaaa()
	{
		$id=I('id','','intval');
		$info =M('upload_projectinfo')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['file_url'];
        $url_file =str_replace('\company','',$url_file); 
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['file_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}	
	// 下载风格文件
	public function downloadfgfile()
	{
		$id=I('id','','intval');
		$info =M('upload_projectinfo')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['mfile_url'];
        $url_file =str_replace('\company','',$url_file); 
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['mfile_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}	
	// 下载方案确认反馈
	public function downloadfagg()
	{
		$id=I('id','','intval');
		$info =M('upload_effect')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['mfile_url'];
        $url_file =str_replace('\company','',$url_file); 
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['mfile_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	public function downloadfamessage()
	{
		$id=I('id','','intval');
		$info =M('upload_effect')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['file_url'];
        $url_file =str_replace('\company','',$url_file); 
        if (file_exists($url_file))
        {
			$p=M('project')->find($info['project_id']);
			if($p['projectvalue']==14 && $p['projecttype']==2)
			{
				M('project')->where("id='".$p['id']."'")->setField('projectvalue',15);
			}
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['file_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
}