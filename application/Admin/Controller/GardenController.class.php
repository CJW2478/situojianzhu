<?php
// +----------------------------------------------------------------------
// | ThinkCMF 园林景观及总图管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 业余爱好者 <649180397@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GardenController extends AdminbaseController {
	// 平面图
	public function index(){				
		
		$project_id =I('project_id','','intval');
		if($project_id)
		{
			$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='园林景观及总图' and p.isdel=0")->group('p.id desc')->find();
			
			// 上传资料信息
			$files =M('upload_data')->where("project_id='".$project_id."'")->select();
			// 第一版平面图信息
			$firstpic =M('uploadpic_data')->where("project_id='".$project_id."' and isfirst=1")->order('createtime desc')->select();
			foreach ($firstpic as $key => $value) {
				$firstpic[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
				$countinfo = M('upload_message')->where("adminid='".session('ADMIN_ID')."' and upid='".$value['id']."' and role_name='园林景观及总图'")->find();
				if($countinfo)
				{
					$firstpic[$key]['isdo'] =1;
					$firstpic[$key]['mid'] =$countinfo['id'];
				}else{
					$firstpic[$key]['isdo']=0;
				}
			}
			// 最终平面图
			$lastpic =M('uploadpic_data')->where("project_id='".$project_id."' and status=1 and status2=1")->order('createtime desc')->select();	
		}
		else
		{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			// 上传资料信息
			$files =M('upload_data')->where("project_id='".$info['id']."'")->select();
			// 第一版平面图信息
			$firstpic =M('uploadpic_data')->where("project_id='".$info['id']."' and isfirst=1")->order('createtime desc')->select();			
				foreach ($firstpic as $key => $value) {
					$firstpic[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');
					$countinfo = M('upload_message')->where("adminid='".session('ADMIN_ID')."' and upid='".$value['id']."' and role_name='园林景观及总图'")->find();
					if($countinfo)
					{
						$firstpic[$key]['isdo'] =1;
						$firstpic[$key]['mid'] =$countinfo['id'];
					}else{
						$firstpic[$key]['isdo']=0;
					}
				}
			// 最终平面图
			$lastpic =M('uploadpic_data')->where("project_id='".$info['id']."' and status=1 and status2=1")->order('createtime desc')->select();	
		}
		if(count($lastpic)>0)
		{
			foreach($lastpic as $k=>$v)
			{
				$lastpic[$k]['user_name'] = M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			}
		}
		// 园林景观及总图相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		
		$minfo = M('upload_message')->where("adminid='".session('ADMIN_ID')."' and upid='".$firstpic['id']."' and role_name='园林景观及总图'")->find();
		if(IS_POST)
		{
			$pdata =I('post.');
			$pdata['role_name'] ='园林景观及总图';
			$pdata['adminid'] =session('ADMIN_ID');			
			$pdata['createtime'] =time();
			if($_FILES['filename']['name'] !='')
			{
				$pdata['filename'] =$_FILES['filename']['name'];
				$pdata['urldata'] =uploadOne($_FILES['filename'],'message');
			}
			$res=M('upload_message')->add($pdata);
			
			// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
			log_insertresult(session('ADMIN_ID'),'反馈第一版平面图','添加项目');
			redirect(U('Garden/index',array('project_id'=>$pdata['project_id'])));						
		}
		if($info['id']>0){
			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='结构专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);

			$this->assign('firstpic',$firstpic);
			$this->assign('lastpic',$lastpic);
			$this->assign('minfo',$minfo);
			$this->assign('info',$info);
			$this->assign('project',$list);
			$this->assign("formget",array_merge($_GET,$_POST)); 
		}		
		$this->display();
	}
	
	// 专业信息
	public function zyinfolist()
	{
		$project_id =I('project_id','','intval');
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 园林景观及总图相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		// 各专业条件图
		if($info['id']>0){
			$count = M('uploadzy_data')->where("project_id='".$project_id."'")->count();
			$page =$this->page($count,8);
			$files =M('uploadzy_data')->where("project_id='".$project_id."'")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
			foreach ($files as $key => $value) {			
				$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');						
			}
			$count1 = M('uploadpic_data')->where("project_id='".$project_id."' and status2=1")->count();

			 //写入标记表
			addreadlog(session('ADMIN_ID'),$info['id'],2,'uploadzy_data',C('DB_PREFIX').'uploadzy_data');

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			$this->assign('info',$info);
			$this->assign('count1',$count1);
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
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		
		// 园林景观及总图相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		// 本专业施工图
		if($info['id']>0){
			$count = M('uploadzysg_data')->where("project_id='".$project_id."' and rolename='园林景观及总图'")->count();
			$page =$this->page($count,8);
			$zysgfile =M('uploadzysg_data')->where("project_id='".$project_id."' and rolename='园林景观及总图'")->limit($page->firstRow , $page->listRows)->order('createtime desc')->select();
			foreach ($zysgfile as $ky => $val) {			
				$zysgfile[$ky]['user_name'] =M('users')->where("id='".$val['adminid']."'")->getField('user_name');						
			}
			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='结构专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);
			
			$count1 = M('uploadpic_data')->where("project_id='".$project_id."' and  status2=1")->count();
			$this->assign('count1',$count1);
			
			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
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
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 园林景观及总图相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		// 施工图内部评审意见
		if($info['id']>0){
			$count =M('upload_data')->where("project_id='".$project_id."' and type=6")->count();
			$page= $this->page($count,8);
			$zysgfile =M('upload_data')->where("project_id='".$project_id."' and type=6")->limit($page->firstRow , $page->listRows)->order('create_time desc')->select();
			foreach ($zysgfile as $ky => $val) {			
				$zysgfile[$ky]['user_name'] =M('users')->where("id='".$val['adminid']."'")->getField('user_name');						
			}

			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='结构专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);

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
		
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 园林景观及总图相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		// 进度动态
		if($info['id']>0){
			$count =M('project_doings')->where("project_id='".$project_id."' and rolename='园林景观及总图'")->count();
			$page =$this->page($count,8);
			$files =M('project_doings')->where("project_id='".$project_id."' and rolename='园林景观及总图'")->limit($page->firstRow,$page->listRows)->order('createtime desc')->select();
			foreach ($files as $key => $value) {			
				$files[$key]['user_name'] =M('users')->where("id='".$value['adminid']."'")->getField('user_name');						
			}
			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='结构专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			$this->assign('info',$info);
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
		$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.id='$project_id' and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->group('p.id desc')->find();
		// 园林景观及总图相关的项目
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$list=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='园林景观及总图' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		// 项目相关的各专业人员
		if($info['id']>0){
			$zylist =D()->field('u.user_name,u.mobile,u.user_duty,u.qq_no,u.wx_no,s.role_name')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."users u")->where("s.by_distribution=u.id and s.project_id='$project_id'and s.status=1")->group('s.by_distribution')->select();
			// 各专业数据未读数
			$zytcount =M('uploadzy_data')->where("role_name !='结构专业' and project_id='".$info['id']."'")->count();
			$zycount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='uploadzy_data' and adminid='".SESSION('ADMIN_ID')."' and type=2")->getField('number');
			$zyncount =$zytcount-$zycount;
			$this->assign('zyncount',$zyncount);

			// 内部施工图标识未读数
			$udcount =M('upload_data')->where("atype=2 and project_id='".$info['id']."' and type=6")->count();
			$ucount=M('uploadreadlog')->where("project_id='".$info['id']."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids=6")->getField('number');
			$nbncount =$udcount-$ucount;
			$this->assign('nbncount',$nbncount);
			$this->assign('info',$info);
			$this->assign('zyall',$zylist);
			$this->assign('project',$list);
			$this->assign("formget",array_merge($_GET,$_POST));  
		}
		$this->display();
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
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'上传各专业图文件','添加项目');	
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Garden/zyinfolist',array('project_id'=>$pdata['project_id']))));
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
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'上传本专业施工图文件','添加项目');
				$this->ajaxReturn(array('status'=>0,'html'=>$html,'type'=>$pdata['type'],'url'=>U('Admin/Garden/bzysglist',array('project_id'=>$pdata['project_id']))));
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
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['filename'],'urldata'=>$info['urldata'],'url'=>U('Admin/Garden/download2',array('id'=>$id))));
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
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['refilename'],'urldata'=>$info['reurldata'],'url'=>U('Admin/Garden/download_pm',array('id'=>$id))));
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
			$this->ajaxReturn(array('status'=>0,'html'=>$content,'filename'=>$info['mfilename'],'urldata'=>$info['murldata'],'url'=>U('Admin/Garden/downloadgg',array('id'=>$id))));
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
			$content = empty($content) ? '未填写备注': $content;
			$this->ajaxReturn(array('status'=>0,'content'=>$content,'filename'=>$info['filename'],'url'=>U('Admin/Garden/download2',array('id'=>$id))));
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
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'编辑项目专业进度动态','编辑项目');
			}else
			{
				$pdata['rolename'] ='园林景观及总图';
				$pdata['adminid'] =session('ADMIN_ID');			
				$pdata['createtime'] =time();
				$res=M('project_doings')->add($pdata);
				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'添加项目专业进度动态','添加项目');
			}			
			if($res)
			{
				
				$this->ajaxReturn(array('status'=>0,'url'=>U('Admin/Garden/doinglist',array('project_id'=>$pdata['project_id']))));
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