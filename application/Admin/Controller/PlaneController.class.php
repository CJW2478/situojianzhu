<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/3
 * Time: 14:48
 */

namespace Admin\Controller;


use Common\Controller\AdminbaseController;
use Common\Model\CommonModel;

class PlaneController extends AdminbaseController
{
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
    protected  $project_model,$uploadgg_data_model,$users_model,$solutions_model,$upload_data_model,$company_model,$upload_projectinfo_model;
    public function _initialize() {
        parent::_initialize();
        $this->project_model = D("Common/Project");
        $this->uploadgg_data_model = D("Common/UploadggData");
        $this->users_model = D('Common/Users');
        $this->solutions_model = D("Common/Solutions");
        $this->upload_data_model = D("Common/UploadData");
        $this->company_model = D("Common/Company");
        $this->upload_projectinfo_model = D('Common/UploadProjectinfo');
    }

    //甲方项目基本信息
    public function index()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);
		
		if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
			if(session('ADMIN_ID')==1 || !empty($finfo))
			{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
			
		}else{
			$project_ids=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
			
		}
        //甲方项目信息
        $condition['a.type'] = 1;
		$condition['a.project_id'] =$principal['id'];
        $count = D('upload_projectinfo as a')->where($condition)->count();
        $page = $this->page($count, 8);
        $project = $this->upload_projectinfo_model->getProject($condition,$page);
		if($principal['id']>0){
			$sign = $this->get_sign($principal['id']);
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'project_info',
				'project_id' => $principal['id'],
				'type' => 1,
				'ids' => 1,
				'number' => $count,
			];
			$this->add_uploadreadlog($param);
		}
        $this->assign('sign',$sign);
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('project',$project);
        $this->assign('count',$count);
        $this->assign('data_page',$page->show('Admin'));
        $this->display();
    }

    //沟通记录文件
    public function communication()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

        if($project_id)
		{
			$principal = M('project')->find($project_id);
			
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
			if(session('ADMIN_ID')==1 || !empty($finfo))
			{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and  p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];
        $condition['type'] = 3;
        $count = D('uploadgg_data')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->uploadgg_data_model->getData($condition,$page);

        foreach ($files as &$v)
        {
            if ($v['atype'] == 1) {
                $info = $this->company_model->getInfo(['a.id'=>$v['adminid'],'b.id'=>$project_id]);
                $v['user_name'] = $info['principal_name'];
            }else{
                $info = $this->users_model->getUserInfo('a.id='.$v['adminid']);
                $v['user_name'] = $info['user_name'];
            }
        }
		if($principal['id']>0){
			$sign = $this->get_sign($principal['id']);
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'communication_plane',
				'project_id' => $principal['id'],
				'type' => 2,
				'ids' => 3,
				'number' => $sign['communication_count'],
			];
			$this->add_uploadreadlog($param);
		}
        $this->assign('sign',$sign);
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->display();
    }

    //规划、指标测算、设计方案
    public function design()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

        if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];

        //规划、指标测算、设计方案
       
        $condition['a.type'] = 4;
        $count = D('upload_data a')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->upload_data_model->getData($condition,$page);
		if($principal['id']>0){
			$sign = $this->get_sign($principal['id']);
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'confirm_design',
				'project_id' => $principal['id'],
				'type' => 2,
				'ids' => 4,
				'number' => $sign['design_count'],
			];
			$this->add_uploadreadlog($param);
			$this->assign('sign',$sign);
		}
       
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->display();
    }

    //平面规划方案
    public function scheme()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMIN_ID');
        $userInfo = $this->users_model->getUserInfo(['a.id'=>$id]);

       if($project_id)
		{
			$principal = M('project')->find($project_id);
		}else{
			$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->group('p.id')->order('s.create_time desc')->find();
			}else{
				$info=D()->field('p.*')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->group('p.id')->order('s.create_time desc')->find();
			}
			 $principal = $this->project_model->where("id='".$info['id']."'")->order('id desc')->find();
		}
		
		$finfo=D()->field('ru.*')->table(C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("r.name='负责人' and r.id=ru.role_id and ru.user_id='".session('ADMIN_ID')."'")->find();	
		if(session('ADMIN_ID')==1 || !empty($finfo))
		{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id  and p.isdel=0 and s.status=1")->order('s.create_time desc')->group('p.id')->select();
		}else{
			$project_ids=D()->field('p.*,s.status')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."project p")->where("p.id=s.project_id and s.role_name='方案师' and p.isdel=0 and s.status=1 and s.by_distribution='".session('ADMIN_ID')."'")->order('s.create_time desc')->group('p.id')->select();
		}
		

        //沟通记录文件
        $condition['project_id'] = $principal['id'];

        //平面规划
       
        $condition['a.type'] = 1;
        $count = D('upload_data a')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->upload_data_model->getData($condition,$page);

        //判断方案确认状态
        if ($this->upload_data_model->where(['project_id'=>$principal['id'],'type'=>4,'status'=>0])->find())
        {
            $status = 1;
        }else{
            $status = 0;
        }
		if($principal['id']>0){
			$sign = $this->get_sign($principal['id']);
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMIN_ID'),
				'modelname' => 'confirm_scheme',
				'project_id' => $principal['id'],
				'type' => 2,
				'ids' => 1,
				'number' => $sign['scheme_count'],
			];
			$this->add_uploadreadlog($param);
		}
        $this->assign('sign',$sign);
        $this->assign('project_ids',$project_ids);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->assign('status',$status);
        $this->display();
    }

    //保存沟通文件
    public function save_file()
    {
        if(IS_POST)
        {
            
            $pdata =I('post.');
      
            $pdata['adminid'] = session('ADMIN_ID');
            $pdata['createtime'] =time();
            $pdata['atype'] = 2;
            $lastid=M('uploadgg_data')->add($pdata);
            if($lastid)
            {
				log_insertresult(session('ADMIN_ID'),'平面图方案阶段','添加项目');
				// 推送沟通记录
				$p =M('project')->where("id='".$pdata['project_id']."'")->find();
				$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
				$info['project_name'] =$p['project_name'];
				$info['message'] ='您的项目有了新动态';
				$info['desc'] ='乙方已上传沟通记录文件，请尽快确认！';
				$this->sendTemplate($info);
                $this->ajaxReturn(array('status'=>0,'url'=>U($pdata['url'],array('project_id'=>$pdata['project_id']))));
            }else
            {
                $this->ajaxReturn(array('status'=>1));
            }
        }
    }
	 //保存上传文件
    public function saveFile()
    {
        if (IS_POST) {
            
            $post = I('post.');
            $post['adminid'] = session('ADMIN_ID');
            $post['create_time'] = time();
            $result = $this->upload_data_model->save_file_info($post);
            if ($result) {
               

                //更变当前项目的具体进度
                if ($post['type'] == 4) {                    
					 //更变当前项目进行的阶段
					$project = $this->project_model->where('id='.$post['project_id'])->find();
					if ($project['projectvalue'] < 4) {
						$this->project_model->where('id='.$project['id'])->save(['projectvalue'=>4]);						
					}
                }
                if ($post['type'] == 1) {
					
					$p =M('project')->where("id='".$post['project_id']."'")->find();
					if($p['projectvalue']<7)
					{
						 $this->change_stage($post['project_id'],7);
					}
					$info= M('account')->field('openid,user_name')->where("mobile='".$p['mobile']."' and atype=1")->find();
					$info['project_name'] =$p['project_name'];
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='乙方已上传方案文件，请尽快确认！';
					$this->sendTemplate($info);
                }

                if ($post['type'] == 5) {
                    $this->change_stage($post['project_id'],19);
                }
				log_insertresult(session('ADMIN_ID'),'上传施工图所需资料','添加项目');
                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
            }
        }
    }
	
	public function downloadbase()
	{
		$id=I('id','','intval');
		$info =M('upload_projectinfo')->where("id='$id'")->find();
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
	public function downloadfkfile()
	{
		$id=I('id','','intval');
		$info =M('upload_data')->where("id='$id'")->find();
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
	public function comdownloadfk()
	{
		$id=I('id','','intval');
		$info =M('uploadgg_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['murldata'];
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['mfilename'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	public function downloadmessage()
	{
		$id=I('id','','intval');
		$info =M('uploadgg_data')->where("id='$id'")->find();
        $url_file = SITE_PATH . '/' . $info['urldata'];
        if (file_exists($url_file))
        {
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['filename'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	public function downloadmessage5()
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
    //获取标签
    public function get_sign($project_id)
    {
        $sign['communication_count'] = $this->uploadgg_data_model->where(['project_id'=>$project_id,'type'=>3,'status2'=>['neq',0]])->count();//平面阶段所有审核后的沟通文件
        $sign['design_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>4,'status'=>0])->count();//已确认的设计方案
        $sign['scheme_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>1,'audit_status'=>0])->count();//已确认的平面规划方案

        $c_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='communication_plane' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 3")->order('id desc')->getField('number');
        $d_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='confirm_design' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 4")->order('id desc')->getField('number');
        $s_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='confirm_scheme' and adminid='".SESSION('ADMIN_ID')."' and type=2 and ids = 1")->order('id desc')->getField('number');

        $sign['communication'] = $sign['communication_count'] - $c_count;
        $sign['design'] = $sign['design_count'] - $d_count;
        $sign['scheme'] = $sign['scheme_count'] - $s_count;
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