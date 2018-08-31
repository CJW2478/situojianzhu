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
<style>
.row-fluid{
	display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 28%; width: 44%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
.row-fluid1{
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
			<li><a href="<?php echo U('Caseplan/index',array('project_id'=>$principal['id']));?>">甲方项目基本信息</a></li>
			<li><a href="<?php echo U('Caseplan/communication',array('project_id'=>$principal['id']));?>">沟通记录文件确认<?php if($sign["communication"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li class="active"><a href="<?php echo U('Caseplan/design',array('project_id'=>$principal['id']));?>">规划、指标测算、设计方案</a></li>
			<li><a href="<?php echo U('Caseplan/scheme',array('project_id'=>$principal['id']));?>">平面规划方案确认<?php if($sign["scheme"] > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
		</ul>
		<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
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
					<label class="control-label" style="margin-left: 75px;width: 200px">规划、指标测算、设计方案：</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						<th style="text-align: center;min-width: 80px;">上传时间</th>						
						<th style="text-align: center;min-width: 120px;">文件名</th>
						<th style="text-align: center;min-width: 80px;">上传人姓名</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody id="messagelist">
					<?php if(is_array($files)): $i = 0; $__LIST__ = $files;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>						
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i',$val["create_time"])); ?></td>
						<td style="text-align: center;"><?php echo ($val["file_name"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;">
							<a  href="<?php echo U('Admin/Caseplan/downloadfamessage',array('file_url'=>$val['file_url'],'file_name'=>$val['file_name'],'project_id'=>$val['project_id']));?>" class="btn" style="margin-right:30px;background:#1abc9c">下载</a>
							<?php if($val['message'] == '' && $val['mfile_name'] == ''): ?><a href="javascript:;" onclick="show_div('<?php echo ($val["id"]); ?>','<?php echo ($principal["id"]); ?>')" class="btn" style="background:#1abc9c">意见反馈</a>
								<?php else: ?>
								<a href="javascript:;" onclick="show_div2('<?php echo ($val["mfile_url"]); ?>','<?php echo ($val["message"]); ?>','<?php echo ($val["mfile_name"]); ?>','<?php echo ($val["id"]); ?>')" class="btn" style="background:#1abc9c">我的反馈</a><?php endif; ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(count($files) == 0): ?><tr><td id="nonumber" colspan="5" style="text-align: center;">暂无资料信息</td>
					</tr><?php endif; ?>
				</tbody>
				<div class="pagination" style="margin-left: 90px;"><?php echo ($page); ?></div>
			</table>
		</fieldset>
		</form>
			<form class="form-horizontal" id="tagforms3" name="tagforms3" method="post" enctype="multipart/form-data" action="<?php echo U('Caseplan/design');?>">
				<fieldset>
					<div class="row-fluid" style="display: none">
						<div style="margin-top:10px;">
							<div class="control-group">
								<label class="control-label" style="width: 120px; ">备注：</label>
								<div class="controls" style="margin-left: 130px;">
									<textarea id="message" name="message" style="width: 80%;height: 230px;" maxlength="200" placeholder="不超过200字"></textarea>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" style="width: 120px; ">上传意见文件：</label>
								<div class="controls" style="margin-left: 130px;">
									<input type="file" name="filename" id="file">
								</div>
							</div>
						</div>
						<div id="messageerror" style="text-align:center;color:red"></div>
						<div style="text-align: center;margin-top: 10px;">
							<input type="hidden" id="project_id" name="project_id" value="">
							<input type="hidden" id="upid" name="id" value="">
							<a href="javascript:;" class="btn btn-primary" onclick="close_div()">取消</a>&nbsp;&nbsp;&nbsp;
							<a href="javascript:;" class="btn btn-primary" onclick="eachSelect()">确认</a>
						</div>
					</div>
				</fieldset>
			</form>
			<div class="row-fluid1" style="display: none">
				<div style="overflow: hidden;"><span style="cursor:pointer;float: right;float: right;background: #1dccaa;border-radius: 50%;width: 20px;height: 20px;line-height: 18px;text-align: center;color: white;" onclick="close_div();">x</span></div>
				<div style="margin-top: 10px;width: 85%;margin: 0 auto;">					
					<div id="messagecontent" style="max-height: 400px;overflow-y:scroll;height: 200px;border:1px solid #ccc;padding:20px;border-radius:5px;">						
					</div>
				</div>
				<div style="margin-top:30px;text-align:center" id="filedata">
					<div id="messagefile">文件名：<span id="filename"></span><a class="btn" id="downurl" href="" style="margin-left: 20px;background: #1abc9c">下载</a>
					</div>
				</div>
			</div>
	</div>
	<div id="bg" onclick="close_div()"></div>
<script src="/company/public/js/common.js"></script>
<script src="/company/public/js/layer/layer.js"></script>
<script src="/company/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
    var select = document.getElementById('select');
    select.onchange = function(){
        var val = this.value;
        location.href = '<?php echo U("Caseplan/design");?>'+'&project_id='+val;
    };

    function show_div(id,project_id) {
        $("#project_id").val(project_id);
        $("#upid").val(id);
        $("#bg").css('display','block');
        $('.row-fluid').css('display','block');
    }

    function show_div2(url,message,file_name,ids) {
        $("#bg").css('display','block');
        $('.row-fluid1').css('display','block');
        if (url == '' || file_name == '') {
            $('#filedata').remove();
        }

		var jump = "<?php echo U('Caseplan/downloadfamessage51');?>"+"&id="+ids;
		if(message =='')
		{
			message ='暂无内容';
		}
		$('#messagecontent').html(message);
		$('#filename').html(file_name);
        $("#downurl").attr('href',jump);
    }
	var istap=0;
    function eachSelect()
    {
		if(istap ==1){return;}        
        var message =$("#message").val();
        var file = $('#file').val();
        if(message == '' && file == '')
        {
			$("#messageerror").show();
			$("#messageerror").html('备注与上传意见文件至少填写一个');
			istap=1;			
			setTimeout(function(){
				$("#messageerror").hide();
				istap=0;
			},2000)
        }else {
			
            document.tagforms3.submit();
		}
    }

    function close_div() {
		$("#message").val('');
		$("#file").val('');
        $('.row-fluid').css('display','none');
        $('.row-fluid1').css('display','none');
        $('#bg').css('display','none');
    }
</script>
</body>
</html>