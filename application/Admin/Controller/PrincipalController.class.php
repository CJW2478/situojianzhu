<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/6/28
 * Time: 16:14
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class PrincipalController extends AdminbaseController
{
    protected $project_model,$company_model,$solutions_model,$upload_data_model,$uploadpic_data_model,$upload_effect_model;
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
    public function _initialize() {
        parent::_initialize();
        $this->project_model = D("Common/Project");
        $this->company_model = D("Common/Company");
        $this->solutions_model = D("Common/Solutions");
        $this->upload_data_model = D('Common/UploadData');
        $this->uploadpic_data_model = D('Common/UploadpicData');
        $this->upload_effect_model = D('Common/UploadEffect');
    }

    //负责人列表
    public function index()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $project = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project['id'];
        //分配列表
        $condition['a.status'] = 0;
        $count = D('solutions as a')->where($condition)->count();
        $user_page = $this->page($count, 8);
        $users = $this->solutions_model->getData($condition,$user_page);
		if($project['id']>0)
		{
			$sign = $this->get_sign($project['id']);
			
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'shreadsolutions',
				'project_id' => $project['id'],
				'type' => 2,
				'ids' => 0,
				'number' => $sign['solutions_unaudit'],
			];
			$this->add_uploadreadlog($param);

			$this->assign('sign',$sign);
		}
        $this->assign('users',$users);
        $this->assign('user_page',$user_page->show('Admin'));
        $this->assign('project_id',$project['id']);
        $this->assign('project_ids',$project_ids);
        $this->assign('project',$project);
        $this->display();
    }

    //人员分配审核
    public function audit()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $project = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project['id'];

        $condition['a.status'] = ['EQ',1];
        $count = D('solutions as a')->where($condition)->count();
        $user_page = $this->page($count, 8);
        $users = $this->solutions_model->getData($condition,$user_page);

        $sign = $this->get_sign($project_id);
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMIN_ID'),
            'modelname' => 'solutions',
            'project_id' => $project_id,
            'type' => 2,
            'ids' => 1,
            'number' => $sign['solutions_count'],
        ];
        $this->add_uploadreadlog($param);

        $this->assign('sign',$sign);
        $this->assign('users',$users);
        $this->assign('user_page',$user_page->show('Admin'));
        $this->assign('project_id',$project_id);
        $this->assign('project_ids',$project_ids);
        $this->assign('project',$project);
        $this->display();
    }

    //平面图
    public function plane()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $project = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project['id'];

        //平面规划设计方案
        $condition['a.type'] = 4;
        $scheme_count = D('upload_data as a')->where($condition)->count();
        $scheme_page = $this->page($scheme_count, 8);
        $scheme_data = $this->upload_data_model->getData($condition,$scheme_page);

        //规划方案确认
        $condition['a.type'] = 1;
		$condition['a.audit_status'] = 0;
        $plane_count = D('upload_data as a')->where($condition)->count();
        $plane_page = $this->page($plane_count, 8);
        $plane_data = $this->upload_data_model->getData($condition,$plane_page);

        $sign = $this->get_sign($project_id);
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMIN_ID'),
            'modelname' => 'upload_data',
            'project_id' => $project_id,
            'type' => 2,
            'ids' => 4,
            'number' => $sign['plane_scheme_count'],
        ];
        $this->add_uploadreadlog($param);

        $param['ids'] = 1;
        $param['number'] = $sign['plane_design_count'];
        $this->add_uploadreadlog($param);

        $this->assign('sign',$sign);
        $this->assign('plane_data',$plane_data);
        $this->assign('scheme_data',$scheme_data);
        $this->assign('plane_page',$plane_page->show('Admin'));
        $this->assign('scheme_page',$scheme_page->show('Admin'));
        $this->assign('project_id',$project_id);
        $this->assign('project_ids',$project_ids);
        $this->assign('project',$project);
        $this->display();
    }

    //效果图
    public function effect()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $project = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project['id'];

        //第一次上传效果图方案
        $condition['a.is_first'] = 1;
        $first_count = D('upload_effect a')->where($condition)->count();
        $first_page = $this->page($first_count, 8);
        $first_effect = $this->upload_effect_model->getData($condition,$first_page);

        //效果图方案
        unset($condition['a.is_first']);
        $condition['_string'] = 'a.audit_status = 0';
        $count = D('upload_effect a')->where($condition)->count();
        $page = $this->page($count, 8);
        $effect = $this->upload_effect_model->getData($condition,$page);

        //规划方案文本
        unset( $condition['a.is_first']);
		unset( $condition['_string']);
        $condition['a.type'] = 5;
        $scheme_count = D('upload_data as a')->where($condition)->count();
        $scheme_page = $this->page($scheme_count, 8);
        $scheme_data = $this->upload_data_model->getData($condition,$scheme_page);

        $sign = $this->get_sign($project_id);
		
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMIN_ID'),
            'modelname' => 'first_effect',
            'project_id' => $project_id,
            'type' => 2,
            'ids' => 1,//第一版
            'number' => $sign['effect_first_count'],
        ];
        $this->add_uploadreadlog($param);

        $param['ids'] = 2;//最终版
		$param['modelname'] = 'final_effect';
        $param['number'] = $sign['effect_final_count'];
        $this->add_uploadreadlog($param);

        $param['modelname'] = 'upload_data';
        $param['ids'] = 5;
        $param['number'] = $sign['effect_scheme_count'];
        $this->add_uploadreadlog($param);

        $this->assign('sign',$sign);
        $this->assign('scheme_data',$scheme_data);
        $this->assign('scheme_page',$scheme_page->show('Admin'));
        $this->assign('effect',$effect);
        $this->assign('page',$page->show('Admin'));
        $this->assign('first_effect',$first_effect);
        $this->assign('first_page',$first_page->show('Admin'));
        $this->assign('project_id',$project_id);
        $this->assign('project_ids',$project_ids);
        $this->assign('project',$project);
        $this->display();
    }

    //施工图
    public function work()
    {
        $project_id = I('get.project_id',0,'intval');

        $where['isdel'] = 0;
        //项目列表
        $project_ids = $this->project_model->where($where)->order('id desc')->select();
        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $project = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project['id'];

        //施工图数据
        $condition['a.atype'] = 2;
        $condition['a.isfirst'] = 1;
        $work_count = D('uploadpic_data as a')->where($condition)->count();
        $work_page = $this->page($work_count, 8);
        $work_data = $this->uploadpic_data_model->getData($condition,$work_page);
		if($project['id']>0){
			$sign = $this->get_sign($project_id);
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'uploadpic_data',
				'project_id' => $project_id,
				'type' => 2,
				'ids' => 2,
				'number' => $sign['work_first_count'],
			];
			$this->add_uploadreadlog($param);

			$this->assign('sign',$sign);
		}
        $this->assign('work_data',$work_data);
        $this->assign('work_page',$work_page->show('Admin'));
        $this->assign('project_id',$project_id);
        $this->assign('project_ids',$project_ids);
        $this->assign('project',$project);
        $this->display();
    }

    //更改分配人员状态
    public function change_status()
    {
        if (IS_POST) {
            
            $pdata =I('post.');
			$pdata['updatetime'] =time();
			$fn =$this->solutions_model->where("project_id='".$pdata['project_id']."' and role_name='方案师' and status=0")->count();
			$res=$this->solutions_model->where("project_id='".$pdata['project_id']."' and status=0")->save($pdata);			
			if ($res!==false) {
				if($pdata['status'] ==1)
				{
					$pinfo =M('project')->find($pdata['project_id']);
					
					if(($pinfo['projectvalue']<3) && ($fn>0))
					{
						$this->change_stage($pdata['project_id'],3);
					}            
				}				
				log_insertresult(session('ADMIN_ID'),'确认分配员状态','编辑项目');
				$this->ajaxReturn(array('status' =>0,'msg'=>'审核成功'));
			} else {
				$this->ajaxReturn(array('status' =>1,'msg'=>'审核失败'));
			}
            
        }
    }

    //确认upload_data表文件状态
    public function confirm_upload_data()
    {
        if (IS_POST) {
           
            if ($this->upload_data_model->create()!==false) {
                if ($this->upload_data_model->save()!==false) {
					
                  
					log_insertresult(session('ADMIN_ID'),'确认项目文件','编辑项目');
					
					// 推送
					$p =M('project')->where("id='".$_POST['project_id']."'")->find();
					if($p['projectvalue']<5 && intval($_POST['step'])==5)
					{
						$this->change_stage($_POST['project_id'],5);
					}
					if($p['projectvalue']<9 && intval($_POST['step'])==9)
					{
						$this->change_stage($_POST['project_id'],9);
						M('project')->where("id='".intval($_POST['project_id'])."'")->save(array('projecttype'=>2,'projectvalue'=>10));
					}
					if($p['projectvalue']<20 && intval($_POST['step'])==20)
					{
						$this->change_stage($_POST['project_id'],20);
					}
					if($p['projectvalue']<21 && intval($_POST['step'])==21)
					{
						$this->change_stage($_POST['project_id'],21);
						M('project')->where("id='".intval($_POST['project_id'])."'")->save(array('projecttype'=>3,'projectvalue'=>22));
					}
					if(intval($_POST['step']) ==5)
					{
						$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
						$info['project_name'] =$p['project_name'];
						$info['message'] ='您的项目有了新动态';
						$info['desc'] ='乙方已上传方案文件，请尽快确认！';
						$this->sendTemplate($info);
					}
                    $this->ajaxReturn(array('status' =>0,'msg'=>'操作成功'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'操作失败'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->upload_data_model->getError()));
            }
        }
    }
	
    //确认uploadpic_data表文件状态
    public function confirm_uploadpic_data()
    {
        if (IS_POST) {           
            if ($this->uploadpic_data_model->create()!==false) {
                if ($this->uploadpic_data_model->save()!==false) {                    
					log_insertresult(session('ADMIN_ID'),'确认第一般平面图','编辑项目');
					// 推送
					$p =M('project')->where("id='".$_POST['project_id']."'")->find();
					$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
					$info['project_name'] =$p['project_name'];
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='乙方已上传方案文件，请尽快确认！';
					
					$this->sendTemplate($info);
                    $this->ajaxReturn(array('status' =>0,'msg'=>'操作成功'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'操作失败'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->uploadpic_data_model->getError()));
            }
        }
    }

    //确认upload_effect表文件状态
    public function confirm_upload_effect()
    {
        if (IS_POST) {
           
            if ($this->upload_effect_model->create()!==false) {
                if ($this->upload_effect_model->save()!==false) {
                   
					log_insertresult(session('ADMIN_ID'),'确认效果图','编辑项目');
					// 推送
					$p =M('project')->where("id='".$_POST['project_id']."'")->find();
					if($p['projectvalue']<14 && $p['projecttype']==2 && intval($_POST['step'])==14)
					{
						 $this->change_stage($_POST['project_id'],14);
					}
					if($p['projectvalue']<18 && $p['projecttype']==2 && intval($_POST['step'])==18)
					{
						 $this->change_stage($_POST['project_id'],18);
					}
					$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
					$info['project_name'] =$p['project_name'];
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='乙方已上传方案文件，请尽快确认！';
					$this->sendTemplate($info);
                    $this->ajaxReturn(array('status' =>0,'msg'=>'操作成功'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'操作失败'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->upload_effect_model->getError()));
            }
        }
    }

    //更改项目状态
    public function audit_status()
    {
        if (IS_POST) {
           
            if ($this->project_model->create()!==false) {
                if ($this->project_model->save()!==false) {
					 log_insertresult(session('ADMIN_ID'),'更改项目状态','编辑项目');
                    $this->ajaxReturn(array('status' =>0,'msg'=>'操作成功'));
                } else {
                    $this->ajaxReturn(array('status' =>1,'msg'=>'操作失败'));
                }
            } else {
                $this->ajaxReturn(array('status' =>1,'msg'=>$this->project_model->getError()));
            }
        }
    }
	
	public function downloadgg()
	{
		$id=I('id','','intval');
		$info =M('upload_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['file_url'];
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['file_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	public function downloadeffect()
	{
		$id=I('id','','intval');
		$info =M('upload_effect')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['file_url'];
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['file_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	public function downloadeffect2()
	{
		$id=I('id','','intval');
		$info =M('upload_effect')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['mfile_url'];
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['mfile_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
   //获取菜单标签
    public function get_sign($project_id)
    {
		// 未审核人员数量
		
        $sign['solutions_count'] = $this->solutions_model->where(['project_id'=>$project_id,'status'=>1])->count();
        $sign['solutions_unaudit_count'] = $this->solutions_model->where(['project_id'=>$project_id,'status'=>0])->count();
        $sign['plane_scheme_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>4])->count();
        $sign['plane_design_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>1])->count();
        $sign['effect_first_count'] = $this->upload_effect_model->where(['project_id'=>$project_id,'is_first'=>1])->count();
        $sign['effect_final_count'] = $this->upload_effect_model->where(['project_id'=>$project_id,'is_first'=>0])->count();
        $sign['effect_scheme_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>5])->count();
        $sign['work_first_count'] = $this->uploadpic_data_model->where(['project_id'=>$project_id,'atype'=>2,'is_first'=>1])->count();
		// 已读未审核数量
		
        $s_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='solutions' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 1")->order('id desc')->getField('number');
        $su_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='shreadsolutions' and adminid='".SESSION('ADMIN_ID')."' and type=2")->order('id desc')->getField('number');
		
		$ps_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 4")->order('id desc')->getField('number');
        $pd_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 1")->order('id desc')->getField('number');
        $ef_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='first_effect' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 1")->order('id desc')->getField('number');
        $efl_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='final_effect' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 2")->order('id desc')->getField('number');
        $es_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='upload_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 5")->order('id desc')->getField('number');
        $wf_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='uploadpic_data' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 2")->order('id desc')->getField('number');
		
		
        $sign['solutions'] = $sign['solutions_count'] - $s_count;
		if($sign['solutions_unaudit_count'] > $su_count)
		{
			$sign['solutions_unaudit'] = $sign['solutions_unaudit_count'] - $su_count;
		}else{
			$sign['solutions_unaudit']=0;
		}
        
        $sign['plane_scheme'] = $sign['plane_scheme_count'] - $ps_count;
        $sign['plane_design'] = $sign['plane_design_count'] - $pd_count;
        $sign['effect_first'] = $sign['effect_first_count'] - $ef_count;
        $sign['effect_final'] = $sign['effect_final_count'] - $efl_count;
        $sign['effect_scheme'] = $sign['effect_scheme_count'] - $es_count;
        $sign['work_first'] = $sign['work_first_count'] - $wf_count;
		
        return $sign;
    }
	
	// 通知推送
	function sendTemplate($info){
		
        $weChatAuth = new \Com\WechatAuth(self::APPID,self::APPSECRET);        
		$token=$weChatAuth->getAccessToken('client')['access_token'];		
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
        $data=array(
            "touser"=>$info['openid'],
            "topcolor"=>"#151516", 
            "template_id"=>"7cLx7eLAcmV_YxHK33ch0kpsvbgj5DxDdrJpUBM2-VM",
            "url"=>"",
            'data'=>array(
                    'first'=>array('value'=>urlencode($info['message']),'color'=>"#151516"),
                    'keyword1'=>array('value'=>urlencode($info['project_name']),'color'=>"#151516"),
                    'keyword2'=>array('value'=>urlencode($info['user_name']),'color'=>"#151516"),
                    'keyword3'=>array('value'=>urlencode($info['desc']),'color'=>"#151516"),
                    'remark'=>array('value'=>urlencode("请知晓"),'color'=>'#151516')
                ));
        $data=urldecode(json_encode($data)); 
        $res= $this->_request($url,true,'POST',$data);		
    }
    function _request($curl,$https=true,$method='GET',$data=null){
        $header = array('Expect:');
        $ch=  curl_init();
        curl_setopt($ch,CURLOPT_URL,$curl);
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        if($https){
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,TRUE);
        }
        if($method=='POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        $content=  curl_exec($ch);
        //返回结果
          if($content){
              curl_close($ch);
              return $content;
          }
          else {
             $errno = curl_errno( $ch );
             $info  = curl_getinfo( $ch );
             $info['errno'] = $errno;
                curl_close( $ch );
             $log = json_encode( $info );
             
              return false;
          }
    }
}