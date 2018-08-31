<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/28
 * Time: 14:45
 */

namespace Common\Model;


class UploadDataModel extends CommonModel
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

    //保存文件信息
    public function save_file_info($data)
    {
        return $this->add($data);
    }

    //获取上传文件信息
    public function getData($where,$page)
    {
        $data = M('upload_data as a')
            ->join('xly_users as b on a.adminid = b.id')
            ->where($where)
            ->field('a.id,a.file_name,a.adminid,a.project_id,a.type,a.file_url,a.mfile_name,a.mfile_url,a.message,a.audit_status,a.status,a.create_time,b.user_name')
            ->order("a.create_time DESC")
            ->limit($page->firstRow, $page->listRows)
            ->select();

        return $data;
    }
}