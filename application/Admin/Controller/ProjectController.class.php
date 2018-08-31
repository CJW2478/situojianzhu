<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/26
 * Time: 17:12
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;

class ProjectController extends AdminbaseController
{
    protected $project_model,$company_model;

    public function _initialize() {
        parent::_initialize();
        $this->project_model = D("Common/Project");
        $this->company_model = D("Common/Company");
    }

    //项目列表
    public function index()
    {
        /**搜索条件**/
        $project_info = I('project_info','','trim');
       
		if(!empty($project_info)){
            $keyword=$project_info;
            $keyword_complex=array();
            $keyword_complex['a.project_name']  = array('like', "%$keyword%");
            $keyword_complex['a.project_no']  = array('like',"%$keyword%");
            $keyword_complex['_logic'] = 'or';
            $where['_complex'] = $keyword_complex;
        }
        $where['a.isdel'] = 0;
        $count = count($this->project_model->getProject($where,''));
        $page = $this->page($count, 8);
        $project = $this->project_model->getProject($where,$page);
		
        $company=$this->company_model->where(['isdel'=>0])->order("id DESC")->select();
		
        $this->assign("page", $page->show('Admin'));
        $this->assign("project",$project);
        $this->assign("company",$company);
		$this->assign("formget",array_merge($_GET,$_POST));  
        $this->display();
    }

    //项目添加提交
    public function add_post()
    {
        if (IS_POST) {
			// 乙方添加的会员
			$count=M('commonusers')->where("mobile='".trim($_POST['mobile'])."' and type=2")->count();
			if($count>0)
			{
				$this->ajaxReturn(array('status'=>1,'msg'=>'该手机号已注册'));
			}
			// 甲方添加的会员
			$count1=M('account')->where("mobile='".trim($_POST['mobile'])."' and type=1 and atype=0")->count();
			if($count1>0)
			{
				$this->ajaxReturn(array('status'=>1,'msg'=>'该手机号已注册'));
			}
            if(!empty($_POST['mobile']))
            {
                if(!preg_match("/^\d{11}$/", trim($_POST['mobile'])))
                {
                    $this->ajaxReturn(array('status'=>1,'msg'=>'请输入正确的电话号码'));
                }
            }
			
			if(!empty($_POST['wx']))
			{
				if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['wx']))
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
				}
				if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $_POST['wx'])>0) {
					$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
				} else if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $_POST['wx'])>0) {
					$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
				}
			}
            if ($this->project_model->create()!==false) {
                $result = $this->project_model->add();
                if ($result!==false) {
                    log_insertresult(session('ADMIN_ID'),'项目添加','添加项目');
                    // 添加项目的负责人账号
					$count=M('commonusers')->where("mobile='".trim($_POST['mobile'])."'")->count();
					if($count==0)
					{
						M('account')->add(array('user_name'=>trim($_POST['principal_name']),'user_duty'=>trim($_POST['duty']),'pid'=>$result,'company_id'=>intval($_POST['company_id']),'mobile'=>trim($_POST['mobile']),'atype'=>1,'password'=>sp_password(123456),'create_time'=>time()));
					}						
                    $this->ajaxReturn(array('status' =>0,'msg'=>'添加成功！'));
                }else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'添加失败！'));
                }
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->project_model->getError()));
            }
        }
    }

    //项目编辑
    public function edit()
    {
        $id = I("post.id",0,'intval');
        $data = $this->project_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->ajaxReturn(array('status'=>1,'msg'=>"信息不存在！"));
        }
        $this->ajaxReturn(array('status'=>0,'data'=>$data));
    }

    //项目编辑提交
    public function edit_post()
    {
        if (IS_POST) {
			if(trim($_POST['oldmobile']) !=trim($_POST['mobile']))
			{
				$count=M('commonusers')->where("mobile='".trim($_POST['mobile'])."' and type=2")->count();
				if($count > 0)
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'该手机号已注册'));
				}
				$count1=M('account')->where("mobile='".trim($_POST['mobile'])."' and type=1 and atype=0")->count();
				if($count1 > 0)
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'该手机号已注册'));
				}
			}
            if(!empty($_POST['mobile']))
            {
                if(!preg_match("/^\d{11}$/", trim($_POST['mobile'])))
                {
                    $this->ajaxReturn(array('status'=>1,'msg'=>'请输入正确的电话号码'));
                }
            }

			if(!empty($_POST['wx']))
			{
				if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['wx']))
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
				}
				if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $_POST['wx'])>0) {
					$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
				} else if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $_POST['wx'])>0) {
					$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
				}
			}
            if ($this->project_model->create()!==false) {
                if ($this->project_model->save()!==false) {
                    log_insertresult(session('ADMIN_ID'),'项目编辑','编辑项目');
					
                    M('account')->where("pid='".intval($_POST['id'])."' and atype=1")->save(array('user_name'=>trim($_POST['principal_name']),'user_duty'=>trim($_POST['duty']),'company_id'=>intval($_POST['company_id']),'mobile'=>trim($_POST['mobile'])));
                    
					$this->ajaxReturn(array('status' =>0,'msg'=>'修改成功！'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'修改失败！'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->project_model->getError()));
            }
        }
    }

    //项目删除
    public function delete()
    {
        if (IS_POST) {
           $info= $this->project_model->find(intval($_POST['id']));
            if ($this->project_model->create()!==false) {
                if ($this->project_model->save()!==false) {
                    log_insertresult(session('ADMIN_ID'),'项目删除','删除项目');
                    M('account')->where("mobile='".$info['mobile']."' and company_id='".$info['company_id']."'")->delete();
                    $this->ajaxReturn(array('status' =>0,'msg'=>'删除成功！'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'删除失败！'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->project_model->getError()));
            }
        }
    }

}