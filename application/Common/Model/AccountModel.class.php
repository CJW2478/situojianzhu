<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/26
 * Time: 15:05
 */

namespace Common\Model;


class AccountModel extends CommonModel
{
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('status', 'number', '用户状态格式错误！', 0, 'regex', CommonModel:: MODEL_UPDATE ),
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

    public function getUsers($where,$page)
    {
        $users = M('account as a')
            ->join('xly_company as b on a.company_id = b.id')
            ->where($where)
            ->field('a.mobile,a.user_name,a.user_duty,a.create_time,a.id,a.company_id,a.status,b.company_name')
            ->order("a.create_time DESC")
            ->limit($page->firstRow, $page->listRows)
            ->group('a.id')
            ->select();

        return $users;
    }
	
	public function getCount($where)
	{
		$count = M('account as a')
            ->join('xly_company as b on a.company_id = b.id')
            ->where($where)
            ->field('a.mobile,a.user_name,a.user_duty,a.create_time,a.id,a.company_id,a.status,b.company_name')
            ->order("a.create_time DESC")
            ->group('a.id')
            ->count();

        return $count;
	}
}