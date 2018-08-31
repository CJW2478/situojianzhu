<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UsersModel extends CommonModel
{
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
		array('user_name', 'require', '请填入完整信息！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		array('user_name', '0,10', '用户名称过长！', 1, 'length', CommonModel:: MODEL_BOTH ),
        array('user_name', '/^([\x{4e00}-\x{9fa5}]{0,10}$)/u', '请输入正确的姓名！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('user_pass', 'require', '请填入完整信息！', 0, 'regex', CommonModel:: MODEL_INSERT ),
        array('user_pass', 'require', '请填入完整信息！', 2, 'regex', CommonModel:: MODEL_UPDATE ),
		array('user_pass', '6,20', '密码不符合规则！', 2,'length', CommonModel:: MODEL_BOTH ),
		array('mobile', 'require', '请填入完整信息！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		array('user_duty', 'require', '请填入完整信息！', 1, 'regex', CommonModel:: MODEL_BOTH),
        array('user_name', '/^([\x{4e00}-\x{9fa5}]{0,10}$)/u', '请输入正确的姓名！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		array('user_duty', '/^([\x{4e00}-\x{9fa5}]{0,6}$)/u', '请输入正确的职务名称！', 1, 'regex', CommonModel:: MODEL_BOTH),
		array('qq_no', 'require', '请填入完整信息！', 1, 'regex', CommonModel:: MODEL_BOTH),
		array('qq_no', 'number', '请填写正确的QQ号！', 1, 'regex', CommonModel:: MODEL_BOTH),
		array('wx_no', 'require', '请填入完整信息！', 1, 'regex', CommonModel:: MODEL_BOTH)
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

    /**
     * 获取所有用户信息
     * @param $where
     * @param $page
     * @return mixed
     */
	public function getUsers($where,$page)
    {
        $users = M('users as a')
            ->join('xly_role_user as b on a.id = b.user_id','left')
            ->join('xly_role as c on c.id = b.role_id','left')
            ->field('a.id,a.mobile,a.user_name,a.user_duty,a.qq_no,a.wx_no,a.create_time,b.role_id,b.user_id,c.name')
            ->where($where)
            ->order("a.create_time DESC")
            ->limit($page->firstRow, $page->listRows)
            ->group('a.id')
            ->select();
		
        return $users;
    }
	public function getUsers2($where,$page)
    {
        $users = M('users as a')
            ->join('xly_role_user as b on a.id = b.user_id','left')
            ->join('xly_role as c on c.id = b.role_id','left')
            ->field('a.id,a.mobile,a.user_name,a.user_duty,a.qq_no,a.wx_no,a.create_time,b.role_id,b.user_id,c.name')
            ->where($where)
			->where("a.status=1")
            ->order("a.create_time DESC")
            ->limit($page->firstRow, $page->listRows)
            ->group('a.id')
            ->select();
		
        return $users;
    }
    /**
     * 获取用户信息
     * @param $where
     * @return mixed
     */
    public function getUserInfo($where)
    {
        $info = M('users a')
            ->join(C('DB_PREFIX').'role_user b on a.id = b.user_id')
            ->join(C('DB_PREFIX').'role c on c.id = b.role_id')
            ->where($where)
            ->find();
        return $info;
    }
}

