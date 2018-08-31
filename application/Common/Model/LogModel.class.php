<?php



namespace Common\Model;



class LogModel extends CommonModel

{

    public function addLog($remark,$adminId=0,$type=1)

    {

        if($adminId == 0){

            $adminId = session('ADMIN_ID');

        }

        $data = array(

            'admin'=>$adminId,

            'remark'=>$remark,

            'create_time'=>time(),

            'type'=>$type

        );
        //dump($data);die;
        return $this->add($data);

    }

}