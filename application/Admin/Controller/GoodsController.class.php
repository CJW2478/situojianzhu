<?php
// +----------------------------------------------------------------------
// | ThinkCMF 客户管理板块
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GoodsController extends AdminbaseController {
	// 后台客户管理列表
	public function index(){				
		$where=array();	
		$this->goods =M('goods');
		$keyword=I('keyword','','trim');
		if(!empty($keyword)){
		    $where['goodsname']=array('like',"%$keyword%");
		}		
		$count=$this->goods->where($where)->count();			
		$page = $this->page($count, 20);			
		$goods=$this->goods
				->where($where)
				->limit($page->firstRow , $page->listRows)
				->order('sortorder asc')
				->select();
        foreach ($goods as $key => $value) {
            $goods[$key]['catname'] = M('category')->where("id='".$value['catid']."'")->getField('catname');
        }
		$this->assign("page", $page->show('Admin'));
		$this->assign("formget",array_merge($_GET,$_POST));
		$this->assign("list",$goods);
		$this->display();
	}	
    public function add()
    {
        $catlist =M('category')->where("status=1")->select();
        $this->assign('catlist',$catlist);
        $this->display();
    }
    public function add_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['goodsname']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请输入商品名称'));
            }
            if(empty($pdata['thumb_img']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请上传缩略图'));
            }
            if(empty($pdata['catid']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请选择商品分类'));
            }
            if(!empty($pdata['photos_alt']) && !empty($pdata['photos_url'])){
                foreach ($pdata['photos_url'] as $key=>$url){
                    
                    $_POST['smeta']['photo'][]=array("url"=>$url,"alt"=>$pdata['photos_alt'][$key]);
                }
            }
            $pdata['createtime']=time();  
            $pdata['goodsdesc'] =strcontentjs(htmlspecialchars_decode($pdata['goodsdesc']));
            $pdata['imgs']=json_encode($_POST['smeta']);         
            $result= M('goods')->add($pdata);
            if($result)
            {
                $this->ajaxReturn(array('status'=>0,'msg'=>'添加成功'));            
            }else
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'添加失败')); 
            }
        }
    }
    public function edit()
    {
        $id =I('id','','intval');
        if($id)
        {
            $goods =M('goods')->find($id);
            if($goods['thumb_img'])
            {
                $goods['thumb_img2'] ='http://'.$_SERVER['HTTP_HOST'].'/'.$goods['thumb_img'];
            }
            $this->assign("imgs",json_decode($goods['imgs'],true));
            $this->assign('goods',$goods);
            $catlist =M('category')->where("status=1")->select();
            $this->assign('catlist',$catlist);
            $this->display();
        }
    }
   
    public function edit_post()
    {
        if(IS_POST)
        {
            $pdata =I('post.');
            if(empty($pdata['goodsname']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请输入商品名称'));
            }
            if(empty($pdata['thumb_img']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请上传缩略图'));
            }
            if(empty($pdata['catid']))
            {
                $this->ajaxReturn(array('status'=>1,'msg'=>'请选择商品分类'));
            }
            if(!empty($pdata['photos_alt']) && !empty($pdata['photos_url'])){
                foreach ($pdata['photos_url'] as $key=>$url){
                    
                    $_POST['smeta']['photo'][]=array("url"=>$url,"alt"=>$pdata['photos_alt'][$key]);
                }
            }
            $pdata['goodsdesc'] =strcontentjs(htmlspecialchars_decode($pdata['goodsdesc']));
            $pdata['imgs']=json_encode($_POST['smeta']);       
            
            $result= M('goods')->where("id='".$pdata['id']."'")->save($pdata);
           
            $this->ajaxReturn(array('status'=>0,'msg'=>'编辑成功'));            
            
        }
    }
    public function delete(){
        if(isset($_GET['id'])){
            $id = I("get.id",0,'intval');
            if (M('goods')->where(array('id'=>$id))->delete() !==false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        
    }
    public function ban(){
        $id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('goods')->where(array("id"=>$id))->setField('issale','0');
    		if ($result!==false) {
    			$this->success("下架成功！", U("goods/index"));
    		} else {
    			$this->error('下架失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    public function cancelban(){
    	$id = I('get.id',0,'intval');
    	if (!empty($id)) {
    		$result = M('goods')->where(array("id"=>$id))->setField('issale','1');
    		if ($result!==false) {
    			$this->success("上架成功！", U("goods/index"));
    		} else {
    			$this->error('上架失败！');
    		}
    	} else {
    		$this->error('数据传入失败！');
    	}
    }
    
	
}