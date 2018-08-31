<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/26
 * Time: 17:47
 */

namespace Common\Model;


class ProjectModel extends CommonModel
{
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('project_name', 'require', '请填写完整信息', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('project_no', 'require', '请填写完整信息', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('company_id', 'require', '请填写完整信息', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('principal_name', 'require', '请填写完整信息', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('duty', 'require', '请填写完整信息', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('mobile', 'require', '请填写完整信息', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('project_name', '0,30', '项目名称过长！', 2, 'length', CommonModel:: MODEL_BOTH ),
        array('project_no', '0,16', '项目编号过长！', 2, 'length', CommonModel:: MODEL_BOTH ),
        array('principal_name', '0,16', '甲方项目负责人字符过长！', 2, 'length', CommonModel:: MODEL_BOTH ),
        array('duty', '0,10', '甲方负责人职务过长！', 2, 'length', CommonModel:: MODEL_BOTH ),
        array('project_name', '', '该项目已创建！', 1, 'unique', CommonModel:: MODEL_BOTH ),
		array('project_no', '', '该项目编号已被占用', 1, 'unique', CommonModel:: MODEL_BOTH )	
    );

    protected $_auto = array(
        array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
    );

    //用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
    function mGetDate() {
        return date('Y-m-d H:i:s');
    }

    protected function _before_write(&$data) {
        parent::_before_write($data);

        if(!empty($data['user_pass']) && strlen($data['user_pass'])<25){
            $data['user_pass']=sp_password($data['user_pass']);
        }
    }

    public function getProject($where,$page)
    {
        $project = M('project as a')
            ->join('xly_company as b on a.company_id = b.id')
            ->where($where)
            ->field('a.mobile,a.project_name,a.project_no,a.duty,a.create_time,a.id,a.company_id,a.qq,a.wx,a.principal_name,a.status,b.company_name')
            ->order("a.create_time DESC")
            ->limit($page->firstRow, $page->listRows)
            ->group('a.id')
            ->select();
		
        return $project;
    }
}