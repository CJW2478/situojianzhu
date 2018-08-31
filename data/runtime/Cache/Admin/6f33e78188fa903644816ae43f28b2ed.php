<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
		.pointUl>li{position: relative;}
		.pointred{
			position: absolute;
			right: 10px;
			top:-20px;
			color: red;
		    font-size: 60px;
		    position: absolute;
		}
		.form-search select{height: 36px;}
		#messagecontent::-webkit-scrollbar {display:none}
		
</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
	//全局变量
	var GV = {
	    ROOT: "/",
	    WEB_ROOT: "/",
	    JS_ROOT: "public/js/",
	    APP:'<?php echo (MODULE_NAME); ?>'/*当前应用名*/
	};
	</script>
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script>
    	$(function(){
    		$("[data-toggle='tooltip']").tooltip();
    	});
    </script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	
	</style><?php endif; ?>
</head>
<style>
.row-fluid{
	display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 28%; width: 44%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
#bg{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}

.table tr th,.table tr td{
	text-align: center;
}

</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">建筑专业</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Construction/index');?>">	
			项目： 
			<select name="project_id" style="width: 150px;" onchange="dochangetype(this.value)">
                <?php if(count($project) != 0): if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?><option value="<?php echo ($va["id"]); ?>" <?php if($formget['project_id'] == $va['id']): ?>selected<?php endif; ?>><?php echo ($va["project_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>  
				<?php else: ?>
				<option>暂未分配项目</option><?php endif; ?>                          
            </select>
		</form>
		<ul class="nav nav-tabs pointUl">
			<li><a href="<?php echo U('Construction/index',array('project_id'=>$info['id']));?>">施工图所需资料<?php if($scount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/messagefile',array('project_id'=>$info['id']));?>">沟通记录文件<?php if($ggcount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			
			<li class="active"><a href="<?php echo U('Construction/numberone',array('project_id'=>$info['id']));?>">平面图</a></li>
			<li><a href="<?php echo U('Construction/zyinfolist',array('project_id'=>$info['id']));?>">各专业条件图<?php if($zyncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/bzysglist',array('project_id'=>$info['id']));?>">本专业施工图</a></li>
			<li><a href="<?php echo U('Construction/nbmessagelist',array('project_id'=>$info['id']));?>">施工图审查意见（内审、外审）<?php if($nbncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/doinglist',array('project_id'=>$info['id']));?>">进度动态</a></li>
			<li><a href="<?php echo U('Construction/allzylist',array('project_id'=>$info['id']));?>">各专业人员基本信息</a></li>
		</ul>
		<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">项目名称：</label><span style="margin-left: 150px;color:#ccc;margin-top: 4px;position: absolute;">注：请建立QQ或微信讨论组</span>	
					<div class="controls" style="margin-top: 5px;">
						<label><?php echo ($info["project_name"]); ?></label>
					</div>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">项目编号：</label>
					<div class="controls" style="margin-top: 5px;">
						<label><?php echo ($info["project_no"]); ?></label>
					</div>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: 55px;">甲方负责人基本信息</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						<th style="text-align: center;">姓名</th>
						<th style="text-align: center;">联系方式</th>
						<th style="text-align: center;">职务</th>
						<th style="text-align: center;">qq号</th>
						<th style="text-align: center;">微信号</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align: center;"><?php echo ($info["principal_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($info["mobile"]); ?></td>
						<td style="text-align: center;"><?php echo ($info["duty"]); ?></td>
						<td style="text-align: center;"><?php echo ($info["qq"]); ?></td>
						<td style="text-align: center;"><?php echo ($info["wx"]); ?></td>
					</tr>
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: 15px;">第一版平面图</label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">&nbsp;</label>
					<div class="controls" style="margin-bottom: 5px;">	
						
						<?php if($info['id'] > 0): ?><a href="javascript:upload_oneplan('文件上传','#thumb','file','','','<?php echo ($info["id"]); ?>');">							
							<span class="btn" style="margin-left: -88px;background:#1abc9c">上传至乙方负责人</span>
						</a>
						<?php else: ?>
						<a href="javascript:;">							
							<span class="btn" style="margin-left: -88px;background:#ccc;">上传至乙方负责人</span>
						</a><?php endif; ?>						
						
						<span style="margin-left: 20px;color:#ccc;">注：上传需要乙方负责人确认，可多次上传</span>										
					</div>
				</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						
						<th style="text-align: center;min-width: 80px;">上传时间</th>						
						<th style="text-align: center;min-width: 120px;">文件名</th>
						<th style="text-align: center;">上传人姓名</th>
						<th style="text-align: center;min-width: 80px;">乙方负责人确认状态</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody id="yifangdatalist">
					<?php if(is_array($files)): $i = 0; $__LIST__ = $files;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["createtime"])); ?></td>						
						<td style="text-align: center;"><?php echo ($val["filename"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>	
						<td style="text-align: center;"><?php if($val['status'] == 0): ?><span style="color: red">未确认</span><?php else: ?>已确认<?php endif; ?></td>						
						<td style="text-align: center;"><?php if($val['status'] == 1): ?><a href="<?php echo U('Construction/download',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a><?php endif; ?></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(count($files) == 0): ?><tr id="nonumber"><td colspan="5" style="text-align: center;">暂无上传资料</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: 80px;width: 220px;">各专业对第一版平面图的意见反馈</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>						
						<th style="text-align: center;min-width: 80px;">所属专业</th>						
						<th style="text-align: center;min-width: 120px;">姓名</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($fanklist)): $i = 0; $__LIST__ = $fanklist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>						
						<td style="text-align: center;"><?php echo ($val["role_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>						
						<td style="text-align: center;">
							<?php if($val['id'] > 0): ?><a class="btn" style="background: #1abc9c;" onclick="show_div('<?php echo ($val["id"]); ?>',1)">查看反馈</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(count($fanklist) == 0): ?><tr><td colspan="5" style="text-align: center;">暂无意见反馈</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: -25px;">平面图</label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">&nbsp;</label>
					<div class="controls" style="margin-bottom: 5px;">	
						<?php if($count1 > 0): ?><a href="javascript:upload_oneplan('文件上传','#thumb','file','','','<?php echo ($info["id"]); ?>','2');">							
							<span class="btn" style="margin-left: -88px;background:#1abc9c">上传至甲方负责人</span>
						</a>	
						<?php else: ?>
						<a href="javascript:;">							
							<span class="btn" style="margin-left: -88px;background:#ccc;">上传至甲方负责人</span>
						</a><?php endif; ?>	
						<span style="margin-left: 20px;color:#ccc;">注：首次上传的平面图经乙方负责人确认后将在此表中显示</span>											
					</div>
				</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						
						<th style="text-align: center;min-width: 80px;">上传时间</th>						
						<th style="text-align: center;min-width: 120px;">文件名</th>
						<th style="text-align: center;">上传人姓名</th>
						<th style="text-align: center;min-width: 80px;">状态</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody id="yifangdatalist">
					<?php if(is_array($piclist)): $i = 0; $__LIST__ = $piclist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["createtime"])); ?></td>						
						<td style="text-align: center;"><?php echo ($val["filename"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>	
						<td style="text-align: center;"><?php if($val['status2'] == 0): ?><span style="color: red">未处理</span><?php endif; if($val['status2'] == 1): ?>审核通过<?php endif; if($val['status2'] == 2): ?><span">审核不通过</span><?php endif; ?>
						</td>						
						<td style="text-align: center;">
							<a href="<?php echo U('Construction/download',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a>
							<?php if($val['status2'] == 2): if($val['recontent'] != '' || $val['refilename'] != ''): ?><a href="javascript:;" class="btn" onclick="show_div('<?php echo ($val["id"]); ?>',2)" style="background:#1abc9c">查看反馈</a><?php endif; endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(count($piclist) == 0): ?><tr id="nonumber"><td colspan="5" style="text-align: center;">暂无上传资料</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px;"><?php echo ($page); ?></div>
			<div class="row-fluid" style="display: none">
				<div style="overflow: hidden;"><span style="float: right;float: right;background: #1dccaa;border-radius: 50%;width: 20px;height: 20px;line-height: 18px;text-align: center;color: white;" onclick="close_div();">x</span></div>
				<div style="margin-top: 10px;width: 85%;margin: 0 auto;">					
					<div id="messagecontent" style="max-height: 400px;overflow-y:scroll;height: 200px;border:1px solid #ccc;padding:20px;border-radius:5px;">						
					</div>
				</div>
				<div style="margin-top:10px;text-align:center" id="filedata">					
					<div id="messagefile">文件名：<span id="filename"></span><a class="btn" id="downurl" href="" style="margin-left: 20px;background: #1abc9c">下载</a>						
					</div>
				</div>
			</div>
		</fieldset>
		</form>
	</div>
	<div id="bg" onclick="close_div()"></div>
	<script src="/public/js/common.js"></script>	
	<script type="text/javascript">
	function close_div() {
        $('.row-fluid').css('display','none');
        $('#bg').css('display','none');
    }
    function show_div(id,atype) {
        $("#bg").css('display','block');
        $('#qg_check').css('display','none');
        $('.row-fluid').css('display','block');
        $("#filedata").show();
        if(atype ==1)
        {
        	$.ajax({
	        	url:"<?php echo U('Admin/Construction/getmessage');?>",
	        	data:{id:id},
	        	type:'post',
	        	success:function (data) {        		
	        		$("#messagecontent").html(data.html);
	        		if(data.urldata!=null)
	        		{
	        			$("#filename").html(data.filename);
	        			$("#downurl").attr('href',data.url);
	        		}else{
	        			$("#filedata").hide();
	        		}        		
	        	}
	        })
        }else{
        	$.ajax({
	        	url:"<?php echo U('Admin/Construction/getpmmessage');?>",
	        	data:{id:id},
	        	type:'post',
	        	success:function (data) {        		
	        		$("#messagecontent").html(data.html);
	        		if(data.urldata!=null)
	        		{
	        			$("#filename").html(data.filename);
	        			$("#downurl").attr('href',data.url);
	        		}else{
	        			$("#filedata").hide();
	        		}        		
	        	}
	        })
        }        
    }
	function  dochangetype(val)
	{
		location.href = '<?php echo U("Construction/index");?>'+'&project_id='+val;
	}
	</script>
</body>
</html>