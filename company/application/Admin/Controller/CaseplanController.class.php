<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/5
 * Time: 16:39
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
use Common\Model\CommonModel;
class CaseplanController extends AdminbaseController
{
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
    protected $project_model,$company_model,$solution_model,$upload_projectinfo,$users_model,$uploadgg_data_model,$upload_data_model;
    public function _initialize() {
        parent::_initialize();
        $this->project_model = D("Common/Project");
        $this->company_model = D("Common/Company");
        $this->solution_model = D("Common/Solutions");
        $this->upload_projectinfo = D("Common/UploadProjectinfo");
        $this->users_model = D("Common/Users");
        $this->uploadgg_data_model = D("Common/UploadggData");
        $this->upload_data_model = D("Common/UploadData");
    }

    //项目基本信息
    public function index()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMINID');

        $where['company_id'] = $id;
        $where['isdel'] = 0;
        //获取该公司下所有项目列表
        $projects = $this->project_model->where($where)->order('id desc')->select();

        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();

        //方案师基本信息
        $solution = $this->solution_model->getData(['a.project_id'=>$principal['id'],'a.status'=>1,'a.role_id'=>CommonModel::USER_SOLUTIONS]);

        //甲方项目信息
        $condition['a.project_id'] = $principal['id'];
        $condition['a.type'] = 1;
        $data_count = D('upload_projectinfo a')->where($condition)->count();
        $data_page = $this->page($data_count,8);
        $data = $this->upload_projectinfo->getProject($condition,$data_page);
		$sign = $this->get_sign($principal['id']);
		
        $this->assign('sign',$sign);
        $this->assign('solution',$solution[0]);
        $this->assign('data',$data);
        $this->assign('data_page',$data_page->show('Admin'));
        $this->assign('project_ids',$projects);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$data_count);
        $this->display();
    }

    //沟通记录文件
    public function communication()
    {
        $project_id = I('get.project_id',0,'intval');
        $id = session('ADMINID');

        if(IS_POST)
        {
            $pdata =I('post.');
            if($_FILES['file']['name'] != '')
            {
                $pdata['mfilename'] =$_FILES['file']['name'];
                $pdata['murldata'] = uploadOne($_FILES['file'],'message');
                if($pdata['murldata'])
                {
                    $pdata['murldata']='./company/'.$pdata['murldata'];
                }				
            }
			$pdata['adminid2'] =session('ADMINID');
			$pdata['mtime']=time();
            $this->uploadgg_data_model->save($pdata);
            redirect(U('Caseplan/communication',array('project_id'=>$pdata['project_id'])));
        }

        $where['company_id'] = $id;
        $where['isdel'] = 0;
        //获取该公司下所有项目列表
        $projects = $this->project_model->where($where)->order('id desc')->select();

        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();

        //方案师基本信息
        $solution = $this->solution_model->getData(['a.project_id'=>$principal['id'],'a.status'=>1,'a.role_id'=>CommonModel::USER_SOLUTIONS]);

        //沟通记录文件
        $condition['project_id'] = $principal['id'];
        $condition['type'] = 3;
        $count = $this->uploadgg_data_model->where($condition)->count();
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
			if($v['murldata'])
			{
				$v['murldata'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$v['murldata'];
			}
        }
		if($principal['id']>0){
			$sign = $this->get_sign($principal['id']);
			// 分配信息写入标记表
			$param = [
				'adminid' => session('ADMINID'),
				'modelname' => 'communication_plane',
				'project_id' => $principal['id'],
				'type' => 1,
				'ids' => 3,
				'number' => $sign['communication_count'],
			];
			$this->add_uploadreadlog($param);
			
			$this->assign('sign',$sign);
		}
        $this->assign('solution',$solution[0]);
        $this->assign('project_ids',$projects);
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
        $id = session('ADMINID');

        if(IS_POST)
        {
            $pdata =I('post.');
            if($_FILES['filename']['name'] != '')
            {
                $pdata['mfile_name'] =$_FILES['filename']['name'];
                $pdata['mfile_url'] = uploadOne($_FILES['filename'],'message');
                if($pdata['mfile_url'])
                {
                    $pdata['mfile_url']='./company/'.$pdata['mfile_url'];
                }
            }

            $this->upload_data_model->save($pdata);
            redirect(U('Caseplan/design',array('project_id'=>$pdata['project_id'])));
        }

        $where['company_id'] = $id;
        $where['isdel'] = 0;
        //获取该公司下所有项目列表
        $projects = $this->project_model->where($where)->order('id desc')->select();

        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project_id;

        //方案师基本信息
        $solution = $this->solution_model->getData(['a.project_id'=>$principal['id'],'a.status'=>1,'a.role_id'=>CommonModel::USER_SOLUTIONS]);

        //规划、指标测算、设计方案
        $condition['a.project_id'] = $project_id;
        $condition['a.type'] = 4;
        $condition['a.status'] = 0;
        $count = D('upload_data a')->where($condition)->count();
        $page = $this->page($count, 8);
        $files = $this->upload_data_model->getData($condition,$page);

		$sign = $this->get_sign($principal['id']);
		
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMINID'),
            'modelname' => 'new_design',
            'project_id' => $principal['id'],
            'type' => 1,
            'ids' => 4,
            'number' => $sign['design_count'],
        ];
        $this->add_uploadreadlog($param);

        $this->assign('sign',$sign);
        $this->assign('solution',$solution[0]);
        $this->assign('project_ids',$projects);
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
        $id = session('ADMINID');

        if(IS_POST)
        {
            $pdata =I('post.');
            if($_FILES['filename']['name'] !='')
            {
                $pdata['mfile_name'] =$_FILES['filename']['name'];
                $pdata['mfile_url'] = uploadOne($_FILES['filename'],'message');
                if($pdata['mfile_url'])
                {
                    $pdata['mfile_url']='./company/'.$pdata['mfile_url'];
                }
            }

            $this->upload_data_model->save($pdata);
			$p =M('project')->find($pdata['project_id']);
			if($p['projectvalue'] <8 && $pdata['audit_status']==0)
			{
				$this->change_stage($pdata['project_id'],8);
			}            
            redirect(U('Caseplan/scheme',array('project_id'=>$pdata['project_id'])));
        }

        $where['company_id'] = $id;
        $where['isdel'] = 0;
        //获取该公司下所有项目列表
        $projects = $this->project_model->where($where)->order('id desc')->select();

        if ($project_id) {
            $where['id'] = $project_id;
        }
        //当前项目信息
        $principal = $this->project_model->where($where)->order('id desc')->find();
        $condition['a.project_id'] = $project_id;

        //方案师基本信息
        $solution = $this->solution_model->getData(['a.project_id'=>$principal['id'],'a.status'=>1,'a.role_id'=>CommonModel::USER_SOLUTIONS]);

        //平面规划
        $condition['a.project_id'] = $principal['id'];
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
		
		$sign = $this->get_sign($principal['id']);
        // 分配信息写入标记表
        $param = [
            'adminid' => session('ADMINID'),
            'modelname' => 'new_scheme',
            'project_id' => $principal['id'],
            'type' => 1,
            'ids' => 1,
            'number' => $sign['scheme_count'],
        ];
        $this->add_uploadreadlog($param);

        $this->assign('sign',$sign);
        $this->assign('solution',$solution[0]);
        $this->assign('project_ids',$projects);
        $this->assign('project_id',$project_id);
        $this->assign('principal',$principal);
        $this->assign('count',$count);
        $this->assign('page',$page->show('Admin'));
        $this->assign('files',$files);
        $this->assign('status',$status);
        $this->display();
    }

    //保存项目基本信息
    public function save_project_info()
    {
        if (IS_POST) {
            $post = I('post.');
            $post['adminid'] = session('ADMINID');
            $post['create_time'] = time();
            $result = $this->upload_projectinfo->add($post);			
            if ($result) {
				$p=M('project')->where("id='".$post['project_id']."'")->find();
				if($p['projectvalue'] ==0)
				{
					M('project')->where("id='".$post['project_id']."'")->setField('projectvalue',1);
				}
				// 推送消息给分配员
				$info = D()->field('u.openid,u.id,u.user_name')->table(C('DB_PREFIX').'role_user ur,'.C('DB_PREFIX')."role r,".C('DB_PREFIX')."users u")->where("ur.role_id=r.id and r.name='分配员' and ur.user_id=u.id")->find();
				$info['message'] ='您的项目有了新动态';
				$info['desc'] ='甲方已上传项目基本资料，请尽快分配方案师';
				$info['project_name'] =$p['project_name'];
				$this->sendTemplate($info);
				
                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
            }
        }
    }

    //保存交流文件
    public function save_communication()
    {
        if (IS_POST) {
            $post = I('post.');
            $post['adminid'] = session('ADMINID');
            $post['createtime'] = time();
            $post['status2'] = 1;
            $result = $this->uploadgg_data_model->add($post);

            if ($result) {
                //$this->change_stage($post['project_id'],6);
                $this->ajaxReturn(array('status' =>0,'url'=>U($post['url'],array('project_id'=>$post['project_id']))));
            }else{
                $this->ajaxReturn(array('status' =>1,'msg'=>'上传失败！'));
            }
        }
    }
	
	//下载文件
	public function downloadfamessage()
	{
		$info = I('get.');
        $url_file = SITE_PATH . '/' . $info['file_url'];
        $url_file =str_replace('\company','',$url_file); 
        if (file_exists($url_file))
        {
			$p =M('project')->find($info['project_id']);
			if($p['projectvalue'] < 6)
			{
				$this->change_stage($info['project_id'],6);
			}
            header('Content-type: application/unknown');
            header('Content-Disposition: attachment; filename="' . $info['file_name'] . '"');
            header("Content-Length: " . filesize($url_file) . "; ");
            readfile($url_file);
        }else{
        	$this->error('下载出错');
        }
	}
	//下载文件
	public function downloadfamessage51()
	{
		$pdata = I('get.');
		$info =M('upload_data')->find($pdata['id']);
        $url_file = SITE_PATH . '/' . $info['mfile_url'];
        $url_file =str_replace('\company','',$url_file); 
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
	public function downloadggfkfile()
	{
		$id = I('id','','intval');
		$info =M('uploadgg_data')->find($id);
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
	//获取标签
    public function get_sign($project_id)
    {
        $sign['communication_count'] = $this->uploadgg_data_model->where(['project_id'=>$project_id,'type'=>3,'status2'=>0])->count();//平面阶段未审核沟通文件
        $sign['design_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>4,'status'=>0])->count();//新增设计方案
        $sign['scheme_count'] = $this->upload_data_model->where(['project_id'=>$project_id,'type'=>1,'audit_status'=>1])->count();//新增平面规划方案

        $c_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='communication_plane' and adminid='".SESSION('ADMINID')."' and type=1 and ids = 3")->order('id desc')->getField('number');
        $d_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='new_design' and adminid='".SESSION('ADMINID')."' and type=1 and ids = 4")->order('id desc')->getField('number');
        $s_count=M('uploadreadlog')->where("project_id='".$project_id."' and modelname='new_scheme' and adminid='".SESSION('ADMINID')."' and type=1 and ids = 1")->order('id desc')->getField('number');

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