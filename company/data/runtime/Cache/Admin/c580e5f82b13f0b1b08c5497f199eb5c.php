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

	<link href="/company/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/company/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/company/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/company/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
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
	<link rel="stylesheet" href="/company/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
	//全局变量
	var GV = {
	    ROOT: "/company/",
	    WEB_ROOT: "/company/",
	    JS_ROOT: "public/js/",
	    APP:'<?php echo (MODULE_NAME); ?>'/*当前应用名*/
	};
	</script>
    <script src="/company/public/js/jquery.js"></script>
    <script src="/company/public/js/wind.js"></script>
    <script src="/company/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
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
		<li class="active"><a href="javascript:;">平面图方案阶段</a></li>
	</ul>
	<form class="well form-search">
		项目：
		<select id="select">
			<?php if(count($project_ids) != 0): if(is_array($project_ids)): foreach($project_ids as $key=>$vo): $selected=$project_id==$vo['id']?"selected":""; ?>
					<option value="<?php echo ($vo["id"]); ?>" <?php echo ($selected); ?>><?php echo ($vo["project_name"]); ?></option><?php endforeach; endif; ?>
				<?php else: ?>
				<option>暂未分配项目</option><?php endif; ?> 
		</select>
	</form>
	<ul class="nav nav-tabs pointUl">
		<li class="active"><a href="<?php echo U('Caseplan/index',array('project_id'=>$principal['id']));?>">甲方项目基本信息</a></li>
		<li><a href="<?php echo U('Caseplan/communication',array('project_id'=>$principal['id']));?>">沟通记录文件确认<?php if($sign["communication"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Caseplan/design',array('project_id'=>$principal['id']));?>">规划、指标测算、设计方案<?php if($sign["design"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Caseplan/scheme',array('project_id'=>$principal['id']));?>">平面规划方案确认<?php if($sign["scheme"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
	</ul>
	<form class="form-horizontal">
		<fieldset>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label">项目名称：</label>
				<div class="controls" style="margin-top: 5px;">
					<label><?php echo ($principal["project_name"]); ?></label>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label">项目编号：</label>
				<div class="controls" style="margin-top: 5px;">
					<label><?php echo ($principal["project_no"]); ?></label>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="margin-left: 45px;">方案师基本信息：</label>
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
				<?php if($solution["id"] != ''): ?><tr>
						<td style="text-align: center;"><?php echo ($solution["solution_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($solution["mobile"]); ?></td>
						<td style="text-align: center;"><?php echo ($solution["user_duty"]); ?></td>
						<td style="text-align: center;"><?php echo ($solution["qq_no"]); ?></td>
						<td style="text-align: center;"><?php echo ($solution["wx_no"]); ?></td>
					</tr>
					<?php else: ?>
					<tr><td colspan="4" style="text-align: center;">暂无资料信息</td></tr><?php endif; ?>
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="margin-left: 60px;">项目基本信息上传：</label>
			</div>
			<div class="controls" style="margin-bottom: 5px;">
				<a href="javascript:upload_projectinfo('文件上传','#thumb','file','','','<?php echo ($principal["id"]); ?>',1,'Caseplan/index')">
					<span class="btn" style="margin-left: -88px;background:#1abc9c">上传基本资料</span>
				</a>
				<span style="margin-left: 20px;color:#ccc;">注：请上传规划设计任务书、红线、设计要求</span>
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
				<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?= date('Y-m-d H:i:s',$val['create_time'])?></td>
						<td style="text-align: center;"><?php echo ($val["principal_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<td style="text-align: center;"><a  href="<?php echo U('Admin/Caseplan/downloadfamessage',array('file_url'=>$val['file_url'],'file_name'=>$val['file_name'],'project_id'=>$val['project_id']));?>" class="btn" style="background:#1abc9c">下载</a></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php if(count($data) == 0): ?><tr><td colspan="4" style="text-align: center;">暂无资料信息</td></tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px"><?php echo ($data_page); ?></div>
		</fieldset>
	</form>
</div>
<script src="/company/public/js/common.js"></script>
<script src="/company/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
    var select = document.getElementById('select');
    select.onchange = function(){
        var val = this.value;
        location.href = '<?php echo U("Caseplan/index");?>'+'&project_id='+val;
    };
</script>
</body>
</html>