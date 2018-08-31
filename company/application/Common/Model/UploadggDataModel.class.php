<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/3
 * Time: 16:20
 */

namespace Common\Model;


class UploadggDataModel extends CommonModel
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

    //获取上传文件信息
    public function getData($where,$page)
    {
        $data = M('uploadgg_data')
            ->where($where)
            ->order("createtime DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();

        return $data;
    }
}