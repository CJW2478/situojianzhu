<?php
// +----------------------------------------------------------------------
// | ThinkCMF 建筑专业管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ConstructionController extends AdminbaseController {
	// 施工图所需资料
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
	public function index(){				
		
		$project_id =I('project_id','','intval');
		if($project_id)
		{
			$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0")->group('p.id desc')->find();
			
		}
		else
		{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
			if(session('ADMIN_ID')==1 || !empty($finfo))
			{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 ")->group('p.id')->order('s.create_time desc')->find();
			
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			
			}
		}
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		if($info['id'] > 0)
		{
			// 项目相关的各专业人员
			$zylist =D()->field('u.user_name,u.mobile,u.user_duty,u.qq_no,u.wx_no,s.role_name')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."users u")->where("s.by_distribution=u.id and s.project_id='".$info['id']."' and s.status=1")->group('s.by_distribution')->select();
				
			// 资料
			$files =M('upload_data')->where("project_id='".$info['id']."' and type=3")->order('create_time desc')->select();
			foreach($files as $k=>$v)
			{
				$files[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			}
			// 资料
			$files2 =M('upload_data')->where("project_id='".$info['id']."' and type=1 and status=0 and audit_status=0")->order('create_time desc')->select();
		
			foreach($files2 as $k=>$v)
			{
				$files2[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			}
			
			// 效果图
			$xglist =M('upload_effect')->where("project_id='".$info['id']."' and status=0 and audit_status=0")->order('create_time desc')->select();
			foreach($xglist as $k=>$v)
			{
				$xglist[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			}		

			// 平面图未读数
			$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$pmcount =$pictcount-$piccount;
			$this->assign('pmcount',$pmcount);

			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);
			// 沟通记录标识未读数
			$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$ggcount =$totalcount-$count;
			$this->assign('ggcount',$ggcount);

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			
			// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			
			$this->assign('bsgcount',$bsgcount);
			// 施工图写入标记表
			addreadlog(session('ADMIN_ID'),$info['id'],2,'upload_data',C('DB_PREFIX').'upload_data',3);
		}
		$this->assign('files2',$files2);
		$this->assign('files',$files);
		$this->assign('info',$info);
		$this->assign('xglist',$xglist);
		$this->assign('project',$list);
		$this->assign('zyall',$zylist);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
	}
	// 第一版平面图
	public function numberone()
	{
		$project_id =I('project_id','','intval');
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		// 第一版平面图信息
		$files =M('uploadpic_data')->where("project_id='".$project_id."' and isfirst=1")->order('createtime desc')->select();
		foreach ($files as $key => $value) {
			$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
		}

		// 第一版平面图意见反馈
		$picid =M('uploadpic_data')->where("project_id='$project_id' and isfirst=1 and status=1")->getField('id');
		
		//$picids =implode(',',$picid);
		//$fanklist=M('upload_message')->where("upid in(".$picids.")")->order('createtime desc')->select();
		
		//foreach ($fanklist as $key => $value) {
		//	$fanklist[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
		//}
		$flist =M('solutions')->where("project_id='$project_id' and status=1 and role_name !='方案师'")->select();
		$fanklist =array();
		foreach($flist as $k=>$vv)
		{
			$upm =M('upload_message')->where("role_name='".$vv['role_name']."' and upid='$picid'")->find();
			if($upm)
			{
				$fanklist[$k]['id'] =$upm['id'];				
			}else{
				$fanklist[$k]['id'] =0;
			}
			$fanklist[$k]['role_name'] =$vv['role_name'];
			$fanklist[$k]['user_name'] =M('users')->where("id='".$vv['by_distribution']."'")->getField('user_name');
		}
		
		
		$count1 =M('uploadpic_data')->where("project_id='".$project_id."' and status=1")->count();
		if($count1>0)
		{
			// 平面图信息
			$count =M('uploadpic_data')->where("project_id='".$project_id."' and status=1")->count();
			$page =$this->page($count,8);
			$piclist =M('uploadpic_data')->where("project_id='".$project_id."' and status=1")->order('createtime desc')->limit($page->firstRow , $page->listRows)->select();
			foreach ($piclist as $key => $value) {
				$piclist[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
			}
			$this->assign("page", $page->show('Admin'));
			$this->assign('piclist',$piclist);	
			
		}
		// 施工图未读数
		$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
		$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
		$scount =$sgtcount-$sgcount;
		$this->assign('scount',$scount);

		// 各专业数据未读数
		$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zyncount =$zytcount-$zycount;
		$this->assign('zyncount',$zyncount);
		// 沟通记录标识未读数
		$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$ggcount =$totalcount-$count;
		$this->assign('ggcount',$ggcount);

		// 内部施工图标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);
		
		// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			$this->assign('bsgcount',$bsgcount);
		 // 沟通记录写入标记表
		 if($info['id'] > 0)
		 { 
			addreadlog(session('ADMIN_ID'),$info['id'],1,'uploadpic_data',C('DB_PREFIX').'uploadpic_data');
		 }
		$this->assign('count1',$count1);
		$this->assign('fanklist',$fanklist);
		$this->assign('info',$info);
		$this->assign('files',$files);
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		$this->display();
		
	}	
	// 沟通记录文件
	public function  messagefile()
	{

		$project_id =I('project_id','','intval');		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		if($info['id']>0)
		{
			$count =M('uploadgg_data')->where("project_id='".$project_id."' and type=1")->count();
			$page =$this->page($count,8);
			$files =M('uploadgg_data')->where("project_id='".$project_id."' and type=1")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
			foreach ($files as $key => $value) {
				if($value['atype'] == 2)
				{
					$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
				}else
				{
					$files[$key]['user_name'] =$info['principal_name'];
				}			
			}
			// 施工图未读数
			$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
			$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
			$scount =$sgtcount-$sgcount;
			$this->assign('scount',$scount);

			// 平面图未读数
			$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$pmcount =$pictcount-$piccount;
			$this->assign('pmcount',$pmcount);

			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);
			

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			$this->assign('bsgcount',$bsgcount);
			 // 沟通记录写入标记表			
			addreadlog(session('ADMIN_ID'),$info['id'],1,'uploadgg_data',C('DB_PREFIX').'uploadgg_data');
		
			$this->assign('info',$info);
			$this->assign('files',$files);
			$this->assign("page", $page->show('Admin'));	
			$this->assign('project',$list);
			$this->assign("formget",array_merge($_GET,$_POST)); 
		}		
		$this->display();
	}
	// 各专业信息
	public function zyinfolist()
	{
		$project_id =I('project_id','','intval');
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		if($info['id']>0){
		// 该项目的平面图
			$piccount=M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();			
			$this->assign('piccount',$piccount);
		// 各专业条件图
		$count = M('uploadzy_data')->where("project_id='".$project_id."'")->count();
		$page =$this->page($count,8);
		$files =M('uploadzy_data')->where("project_id='".$project_id."'")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
		foreach ($files as $key => $value) {			
			$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');						
		}
		// 施工图未读数
		$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
		$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
		$scount =$sgtcount-$sgcount;
		$this->assign('scount',$scount);
		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$piccount1=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$pmcount =$pictcount-$piccount1;
		$this->assign('pmcount',$pmcount);

		
		// 沟通记录标识未读数
		$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$ggcount =$totalcount-$count;
		$this->assign('ggcount',$ggcount);

		// 内部施工图标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);
		
		// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			$this->assign('bsgcount',$bsgcount);
		 //写入标记表
		 
		addreadlog(session('ADMIN_ID'),$info['id'],2,'uploadzy_data',C('DB_PREFIX').'uploadzy_data');
		 
		$this->assign('info',$info);
		$this->assign('files',$files);
		$this->assign('project',$list);
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));  
		}
		$this->display();
	}
	// 本专业施工图
	public function bzysglist()
	{
		$project_id =I('project_id','','intval');
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		if($info['id']>0){
			
		// 该项目的平面图
		$piccount=M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
		
		$this->assign('piccount',$piccount);
		
		// 本专业施工图
		$count = M('uploadzysg_data')->where("project_id='".$project_id."' and rolename='建筑专业'")->count();
		$page =$this->page($count,8);
		$zysgfile =M('uploadzysg_data')->where("project_id='".$project_id."' and rolename='建筑专业'")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
		foreach ($zysgfile as $ky => $val) {			
			$zysgfile[$ky]['user_name'] =M('users')->where("id='".$val['adminid']."'")->getField('user_name');						
		}
		
			// 施工图未读数
		$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
		$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
		$scount =$sgtcount-$sgcount;
		$this->assign('scount',$scount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业数据未读数
		$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zyncount =$zytcount-$zycount;
		$this->assign('zyncount',$zyncount);
		
		// 沟通记录标识未读数
		$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$ggcount =$totalcount-$count;
		$this->assign('ggcount',$ggcount);
		
		// 内部施工图标识未读数
		$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
		$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
		$nbncount =$udcount-$ucount;
		$this->assign('nbncount',$nbncount);
		if($info['id']>0)
		{
			addreadlogm(session('ADMIN_ID'),$info['id'],0,'uploadpic_datasg',C('DB_PREFIX').'uploadpic_data',0);
		}
		$this->assign('info',$info);		
		$this->assign('zysgfile',$zysgfile);
		$this->assign('project',$list);
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));  
		}
		$this->display();
	}
	// 内部施工图评审意见
	public function nbmessagelist()
	{
		$project_id =I('project_id','','intval');
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		// 施工图内部评审意见
		if($info['id']>0){
		$count =M('upload_data')->where("project_id='".$project_id."' and type=6")->count();
		$page= $this->page($count,8);
		$zysgfile =M('upload_data')->where("project_id='".$project_id."' and type=6")->limit($page->firstRow , $page->listRows)->order('create_time desc')->select();
		foreach ($zysgfile as $ky => $val) {			
			$zysgfile[$ky]['user_name'] =M('users')->where("id='".$val['adminid']."'")->getField('user_name');						
		}
		
		// 施工图未读数
		$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
		$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
		$scount =$sgtcount-$sgcount;
		$this->assign('scount',$scount);

		// 平面图未读数
		$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$pmcount =$pictcount-$piccount;
		$this->assign('pmcount',$pmcount);

		// 各专业数据未读数
		$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
		$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
		$zyncount =$zytcount-$zycount;
		$this->assign('zyncount',$zyncount);

		// 沟通记录标识未读数
		$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
		$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
		$ggcount =$totalcount-$count;
		$this->assign('ggcount',$ggcount);
		
		// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			$this->assign('bsgcount',$bsgcount);
		//写入标记表
		
		addreadlog(session('ADMIN_ID'),$info['id'],2,'upload_data',C('DB_PREFIX').'upload_data',6);
		
		$this->assign('info',$info);
		$this->assign('zysgfile',$zysgfile);
		$this->assign("page", $page->show('Admin'));
		$this->assign('project',$list);
		$this->assign("formget",array_merge($_GET,$_POST));  
		}
		$this->display();
	}
	// 进度动态
	public function doinglist()
	{
		$project_id =I('project_id','','intval');
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		if($info['id']>0)
		{
			// 进度动态
			$count =M('project_doings')->where("project_id='".$project_id."' and rolename='建筑专业'")->count();
			$page =$this->page($count,8);
			$files =M('project_doings')->where("project_id='".$project_id."' and rolename='建筑专业'")->limit($page->firstRow,$page->listRows)->order('createtime desc')->select();
			foreach ($files as $key => $value) {			
				$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');						
			}
			// 施工图未读数
			$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
			$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
			$scount =$sgtcount-$sgcount;
			$this->assign('scount',$scount);

			// 平面图未读数
			$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$pmcount =$pictcount-$piccount;
			$this->assign('pmcount',$pmcount);

			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);
			
			// 沟通记录标识未读数
			$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$ggcount =$totalcount-$count;
			$this->assign('ggcount',$ggcount);
			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			
			// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			$this->assign('bsgcount',$bsgcount);
		
			$this->assign('info',$info);
			$this->assign('projectid',$project_id);
			$this->assign('doinglist',$files);
			$this->assign("page", $page->show('Admin'));
			$this->assign('project',$list);
			$this->assign("formget",array_merge($_GET,$_POST)); 
		}		
		$this->display();
	}
	// 各专业人员基本信息
	public function allzylist()
	{

		$project_id =I('project_id','','intval');
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 建筑专业相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='建筑专业' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();

		}
		if($info['id']>0)
		{
			// 项目相关的各专业人员
			$zylist =D()->field('u.user_name,u.mobile,u.user_duty,u.qq_no,u.wx_no,s.role_name')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."users u")->where("s.by_distribution=u.id and s.project_id='$project_id' and s.status=1")->group('s.by_distribution')->select();

			// 施工图未读数
			$sgtcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=3")->count();
			$sgcount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=3")->getField('number');
			$scount =$sgtcount-$sgcount;
			$this->assign('scount',$scount);

			// 平面图未读数
			$pictcount =M('uploadpic_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$piccount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$pmcount =$pictcount-$piccount;
			$this->assign('pmcount',$pmcount);

			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='建筑专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);
			
			// 沟通记录标识未读数
			$totalcount =M('uploadgg_data')->where("atype=1 and project_id='".$info['id']."'")->count();
			$count=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadgg_data' and adminid='".SESSION('ADMIN_ID')."' and type=1")->getField('number');
			$ggcount =$totalcount-$count;
			$this->assign('ggcount',$ggcount);

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			
			// 本专业施工图未读数
			$bzsgcount =M('uploadpic_data')->where("project_id='".$info['id']."' and status2=1")->count();
			$bscount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadpic_datasg' and adminid='".SESSION('ADMIN_ID')."' and type=0 and ids=0")->getField('number');
			$bsgcount =$bzsgcount-$bscount;
			$this->assign('bsgcount',$bsgcount);
		
			$this->assign('info',$info);
			$this->assign('zyall',$zylist);
			$this->assign('project',$list);
			$this->assign("formget",array_merge($_GET,$_POST)); 
		}		
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
				$pdata['atype'] =1;
			}
			else
			{
				$pdata['isfirst'] =1;
			}

			$pdata['createtime'] =time();
			$pdata['role_name'] ='建筑专业';
			if(!isset($pdata['filename'])||empty($pdata['filename'])){
				$pdata['filename']='网络文件';
				$pdata['urldata']=str_replace('./data/upload/','',$pdata['urldata']);
			}
			$lastid=M('uploadpic_data')->add($pdata);
			if($lastid)
			{
				if($pdata['type'] =='2')
				{
					// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
					log_insertresult(session('ADMIN_ID'),'上传平面图至甲方负责人','添加项目');					
					// 推送
					$p =M('project')->where("id='".$pdata['project_id']."'")->find();
					$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
					$info['project_name'] =$p['project_name'];
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='乙方已上传方案文件，请尽快确认！';
					$this->sendTemplate($info);
				}else
				{
					// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
					log_insertresult(session('ADMIN_ID'),'上传平面图至乙方负责人','添加项目');		
				}
			
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Construction/numberone',array('project_id'=>$pdata['project_id']))));
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
			$pdata['type']=1;  // 施工图阶段
			$pdata['atype']=2;  // 乙方上传
			$pdata['createtime'] =time();
			if(!isset($pdata['filename'])||empty($pdata['filename'])){
				$pdata['filename']='网络文件';
				$pdata['urldata']=str_replace('./data/upload/','',$pdata['urldata']);
			}
			$lastid=M('uploadgg_data')->add($pdata);
			if($lastid)
			{
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'上传沟通记录文件','添加项目');
				// 推送
				$p =M('project')->where("id='".$pdata['project_id']."'")->find();
				$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
				$info['project_name'] =$p['project_name'];
				$info['message'] ='您的项目有了新动态';
				$info['desc'] ='乙方已上传方案文件，请尽快确认！';
				$this->sendTemplate($info);
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Construction/messagefile',array('project_id'=>$pdata['project_id']))));
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
			if(!isset($pdata['filename'])||empty($pdata['filename'])){
				$pdata['filename']='网络文件';
				$pdata['urldata']=str_replace('./data/upload/','',$pdata['urldata']);
			}
			$lastid=M('uploadzy_data')->add($pdata);
			if($lastid)
			{
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'上传各专业图文件','添加项目');			
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Construction/zyinfolist',array('project_id'=>$pdata['project_id']))));
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
			if(!isset($pdata['filename'])||empty($pdata['filename'])){
				$pdata['filename']='网络文件';
				$pdata['urldata']=str_replace('./data/upload/','',$pdata['urldata']);
			}
			$lastid=M('uploadzysg_data')->add($pdata);
			if($lastid)
			{
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'上传本专业施工图文件','添加项目');
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Construction/bzysglist',array('project_id'=>$pdata['project_id']))));
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
			$content = empty($content) ? '暂无内容':$content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['filename'],'urldata'=>$info['urldata'],'url'=>U('Admin/Construction/download2',array('id'=>$id))));
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
			$content = empty($content) ? '暂无内容':$content;
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['refilename'],'urldata'=>$info['reurldata'],'url'=>U('Admin/Construction/download_pm',array('id'=>$id))));
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
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfilename'],'urldata'=>$info['murldata'],'url'=>U('Admin/Construction/downloadgg',array('id'=>$id))));
		}
	}
	// 下载文件
	public function download()
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
		$info =M('uploadgg_data')->where("id='$id'")->find();
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
	// 下载效果图文件
	public function downloadxgfile()
	{
		$id=I('id','','intval');
		$info =M('upload_effect')->where("id='$id'")->find();
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
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'编辑项目专业进度动态','编辑项目');
			}else
			{
				$pdata['rolename'] ='建筑专业';
				$pdata['adminid'] =session('ADMIN_ID');			
				$pdata['createtime'] =time();
				$res=M('project_doings')->add($pdata);
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'添加项目专业进度动态','添加项目');
			}			
			if($res)
			{				
				$this->ajaxReturn(array('status'=>0,'url'=>U('Admin/Construction/doinglist',array('project_id'=>$pdata['project_id']))));
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
	
	// 通知推送
	function sendTemplate($info){
		
        $weChatAuth = new \Com\WechatAuth(self::APPID,self::APPSECRET);        
		$token=$weChatAuth->getAccessToken('client')['access_token'];		
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
        $data=array(
            "touser"=>$info['openid'],
            "topcolor"=>"#151516", 
            "template_id"=>"7cLx7eLAcmV_YxHK33ch0kpsvbgj5DxDdrJpUBM2-VM",
            "url"=>"",
            'data'=>array(
                    'first'=>array('value'=>urlencode($info['message']),'color'=>"#151516"),
                    'keyword1'=>array('value'=>urlencode($info['project_name']),'color'=>"#151516"),
                    'keyword2'=>array('value'=>urlencode($info['user_name']),'color'=>"#151516"),
                    'keyword3'=>array('value'=>urlencode($info['desc']),'color'=>"#151516"),
                    'remark'=>array('value'=>urlencode("请知晓"),'color'=>'#151516')
                ));
        $data=urldecode(json_encode($data)); 
        $res= $this->_request($url,true,'POST',$data);		
    }
    function _request($curl,$https=true,$method='GET',$data=null){
        $header = array('Expect:');
        $ch=  curl_init();
        curl_setopt($ch,CURLOPT_URL,$curl);
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        if($https){
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,TRUE);
        }
        if($method=='POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        $content=  curl_exec($ch);
        //返回结果
          if($content){
              curl_close($ch);
              return $content;
          }
          else {
             $errno = curl_errno( $ch );
             $info  = curl_getinfo( $ch );
             $info['errno'] = $errno;
                curl_close( $ch );
             $log = json_encode( $info );
             
              return false;
          }
    }
}