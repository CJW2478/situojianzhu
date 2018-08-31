<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/25
 * Time: 15:55
 */

namespace Common\Model;


class CompanyModel extends CommonModel
{
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('company_name', 'require', '请填入完整信息！', 0, 'regex', CommonModel:: MODEL_BOTH ),
		array('company_name', '/^([\x{4e00}-\x{9fa5}]{0,30}$)/u', '公司名称不符合规则', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('user_login', 'require', '请填入完整信息！', 0, 'regex', CommonModel:: MODEL_BOTH ),
        array('user_pass', 'require', '请填入完整信息！', 0, 'regex', CommonModel:: MODEL_INSERT ),
        array('user_pass', 'require', '请填入完整信息！', 2, 'regex', CommonModel:: MODEL_UPDATE ),
        array('company_name', '', '该公司已创建！', 1, 'unique', CommonModel:: MODEL_BOTH ),
        array('user_login', '', '该账号已创建！',1, 'unique', CommonModel:: MODEL_BOTH ),
        array('company_name', '1,30', '公司名称过长！', 2, 'length', CommonModel:: MODEL_BOTH ),
        array('user_login', '6,20', '账号不符合规则！', 2, 'length', CommonModel:: MODEL_BOTH ),
        array('user_pass', '6,20', '登录密码不符合规则！', 2, 'length', CommonModel:: MODEL_BOTH )        
    );

    protected $_auto = array(
        array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
    );

    //用于获取时间
    function mGetDate() {
        return time();
    }

    protected function _before_write(&$data) {
        parent::_before_write($data);

        if(!empty($data['user_pass']) && strlen($data['user_pass'])<20){
            $data['user_pass']=sp_password(strtolower($data['user_pass']));
        }
    }

    //获取到甲方信息
    public function getInfo($where)
    {
        $info = M("company a")
            ->join(C('DB_PREFIX')."project b on a.id = b.company_id")
            ->where($where)
            ->find();

        return $info;
    }

}