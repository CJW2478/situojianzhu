<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/28
 * Time: 8:42
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\CommonModel;

class DistributionController extends AdminbaseController
{
    protected  $project_model,$solutions_model,$role_model,$users_model,$upload_data_model,$upload_projectinfo_model;
    public function _initialize() {
        parent::_initialize();
        $this->project_model = D("Common/Project");
        $this->solutions_model = D("Common/Solutions");
        $this->role_model = D("Common/Role");
        $this->users_model = D('Common/Users');
        $this->upload_data_model = D('Common/UploadData');
        $this->upload_projectinfo_model = D('Common/UploadProjectinfo');
    }

    //分配员列表
    public function index()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $principal['id'];

        //甲方项目信息
        $condition['a.type'] = 1;
        $count = D('upload_projectinfo as a')->where($condition)->count();
        $page = $this->page($count, 8);
        $project = $this->upload_projectinfo_model->getProject($condition,$page);

        $sign = $this->get_sign($principal['id']);
		
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('project',$project);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('sign',$sign);
        $this->display();
    }

    //方案师列表
    public function solutions()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //方案师角色信息
        $solution = $this->role_model->where(['id'=>CommonModel::USER_SOLUTIONS])->find();

        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();
		
       
		
		$count = M('solutions')->where("project_id='".$principal['id']."' and role_name='方案师'")->count();
        $page = $this->page($count, 8);
		$data = $this->solutions_model->where("project_id='".$principal['id']."' and role_name='方案师'")
				->limit($page->firstRow, $page->listRows)
				->order('create_time desc')
                ->select();
		
		foreach($data as $k=>$v)
		{
			$data[$k]['distribution_name'] =M('users')->where("id='".$v['distribution']."'")->getField('user_name');
			$data[$k]['by_distribution_name'] =M('users')->where("id='".$v['by_distribution']."'")->getField('user_name');
		}
		
        //被分配人员列表
        
        $users = D()->field('u.*')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.id=ru.role_id and r.name='方案师' and ru.user_id=u.id and u.status=1")->order('u.create_time desc')->select();
		
        $sign = $this->get_sign($principal['id']);
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMIN_ID'),
            'modelname' => 'solutions',
            'project_id' => $principal['id'],
            'type' => 2,
            'ids' => 2,
            'number' => $sign['solutions_count'],
        ];
        $this->add_uploadreadlog($param);

        if (M('solutions')->where([''=>CommonModel::USER_SOLUTIONS,'status'=>1,'project_id'=>$principal['id']])->find()) {
            $status = 1;
        }else{
            $status = 0;
        }
		
        $this->assign('principal',$principal);
        $this->assign('users',$users);
        $this->assign('data',$data);
        $this->assign('solution',$solution);
        $this->assign('id',$id);
        $this->assign('status',$status);
        $this->assign('project_id',$project_id);
        $this->assign('project_ids',$project_ids);
        $this->assign("page", $page->show('Admin'));
        $this->assign('sign',$sign);
        $this->display();
    }

    //添加方案师分配信息
    public function solution_add_solutions()
    {
        $post = I('post.');
        
        if ($post) {
			$count =$this->solutions_model->where("project_id='".$post['project_id']."' and by_distribution='".$post['by_distribution']."' and status!=2")->count();
			if($count >0)
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'该项目已分配该方案师'));
			}
            if ($this->solutions_model->create()!==false) {
                $result=$this->solutions_model->add();
                if ($result) {
					$pinfo =M('project')->find($post['project_id']);
					if($pinfo['projectvalue'] <2)
					{
						$this->change_stage($post['project_id'],2);
					}                    
					log_insertresult(session('ADMIN_ID'),'分配方案师','添加项目');
                    $this->ajaxReturn(array('status' =>0,'msg'=>'保存成功！'));
                }else{
                    $this->ajaxReturn(array('status' =>1,'msg'=>'保存失败！'));
                }
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->solutions_model->getError()));
            }
        }
    }

    //分配各专业人员列表
    public function professional()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $principal['id'];

        //方案师角色信息
        $roles = $this->role_model->where("id in (5,6,7,8,9,10)")->select();
		
        $condition['_string'] = "a.role_id in(5,6,7,8,9,10)";
        $count = D('solutions as a')->where($condition)->count();
        $page = $this->page($count, 8);
        $data = $this->solutions_model->getData($condition,$page);

        $sign = $this->get_sign($principal['id']);
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMIN_ID'),
            'modelname' => 'solutions',
            'project_id' => $project_id,
            'type' => 2,
            'ids' => 3,
            'number' => $sign['professional_count'],
        ];
        $this->add_uploadreadlog($param);

        $this->assign('principal',$principal);
        $this->assign('data',$data);
        $this->assign('roles',$roles);
        $this->assign('id',$id);
        $this->assign('project_id',$project_id);
        $this->assign('project_ids',$project_ids);
        $this->assign('users',$users);
        $this->assign("page", $page->show('Admin'));
        $this->assign('sign',$sign);
        $this->display();
    }

    //施工图所需资料
    public function work_plan()
    {
        $project_id = I('get.project_id',0,'intval');

        if ($project_id) {
            $where['id'] = $project_id;
        }

        //项目列表
        $project_ids = $this->project_model->order('id desc')->select();
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();

        //资料
        $condition['a.project_id'] = $project_id;
        $condition['a.type'] = 3;
        $data_count = D('upload_data as a')->where($condition)->count();
        $data_page = $this->page($data_count, 8);
        $data = $this->upload_data_model->getData($condition,$data_page);

        $this->assign('data',$data);
        $this->assign('count',$data_count);
        $this->assign('data_page',$data_page->show('Admin'));
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->display();
    }

    //施工图内部评审意见
    public function opinion()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();

        //意见
        $condition['a.project_id'] = $project_id;
        $condition['a.type'] = 6;
        $opinion_count = D('upload_data as a')->where($condition)->count();
        $opinion_page = $this->page($opinion_count, 8);
        $opinion = $this->upload_data_model->getData($condition,$opinion_page);

        $this->assign('opinion',$opinion);
        $this->assign('count',$opinion_count);
        $this->assign('opinion_page',$opinion_page->show('Admin'));
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->display();
    }

    //添加各专业人员分配信息
    public function solution_add_professional()
    {
        $post = I('post.');
        log_insertresult(session('ADMIN_ID'),'分配各专业人员','添加项目');
        if ($post) {
			
            if ($this->solutions_model->create()!==false) {
			    foreach ($post['arr'] as $kk=> $v)
                {
					
					$acount=M('solutions')->where("by_distribution='".$v['by_distribution']."' and project_id='".$v['project_id']."' and (status=1 or status=0)")->count();
					if($acount > 0)
					{
						$this->ajaxReturn(array('status' =>1,'msg'=>'该专业人员已添加'));
					}
                }
				$aresult =0;
				foreach ($post['arr'] as $k=> $vv)
                {
					$count =M('solutions')->where("by_distribution='".$vv['by_distribution']."' and project_id='".$vv['project_id']."' and role_id='".$vv['role_id']."' and role_name='".$vv['role_name']."' and status!=2")->count();
                    
					if($count==0)
					{
						$vv['create_time'] = time();
						$result=$this->solutions_model->add($vv);
						$aresult=$aresult+1;
					}					
                }
				
                if ($aresult>0) {
					
                    $this->ajaxReturn(array('status' =>0,'msg'=>'保存成功！'));
                }else{
                    $this->ajaxReturn(array('status' =>1,'msg'=>'保存失败！'));
                }
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->solutions_model->getError()));
            }
        }
    }

    //保存上传文件
    public function saveFile()
    {
        if (IS_POST) {
            log_insertresult(session('ADMIN_ID'),'上传施工图所需资料','添加项目');
            $post = I('post.');
            $post['adminid'] = session('ADMIN_ID');
            $post['create_time'] = time();
            $result = $this->upload_data_model->save_file_info($post);
            if ($result) {
                //更变当前项目进行的阶段
                $project = $this->project_model->where('id='.$post['project_id'])->find();
                if ($project['projecttype'] == 2) {
                    $this->project_model->where('id='.$project['id'])->save(['projecttype'=>3]);
                }

                //更变当前项目的具体进度
                if ($post['type'] == 4) {
                    $this->change_stage($post['project_id'],4);
                }
                if ($post['type'] == 1) {
                    $this->change_stage($post['project_id'],7);
                }

                if ($post['type'] == 5) {
                    $this->change_stage($post['project_id'],19);
                }

                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
            }
        }
    }

    //获取标签
    public function get_sign($project_id)
    {
        $sign['solutions_count'] = $this->solutions_model->where(['project_id'=>$project_id,'role_id'=>CommonModel::USER_SOLUTIONS,'status'=>['neq',0]])->count();
        $sign['professional_count'] = $this->solutions_model->where(['project_id'=>$project_id,'role_id'=>['NOT IN',[CommonModel::USER_SUPERADMIN,CommonModel::USER_DISTRIBUTION,CommonModel::USER_PRINCIPAL,CommonModel::USER_SOLUTIONS]],'status'=>['neq',0]])->count();
        $s_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='solutions' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 2")->order('id desc')->getField('number');
		//file_put_contents('a9012.txt',M('uploadreadlog')->getLastSql());
	   $p_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='solutions' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 3")->order('id desc')->getField('number');
        $sign['solutions'] = $sign['solutions_count'] - $s_count;
		file_put_contents('a901.txt',$sign['solutions_count'].'|'.$s_count);
        $sign['professional'] = $sign['professional_count'] - $p_count;
        return $sign;
    }
	
	public function getroleusers()
	{
		if(IS_POST)
		{
			$role_id=I('role_id','','intval');
			$number =I('number','0','intval');
			$html ='';
			$list =D()->field('u.id,u.user_name')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."role_user ru")->where("u.id=ru.user_id and ru.role_id='$role_id' and u.status=1")->group('u.id')->order('u.create_time desc')->select();
			foreach($list as $k=>$v)
			{
				$html .= "<option value='".$v['id']."'>".$v['user_name']."</option>";
			}
			$this->ajaxReturn(array('status'=>0,'html'=>$html,'number'=>$number));
		}
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
	public function downziliao()
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
}