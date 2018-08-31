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
			<li class="active"><a href="<?php echo U('Construction/index',array('project_id'=>$info['id']));?>">施工图所需资料</a></li>
			<li><a href="<?php echo U('Construction/messagefile',array('project_id'=>$info['id']));?>">沟通记录文件<?php if($ggcount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/numberone',array('project_id'=>$info['id']));?>">平面图<?php if($pmcount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/zyinfolist',array('project_id'=>$info['id']));?>">各专业条件图 <?php if($zyncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/bzysglist',array('project_id'=>$info['id']));?>">本专业施工图 <?php if($bsgcount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/nbmessagelist',array('project_id'=>$info['id']));?>">施工图审查意见（内审、外审）<?php if($nbncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Construction/doinglist',array('project_id'=>$info['id']));?>">进度动态</a></li>
			<li><a href="<?php echo U('Construction/allzylist',array('project_id'=>$info['id']));?>">各专业人员基本信息</a></li>
		</ul>
		<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">项目名称：</label>
					<span style="margin-left: 150px;color:#ccc;margin-top: 4px;position: absolute;">注：请建立QQ或微信讨论组</span>	
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
					<label class="control-label" style="margin-left: 30px;">施工图所需资料</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						<th style="text-align: center;">上传时间</th>
						<th style="text-align: center;">上传人姓名</th>
						<th style="text-align: center;">文件名</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($files)): $i = 0; $__LIST__ = $files;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<td style="text-align: center;"><a href="<?php echo U('Admin/Construction/downloadfangan',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: 15px;">平面规划方案</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						<th style="text-align: center;">上传时间</th>
						<th style="text-align: center;">上传人姓名</th>
						<th style="text-align: center;">文件名</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($files2)): $i = 0; $__LIST__ = $files2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<td style="text-align: center;"><a href="<?php echo U('Admin/Construction/downloadfangan',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: 2px;">效果图方案</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						<th style="text-align: center;">上传时间</th>
						<th style="text-align: center;">上传人姓名</th>
						<th style="text-align: center;">文件名</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($xglist)): $i = 0; $__LIST__ = $xglist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<td style="text-align: center;"><a href="<?php echo U('Admin/Construction/downloadxgfile',array('id'=>$val['id']));?>" class="btn" style="background: #1abc9c;">下载</a></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				
				</tbody>
			</table>
		</fieldset>
		</form>
	</div>
	<script src="/public/js/common.js"></script>	
	<script type="text/javascript">
	function  dochangetype(val)
	{
		location.href = '<?php echo U("Construction/index");?>'+'&project_id='+val;
	}
	</script>
</body>
</html>