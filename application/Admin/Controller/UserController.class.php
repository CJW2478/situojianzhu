<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class UserController extends AdminbaseController{

	protected $users_model,$role_model;

	public function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->role_model = D("Common/Role");
	}

	// 管理员列表
	public function index(){
		/**搜索条件**/
		$user_name = I('request.user_name');
		$user_mobile = trim(I('request.mobile'));
		if($user_name){
			$where['a.user_name'] = array('like',"%$user_name%");
		}
		
		if($user_mobile){
			$where['a.mobile'] = array('like',"%$user_mobile%");;
		}

		$where['a.id'] = array('NEQ',sp_get_current_admin_id());
		$where['a.status'] =1;
		$count = D('users as a')->where($where)->count();
		$page = $this->page($count, 8);
        $users = $this->users_model->getUsers($where,$page);
		$roles_src=$this->role_model->where('id != 1')->select();
		$this->assign("page", $page->show('Admin'));
		$this->assign("roles",$roles_src);
		$this->assign("users",$users);
		$this->display();
	}

	// 管理员添加提交
	public function add_post(){
        if (IS_POST) {
			$pdata =I('post.');
			if (!preg_match('/^1[345789]{1}\d{9}$/', $_POST['mobile'])) {
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的手机号'));
			}
            $count=M('commonusers')->where("mobile='".trim($_POST['mobile'])."' and status=1")->count();
			if($count > 0)
			{
				 $this->ajaxReturn(array('status' =>1,'msg'=>'该手机号已注册'));
			}
			if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$pdata['wx_no']))
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
			}
			if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$pdata['user_pass']))
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'密码不能含有特殊字符'));
			}
			if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $pdata['wx_no'])>0) {
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
			} else if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $pdata['wx_no'])>0) {
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
			}			
            if ($this->users_model->create()!==false) {
                $result = $this->users_model->add();
                if ($result!==false) {
                        $role_user_model=M("RoleUser");
                        $role_user_model->where(array("user_id"=>$result))->delete();
                        if(sp_get_current_admin_id() != 1 && $_POST['role_id'] == 1){
                            $this->ajaxReturn(array('status' =>1,'msg'=>'您不能创建超级管理员'));
                        }
                        $role_user_model->add(array("role_id"=>$_POST['role_id'],"user_id"=>$result));
                        // 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
						log_insertresult(session('ADMIN_ID'),'添加管理员','添加项目');
                        $this->ajaxReturn(array('status' =>0,'msg'=>'保存成功！'));
                }else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'添加失败！'));
                }
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->users_model->getError()));
            }
        }
	}

	// 管理员编辑
	public function edit(){
	    $id = I('post.id',0,'intval');
        $user=$this->users_model->where(array("id"=>$id))->find();
        if (!$user) {
            $this->ajaxReturn(array('status'=>1,'msg'=>"信息不存在！"));
        }
		$role_user_model=M("RoleUser");
		$user['role_id']=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
        $this->ajaxReturn(array('status'=>0,'data'=>$user));
	}

	// 管理员编辑提交
	public function edit_post(){
        if (IS_POST) {
            $post = I('post.');
			if (!preg_match('/^1[345789]{1}\d{9}$/', $post['mobile'])) {
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的手机号'));
			}
			if($post['oldmobile'] != $post['mobile'])
			{
				$count =M('commonusers')->where("mobile='".$post['mobile']."' and status=1")->count();
				if($count >0)
				{
					 $this->ajaxReturn(array('status' =>1,'msg'=>'该手机号已注册'));
				}
			}
			if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$post['wx_no']))
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
			}
			if($post['olduser_pass'] != $post['user_pass'])
			{
				if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$post['user_pass']))
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'密码不能含有特殊字符'));
				}
			}
			if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $post['wx_no'])>0) {
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
			} else if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $post['wx_no'])>0) {
				$this->ajaxReturn(array('status' =>1,'msg'=>'请填写正确的微信号'));
			}
			if($post['oldrole_id'] !=$post['role_id'])
			{
				$ccount=M('solutions')->where("by_distribution='".$post['id']."' and (status=1 or status=0)")->count();
				if($ccount>0)
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'该用户已有分配项目'));	
				}
			}
            $user = $this->users_model->where(['id'=>$post['id']])->find();
            if ($user['user_pass'] === $post['user_pass']) {
                unset($post['user_pass']);
            }
            if ($this->users_model->create($post)!==false) {
                $result = $this->users_model->save($post);
                if ($result!==false) {
                    $role_user_model=M("RoleUser");
                    $role_user_model->where(array("user_id"=>$post['id']))->delete();
                    if(sp_get_current_admin_id() != 1 && $_POST['role_id'] == 1){
                        $this->ajaxReturn(array('status' =>1,'msg'=>'您不能更改超级管理员身份'));
                    }
                    $role_user_model->add(array("role_id"=>$_POST['role_id'],"user_id"=>$post['id']));
                    // 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
					log_insertresult(session('ADMIN_ID'),'编辑管理员','编辑项目');
                    $this->ajaxReturn(array('status' =>0,'msg'=>'保存成功！'));
                }else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'添加失败！'));
                }
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->users_model->getError()));
            }
        }
	}

	// 管理员删除
	public function delete(){
	    $id = I('post.id',0,'intval');
		
		//$sinfo =M('solutions')->where("by_distribution='$id'")->count();
		$sinfo =D()->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.by_distribution='$id' and p.isdel=0")->count();
		if($sinfo>0)
		{
			$this->ajaxReturn(array('status'=>1,'msg'=>'该用户已有分配项目'));
		}
		else
		{
			if ($this->users_model->where("id='$id'")->setField('status',2)) {
				

				// 记录操作日志 3个必填字段（用户id,操作内容，操作类型）
				log_insertresult(session('ADMIN_ID'),'删除管理员','删除项目');
				$this->ajaxReturn(array('status'=>0,'msg'=>'删除成功'));
			} else {
				$this->ajaxReturn(array('status'=>1,'msg'=>'删除失败'));
			}
		}
	}

	
	// 停用管理员
    public function ban(){
        $id = I('get.id',0,'intval');
    	if (!empty($id)) {
            log_insertresult(session('ADMIN_ID'),'停用管理员','编辑项目');
    		$result = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','0');
    		if ($result!==false) {
    			$this->success("管理员停用成功！", U("user/index"));
    		} else {
    			$this->error('管理员停用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }

    // 启用管理员
    public function cancelban(){
    	$id = I('get.id',0,'intval');
    	if (!empty($id)) {
            log_insertresult(session('ADMIN_ID'),'启用管理员','编辑项目');
    		$result = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','1');
    		if ($result!==false) {
    			$this->success("管理员启用成功！", U("user/index"));
    		} else {
    			$this->error('管理员启用失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
}