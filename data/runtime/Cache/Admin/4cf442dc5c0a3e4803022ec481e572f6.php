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
	input[type="text"],input[type="number"]{padding:3.5px 6px;}

	.row-fluid{
		display:none;position: absolute;  top: 20%;border-radius: 3px;  left: 28%; width: 44%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
	}
	#bg{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}

	.table tr th,.table tr td{
		text-align: center;
	}
	.add-btn{
		margin-left: 22px;
	}
	#addmessage{text-align:center;color:red}
</style>
<body>
<div class="wrap js-check-wrap">
	<ul class="nav nav-tabs">
		<li class="active"><a href="javascript:;">分配员</a></li>
	</ul>
	<form class="well form-search">
		项目：
		<select id="select">
			<?php if(is_array($project_ids)): foreach($project_ids as $key=>$vo): $selected=$principal['id']==$vo['id']?"selected":""; ?>
				<option value="<?php echo ($vo["id"]); ?>" <?php echo ($selected); ?>><?php echo ($vo["project_name"]); ?></option><?php endforeach; endif; ?>
		</select>
	</form>
	<ul class="nav nav-tabs pointUl">
		<li><a href="<?php echo U('Distribution/index',array('project_id'=>$project_id));?>">甲方项目基本信息</a></li>
		<li class="active"><a href="<?php echo U('Distribution/solutions',array('project_id'=>$project_id));?>">分配方案师</a></li>
		<li><a href="<?php echo U('Distribution/professional',array('project_id'=>$project_id));?>">分配各专业人员<?php if($sign["professional"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
		<li><a href="<?php echo U('Distribution/work_plan',array('project_id'=>$project_id));?>">上传施工图所需资料</a>
		<li><a href="<?php echo U('Distribution/opinion',array('project_id'=>$project_id));?>">上传施工图审查意见（内部、外部）</a></li>
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
					<td style="text-align: center;"><?php echo ($principal["principal_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($principal["mobile"]); ?></td>
					<td style="text-align: center;"><?php echo ($principal["duty"]); ?></td>
					<td style="text-align: center;"><?php echo ($principal["qq"]); ?></td>
					<td style="text-align: center;"><?php echo ($principal["wx"]); ?></td>
				</tr>
				</tbody>
			</table>
			<table  class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
				<tr>
					
				<a id="solutions" class="btn btn-primary" onclick="show_div()"  style="margin-left:90px;padding: 8px 15px;color: white;background:#1abc9c;">分配方案师</a>
					
				</tr>
				<tr>
					<th style="text-align: center;">分配时间</th>
					<th style="text-align: center;">分配员</th>
					<th style="text-align: center;">分配角色</th>
					<th style="text-align: center;">被分配者姓名</th>
					<th style="text-align: center;">状态</th>
					<th style="text-align: center;">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php if(is_array($data)): foreach($data as $key=>$vo): $status=$vo['status']==1?'审核通过':($vo['status']==2?'审核未通过':未审核) ?>
					<tr>
						<td style="text-align: center;"><?= date('Y-m-d H:i:s',$vo['create_time']) ?></td>
						<td style="text-align: center;"><?php echo ($vo["distribution_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($vo["role_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($vo["by_distribution_name"]); ?></td>
						<td style="text-align: center;">
							<?php if($vo['status'] == 0): ?><font style="color: red"><?php echo ($status); ?></font>
								<?php else: ?>
								<?php echo ($status); endif; ?>
						</td>
						<td>
							<?php if($vo['remark'] == ''): else: ?>
								<a class="btn btn-primary" onclick='show_remark("<?php echo ($vo["remark"]); ?>")' style="padding: 8px 15px;color: white;background:#1abc9c;">查看备注</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; ?>
				<?php if(count($data) == 0): ?><tr><td colspan="6" style="text-align: center;">暂无方案师信息</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px"><?php echo ($page); ?></div>
				<div class="control-group" id="category-list">
					<div class="row-fluid" style="display: none">
						<div style="margin-top:70px;margin-left:40px;margin-bottom: 5px">
							<table>
								<tr>
									<td>角色：</td>
									<td>
										<select id="role" style="width: 150px;margin-right: 40px">
											<option value="<?php echo ($solution["id"]); ?>"><?php echo ($solution["name"]); ?></option>
										</select>
									</td>
									<td>人员：</td>
									<td>
										<select id="users" style="width: 150px">
											<?php if(is_array($users)): foreach($users as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["user_name"]); ?></option><?php endforeach; endif; ?>
										</select>
									</td>
								</tr>
							</table>
							
						</div>
						<div id="addmessage"></div>
						<div style="height: 50px;border-bottom: 1px solid #ccc;"></div>
						<div style="text-align: center;margin-top: 10px;">
							<a href="javascript:;" class="btn btn-primary" onclick="close_div()">取消</a>&nbsp;&nbsp;&nbsp;
							<a href="javascript:;" class="btn btn-primary" onclick="confirm()">确认</a>
						</div>
						<div class="row" id="page-info">
						</div>
					</div>
					<input type="hidden" id="project_id" name="project_id"  value="<?php echo ($project_id); ?>">
					<input type="hidden" id="distribution" name="distribution"  value="<?php echo ($id); ?>">
				</div>
			
		</fieldset>
	</form>
</div>
<div id="bg" onclick="close_div()"></div>
<script src="/public/js/common.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
<script src="/public/js/layer/layer.js"></script>
<script type="text/javascript">
    var select = document.getElementById('select');
    select.onchange = function(){
        var val = this.value;
        location.href = '<?php echo U("Distribution/index");?>'+'&project_id='+val;
    };

    function close_div() {
		$("#addmessage").hide();
        $('.row-fluid').css('display','none');
        $('#bg').css('display','none');
    }
    function confirm(){
        var role_id = $('#role').val();
        var by_distribution = $('#users').val();
        var distribution = $('#distribution').val();
        var project_id = $('#project_id').val();
        var role_name = $('#role option:selected').text();
        $.ajax({
            url:'<?php echo U("distribution/solution_add_solutions");?>',
            data:{role_id:role_id,by_distribution:by_distribution,distribution:distribution,project_id:project_id,role_name:role_name},
            type:"POST",
            dataType:"json",
            success:function (res) {
                if(res.status == 0){
					 close_div();
                    $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});                   
                    location.href='<?php echo U("distribution/solutions");?>'+"&project_id="+project_id;                    
                }
                else {
					$("#addmessage").show();
                    $("#addmessage").html(res.msg);
					setInterval(function(){
						$("#addmessage").hide();
					},2000)
                }
            }
        })
    }
    function show_div() {
        $("#bg").css('display','block');
        $('#qg_check').css('display','none');
        $('.row-fluid').css('display','block');
    }

    function show_remark($remark) {
        layer.open({
            type: 1,
            title:'备注',
            skin: 'layui-layer-lan', //加上边框
            area: ['500px', '300px'], //宽高
            content: $remark
        });
    }
</script>
</body>
</html>