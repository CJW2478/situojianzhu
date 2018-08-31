<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/4
 * Time: 15:48
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\CommonModel;

class EffectController extends AdminbaseController
{
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
    protected  $project_model,$uploadgg_data_model,$users_model,$solutions_model,$upload_data_model,$upload_projectinfo,$upload_effect,$company_model;
    public function _initialize() {
        parent::_initialize();
        $this->project_model = D("Common/Project");
        $this->uploadgg_data_model = D("Common/UploadggData");
        $this->users_model = D('Common/Users');
        $this->solutions_model = D("Common/Solutions");
        $this->upload_data_model = D("Common/UploadData");
        $this->upload_projectinfo = D("Common/UploadProjectinfo");
        $this->upload_effect = D("Common/UploadEffect");
        $this->company_model = D("Common/Company");
    }

    //甲方项目基本信息
    public function index()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);
		
         if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
			if(session('ADMIN_ID')==1 || !empty($finfo))
			{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];

        //甲方项目信息
        $condition['a.type'] = 1;
        $count = D('upload_projectinfo as a')->where($condition)->count();
        $page = $this->page($count, 8);
        $project = $this->upload_projectinfo->getProject($condition,$page);
		
		//沟通记录文件未读数
		$ggcount = M('uploadgg_data')->where("type=4 and project_id='".$principal['id']."' and status2 !=0")->count();
		$gcount =M('uploadreadlog')->where("modelname='uploadgg_dataeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=4")->getField('number');
		$sign['ggcount'] =$ggcount-$gcount;
		
		// 项目风格图片
		
		$fgcount = M('upload_projectinfo')->where("type=2 and project_id='".$principal['id']."' and status=1")->count();
		$fcount =M('uploadreadlog')->where("modelname='upload_projectinfotwo' and adminid='".session('ADMIN_ID')."' and ids=0 and type=2")->getField('number');
		$sign['fgcount'] =$fgcount-$fcount;
		
		// 效果方案图
		$con['_string'] = 'status != 0 or audit_status != 0';
		$con['project_id'] =$principal['id'];
        $xgcount = M('upload_effect')->where($con)->count();		
		$xcount = M('uploadreadlog')->where("modelname='upload_projectinfoeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=0")->getField('number');
		$sign['xgcount'] =$xgcount-$xcount;
		
		//编制方案
		$bzcount = M('upload_data')->where("type=5 and project_id='".$principal['id']."' and status=0")->count();
		$bcount = M('uploadreadlog')->where("modelname='upload_databzeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=5")->getField('number');
		$sign['bzcount'] =$bzcount-$bcount;
		$this->assign('sign', $sign);
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('project',$project);
        $this->assign('count',$count);
        $this->assign('data_page',$page->show('Admin'));
        $this->display();
    }

    //沟通记录文件
    public function communication()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

         if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];
        $condition['a.type'] = 4;
        $count = D('uploadgg_data a')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->uploadgg_data_model->getData($condition,$page);

        foreach ($files as &$v)
        {
            if ($v['atype'] == 1) {
                $info = $this->company_model->getInfo(['a.id'=>$v['adminid'],'b.id'=>$project_id]);
                $v['user_name'] = $info['principal_name'];
            }else{
                $info = $this->users_model->getUserInfo('a.id='.$v['adminid']);
                $v['user_name'] = $info['user_name'];
            }
        }
		// 项目风格图片		
		$fgcount = M('upload_projectinfo')->where("type=2 and project_id='".$principal['id']."' and status=1")->count();
		$fcount =M('uploadreadlog')->where("modelname='upload_projectinfotwo' and adminid='".session('ADMIN_ID')."' and ids=0 and type=2")->getField('number');
		$sign['fgcount'] =$fgcount-$fcount;
		
		// 效果方案图
		$con['_string'] = 'status != 0 or audit_status != 0';
		$con['project_id'] =$principal['id'];
        $xgcount = M('upload_effect')->where($con)->count();		
		$xcount = M('uploadreadlog')->where("modelname='upload_projectinfoeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=0")->getField('number');
		$sign['xgcount'] =$xgcount-$xcount;
		
		//编制方案
		$bzcount = M('upload_data')->where("type=5 and project_id='".$principal['id']."' and status=0")->count();
		$bcount = M('uploadreadlog')->where("modelname='upload_databzeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=5")->getField('number');
		$sign['bzcount'] =$bzcount-$bcount;
		$this->assign('sign', $sign);
		
		if($principal['id']>0){
			$ggcount = M('uploadgg_data')->where("type=4 and project_id='".$principal['id']."' and status2 !=0")->count();
			// 写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'uploadgg_dataeffect',
				'project_id' => $principal['id'],
				'type' => 4,
				'ids' => 0,
				'number' => $ggcount,
			];
			$this->add_uploadreadlog2($param);
		}
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->display();
    }

    //项目风格图片
    public function project_style()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

         if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];
        $condition['a.type'] = 2;
        $count = D('upload_projectinfo a')->where($condition)->count();
		
        $page = $this->page($count, 8);
        $files = $this->upload_projectinfo->getData($condition,$page);
		
		//沟通记录文件未读数
		$ggcount = M('uploadgg_data')->where("type=4 and project_id='".$principal['id']."' and status2!=0")->count();
		$gcount =M('uploadreadlog')->where("modelname='uploadgg_dataeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=4")->getField('number');
		$sign['ggcount'] =$ggcount-$gcount;
		
		// 效果方案图
		$con['_string'] = 'status != 0 or audit_status != 0';
		$con['project_id'] =$principal['id'];
        $xgcount = M('upload_effect')->where($con)->count();		
		$xcount = M('uploadreadlog')->where("modelname='upload_projectinfoeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=0")->getField('number');
		$sign['xgcount'] =$xgcount-$xcount;
		
		//编制方案
		$bzcount = M('upload_data')->where("type=5 and project_id='".$principal['id']."' and status=0")->count();
		$bcount = M('uploadreadlog')->where("modelname='upload_databzeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=5")->getField('number');
		$sign['bzcount'] =$bzcount-$bcount;
		$this->assign('sign', $sign);
		if($principal['id']>0)
		{
			$fgcount = M('upload_projectinfo')->where("type=2 and project_id='".$principal['id']."' and status=1")->count();
			// 写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'upload_projectinfotwo',
				'project_id' => $principal['id'],
				'type' => 2,
				'ids' => 0,
				'number' => $fgcount,
			];
			$this->add_uploadreadlog2($param);
		}
		
		
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->display();
    }

    //效果图方案
    public function effect_plan()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

        if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and  p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];

        //第一次上传效果图方案
        $condition['a.is_first'] = 1;
        $first_count = D('upload_effect a')->where($condition)->count();
        $first_page = $this->page($first_count, 8);
        $first_files = $this->upload_effect->getData($condition,$first_page);

        //效果图方案
        unset($condition['a.is_first']);
        $condition['_string'] = '(a.is_first = 1 and a.status = 0) or (a.is_first = 0)';
        $count = D('upload_effect a')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->upload_effect->getData($condition,$page);

        //判断方案确认状态
        if ($this->upload_effect->where(['project_id'=>$principal['id'],'status'=>0,'is_first'=>1])->find())
        {
            $status = 1;
        }else{
            $status = 0;
        }
		
		//沟通记录文件未读数
		$ggcount = M('uploadgg_data')->where("type=4 and project_id='".$principal['id']."' and status2!=0")->count();
		$gcount =M('uploadreadlog')->where("modelname='uploadgg_dataeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=4")->getField('number');
		$sign['ggcount'] =$ggcount-$gcount;
		
		// 项目风格图片		
		$fgcount = M('upload_projectinfo')->where("type=2 and project_id='".$principal['id']."' and status=1")->count();
		$fcount =M('uploadreadlog')->where("modelname='upload_projectinfotwo' and adminid='".session('ADMIN_ID')."' and ids=0 and type=2")->getField('number');
		$sign['fgcount'] =$fgcount-$fcount;
		
		//编制方案
		$bzcount = M('upload_data')->where("type=5 and project_id='".$principal['id']."' and status=0")->count();
		$bcount = M('uploadreadlog')->where("modelname='upload_databzeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=5")->getField('number');
		$sign['bzcount'] =$bzcount-$bcount;
		$this->assign('sign', $sign);
		if($principal['id']>0)
		{
			$con['_string'] = 'status != 0 or audit_status != 0';
			$con['project_id'] =$principal['id'];
			$xgcount = M('upload_effect')->where($con)->count();
			// 写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'upload_projectinfoeffect',
				'project_id' => $principal['id'],
				'type' => 0,
				'ids' => 0,
				'number' => $xgcount,
			];
			$this->add_uploadreadlog2($param);
		}
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('page',$page->show('Admin'));
        $this->assign('first_page',$first_page->show('Admin'));
        $this->assign('files',$files);
        $this->assign('first_files',$first_files);
        $this->assign('status',$status);
        $this->display();
    }

    //编制规划方案文本
    public function scheme()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

         if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];
        $condition['a.type'] = 5;
        $count = D('upload_data a')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->upload_data_model->getData($condition,$page);

        //判断方案确认状态
        if ($this->upload_data_model->where(['project_id'=>$principal['id'],'type'=>4,'status'=>0])->find())
        {
            $status = 1;
        }else{
            $status = 0;
        }
		//沟通记录文件未读数
		$ggcount = M('uploadgg_data')->where("type=4 and project_id='".$principal['id']."' and status2 !=0")->count();
		$gcount =M('uploadreadlog')->where("modelname='uploadgg_dataeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=4")->getField('number');
		$sign['ggcount'] =$ggcount-$gcount;
		
		// 项目风格图片		
		$fgcount = M('upload_projectinfo')->where("type=2 and project_id='".$principal['id']."' and status=1")->count();
		$fcount =M('uploadreadlog')->where("modelname='upload_projectinfotwo' and adminid='".session('ADMIN_ID')."' and ids=0 and type=2")->getField('number');
		$sign['fgcount'] =$fgcount-$fcount;
		
		// 效果方案图
		$con['_string'] = 'status != 0 or audit_status != 0';
		$con['project_id'] =$principal['id'];
        $xgcount = M('upload_effect')->where($con)->count();		
		$xcount = M('uploadreadlog')->where("modelname='upload_projectinfoeffect' and adminid='".session('ADMIN_ID')."' and ids=0 and type=0")->getField('number');
		$sign['xgcount'] =$xgcount-$xcount;
		$this->assign('sign', $sign);
		if($principal['id']>0)
		{
			$bzcount = M('upload_data')->where("type=5 and project_id='".$principal['id']."' and status=0")->count();
			// 写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'upload_databzeffect',
				'project_id' => $principal['id'],
				'type' => 5,
				'ids' => 0,
				'number' => $bzcount,
			];
			$this->add_uploadreadlog2($param);
		}
		
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->assign('status',$status);
        $this->display();
    }
	public function downloadbase()
	{
		$id=I('id','','intval');
		$info =M('upload_projectinfo')->where("id='$id'")->find();
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
	public function downloadeffect()
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
	public function downloadeffect2()
	{
		$id=I('id','','intval');
		$info =M('upload_effect')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['mfile_url'];
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
	public function downloadbase2()
	{
		$id=I('id','','intval');
		$info =M('upload_projectinfo')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['mfile_url'];
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
	public function comdownloadfk()
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
	public function downloadgg()
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
    //保存上传文件
    public function saveFile()
    {
        if (IS_POST) {
           
            $post = I('post.');
            $post['adminid'] = session('ADMIN_ID');
            $post['create_time'] = time();
			$post['status'] = 1;
            $result = $this->upload_effect->save_file_info($post);
            if ($result) {
				$p=M('project')->find($post['project_id']);
                if ($post['is_first'] == 1) {
					if($p['projectvalue']<13 && $p['projecttype']==2)
					{
						$this->change_stage($post['project_id'],13);
					}                    
                }else{
                   
					if($p['projectvalue']<16 && $p['projecttype']==2)
					{
						$this->change_stage($post['project_id'],16);
					}  
					// 推送
					$p =M('project')->where("id='".$post['project_id']."'")->find();
					$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
					$info['project_name'] =$p['project_name'];
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='乙方已上传方案文件，请尽快确认！';
					$this->sendTemplate($info);
                }
				 log_insertresult(session('ADMIN_ID'),'上传效果图','添加项目');
                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
            }
        }
    }

	//保存上传文件
    public function saveFile2()
    {
        if (IS_POST) {
            
            $post = I('post.');
            $post['adminid'] = session('ADMIN_ID');
            $post['create_time'] = time();
            $result = $this->upload_data_model->save_file_info($post);
            if ($result) {
                //更变当前项目进行的阶段
                $project = $this->project_model->where('id='.$post['project_id'])->find();
                
                //更变当前项目的具体进度
                if ($post['type'] == 4) {
                    $this->change_stage($post['project_id'],4);
                }
                if ($post['type'] == 1) {
                    $this->change_stage($post['project_id'],7);
                }

                if ($post['type'] == 5) {
					if($project['projectvalue']<19 && $project['projecttype']==2)
					{
						$this->change_stage($post['project_id'],19);
					}                    
                }
				log_insertresult(session('ADMIN_ID'),'上传施工图所需资料','添加项目');
                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
            }
        }
    }
    //保存项目文件
    public function save_project_info()
    {
        if (IS_POST) {
           
            $post = I('post.');
            $post['adminid'] = session('ADMIN_ID');
            $post['create_time'] = time();
            $post['atype'] = 2;
            $result = $this->upload_projectinfo->save_file_info($post);
            if ($result) {
                $project = $this->project_model->where('id='.$post['project_id'])->find();
				log_insertresult(session('ADMIN_ID'),'上传项目风格图片','添加项目');
                
				// 推送
				$p =M('project')->where("id='".$_POST['project_id']."'")->find();
				if($p['projectvalue']< 11 && $p['projecttype']==2)
				{
					$this->change_stage($post['project_id'],11);
				}
				$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
				$info['project_name'] =$p['project_name'];
				$info['message'] ='您的项目有了新动态';
				$info['desc'] ='乙方已上传方案文件，请尽快确认！';
				$this->sendTemplate($info);
                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
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