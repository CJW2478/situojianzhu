<?php
namespace User\Controller;

use Common\Controller\HomebaseController;

class LoginController extends HomebaseController {
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
    // 前台用户登录
	public function index(){
	    if(sp_is_user_login()){ //已经登录时直接跳到首页
	        redirect(U('User/Center/index'));
	    }else{
            if(!$_SESSION['user']['openid'])
            {
                $str=urlencode('http://'.$_SERVER['HTTP_HOST'].'/index.php?g=User&m=Wexin&a=get_user');
                $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.self::APPID.'&redirect_uri='.$str.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
                header("location:$url");
            }
	        $this->display(":login");
	    }
	}
	
	 // 登录验证提交
    public function dologin(){
        $pdata=I('post.');
        if(!check_send_code($pdata['mobile'],trim($pdata['yzcode']))){
                $this->ajaxReturn(['status'=>1,'msg'=>'验证码错误']);
            }
        
        $users_model=M("commonusers");
        $result = $users_model->where("mobile='".$pdata['mobile']."'")->find();
      
        if(!empty($result)){


                if($result['type'] ==1)
                {
                    M('account')->where("id='".$result['id']."'")->save(array('openid'=>$_SESSION['user']['openid']));
                    $result =M('account')->where("id='".$result['id']."'")->find();
                    $usermodel =M('account');
                }else
                {
                    M('users')->where("id='".$result['id']."'")->save(array('openid'=>$_SESSION['user']['openid']));
                    $result =M('users')->where("id='".$result['id']."'")->find();
                    $usermodel =M('users');
                }
                session('user',$result);
                //写入此次登录信息
                $data = array(
                    'last_login_time' => date("Y-m-d H:i:s"),
                    'last_login_ip' => get_client_ip(0,true),
                );
                $users_model->where(array('id'=>$result["id"]))->save($data);              
                // 用户是否完善商家资料，跳转地址
                $redirect = U('User/Center/index');
                  
                $this->ajaxReturn(array('status'=>0,'msg'=>"登录成功！",'url'=>$redirect));
            
        }else{
            $this->ajaxReturn(array('status'=>1,'msg'=>"该用户不存在"));
        }
         
    }
}