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
		<li><a href="<?php echo U('Principal/index',array('project_id'=>$project['id']));?>">人员分配审核<?php if($sign["solutions_unaudit"] > 0 ): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Principal/audit',array('project_id'=>$project['id']));?>">人员分配列表<?php if($sign["solutions"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Principal/plane',array('project_id'=>$project['id']));?>">平面图方案阶段方案确认<?php if($sign["plane_scheme"] > 0 || $sign["plane_design"] > 0 ): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li class="active"><a href="<?php echo U('Principal/effect',array('project_id'=>$project['id']));?>">效果图方案阶段方案确认</a></li>
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
				<label class="control-label" style="margin-left: 80px;width: 220px">方案师首次上传效果图方案确认：</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
				<tr>
					<th style="text-align: center;">上传时间</th>
					<th style="text-align: center;">上传人姓名</th>
					<th style="text-align: center;">文件名</th>
					<th style="text-align: center;">状态</th>
					<th style="text-align: center;">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php if(is_array($first_effect)): $i = 0; $__LIST__ = $first_effect;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<?php if($val['status']==0): ?><td style="text-align: center;">已确认</td>
						<?php else: ?>
							<td style="text-align: center;color: red">未处理</td><?php endif; ?>
						<td style="text-align: center;">
							<a href="<?php echo U('Principal/downloadeffect',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a>
							<?php if($val['status']==1): ?><a onclick="effect_confirm('<?php echo ($val["id"]); ?>','<?php echo ($project_id); ?>',14)" class="btn" style="background:#1abc9c">确认</a>
								<?php else: ?>
								<a class="btn" style="background:grey;cursor: default" href ="javascript:return false;" onclick="return false;">已确认</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php if(count($first_effect) == 0): ?><tr><td colspan="4" style="text-align: center;">暂无上传资料</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px"><?php echo ($first_page); ?></div>

			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="margin-left: 30px;width: 200px">最终效果图方案确认：</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
				<tr>
					<th style="text-align: center;">上传时间</th>
					<th style="text-align: center;">上传人姓名</th>
					<th style="text-align: center;">文件名</th>
					<th style="text-align: center;">状态</th>
					<th style="text-align: center;">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php if(is_array($effect)): $i = 0; $__LIST__ = $effect;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<?php if($val['status']==0): ?><td style="text-align: center;">已确认</td>
							<?php else: ?>
							<td style="text-align: center;color: red">未处理</td><?php endif; ?>
						<td style="text-align: center;">
							<a href="<?php echo U('Principal/downloadeffect',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a>
							<?php if($val['status']==1): ?><a onclick="effect_confirm('<?php echo ($val["id"]); ?>','<?php echo ($project_id); ?>',18)" class="btn" style="background:#1abc9c">确认</a>
								<?php else: ?>
								<a class="btn" style="background:grey;cursor: default" href ="javascript:return false;" onclick="return false;"  >已确认</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php if(count($effect) == 0): ?><tr><td colspan="4" style="text-align: center;">暂无上传资料</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px"><?php echo ($page); ?></div>

			<div class="control-group" style="margin-bottom: 0px;">
				<label class="control-label" style="margin-left: -10px;width: 200px">规划方案文本：</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
				<tr>
					<th style="text-align: center;">上传时间</th>
					<th style="text-align: center;">上传人姓名</th>
					<th style="text-align: center;">文件名</th>
					<th style="text-align: center;">状态</th>
					<th style="text-align: center;">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php if(is_array($scheme_data)): $i = 0; $__LIST__ = $scheme_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<?php if($val['status']==0): ?><td style="text-align: center;">已确认</td>
							<?php else: ?>
							<td style="text-align: center;color: red">未处理</td><?php endif; ?>
						<td style="text-align: center;">
							<a href="<?php echo U('Principal/downloadgg',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a>
							<?php if($val['status']==0): ?><a class="btn" style="background:grey;cursor: default" href ="javascript:return false;" onclick="return false;">已确认</a>
								<?php if($val['audit_status'] == 1): ?><a onclick="audit_status('<?php echo ($val["id"]); ?>','<?php echo ($project_id); ?>',21)" class="btn" style="background:#1abc9c">相关部门审核通过</a>
									<?php else: ?>
									<a class="btn" style="background:grey;cursor: default" href ="javascript:return false;" onclick="return false;"  >效果图方案阶段结束</a><?php endif; ?>
							<?php else: ?>
								<a onclick="change_status('<?php echo ($val["id"]); ?>','<?php echo ($project_id); ?>',20)" class="btn" style="background:#1abc9c">确认</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php if(count($scheme_data) == 0): ?><tr><td colspan="4" style="text-align: center;">暂无上传资料</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px"><?php echo ($scheme_page); ?></div>
		</fieldset>
	</form>
</div>
<script src="/public/js/common.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
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

    function change_status(id,project_id,step) {
		$.ajax({
            url:'<?php echo U("Principal/confirm_upload_data");?>',
            data:{id:id,status:0,project_id:project_id,step:step},
            type:"POST",
            dataType:"json",
            success:function (res) {
                if(res.status == 0){
                    $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                 
                        location.href='<?php echo U("Principal/effect");?>'+"&project_id="+project_id;
                   
                }
                else {
                    $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                }
            }
		})
    }

    function audit_status(id,project_id,step) {
        $.ajax({
            url:'<?php echo U("Principal/confirm_upload_data");?>',
            data:{id:id,audit_status:0,project_id:project_id,step:step},
            type:"POST",
            dataType:"json",
            success:function (res) {
                if(res.status == 0){
                    $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                   
                        location.href='<?php echo U("Principal/effect");?>'+"&project_id="+project_id;
                   
                }
                else {
                    $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                }
            }
        })
    }

    function effect_confirm(id,project_id,step) {
        $.ajax({
            url:'<?php echo U("Principal/confirm_upload_effect");?>',
            data:{id:id,status:0,project_id:project_id,step:step},
            type:"POST",
            dataType:"json",
            success:function (res) {
                if(res.status == 0){
                    $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                   
                        location.href='<?php echo U("Principal/effect");?>'+"&project_id="+project_id;
                   
                }
                else {
                    $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                }
            }
        })
    }
</script>
</body>
</html>