<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
	    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
		<title>流程展示</title>
	    <!--<link rel="stylesheet" href="/public/app/lib/weui/jquery-weui.css" />
	    <link rel="stylesheet" href="/public/app/lib/weui/weui.min.css" />-->
		<link rel="stylesheet" href="/public/app/lib/viewer/views_min.css" />
	    <link rel="stylesheet" href="/public/app/css/public.css" />
	    <link rel="stylesheet" href="/public/app/css/style.css" />
		<!--<script type="text/javascript" src="http://jqweui.com/dist/lib/jquery-2.1.4.js" ></script>
		<script type="text/javascript" src="http://jqweui.com/dist/js/jquery-weui.js" ></script>
	    <script type="text/javascript" src="/public/app/js/fixed.js" ></script>
	    <script type="text/javascript" src="/public/app/js/common.js" ></script>
		
		<script type="text/javascript" src="http://jqweui.com/dist/lib/fastclick.js" ></script>
		
		<script type="text/javascript" src="/public/app/js/swiper.js" ></script>-->
	    <script type="text/javascript" src="/public/app/lib/jq/jquery-1.10.2.js" ></script>
	    <script type="text/javascript" src="/public/app/lib/viewer/views.js" ></script>
	    <script type="text/javascript" src="/public/app/js/v.min.js" ></script>
	</head>
	<style>
		<!--.swiper-slide img{ height:auto}-->
		.viewer-backdrop{ background-color: rgba(0,0,0,1);}
		.viewer-footer{ display: none;}
	</style>
	<body>
		<section class="mainSec" id="app">
			<div class="tabs clearfix">
				<div class="active" @click="tabsFn($event,1)">平面图方案阶段</div>
				<div @click="tabsFn($event,2)">效果图方案阶段</div>
				<div @click="tabsFn($event,3)">施工图方案阶段</div>
			</div>
			<div v-show='type==1' class="pt2rem contentBox">
				<?php if(is_array($article)): $i = 0; $__LIST__ = $article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i; if($va['id'] == 1): ?><div class="pb1rem"><?php echo ($va["content"]); ?></div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div v-show='type==2' class="pt2rem contentBox">
				<?php if(is_array($article)): $i = 0; $__LIST__ = $article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i; if($va['id'] == 2): ?><div class="pb1rem"><?php echo ($va["content"]); ?></div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div v-show='type==3' class="pt2rem contentBox">
				<?php if(is_array($article)): $i = 0; $__LIST__ = $article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i; if($va['id'] == 3): ?><div class="pb1rem"><?php echo ($va["content"]); ?></div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<a :href="hrefs" class="callService"></a>
		</section>
		
	</body>
</html>
<script>
	var app = new Vue({
		el:'#app',
		data:{
			mobile:'<?php echo ($mobile); ?>',
			hrefs:'tel:'+'<?php echo ($mobile); ?>',
			type:1
		},
		methods:{
			tabsFn:function(evt,type){
				var self=this;
				self.type=type;
				$(evt.target).addClass('active').siblings().removeClass('active');
			}
		}
	})
	$(function(){
		$('.contentBox').each(function(){
			$(this).find('*').css('max-width','100%');
		})
		
	})
	//点击图片放大
	/*$('body img').on('click',function(){
		var preview = $.photoBrowser({
		  items: [
			$(this).attr('src')
		  ],
		  onClose:function(){
			preview.close();
		  }
		});
		preview.open();
	})*/
	var viewer2 = new Viewer(document.getElementById('app'))
</script>