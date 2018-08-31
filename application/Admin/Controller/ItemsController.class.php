<?php
// +----------------------------------------------------------------------
// | ThinkCMF 项目总汇管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ItemsController extends AdminbaseController {
	// 施工图所需资料
	public function index(){				
		
		$project_id =I('project_id','','intval');
		if($project_id)
		{
			$info=M('project')->find($project_id);
		}
		else
		{
			//$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->group('p.id')->order('s.create_time desc')->find();
			$info =M('project')->where("isdel=0")->order('create_time desc')->find();
		}
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->order('s.create_time desc')->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 施工图资料
		$files =M('upload_data')->where("project_id='".$info['id']."' and type=3")->order('create_time desc')->select();
		foreach($files as $k=>$v)
		{
			$files[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
		}
		
		// 平面图规划方案未读数
		$pmghtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=1")->count();
		$pmghcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspmgh' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pcount =$pmghtcount-$pmghcount;
		$this->assign('pcount',$pcount);

		// 效果图方案未读数
		$xgtcount =M('upload_effect')->where("atype=2 and project_id='".$info['id']."'")->count();
		$xgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsxg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$xcount =$xgtcount-$xgcount;
		$this->assign('xcount',$xcount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspic' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业条件图未读数
		$zytcount =M('uploadzy_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszy' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zcount =$zytcount-$zycount;
		$this->assign('zcount',$zcount);

		// 各专业施工图未读数
		$zysgtcount =M('uploadzysg_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zysgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszysg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$sgcount =$zysgtcount-$zysgcount;
		$this->assign('sgcount',$sgcount);

		// 内部施工图审核意见标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsmessage' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);

		$this->assign('files',$files);		
		$this->assign('info',$info);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	// 平面图规划方案
	public function piccaselist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->order('s.create_time desc')->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 平面图规划
		$files =M('upload_data')->where("project_id='".$info['id']."' and type=1 and status=0 and audit_status=0")->order('create_time desc')->select();

		foreach($files as $k=>$v)
		{
			$files[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
		}



		// 效果图方案未读数
		$xgtcount =M('upload_effect')->where("atype=2 and project_id='".$info['id']."'")->count();
		$xgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsxg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$xcount =$xgtcount-$xgcount;
		$this->assign('xcount',$xcount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspic' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业条件图未读数
		$zytcount =M('uploadzy_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszy' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zcount =$zytcount-$zycount;
		$this->assign('zcount',$zcount);

		// 各专业施工图未读数
		$zysgtcount =M('uploadzysg_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zysgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszysg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$sgcount =$zysgtcount-$zysgcount;
		$this->assign('sgcount',$sgcount);

		// 内部施工图审核意见标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsmessage' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);

		// 平面图规划方案写入标记表
		addreadlog(session('ADMIN_ID'),$info['id'],2,'itemspmgh',C('DB_PREFIX').'upload_data',1);
		$this->assign('files',$files);		
		$this->assign('info',$info);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST)); 
		$this->display();
	}
	// 效果方案
	public function xgcaselist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->order('s.create_time desc')->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 效果图方案
		$files =M('upload_effect')->where("project_id='".$info['id']."' and status=0 and audit_status=0")->order('create_time desc')->select();

		foreach($files as $k=>$v)
		{
			$files[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
		}

		// 平面图规划方案未读数
		$pmghtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=1")->count();
		$pmghcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspmgh' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pcount =$pmghtcount-$pmghcount;
		$this->assign('pcount',$pcount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspic' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业条件图未读数
		$zytcount =M('uploadzy_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszy' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zcount =$zytcount-$zycount;
		$this->assign('zcount',$zcount);

		// 各专业施工图未读数
		$zysgtcount =M('uploadzysg_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zysgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszysg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$sgcount =$zysgtcount-$zysgcount;
		$this->assign('sgcount',$sgcount);

		// 内部施工图审核意见标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsmessage' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);
		addreadlog(session('ADMIN_ID'),$info['id'],2,'itemsxg',C('DB_PREFIX').'upload_effect');
		$this->assign('files',$files);		
		$this->assign('info',$info);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST)); 
		$this->display();
	}
	// 平面图
	public function indexlist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->order('s.create_time desc')->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 平面图
		$files =M('uploadpic_data')->where("project_id='".$info['id']."' and status=1 and status2=1")->order('createtime desc')->select();
		foreach($files as $k=>$v)
		{
			$files[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
		}

		// 平面图规划方案未读数
		$pmghtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=1")->count();
		$pmghcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspmgh' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pcount =$pmghtcount-$pmghcount;
		$this->assign('pcount',$pcount);

		// 效果图方案未读数
		$xgtcount =M('upload_effect')->where("atype=2 and project_id='".$info['id']."'")->count();
		$xgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsxg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$xcount =$xgtcount-$xgcount;
		$this->assign('xcount',$xcount);
		
		// 各专业条件图未读数
		$zytcount =M('uploadzy_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszy' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zcount =$zytcount-$zycount;
		$this->assign('zcount',$zcount);

		// 各专业施工图未读数
		$zysgtcount =M('uploadzysg_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zysgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszysg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$sgcount =$zysgtcount-$zysgcount;
		$this->assign('sgcount',$sgcount);

		// 内部施工图审核意见标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsmessage' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);
		addreadlog(session('ADMIN_ID'),$info['id'],2,'itemspic',C('DB_PREFIX').'uploadpic_data');
		$this->assign('files',$files);		
		$this->assign('info',$info);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST)); 
		$this->display();
	}
	// 专业信息
	public function zyinfolist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->group('p.id')->select();
		// 各专业条件图
		$count = M('uploadzy_data')->where("project_id='".$project_id."'")->count();
		$page =$this->page($count,8);
		$files =M('uploadzy_data')->where("project_id='".$project_id."'")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
		foreach ($files as $key => $value) {			
			$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');						
		}
		
		// 平面图规划方案未读数
		$pmghtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=1")->count();
		$pmghcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspmgh' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pcount =$pmghtcount-$pmghcount;
		$this->assign('pcount',$pcount);

		// 效果图方案未读数
		$xgtcount =M('upload_effect')->where("atype=2 and project_id='".$info['id']."'")->count();
		$xgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsxg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$xcount =$xgtcount-$xgcount;
		$this->assign('xcount',$xcount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspic' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业施工图未读数
		$zysgtcount =M('uploadzysg_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zysgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszysg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$sgcount =$zysgtcount-$zysgcount;
		$this->assign('sgcount',$sgcount);

		// 内部施工图审核意见标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsmessage' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);

		addreadlog(session('ADMIN_ID'),$info['id'],2,'itemszy',C('DB_PREFIX').'uploadzy_data');
		$this->assign('info',$info);
		$this->assign('files',$files);
		$this->assign('project',$list);
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	// 各专业施工图
	public function bzysglist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 本专业施工图
		$count = M('uploadzysg_data')->where("project_id='".$project_id."'")->count();
		$page =$this->page($count,8);
		$zysgfile =M('uploadzysg_data')->where("project_id='".$project_id."'")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
		foreach ($zysgfile as $ky => $val) {			
			$zysgfile[$ky]['user_name'] =M('users')->where("id='".$val['adminid']."'")->getField('user_name');						
		}

		// 平面图规划方案未读数
		$pmghtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=1")->count();
		$pmghcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspmgh' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pcount =$pmghtcount-$pmghcount;
		$this->assign('pcount',$pcount);

		// 效果图方案未读数
		$xgtcount =M('upload_effect')->where("atype=2 and project_id='".$info['id']."'")->count();
		$xgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsxg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$xcount =$xgtcount-$xgcount;
		$this->assign('xcount',$xcount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspic' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业条件图未读数
		$zytcount =M('uploadzy_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszy' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zcount =$zytcount-$zycount;
		$this->assign('zcount',$zcount);

		// 内部施工图审核意见标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsmessage' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);

		addreadlog(session('ADMIN_ID'),$info['id'],2,'itemszysg',C('DB_PREFIX').'uploadzysg_data');
		$this->assign('info',$info);		
		$this->assign('zysgfile',$zysgfile);
		$this->assign('project',$list);
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	// 内部施工图评审意见
	public function nbmessagelist()
	{
		$project_id =I('project_id','','intval');
		
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 施工图内部评审意见
		$count =M('upload_data')->where("project_id='".$project_id."' and type=6")->count();
		$page= $this->page($count,8);
		$zysgfile =M('upload_data')->where("project_id='".$project_id."' and type=6")->limit($page->firstRow , $page->listRows)->order('create_time desc')->select();
		foreach ($zysgfile as $ky => $val) {			
			$zysgfile[$ky]['user_name'] =M('users')->where("id='".$val['adminid']."'")->getField('user_name');						
		}
		// 平面图规划方案未读数
		$pmghtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=1")->count();
		$pmghcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspmgh' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pcount =$pmghtcount-$pmghcount;
		$this->assign('pcount',$pcount);

		// 效果图方案未读数
		$xgtcount =M('upload_effect')->where("atype=2 and project_id='".$info['id']."'")->count();
		$xgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemsxg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$xcount =$xgtcount-$xgcount;
		$this->assign('xcount',$xcount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemspic' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业条件图未读数
		$zytcount =M('uploadzy_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszy' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zcount =$zytcount-$zycount;
		$this->assign('zcount',$zcount);

		// 各专业施工图未读数
		$zysgtcount =M('uploadzysg_data')->where("atype=2 and project_id='".$info['id']."'")->count();
		$zysgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='itemszysg' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$sgcount =$zysgtcount-$zysgcount;
		$this->assign('sgcount',$sgcount);

		addreadlog(session('ADMIN_ID'),$info['id'],2,'itemsmessage',C('DB_PREFIX').'upload_data',6);
		$this->assign('info',$info);
		$this->assign('zysgfile',$zysgfile);
		$this->assign("page", $page->show('Admin'));
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	
	// 各专业人员基本信息
	public function allzylist()
	{

		$project_id =I('project_id','','intval');
		$info=M('project')->find($project_id);
		// 项目总汇相关的项目
		//$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0")->group('p.id')->select();
		$list =M('project')->where("isdel=0")->order('create_time desc')->select();
		// 项目相关的各专业人员
		$zylist =D()->field('u.user_name,u.mobile,u.user_duty,u.qq_no,u.wx_no,s.role_name')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."users u")->where("s.by_distribution=u.id and s.project_id='$project_id'and s.status=1")->group('s.by_distribution')->select();
		$this->assign('info',$info);
		$this->assign('zyall',$zylist);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	// ajax 上传文件
	public function uploadplan()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['adminid'] =session('ADMIN_ID');
			// 上传甲方负责人
			if(intval($pdata['type']) == '2')
			{
				$pdata['isfirst'] =0;
				$pdata['status'] =1;
				$pdata['type'] ='甲方负责人';
			}
			else
			{
				$pdata['isfirst'] =1;
			}

			$pdata['createtime'] =time();
			$pdata['role_name'] ='项目总汇';
			$lastid=M('uploadpic_data')->add($pdata);
			if($lastid)
			{
				$username =M('users')->where("id='".$pdata['adminid']."'")->getField('user_name');

				$html ="<tr><td style='text-align:center'>".date('Y-m-d H:i')."</td><td style='text-align:center'>".$pdata['filename']."</td><td style='text-align:center'>".$username."</td><td style='text-align:center;color:red;'>未处理</td><td style='text-align:center'><a href='".U('Items/download',array('id'=>$lastid))."' class='btn' style='background:#1abc9c'>下载</a></td></tr>";
								
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Items/numberone',array('project_id'=>$pdata['project_id']))));
			}else
			{
				$this->ajaxReturn(array('status'=>1));
			}
		}
	}
	// ajax 上传沟通记录文件
	public function uploadmessage()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['adminid'] =session('ADMIN_ID');
			$pdata['type']=1;
			$pdata['createtime'] =time();
			$lastid=M('uploadgg_data')->add($pdata);
			if($lastid)
			{
				$username =M('users')->where("id='".$pdata['adminid']."'")->getField('user_name');

				$html ="<tr><td style='text-align:center'>".date('Y-m-d H:i')."</td><td style='text-align:center'>".$pdata['filename']."</td><td style='text-align:center'>".$username."</td><td style='text-align:center;color:red;'>未处理</td><td style='text-align:center'><a href='".U('Items/downloadmessage',array('id'=>$lastid))."' class='btn' style='background:#1abc9c'>下载</a></td></tr>";
								
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Items/messagefile',array('project_id'=>$pdata['project_id']))));
			}else
			{
				$this->ajaxReturn(array('status'=>1));
			}
		}
	}
	// 上传各专业图
	public function uploadzyfile()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['adminid'] =session('ADMIN_ID');
			$pdata['createtime'] =time();
			$lastid=M('uploadzy_data')->add($pdata);
			if($lastid)
			{
				$username =M('users')->where("id='".$pdata['adminid']."'")->getField('user_name');

				$html ="<tr><td style='text-align:center'>项目总汇</td><td style='text-align:center'>".date('Y-m-d H:i')."</td><td style='text-align:center'>".$pdata['filename']."</td><td style='text-align:center;'>".$username."</td><td style='text-align:center'><a href='".U('Items/downloadzy',array('id'=>$lastid))."' class='btn' style='background:#1abc9c'>下载</a></td></tr>";
								
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Items/zyinfolist',array('project_id'=>$pdata['project_id']))));
			}else
			{
				$this->ajaxReturn(array('status'=>1));
			}
		}
	}
	// 本专业施工图
	public function uploadzysg()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['adminid'] =session('ADMIN_ID');
			$pdata['createtime'] =time();
			$lastid=M('uploadzysg_data')->add($pdata);
			if($lastid)
			{
				$username =M('users')->where("id='".$pdata['adminid']."'")->getField('user_name');

				$html ="<tr><td style='text-align:center'>".date('Y-m-d H:i')."</td><td style='text-align:center'>".$pdata['filename']."</td><td style='text-align:center;'>".$username."</td><td style='text-align:center'><a href='".U('Items/downloadzysg',array('id'=>$lastid))."' class='btn' style='background:#1abc9c'>下载</a></td></tr>";
								
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Items/bzysglist',array('project_id'=>$pdata['project_id']))));
			}else
			{
				$this->ajaxReturn(array('status'=>1));
			}
		}
	}

	// 获取反馈信息
	public function getmessage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('upload_message')->find($id);
			$content =htmlspecialchars_decode($info['content']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['filename'],'urldata'=>$info['urldata'],'url'=>U('Admin/Items/download2',array('id'=>$id))));
		}
	}
	// 获取平面图的反馈意见
	public function getpmmessage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('uploadpic_data')->find($id);
			$content =htmlspecialchars_decode($info['recontent']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['refilename'],'urldata'=>$info['reurldata'],'url'=>U('Admin/Items/download_pm',array('id'=>$id))));
		}
	}
	public function getggmesage()
	{
		if(IS_POST)
		{
			$id=I('id','','intval');
			$info =M('uploadgg_data')->find($id);
			$content =htmlspecialchars_decode($info['message']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfilename'],'urldata'=>$info['murldata'],'url'=>U('Admin/Items/downloadgg',array('id'=>$id))));
		}
	}
	// 获取我的反馈
	public function getmessageinfo()
	{
		if(IS_POST)
		{
			$id=I('mid','','intval');			
			$info =M('upload_message')->find($id);
			$content =htmlspecialchars_decode($info['content']);
			$content=empty($content) ? '暂无内容': $content;
			$this->ajaxReturn(array('status'=>0,'content'=>$content,'filename'=>$info['filename'],'url'=>U('Admin/Items/download2',array('id'=>$id))));
		}
	}
	// 下载文件
	public function download()
	{
		$id=I('id','','intval');
		$info =M('upload_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['file_url'];
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
	public function download_pm()
	{
		$id=I('id','','intval');
		$info =M('uploadpic_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['reurldata'];
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
	public function download5()
	{
		$id=I('id','','intval');
		$info =M('uploadpic_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
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
	// 下载第一版平面图反馈意见
	public function download2()
	{
		$id=I('id','','intval');
		$info =M('upload_message')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
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
	// 下载沟通记录反馈
	public function downloadgg()
	{
		$id=I('id','','intval');
		$info =M('uploadgg_message')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['murldata'];
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
	public function downloadzy()
	{
		$id=I('id','','intval');
		$info =M('uploadzy_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
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
	public function downloadzysg()
	{
		$id=I('id','','intval');
		$info =M('uploadzysg_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
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
	// 下载记录文件
	public function downloadmessage()
	{
		$id=I('id','','intval');
		$info =M('uploadgg_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
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
	// 下载方案记录文件
	public function downloadfangan()
	{
		$id=I('id','','intval');
		$info =M('upload_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['file_url'];
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
	// 添加编辑动态
	public function adddoing()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			
			if($pdata['ids'] != 0)
			{
				$res=M('project_doings')->where("id='".$pdata['ids']."'")->save($pdata);
			}else
			{
				$pdata['rolename'] ='项目总汇';
				$pdata['adminid'] =session('ADMIN_ID');			
				$pdata['createtime'] =time();
				$res=M('project_doings')->add($pdata);
			}			
			if($res)
			{
				$this->ajaxReturn(array('status'=>0,'url'=>U('Admin/Items/doinglist',array('project_id'=>$pdata['project_id']))));
			}
		}
	}
	
	// 查看详情
	public function getdoing()
	{
		if(IS_POST)
		{
			$pdata =I('post.');
			
			$res=M('project_doings')->find($pdata['ids']);
			if($res)
			{
				$this->ajaxReturn(array('status'=>0,'content'=>$res['content'],'persont'=>$res['persont']));
			}
		}
	}
	
}