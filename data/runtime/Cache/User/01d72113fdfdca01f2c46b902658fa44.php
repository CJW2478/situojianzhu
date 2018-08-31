<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
	    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
		<title>方案师介绍</title>
	    <link rel="stylesheet" href="/public/app/lib/weui/jquery-weui.css" />
	    <link rel="stylesheet" href="/public/app/lib/weui/weui.min.css" />
	    <link rel="stylesheet" href="/public/app/lib/swiper/swiper.min.css" />
	    <link rel="stylesheet" href="/public/app/css/public.css" />
	    <link rel="stylesheet" href="/public/app/css/style.css" />
	    <script type="text/javascript" src="/public/app/lib/jq/jquery-1.10.2.js" ></script>
	    <script type="text/javascript" src="/public/app/lib/weui/jquery-weui.js" ></script>
	    <script type="text/javascript" src="/public/app/js/v.min.js" ></script>
	    <script type="text/javascript" src="/public/app/js/common.js" ></script>
	</head>
	<body>
		<section class="mainSec" id="app">
			<div><?php echo ($info["resume"]); ?></div>
		</section>		
	</body>
</html>
<script>
	var app = new Vue({
		el:'#app',
		data:{
			mobile:'18725950959'
		},
		methods:{
			tabsFn:function(evt){
				var self=this;
				$(evt.target).addClass('active').siblings().removeClass('active');
			}
		}
	})
	/*window.onload = function() {
		var content=document.getElementByTagName('img');
		document.write(content);
		for(var i=0;i<content.length;i++){
			content[i].style.height="auto";
		}
	}*/
	
	
	$(function(){
		
		var bili=[];
		var imgs=$('.mainSec').find('img');
		console.log(imgs);
		/*for(var i=0;i<imgs.length;i++){
			var width=$(imgs[i]).width();
			var height=$(imgs[i]).height();
			bili[i]=width/height;
			console.log(width);
			console.log(height);
			console.log(bili);
		}
		$('.mainSec').find('*').css('max-width','100%');
		for(var j=0;j<imgs.length;j++){
			var width=$(imgs[j]).width();
			var height=width/bili[j];
			console.log(width);
			console.log(height);
			$(imgs[j]).height(height);
		}*/
		
	})
	
</script>