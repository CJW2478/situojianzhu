<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/25
 * Time: 14:48
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CompanyController extends AdminbaseController
{
    protected $company_model;
    public function _initialize() {
        parent::_initialize();
        $this->company_model = D("Common/Company");
    }

    //公司列表
    public function index()
    {
        $count = $this->company_model->where('isdel=0')->count();
        $page = $this->page($count, 8);
        $data = $this->company_model->order(array("id" => "DESC"))->where(['isdel'=>0])->limit($page->firstRow, $page->listRows)->select();

        $this->assign("company", $data);
        $this->assign("page",$page->show('Admin'));
        $this->display();
    }

    //添加公司
    public function add()
    {
        if (IS_POST) {
			if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['user_login']))
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'账号不能含有特殊字符'));
			}
			if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['user_pass']))
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'密码不能含有特殊字符'));
			}
            if ($this->company_model->create() !== false) {
                if ($this->company_model->add()!==false) {
					log_insertresult(session('ADMIN_ID'),'添加公司','添加项目');
                    $this->ajaxReturn(array('status'=>0,'msg'=>"添加成功"));
                } else {
                    $this->ajaxReturn(array('status'=>1,'msg'=>"添加失败"));
                }
            }else {
                $this->ajaxReturn(array('status'=>1,'msg'=>$this->company_model->getError()));
            }
        }
    }

    //删除公司
    public function delete()
    {
        if (IS_POST) {
            
			$count =M('project')->where("company_id='".intval($_POST['id'])."' and isdel=0")->count();
			if($count > 0)
			{
				$this->ajaxReturn(array('status'=>1,'msg'=>"该公司含有跟进的项目"));
			}
            if ($this->company_model->create()!==false) {
                if ($this->company_model->save()!==false) {
					log_insertresult(session('ADMIN_ID'),'删除公司','删除项目');
                    $this->ajaxReturn(array('status'=>0,'msg'=>"删除成功！"));
                } else {
                    $this->ajaxReturn(array('status'=>1,'msg'=>"修改失败！"));
                }
            } else {
                $this->ajaxReturn(array('status'=>1,'msg'=>$this->company_model->getError()));
            }
        }
    }

    //编辑公司
    public function edit()
    {
        $id = I("post.id",0,'intval');
        $data = $this->company_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->ajaxReturn(array('status'=>1,'msg'=>"信息不存在！"));
        }
        $this->ajaxReturn(array('status'=>0,'data'=>$data));
    }

    //编辑公司提交
    public function edit_post()
    {
        if (IS_POST) {
            
            $post = I('post.');
			if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$post['user_login']))
			{
				$this->ajaxReturn(array('status' =>1,'msg'=>'账号不能含有特殊字符'));
			}
			if($post['olduser_pass'] != $post['user_pass'])
			{
				if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$_POST['user_pass']))
				{
					$this->ajaxReturn(array('status' =>1,'msg'=>'密码不能含有特殊字符'));
				}
			}
			
            $user = $this->company_model->where(['id'=>$post['id']])->find();
            if ($user['user_pass'] === $post['user_pass']) {
                unset($post['user_pass']);
            }
			
            if ($this->company_model->create($post)!==false) {
                if ($this->company_model->save($post)!==false) {
                    log_insertresult(session('ADMIN_ID'),'编辑公司','删除公司');  
                    $this->ajaxReturn(array('status'=>0,'msg'=>'修改成功！'));
                } else {
                    $this->ajaxReturn(array('status'=>0,'msg'=>'修改失败！'));
                }
            } else {
                $this->ajaxReturn(array('status'=>1,'msg'=>$this->company_model->getError()));
            }
        }
    }
}