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
		display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 28%; width: 44%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
	}
	#bg{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}

	.table tr th,.table tr td{
		text-align: center;
	}
	.add-btn{
		margin-left: 22px;
	}
</style>
<body>
<div class="wrap js-check-wrap">
	<ul class="nav nav-tabs">
		<li class="active"><a href="javascript:;">负责人</a></li>
	</ul>
	<form class="well form-search">
		项目：
		<select name="project_id" id="select" style="width: 150px;" onchange="dochangetype()">
			<?php if(count($project_ids) != 0): if(is_array($project_ids)): foreach($project_ids as $key=>$vo): $selected=$project_id==$vo['id']?"selected":""; ?>
				<option value="<?php echo ($vo["id"]); ?>" <?php echo ($selected); ?>><?php echo ($vo["project_name"]); ?></option><?php endforeach; endif; ?>
			<?php else: ?>
				<option>暂未分配项目</option><?php endif; ?>
		</select>
	</form>
	<ul class="nav nav-tabs pointUl">
		<li class="active"><a href="<?php echo U('Principal/index',array('project_id'=>$project['id']));?>">人员分配审核</a></li>
		<li><a href="<?php echo U('Principal/audit',array('project_id'=>$project['id']));?>">人员分配列表<?php if($sign["solutions"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
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
				<label class="control-label" style="margin-left: 15px;">人员分配审核</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
				<tr>
					<th style="text-align: center;">分配时间</th>
					<th style="text-align: center;">分配员</th>
					<th style="text-align: center;">分配角色</th>
					<th style="text-align: center;">被分配者姓名</th>
					<th  style="text-align: center;">操作</th>
				</tr>
				</thead>
				<tbody>
				<?php if(is_array($users)): foreach($users as $key=>$vo): ?><tr>
						<td style="text-align: center;"><?= date('Y-m-d H:i:s',$vo['create_time']) ?></td>
						<td style="text-align: center;"><?php echo ($vo["distribution_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($vo["role_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($vo["by_distribution_name"]); ?></td>
						<?php if($key == 0): ?><td rowspan='<?php echo count($users);?>'>
							<a class="btn btn-primary" onclick='show_div("<?php echo ($project["id"]); ?>")' style="padding: 8px 15px;color: white;background-color:#0e90d2;">审核</a>
						</td><?php endif; ?>
					</tr><?php endforeach; endif; ?>
				<?php if(count($users) == 0): ?><tr><td colspan="6" style="text-align: center;">暂无专业人员信息</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<fieldset>
				<div class="control-group" id="category-list">
					<div class="row-fluid" style="display: none">
						<div style="margin-top:70px;margin-left:40px;margin-bottom: 5px">
							<table id="box">
								<tr style="margin-left:300px">
									<td style="text-align: center;width: 300px">审核结果：</td>
									<td style="width: 300px">
										<select id="result" style="width: 150px">
											<option value="1">审核通过</option>
											<option value="2">审核不通过</option>
										</select>
									</td>
									<input type="hidden" id="solution_id">
							</table>
							
						</div>
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
			<div class="pagination" style="margin-left: 90px"><?php echo ($user_page); ?></div>
		</fieldset>
	</form>
</div>
<div id="bg" onclick="close_div()"></div>
<script src="/public/js/common.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
    var select = document.getElementById('select');
    select.onchange = function(){
        var val = this.value;
        location.href = '<?php echo U("Principal/index");?>'+'&project_id='+val;
    };

    function close_div() {
		$("#remark").val('');
        $('.row-fluid').css('display','none');
        $('#bg').css('display','none');
    }

    function confirm(){
        $('.row-fluid').css('display','none');
        $('#bg').css('display','none');
        var id = $('#solution_id').val();
        var status = $('#result').val();
        var remark = $('#remark').val();
        var project_id = <?php echo ($project_id); ?>;
        $.ajax({
            url:'<?php echo U("principal/change_status");?>',
            data:{status:status,remark:remark,project_id:project_id},
            type:"POST",
            dataType:"json",
            success:function (res) {
                if(res.status == 0){
                    $.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});
                   
                    location.href='<?php echo U("principal/index");?>'+"&project_id="+project_id;
                    
                }
                else {
                    $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                }
            }
        })
    }

    function show_div($id) {
        $("#bg").css('display','block');
        $('#qg_check').css('display','none');
        $('.row-fluid').css('display','block');
        $('#solution_id').val($id);
    }

    $('#result').change(function () {
        var status = $('#result').val();
        if (status == 2) {
            var tr="<tr style='margin-left:300px' id='bz'><td style='text-align: center;width: 300px'>备注："+
                "</td><td style='width: 300px'><textarea style='height: 150px' maxlength='200' placeholder='不超过200字' id='remark'></textarea></td></tr>";
            $('#box').append(tr);
        }else {
            $('#bz').remove();
        }
    })
</script>
</body>
</html>