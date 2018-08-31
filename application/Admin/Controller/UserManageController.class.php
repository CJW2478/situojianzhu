<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/25
 * Time: 16:49
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;

class UserManageController extends AdminbaseController
{
    protected $user_model;
    protected $company_model;

    public function _initialize() {
        parent::_initialize();
        $this->user_model = D("Common/Account");
        $this->company_model = D("Common/Company");
    }

    //用户管理列表
    public function index()
    {
        /**搜索条件**/
        $user_info = I('request.user_info');
        $company_id = trim(I('request.company_id'));
        $where['b.isdel']=0;
        if($user_info){
            if (is_numeric($user_info)) {
                $where['a.mobile'] = array('like',"%$user_info%");
            }else{
                $where['a.user_name'] = array('like',"%$user_info%");
            }
        }

        if($company_id){
            $where['a.company_id'] = array('like',"%$company_id%");
        }

        $count = $this->user_model->getCount($where);
        $page = $this->page($count, 20);
        $account = $this->user_model->getUsers($where,$page);
        $company = $this->company_model->where("isdel=0")->order('create_time desc')->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("account", $account);
        $this->assign("company_id", $company_id);
        $this->assign("company", $company);
        $this->display();
    }

    //改变用户状态
    public function change_status()
    {
        if (IS_POST) {
            if ($this->user_model->create()!==false) {
                if ($this->user_model->save()!==false) {
                    $this->ajaxReturn(array('status' =>0,'msg'=>'操作成功！'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'操作失败！'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->user_model->getError()));
            }
        }
    }

}