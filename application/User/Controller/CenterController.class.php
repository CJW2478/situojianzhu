<?php
namespace User\Controller;

use Common\Controller\MemberbaseController;

class CenterController extends MemberbaseController {
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
	function _initialize(){
		parent::_initialize();
	}
	
    // 会员中心首页
	public function index() {
		$this->assign($this->user);
		if(!$_SESSION['user']['id'])
		{
			redirect(U('User/Login/index'));
		}
		// 作为甲方创建用户
		if($_SESSION['user']['type'] ==1)
		{
			// 甲方负责人
			if($_SESSION['user']['atype'] ==1)
			{
				$project =M('project')->field('id,project_name')->where("mobile='".$_SESSION['user']['mobile']."'  and isdel=0")->order('create_time desc')->select();
			}else{
				$project =M('project')->field('id,project_name')->where("company_id='".$_SESSION['user']['company_id']."'  and isdel=0")->order('create_time desc')->select();
			}
				
		}else
		{			
			// 当前会员是否是乙方负责人
			$info = D()->field('ur.*')->table(C('DB_PREFIX').'role_user ur,'.C('DB_PREFIX')."role r")->where("ur.role_id=r.id and r.name='负责人' and ur.user_id='".$_SESSION['user']['id']."'")->find();			
			// 当前会员是否是乙方分配员
			$info2 = D()->field('ur.*')->table(C('DB_PREFIX').'role_user ur,'.C('DB_PREFIX')."role r")->where("ur.role_id=r.id and r.name='分配员' and ur.user_id='".$_SESSION['user']['id']."'")->find();
			if(!empty($info) || !empty($info2))
			{
				$project =M('project')->field('id,project_name')->where("isdel=0")->order('create_time desc')->select();
				
			}else
			{
				$project =D()->field('p.id,p.project_name')->table(C('DB_PREFIX')."project p,".C('DB_PREFIX')."solutions s")->where("p.id=s.project_id and s.by_distribution and s.by_distribution='".$_SESSION['user']['id']."' and  p.isdel=0")->order('p.create_time desc')->select();
				
			}			
		}
		
		if(count($project) > 0)
		{
			foreach ($project as $key => $value) {
	        	$project[$key]['url'] =U('Center/prdetail',array('id'=>$value['id']));
	        }
	        $this->assign('project', json_encode($project));
	        $this->assign('ids', $project[0]['id']);
		}
		
        if(count($project) == 1)
        {
           redirect(U('User/center/prdetail',array('id'=>$project[0]['id'])));
        }
        $this->assign('number',   count($project));
    	$this->display(':center');
    }
    // 项目详情
    public function prdetail()
    {
    	$id=I('id','','intval');
    	$pinfo =M('project')->find($id);
		
    	$pinfo['company_name'] =M('company')->where("id='".$pinfo['company_id']."'")->getField('company_name');
    	//  甲方会员
    	if($_SESSION['user']['type'] ==1)
    	{
    		if($_SESSION['user']['atype'] ==1)
			{
				$info['isfzr']=1;
				$info['id'] =$_SESSION['user']['id'];
				$info['typeall'] =1;
				$this->assign('info',$info);
			}	
    	}else
    	{
    		$info = D()->field('ur.*')->table(C('DB_PREFIX').'role_user ur,'.C('DB_PREFIX')."role r")->where("ur.role_id=r.id and r.name='负责人' and ur.user_id='".$_SESSION['user']['id']."'")->find();
			if(!empty($info))
			{
				$info['isfzr'] =1;
				$info['id'] =$info['user_id'];
				$info['typeall'] =2;
				$this->assign('info',$info);
			}
    	}
    	
    	// 乙方负责人信息
    	$uinfo =D()->field('u.id,u.mobile,u.user_name')->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."role_user ru,".C('DB_PREFIX')."role r")->where("u.id=ru.user_id and ru.role_id=r.id and r.name='负责人'")->order('u.create_time desc')->find();
    	
		// 项目乙方方案师
    	$fanganer =D()->field('u.id,u.mobile,u.user_name,u.qq_no,u.wx_no,u.resume')->table(C('DB_PREFIX')."solutions s,".C('DB_PREFIX')."users u")->where("s.by_distribution=u.id and s.project_id='$id' and s.role_name='方案师' and s.status=1")->order('s.create_time desc')->find();
    	

        //if($_SESSION['user']['id']==$)
        // 项目平面图阶段进度        
    	$pmlist[0]['step'] ='1';
    	$pmlist[0]['textvalue'] ='甲方上传基础资料';
    	$pmlist[1]['step'] ='2';
        $pmlist[1]['textvalue'] ='乙方分配方案师';
        $pmlist[2]['step'] ='3';
        $pmlist[2]['textvalue'] ='乙方负责人确认方案师分配';
        $pmlist[3]['step'] ='4';
        $pmlist[3]['textvalue'] ='方案师上传规划、指标、设计方向';
        $pmlist[4]['step'] ='5';
        $pmlist[4]['textvalue'] ='乙方负责人确认';
        $pmlist[5]['step'] ='6';
        $pmlist[5]['textvalue'] ='甲方收取文件并与方案师沟通';
        $pmlist[6]['step'] ='7';
        $pmlist[6]['textvalue'] ='方案师上传平面规划方案';
        $pmlist[7]['step'] ='8';
        $pmlist[7]['textvalue'] ='甲方确认方案';
        $pmlist[8]['step'] ='9';
        $pmlist[8]['textvalue'] ='乙方负责人确认方案';
        if($pinfo['projecttype']>1)
        {
        	$pmlist[9]['step'] =$pinfo['projectvalue'];
        	$pmlist[9]['textvalue'] ='第一阶段结束';
        }else
        {
        	$pmlist[9]['step'] ='10';
        	$pmlist[9]['textvalue'] ='第一阶段结束';
        }
	        
        
    	$pmlist2[0]['step'] ='11';
    	$pmlist2[0]['textvalue'] ='方案师上传项目风格图片';
    	$pmlist2[1]['step'] ='12';
        $pmlist2[1]['textvalue'] ='甲方选择项目风格';
        $pmlist2[2]['step'] ='13';
        $pmlist2[2]['textvalue'] ='方案师上传效果图方案';
        $pmlist2[3]['step'] ='14';
        $pmlist2[3]['textvalue'] ='乙方负责人确认';
        $pmlist2[4]['step'] ='15';
        $pmlist2[4]['textvalue'] ='甲方收取文件并与方案师沟通';
        $pmlist2[5]['step'] ='16';
        $pmlist2[5]['textvalue'] ='方案师上传修改后效果图方案';
        $pmlist2[6]['step'] ='17';
        $pmlist2[6]['textvalue'] ='甲方确认效果图方案';
        $pmlist2[7]['step'] ='18';
        $pmlist2[7]['textvalue'] ='乙方负责人确认';
        $pmlist2[8]['step'] ='19';
        $pmlist2[8]['textvalue'] ='方案师上传编制规划方案文本';
        $pmlist2[9]['step'] ='20';
        $pmlist2[9]['textvalue'] ='乙方负责人确认';
        $pmlist2[10]['step'] ='21';
        $pmlist2[10]['textvalue'] ='报请相关部门审批并修改通过';
        if($pinfo['projecttype']>2)
        {
        	$pmlist2[11]['step'] =22;
        	$pmlist2[11]['textvalue'] ='第二阶段结束';
        }else
        {
        	$pmlist2[11]['step'] ='22';
        	$pmlist2[11]['textvalue'] ='第二阶段结束';
        }        
        if($pinfo['projecttype']>2)
		{
			$this->assign('projectvalue',$pinfo['projectvalue']);			
		}else{
			$this->assign('projectvalue',$pinfo['projectvalue']+1);
		}
        
        $this->assign('stepList',json_encode($pmlist));
        $this->assign('stepList2',json_encode($pmlist2));
        // 平面图沟通记录文件   		
        $pmgglist1 = M('uploadgg_data')->field('id,createtime,adminid,filename,urldata,atype')->where("type=3 and status2=1 and project_id='$id' and atype=1")->order('createtime desc')->select();       
        $pmgglist2 = M('uploadgg_data')->field('id,mtime as createtime,adminid,mfilename as filename,murldata as urldata,atype')->where("type=3 and status2=1 and project_id='$id' and atype=2 and mfilename !=''")->order('mtime desc')->select();       
        
		$pmgglist=array_merge($pmgglist1,$pmgglist2);		
		$arrs=array();
		foreach($pmgglist as $val){
		   $arrs[] = $val['createtime'];
		   }
		array_multisort($arrs, SORT_DESC, $pmgglist);
		
		$pmggliststr =array();
        $people = array(".jpg", ".gif", ".png", ".jpeg");
        foreach($pmgglist as $k=>$v)
        {
        	$pmggliststr[$k]['id'] =$v['id'];
        	$pmggliststr[$k]['time'] =date('Y-m-d H:i',$v['createtime']);
			//if($v['atype'] ==1)
			//{
				$pmggliststr[$k]['person'] =$pinfo['principal_name'];
			//}else{				
			//	$pmggliststr[$k]['person'] =M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			//}
        	$pmggliststr[$k]['texts'] =$v['filename'];
        	
        	if(!in_array(strrchr($v['urldata'],'.'),$people))
        	{
        		$pmggliststr[$k]['img'] ='';
        	}else
        	{
        		$pmggliststr[$k]['img'] =$v['urldata'];
        	}
        	
        }
        // 效果图阶段沟通记录
        
		$pmgglist22 = M('uploadgg_data')->field('id,createtime,adminid,filename,urldata,atype')->where("type=4 and status2=1 and project_id='$id' and atype=1")->order('createtime desc')->select();       
        $pmgglist222 = M('uploadgg_data')->field('id,mtime as createtime,adminid,mfilename as filename,murldata as urldata,atype')->where("type=4 and status2=1 and project_id='$id' and atype=2 and mfilename !=''")->order('mtime desc')->select();       
        $pmgglist2=array_merge($pmgglist22,$pmgglist222);
		
		$arrs=array();
		foreach($pmgglist2 as $val){
		   $arrs[] = $val["createtime"];
		}
		array_multisort($arrs, SORT_DESC, $pmgglist2);
		$pmggliststr2 =array();
        foreach($pmgglist2 as $k=>$v)
        {
        	$pmggliststr2[$k]['id'] =$v['id'];
        	$pmggliststr2[$k]['time'] =date('Y-m-d H:i',$v['createtime']);
        	//if($v['atype'] ==1)
			//{
				$pmggliststr2[$k]['person'] =$pinfo['principal_name'];
			//}else{				
			//	$pmggliststr2[$k]['person'] =M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			//}
        	$pmggliststr2[$k]['texts'] =$v['filename'];
        	
        	if(!in_array(strrchr($v['urldata'],'.'),$people))
        	{
        		$pmggliststr2[$k]['img'] ='';
        	}else
        	{
        		$pmggliststr2[$k]['img'] =$v['urldata'];
        	}
        }
        // 施工图阶段沟通记录
        $pmgglist33 = M('uploadgg_data')->field('id,createtime,adminid,filename,urldata,atype')->where("type=1 and status2=1 and project_id='$id' and atype=1")->order('createtime desc')->select();       
        $pmgglist343 = M('uploadgg_data')->field('id,mtime as createtime,adminid,mfilename as filename,murldata as urldata,atype')->where("type=1 and status2=1 and project_id='$id' and atype=2 and mfilename !=''")->order('mtime desc')->select();       
        $sggglist3=array_merge($pmgglist33,$pmgglist343);
		$arrs=array();
		foreach($sggglist3 as $val){
		   $arrs[] = $val["createtime"];
		   }
		array_multisort($arrs, SORT_DESC, $sggglist3);
		$sgggliststr2 =array();
        foreach($sggglist3 as $k=>$v)
        {
        	$sgggliststr2[$k]['id'] =$v['id'];
        	$sgggliststr2[$k]['time'] =date('Y-m-d H:i',$v['createtime']);
        	//if($v['atype'] ==1)
			//{
				$sgggliststr2[$k]['person'] =$pinfo['principal_name'];
			//}else{				
			//	$sgggliststr2[$k]['person'] =M('users')->where("id='".$v['adminid']."'")->getField('user_name');
			//}
        	$sgggliststr2[$k]['texts'] =$v['filename'];
        	
        	if(!in_array(strrchr($v['urldata'],'.'),$people))
        	{
        		$sgggliststr2[$k]['img'] ='';
        	}else
        	{
        		$sgggliststr2[$k]['img'] =$v['urldata'];
        	}
        }
        $this->assign('List',json_encode($pmggliststr));       
        $this->assign('List2',json_encode($pmggliststr2));
        $this->assign('List3',json_encode($sgggliststr2));
        $doinglist =array();
        // 项目施工阶段进度
		$jzlist = M('solutions')->where("project_id='$id' and role_name='建筑专业' and status=1")->count();
        $jzlistinfo = M('project_doings')->where("project_id='$id' and rolename='建筑专业'")->order('createtime desc')->find();
        if($jzlist > 0)
        {
        	$doinglist[1]['name'] ='建筑专业';
        	$doinglist[1]['ids'] ='chart2';
			$mobile = M('users')->where("id='".$jzlistinfo['adminid']."'")->getField('mobile');
			if(!empty($mobile))
			{
				$doinglist[1]['mobile'] =$mobile;
			}else{
				$doinglist[1]['mobile'] =D()->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("u.id=s.by_distribution and s.role_name='建筑专业'  and s.project_id='$id' and s.status=1")->order('s.updatetime desc')->getField('u.mobile');
			}
        	
        	$doinglist[1]['percent'] =number_format($jzlistinfo['persont']);
        	$doinglist[1]['desc'] =$jzlistinfo['content'];
        }

        // 结构专业进度
		$jglist = M('solutions')->where("project_id='$id' and role_name='结构专业' and status=1")->count();
        $jglistinfo = M('project_doings')->where("project_id='$id' and rolename='结构专业'")->order('createtime desc')->find();
        if($jglist > 0)
        {
        	$doinglist[2]['name'] ='结构专业';
        	$doinglist[2]['ids'] ='chart3';
        	$mobile = M('users')->where("id='".$jglistinfo['adminid']."'")->getField('mobile');
			if(!empty($mobile))
			{
				$doinglist[2]['mobile'] =$mobile;
			}else{
				$doinglist[2]['mobile'] =D()->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("u.id=s.by_distribution and s.role_name='结构专业'  and s.project_id='$id' and s.status=1")->order('s.updatetime desc')->getField('u.mobile');
			}
        	$doinglist[2]['percent'] =number_format($jglistinfo['persont']);
        	$doinglist[2]['desc'] =$jglistinfo['content'];
        }
        // 设备专业进度
		$sblist = M('solutions')->where("project_id='$id' and role_name='设备专业' and status=1")->count();
        $sblistinfo = M('project_doings')->where("project_id='$id' and rolename='设备专业'")->order('createtime desc')->find();
        if($sblist>0)
        {
        	$doinglist[3]['name'] ='设备专业';
        	$doinglist[3]['ids'] ='chart4';
        	$mobile = M('users')->where("id='".$sblistinfo['adminid']."'")->getField('mobile');
			if(!empty($mobile))
			{
				$doinglist[3]['mobile'] =$mobile;
			}else{
				$doinglist[3]['mobile'] =D()->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("u.id=s.by_distribution and s.role_name='设备专业'  and s.project_id='$id' and s.status=1")->order('s.updatetime desc')->getField('u.mobile');
			}
        	$doinglist[3]['percent'] =number_format($sblistinfo['persont']);
        	$doinglist[3]['desc'] =$sblistinfo['content'];
        }
        // 电气专业进度
		$dqlist = M('solutions')->where("project_id='$id' and role_name='电气专业' and status=1")->count();
        $dqlistinfo = M('project_doings')->where("project_id='$id' and rolename='电气专业'")->order('createtime desc')->find();
        if($dqlist > 0)
        {
        	$doinglist[4]['name'] ='电气专业';
        	$doinglist[4]['ids'] ='chart5';
        	$mobile = M('users')->where("id='".$dqlistinfo['adminid']."'")->getField('mobile');
			if(!empty($mobile))
			{
				$doinglist[4]['mobile'] =$mobile;
			}else{
				$doinglist[4]['mobile'] =D()->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("u.id=s.by_distribution and s.role_name='电气专业'  and s.project_id='$id' and s.status=1")->order('s.updatetime desc')->getField('u.mobile');
			}
        	$doinglist[4]['percent'] =number_format($dqlistinfo['persont']);
        	$doinglist[4]['desc'] =$dqlistinfo['content'];
        }
         // 园林景观及总图
		$yllist = M('solutions')->where("project_id='$id' and role_name='园林景观及总图' and status=1")->count();
        $yllistinfo = M('project_doings')->where("project_id='$id' and rolename='园林景观及总图'")->order('createtime desc')->find();
		
        if($yllist > 0)
        {
        	$doinglist[5]['name'] ='园林景观及总图';
        	$doinglist[5]['ids'] ='chart6';
        	$mobile = M('users')->where("id='".$yllistinfo['adminid']."'")->getField('mobile');
			if(!empty($mobile))
			{
				$doinglist[5]['mobile'] =$mobile;
			}else{
				$doinglist[5]['mobile'] =D()->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("u.id=s.by_distribution and s.role_name='园林景观及总图'  and s.project_id='$id' and s.status=1")->order('s.updatetime desc')->getField('u.mobile');
			}
        	$doinglist[5]['percent'] =number_format($yllistinfo['persont']);
        	$doinglist[5]['desc'] =$yllistinfo['content'];
        }
		// 给排水专业
		$gpslist = M('solutions')->where("project_id='$id' and role_name='给排水专业' and status=1")->count();
        $gpslistinfo = M('project_doings')->where("project_id='$id' and rolename='给排水专业'")->order('createtime desc')->find();
		
        if($gpslist > 0)
        {
        	$doinglist[6]['name'] ='给排水专业';
        	$doinglist[6]['ids'] ='chart7';
        	$mobile = M('users')->where("id='".$gpslistinfo['adminid']."'")->getField('mobile');
			if(!empty($mobile))
			{
				$doinglist[6]['mobile'] =$mobile;
			}else{
				$doinglist[6]['mobile'] =D()->table(C('DB_PREFIX')."users u,".C('DB_PREFIX')."solutions s")->where("u.id=s.by_distribution and s.role_name='给排水专业'  and s.project_id='$id' and s.status=1")->order('s.updatetime desc')->getField('u.mobile');
			}
        	$doinglist[6]['percent'] =number_format($gpslistinfo['persont']);
        	$doinglist[6]['desc'] =$gpslistinfo['content'];
        }
		if(count($doinglist) != 0)
		{
			$mcount =count($doinglist);
		}
		
		$doinglist[0]['name'] ='总进度';
        $doinglist[0]['ids'] ='chart1';
        $doinglist[0]['percent'] =number_format(($doinglist[1]['percent']+$doinglist[2]['percent']+$doinglist[3]['percent']+$doinglist[4]['percent']+$doinglist[5]['percent']+$doinglist[6]['percent'])/$mcount);
        ksort($doinglist);
		//file_put_contents('a90990.txt',var_export($doinglist,true));
        $doinglist=array_values($doinglist);
        $this->assign('doinglist',json_encode($doinglist));
		// 甲方负责人
		if($info['isfzr']==1 && $info['typeall']==1)
		{
			if($pinfo['nexttime']<= time())
			{
				M('project')->where("id='".$pinfo['id']."'")->setField('isjiaji',0);
				$pinfo['isjiaji']=0;
			}
		}
        // 乙方负责人
		if($info['isfzr']==1 && $info['typeall']==2)
		{
			if($pinfo['nexttime2']<= time())
			{
				M('project')->where("id='".$pinfo['id']."'")->setField('isjiaji2',0);
				$pinfo['isjiaji']=0;
			}
		}
		// 客服电话
        $mobile=M('options')->where("option_name='mobile_setting'")->getField('option_value');
        $this->assign('mobile',$mobile);
    	$this->assign('pinfo',$pinfo);
    	$this->assign('fanganer',$fanganer);
    	$this->assign('uinfo',$uinfo);
    	$this->assign('id',      $id);
    	$this->assign('jiaji', $pinfo['isjiaji']);
    	$this->display(':prdetail');
    }
    // 方案师简历
    public function jianli()
    {
    	$id=I('id','','intval');
    	$info =M('users')->field('id,resume')->find($id);
    	$this->assign('info',$info);
    	$this->display(':jianli');
    }

    public function changejiaji()
    {
    	if(IS_POST)
    	{
    		$projectid =I('projectid','','intval');
    		$type=I('type','','intval');
    		$user_id =I('user_id','','intval');
			if($type==1){
				$res=M('project')->where("id='$projectid'")->save(array('isjiaji'=>1,'nexttime'=>time()+3600*24));
			}else{
				$res=M('project')->where("id='$projectid'")->save(array('isjiaji2'=>1,'nexttime2'=>time()+3600*24));
			}
    		if($res)
    		{
    			// 模版推送 甲方推送给乙方
    			if($type==1)
    			{
    				$info = D()->field('u.openid,u.id,u.mobile,u.user_name')->table(C('DB_PREFIX').'role_user ur,'.C('DB_PREFIX')."role r,".C('DB_PREFIX')."users u")->where("ur.role_id=r.id and r.name='负责人' and ur.user_id=u.id")->order('u.create_time desc')->find();
					$info['project_name'] =M('project')->where("id='".$projectid."'")->getField('project_name');
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='该项目被要求加急，请尽快推进该项目！';					
					$this->sendTemplate($info);
    			}else
    			{
					$pinfo =M('project')->find($projectid);
					$info['openid'] = M('account')->where("mobile='".$pinfo['mobile']."' and atype=1")->getField('openid');
					$info['user_name'] = $pinfo['principal_name'];
					$info['project_name'] =$pinfo['project_name'];
					$info['message'] ='您的项目有了新动态';
					$info['desc'] ='该项目被要求加急，请尽快推进该项目！';					
					$this->sendTemplate($info);
    			}
    			$this->ajaxReturn(array('status'=>0));
    		}
    	}
    }

    // 加急通知推送
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
