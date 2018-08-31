<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
	    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
	    <meta name="format-detection" content="telephone=no,email=no,date=no,address=no">
		<title>进度展示</title>
	    <link rel="stylesheet" href="/public/app/lib/weui/jquery-weui.css" />
	    <link rel="stylesheet" href="/public/app/lib/weui/weui.min.css" />
	    <link rel="stylesheet" href="/public/app/css/public.css" />
	    <link rel="stylesheet" href="/public/app/css/style.css" />
		<script type="text/javascript" src="http://jqweui.com/dist/lib/jquery-2.1.4.js" ></script>
		<script type="text/javascript" src="http://jqweui.com/dist/js/jquery-weui.js" ></script>
	    <script type="text/javascript" src="/public/app/js/v.min.js" ></script>
	    <script type="text/javascript" src="/public/app/js/fixed.js" ></script>
	    <script type="text/javascript" src="/public/app/js/echarts.common.min.js" ></script>
	    <script type="text/javascript" src="/public/app/js/common.js" ></script>
	
		<script type="text/javascript" src="http://jqweui.com/dist/lib/fastclick.js" ></script>
		
		<script type="text/javascript" src="/public/app/js/swiper.js" ></script>
	</head>
	<style>
		.weui-photo-browser-modal .photo-container img{ height: auto;max-height:100%; margin:20px auto}
		.weui-photo-browser-modal .swiper-container .swiper-pagination,
		.weui-photo-browser-modal .swiper-container-visible .caption{ display:none}
		.swiper-container.slideBox{ z-index: 0; padding-bottom:1rem}
		.opacity0{ opacity:0}
		.speedbox{
			-webkit-tap-highlight-color: rgba(0,0,0,0);
			-webkit-tap-highlight-color: transparent;
		}
	</style>
	<body>
		<section class="mainSec" id="app">
			<div class="banner">
				<?php if($info['isfzr'] == 1): ?><div class="jiaji" :class="isjiaji?'active':''" @click="jiaji($event,'<?php echo ($id); ?>','<?php echo ($info["id"]); ?>','<?php echo ($info["typeall"]); ?>')" v-text="jiajitext"></div><?php endif; ?>
				<img src="/public/app/img/banner@2x.png" class="none" />
				<div class="projectInfo">
					<p class="tit"><?php echo ($pinfo["project_name"]); ?></p>
					<p><?php echo ($pinfo["company_name"]); ?></p>
					<p><?php echo ($pinfo["project_no"]); ?></p>
				</div>
			</div>
			<div class="tabs clearfix" style="margin: 1.5rem 0;">
				<div class="active" @click="tabsFn($event,1)">平面图方案阶段</div>
				<div @click="tabsFn($event,2)">效果图方案阶段</div>
				<div @click="tabsFn($event,3)">施工图阶段</div>
			</div>
			<div class="contactBox disbox" v-show="state==1||state==2">
				<div>
					<!--<div class="tableBox">-->
						<div class="contentBox">
							<p>乙方负责人</p>
							<div class="names"><?php echo ($uinfo["user_name"]); ?><img src="/public/app/img/xiaoboda@2x.png" onclick="call(this,'<?php echo ($uinfo["mobile"]); ?>')" /></div>
						</div>
					<!--</div>-->
				</div>
				<div class="disflex">
					<!--<div class="tableBox">-->
						<div class="contentBox">
							<?php if($fanganer['user_name'] != ''): ?><p>乙方方案师 
								<?php if($fanganer['resume'] != ''): ?><a href="<?php echo U('User/Center/jianli',array('id'=>$fanganer['id']));?>" class="jianli" v-show="hasjl">(查看简历)</a><?php endif; ?>
							</p>
							<div class="names"><?php echo ($fanganer["user_name"]); ?><img src="/public/app/img/xiaoboda@2x.png" onclick="call(this,'<?php echo ($fanganer["mobile"]); ?>')" /></div>
							<p>QQ号:<?php echo ($fanganer["qq_no"]); ?></p>
							<p>微信号:<?php echo ($fanganer["wx_no"]); ?></p>
							<?php else: ?>待分配确认方案师<?php endif; ?>
						</div>
					<!--</div>-->
				</div>
			</div>
			<div class="speedbox">
				<div class="tit"><span v-text="stepName"></span>进度</div>
				<div class="speed1" v-show="state==1" :class="state!=1?'opacity0':''">
					<div class="swiper-container swiper-container1 slideBox">
						<ul class="swiper-wrapper">
							<li class="swiper-slide" v-for="prop in stepList" :class="step1==(prop.step>10?10:prop.step)?'active':''">
								<p v-text="prop.textvalue"></p>
							</li>
							<!--<li class="swiper-slide" v-for="prop in stepList2" :class="{'active':step==prop.step,'opacity0':state!=2}" v-text="prop.textvalue"></li>-->
						</ul>
					</div>
				</div>
				<div class="speed2" v-show="state==2" :class="state!=2?'opacity0':''">
					<div class="swiper-container swiper-container2 slideBox">
						<ul class="swiper-wrapper">
							<li class="swiper-slide" v-for="prop in stepList2" :class="step2==(prop.step>10?prop.step-10:0)?'active':''">
								<p v-text="prop.textvalue"></p>
							</li>
						</ul>
					</div>
				</div>
				<div class="chartBox" v-show="state==3">
					<div class="chartItem" v-for="prop,index in step3List">
						<div class="calls" @click="call($event,prop.mobile)"></div>
						<div class="chart" :id="prop.ids"></div>
						<div class="texts" v-text="prop.name" @click="speed($event,prop.desc,index)"></div>
					</div>
				</div>
			</div>
			<div class="listBox">
				<div class="tit"><span v-text="stepName"></span>沟通记录</div>
				<div class="xmList">
					<ul v-show="state==1">
						<div v-if="list.length<1" style="text-align:center; padding-top:1rem">暂无沟通记录~</div>
						<li v-for="prop in list" style="padding: 10px;background-image: none;">
							<div class="yulan" @click="yulan($event,prop.img)">预览</div>
							<div class="disbox" style="margin-left: 5rem;">
								<span class="disflex" style="color: #999; font-size: 1.1rem;" v-text="prop.time"></span>
								<span style="color: #000; font-size: 1.1rem;">上传人 : <span v-text="prop.person"></span></span>
							</div>
							<div class="Line3" style="padding-top: 10px;color: #666;" v-text="prop.texts"></div>
						</li>
					</ul>
					<ul v-show="state==2">
						<div v-if="list2.length<1" style="text-align:center; padding-top:1rem">暂无沟通记录~</div>
						<li v-for="prop in list2" style="padding: 10px;background-image: none;">
							<div class="yulan" @click="yulan($event,prop.img)">预览</div>
							<div class="disbox" style="margin-left: 5rem;">
								<span class="disflex" style="color: #999; font-size: 1.1rem;" v-text="prop.time"></span>
								<span style="color: #000; font-size: 1.1rem;">上传人 : <span v-text="prop.person"></span></span>
							</div>
							<div class="Line3" style="padding-top: 10px;color: #666;" v-text="prop.texts"></div>
						</li>
					</ul>
					<ul v-show="state==3">
						<div v-if="list3.length<1" style="text-align:center; padding-top:1rem">暂无沟通记录~</div>
						<li v-for="prop in list3" style="padding: 10px;background-image: none;">
							<div class="yulan" @click="yulan($event,prop.img)">预览</div>
							<div class="disbox" style="margin-left: 5rem;">
								<span class="disflex" style="color: #999; font-size: 1.1rem;" v-text="prop.time"></span>
								<span style="color: #000; font-size: 1.1rem;">上传人 : <span v-text="prop.person"></span></span>
							</div>
							<div class="Line3" style="padding-top: 10px;color: #666;" v-text="prop.texts"></div>
						</li>
					</ul>
				</div>
			</div>
			<div class="callService" @click="call(this,mobile)"></div>
			<div v-show="speedclick" class="meng"></div>
			<div v-show="speedclick" class="speedContent">
				<div class="desc"></div>
				<div class="close" @click="close($event)"></div>
			</div>
		</section>
	</body>
</html>
<script>
bgImg('banner',.57);
	var getjiaji = '<?php echo ($jiaji); ?>';
	var app = new Vue({
		el:'#app',
		data:{
			mobile:'<?php echo ($mobile); ?>',
			isjiaji:false,    //是否已经加急
			jiajiState:0,    //是否点击了一次
			jiajitext:'加急',
			hasjl:true,    //该方案师是否有简历
			step:'<?php echo ($projectvalue); ?>',     //当前进行的步骤
			//step1:4,     //该阶段当前进行的步骤
			step1:<?php echo ($projectvalue); ?>>10?10:<?php echo ($projectvalue); ?>,   //第一阶段进度
			step2:<?php echo ($projectvalue); ?>>10?<?php echo ($projectvalue); ?>-10:0,   //第二阶段进度
			//step:14,
			//step1:10,
			//step2:11,
			state:1,     //当前选中第几阶段
			name1:'平面图方案阶段',
			name2:'效果图方案阶段',
			name3:'施工图阶段',
			stepName:'平面图方案阶段',
			speedclick:false,    //是否打开进度详情框
			step3List:<?php echo ($doinglist); ?>,     //第三阶段进度列表
			list:<?php echo ($List); ?>,       //阶段沟通列表
			list2:<?php echo ($List2); ?>,       //阶段沟通列表
			list3:<?php echo ($List3); ?>,       //阶段沟通列表
			stepList:<?php echo ($stepList); ?>,       //阶段步骤列表
			stepList2:<?php echo ($stepList2); ?>,       //阶段步骤列表
			projectid:<?php echo ($id); ?>
		},
		methods:{
			//切换当前方案
			tabsFn:function(evt,type){
				var self=this;
				//ajax
				self.state=type;
				self.stepName=(type==1?self.name1:(type==2?self.name2:self.name3));
				initSwiper();
				$(evt.target).addClass('active').siblings().removeClass('active');
			},
			//点击加急
			jiaji:function(evt,projectid,user_id,type){
				var self=this;
				if(self.jiajiState==1){
					return
				}
				if(self.isjiaji==false){   //未加急
					self.jiajiState=1;
					//ajax
					$.ajax({
						url:"<?php echo U('User/Center/changejiaji');?>",
						data:{projectid:self.projectid,user_id:user_id,type:type},
						type:'post',
						success:function(data)
						{
							if(data.status==0)
							{
								app.isjiaji=true;
								app.jiajitext ='已加急';
								self.jiajiState=0;
							}
						},
						fail:function(){
							self.jiajiState=0;
						}
					})
				}
			},
			//查看进度描述
			speed:function(evt,desc,index){
				var self=this;
				if(index==0){    //第一个，不可点击
					return false;
				}
				if(desc.length<1){
					$.toast("该专业人员还没有填写进度动态","text");
					return false;
				}
				self.speedclick=true;
				$('.desc').html(desc);
				$('body').addClass('overflow');
			},
			//关闭进度描述
			close:function(evt){
				var self=this;
				self.speedclick=false;
				$('body').removeClass('overflow');
			},
			//点击预览
			yulan:function(evt,img){
				if(img.length<1){
					$.toast("抱歉，由于后台上传的文件不是图片格式，无法预览","text");
					return false;
				}
				var preview = $.photoBrowser({
				  items: [
					img
				  ],
				  onClose:function(){
					preview.close();
				  }
				});
				preview.open();
			}
		}
	})

	function initSwiper(){
		if(getjiaji == 1)
		{
			app.isjiaji =true;
			app.jiajitext='已加急';
		}
		var mySwiper1 = new Swiper('.speed1 .swiper-container',{
			initialSlide :app.step1-1,
			slidesPerView : 2,
			spaceBetween : 10,
			centeredSlides : true,
			observer:true,/*启动动态检查器，当改变swiper的样式（例如隐藏/显示）或者修改swiper的子元素时，自动初始化swiper。*/
			observeParents:true,/*将observe应用于Swiper的父元素。当Swiper的父元素变化时，例如window.resize，Swiper更新。*/
		});
		var mySwiper2 = new Swiper('.speed2 .swiper-container',{
			initialSlide :app.step2-1,
			slidesPerView : 2,
			spaceBetween : 10,
			centeredSlides : true,
			observer:true,
			observeParents:true,
		})
    }
	function initCharts(){
		var color=["#3179fc","#51c7f2","#9a7acb","#eb4986","#51c7f2","#9a7acb","#eb4986","#51c7f2","#9a7acb","#eb4986"];
		for(var i=0;i<app.step3List.length;i++){
			if(i==0){
				var radius=['60%','70%'];
			}
			else{
				var radius=['65%','70%'];
			}
			var myChart="myChart"+app.step3List[i].ids;
			var ids=app.step3List[i].ids;
			var num=app.step3List[i].percent;
			var pernum=app.step3List[i].percent+"%";
			myChart = echarts.init(document.getElementById(ids));
			option1 = {
				color:[color[i],"#ccc"],
				 series: [
					{
						name:app.step3List[i].name,
						type:'pie',
						radius: radius,
						avoidLabelOverlap: false,
						animation: false,
						silent:true,
						label: {
							normal: {
								show: true,
				                    position: 'center',
				                    textStyle: {
				                        fontSize: '20',
				                        color:'#000',
				                    }
				               },
				           },
			            data:[
			                {value:num, name:pernum},
			                {value:100-num, name:''}
			            ]
			        }
			    ]
			};
			myChart.setOption(option1);
		}
		
	}
	$(function(){
		initSwiper();
		initCharts();
	})
</script>