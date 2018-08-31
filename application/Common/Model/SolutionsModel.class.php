<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/27
 * Time: 16:17
 */

namespace Common\Model;


class SolutionsModel extends CommonModel
{
    protected $_auto = array(
        array('create_time','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
    );

    //用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
    function mGetDate() {
        return time();
    }

    protected function _before_write(&$data) {
        parent::_before_write($data);
    }

    /**
     * 获取分配数据
     * @param $where
     * @param $page
     * @return mixed
     */
    public function getData($where,$page)
    {
        $data = M('solutions as a ')
            ->join('xly_users as b on a.distribution = b.id')
            ->join('xly_users as c on a.by_distribution = c.id')
            ->field('a.remark,a.status,a.create_time,a.role_id,a.distribution,a.role_name,a.by_distribution,a.project_id,a.id,b.user_name distribution_name,c.user_name by_distribution_name')
            ->where($where)
            ->order("a.create_time DESC")
			->group('a.id')
            ->limit($page->firstRow, $page->listRows)
            ->select();
		
        return $data;
    }
}