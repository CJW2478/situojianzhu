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
		<li class="active"><a href="javascript:;">负责人</a></li>
	</ul>
	<form class="well form-search" method="post" action="<?php echo U('Principal/index');?>">
		项目：
		<select name="project_id" id="select" style="width: 150px;" onchange="dochangetype()">
			
			<?php if(count($project_ids) != 0): if(is_array($project_ids)): foreach($project_ids as $key=>$vo): $selected=$project_id==$vo['id']?"selected":""; ?>
				<option value="<?php echo ($vo["id"]); ?>" <?php echo ($selected); ?>><?php echo ($vo["project_name"]); ?></option><?php endforeach; endif; ?> 
			<?php else: ?>
			<option>暂未分配项目</option><?php endif; ?>
		</select>
	</form>
	<ul class="nav nav-tabs pointUl">
		<li><a href="<?php echo U('Principal/index',array('project_id'=>$project['id']));?>">人员分配审核<?php if($sign['solutions_unaudit'] > 0 ): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li class="active"><a href="<?php echo U('Principal/audit',array('project_id'=>$project['id']));?>">人员分配列表</a></li>
		<li><a href="<?php echo U('Principal/plane',array('project_id'=>$project['id']));?>">平面图方案阶段方案确认<?php if($sign["plane_scheme"] > 0 || $sign["plane_design"] > 0 ): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Principal/effect',array('project_id'=>$project['id']));?>">效果图方案阶段方案确认<?php if($sign["effect_first"] > 0 || $sign["effect_final"] > 0 || $sign["effect_scheme"] > 0 ): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Principal/work',array('project_id'=>$project['id']));?>">施工图方案阶段方案确认<?php if($sign["work_first"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
	</ul>
	<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label">项目名称：</label>
				<div class="controls" style="margin-top: 5px;">
					<label><?php echo ($project["project_name"]); ?></label>
				</div>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label">项目编号：</label>
				<div class="controls" style="margin-top: 5px;">
					<label><?php echo ($project["project_no"]); ?></label>
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
					<td style="text-align: center;"><?php echo ($project["principal_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($project["mobile"]); ?></td>
					<td style="text-align: center;"><?php echo ($project["duty"]); ?></td>
					<td style="text-align: center;"><?php echo ($project["qq"]); ?></td>
					<td style="text-align: center;"><?php echo ($project["wx"]); ?></td>
				</tr>
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="margin-left: 55px;">各专业人员基本信息</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
				<tr>
					<th style="text-align: center;">分配时间</th>
					<th style="text-align: center;">分配员</th>
					<th style="text-align: center;">分配角色</th>
					<th style="text-align: center;">被分配者姓名</th>
				</tr>
				</thead>
				<tbody>
				<?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?= date('Y-m-d H:i:s',$val['create_time']) ?></td>
						<td style="text-align: center;"><?php echo ($val["distribution_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["role_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["by_distribution_name"]); ?></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php if(count($users) == 0): ?><tr><td colspan="6" style="text-align: center;">暂无专业人员信息</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px"><?php echo ($user_page); ?></div>
		</fieldset>
	</form>
</div>
<script src="/public/js/common.js"></script>
<script type="text/javascript">
    function  dochangetype()
    {
        document.all.formgetstr.submit();
    }

    var select = document.getElementById('select');
    select.onchange = function(){
        var val = this.value;
        location.href = '<?php echo U("Principal/index");?>'+'&project_id='+val;
    };
</script>
</body>
</html>