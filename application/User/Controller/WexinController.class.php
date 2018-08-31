<?php
/**
 * 微信授权登录
 */
namespace User\Controller;
use Think\Controller;
class WexinController extends Controller {
   
	const APPID ='wx41966cffe0d3ace1';
    const APPSECRET='34c04270d8e080b3ba9c5f360354fedd';
    const TOKEN = "weixin";
    public function get_user()
    {
        $wechatObj = new \Think\WeChat(self::APPID,self::APPSECRET,self::TOKEN);
        $code=$_GET['code'];
        $openidarr=$wechatObj->get_snsapi_base($code);
        
        if($openidarr['scope']=='snsapi_base'){
            dump($openidarr['openid']);
        }
        $access_token=$openidarr['access_token'];
        $openid=$openidarr['openid'];
                
        if($openidarr['scope']=='snsapi_userinfo'){           
            $info=$wechatObj->get_snsapi_userinfo($access_token, $openid); 
        }   
        //$userinfo = M('commonusers')->where("openid= '" . $info['openid'] . "'")->find();		
        //if($userinfo)
       // {
        //    if($userinfo['type']==1)
        //    {
        //        $info =M('account')->where("id='".$userinfo['id']."'")->find();
        //    }else
        //    {
        //       $info =M('users')->where("id='".$userinfo['id']."'")->find();
        //    }
        //    $_SESSION['user'] =$info;			
        //    redirect(U('User/center/index'));exit();
        //}
        //else
        //{             
			
            $_SESSION['user']['openid'] =$info['openid']; 			
			redirect(U('User/login/index'));exit();			
        //}        
    }
}