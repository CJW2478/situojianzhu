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
	display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 28%; width: 44%;height: 400px; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
#bg{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}
.row-fluid2{
	display:none;position: fixed;  top: 20%;border-radius: 3px;  left: 28%; width: 44%;height: 400px; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
}
#bg2{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}
.table tr th,.table tr td{
	text-align: center;
}
#messageerror{text-align: center;color: red;}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">设备专业</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Equipment/index');?>">	
			项目： 
			<select name="project_id" style="width: 150px;" onchange="dochangetype(this.value)">
                <?php if(count($project) != 0): if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?><option value="<?php echo ($va["id"]); ?>" <?php if($formget['project_id'] == $va['id']): ?>selected<?php endif; ?>><?php echo ($va["project_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>  
				<?php else: ?>
				<option>暂未分配项目</option><?php endif; ?>                          
            </select>
		</form>
		<ul class="nav nav-tabs pointUl">
			<li class="active"><a href="<?php echo U('Equipment/index',array('project_id'=>$info['id']));?>">平面图</a></li>
			<li><a href="<?php echo U('Equipment/zyinfolist',array('project_id'=>$info['id']));?>">各专业条件图<?php if($zyncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Equipment/bzysglist',array('project_id'=>$info['id']));?>">本专业施工图</a></li>
			<li><a href="<?php echo U('Equipment/nbmessagelist',array('project_id'=>$info['id']));?>">施工图审查意见（内审、外审）<?php if($nbncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Equipment/doinglist',array('project_id'=>$info['id']));?>">进度动态</a></li>
			<li><a href="<?php echo U('Equipment/allzylist',array('project_id'=>$info['id']));?>">各专业人员基本信息</a></li>
		</ul>
		<form class="form-horizontal" id="tagforms" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">项目名称：</label>
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
					<span style="margin-left: 20px;color:#ccc;margin-top: 4px;position: absolute;">注：请针对第一版平面图整理反馈意见并一次性上传</span>		
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						
						<th style="text-align: center;min-width: 80px;">上传时间</th>						
						<th style="text-align: center;min-width: 120px;">文件名</th>
						<th style="text-align: center;">上传人姓名</th>						
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody>
				<?php if(is_array($firstpic)): $i = 0; $__LIST__ = $firstpic;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i; if($val['filename'] != ''): ?><tr>						
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["createtime"])); ?></td>						
						<td style="text-align: center;"><?php echo ($val["filename"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;">
							<a href="<?php echo U('Equipment/download',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a>
							<?php if($val['isdo'] == 0): ?><a href="javascript:;" onclick="show_div('<?php echo ($info["id"]); ?>','<?php echo ($val["id"]); ?>')" class="btn" style="background:#1abc9c">意见反馈</a>
							<?php else: ?>
							<a href="javascript:;" onclick="show_div2('<?php echo ($info["id"]); ?>','<?php echo ($val["id"]); ?>','<?php echo ($val["mid"]); ?>')" class="btn" style="background:#1abc9c">我的反馈</a><?php endif; ?>
						</td>
					</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label" style="margin-left: 15px;">最终版平面图</label>
			</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						
						<th style="text-align: center;min-width: 80px;">上传时间</th>						
						<th style="text-align: center;min-width: 120px;">文件名</th>
						<th style="text-align: center;">上传人姓名</th>						
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lastpic)): $i = 0; $__LIST__ = $lastpic;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i; if($val['filename'] != ''): ?><tr>						
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["createtime"])); ?></td>						
						<td style="text-align: center;"><?php echo ($val["filename"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>
						<td style="text-align: center;">
							<a href="<?php echo U('Equipment/download',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a>
						</td>
					</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
			</table>
			<div class="row-fluid2" style="display: none">
					<div style="margin-top:10px;">					
						<div class="control-group">
							<label class="control-label" style="width: 120px; ">备注：</label>
							<div class="controls" style="margin-left: 130px;">
								<textarea id="contentdesc" maxlength='200' placeholder='不超过200字' style="width: 80%;height: 230px;"></textarea>
							</div>
						</div>
						<div class="control-group" id="filedata">
							<label class="control-label" style="width: 120px; ">上传意见文件：</label>
							<div class="controls" style="margin-left: 130px;">
								<span id="filename1">&nbsp;</span><a href="javascript:;" id="fileurldata" class="btn" style="margin-left: 5px;background:#1abc9c">下载</a>
							</div>
						</div>
					</div>
			</fieldset>
			</form>
			<form class="form-horizontal" name="tagforms3" method="post" enctype="multipart/form-data" action="<?php echo U('Equipment/index');?>">
			<fieldset>
				<div class="row-fluid" style="display: none">
					<div style="margin-top:10px;">					
						<div class="control-group">
							<label class="control-label" style="width: 120px; ">备注：</label>
							<div class="controls" style="margin-left: 130px;">
								<textarea id="content" maxlength='200' placeholder='不超过200字' name="content" style="width: 80%;height: 230px;"></textarea>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" style="width: 120px; ">上传意见文件：</label>
							<div class="controls" style="margin-left: 130px;">
								<input type="file" name="filename" onclick="showfiledata()" id="filenamestr" value="">
								<input type="hidden" id="filetype" value="0">
							</div>
						</div>
					</div>
					<div id="messageerror"></div>
					<div style="text-align: center;margin-top: 10px;">
						<input type="hidden" id="project_id" name="project_id" value="0">
						<input type="hidden" id="upid" name="upid" value="0">
						<a href="javascript:;" class="btn btn-primary" onclick="close_div()">取消</a>&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" class="btn btn-primary" onclick="eachSelect()">确认</a>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div id="bg" onclick="close_div()"></div>
	<div id="bg2" onclick="close_div2()"></div>
	<script src="/public/js/common.js"></script>	
	<script type="text/javascript">
	function  dochangetype(val)
	{
		console.log(val);
		location.href = '<?php echo U("Equipment/index");?>'+'&project_id='+val;
	}
	function showfiledata()
	{
		$("#filetype").val('1');
	}
	</script>
	<script type="text/javascript">
	function close_div() {
		$("#content").val('');
		$("#filenamestr").val('');
        $('.row-fluid').css('display','none');
        $('#bg').css('display','none');
    }
    function close_div2() {
        $('.row-fluid2').css('display','none');
        $('#bg2').css('display','none');
    }
    function show_div(id,ids) {
    	$("#project_id").val(id);  
    	$("#upid").val(ids);  	
        $("#bg").css('display','block');
        $('.row-fluid').css('display','block');     
    }
    function show_div2(projectid,ids,mid) {     
    	$("#bg2").css('display','block');
        $('.row-fluid2').css('display','block');      
        $.ajax({
        	url:"<?php echo U('Admin/Equipment/getmessageinfo');?>",
        	data:{mid:mid,project_id:projectid,upid:ids},
        	type:'POST',
        	success:function(data)
        	{
        		if(data.status==0)
        		{
					console.log(data);
        			$("#contentdesc").val(data.content);
        			$("#filename1").html(data.filename);
					if(data.filename !=null){
						$("#fileurldata").attr('href',data.url);
					}else{
						$("#fileurldata").css('background','#ccc');
					}
        		}
        	}
        })   
    }
	var istap=0;
	function eachSelect()
	{
		if(istap==1){return;}
		var msg =true;
		var content =$("#content").val();
		var filetype =$("#filetype").val();
		if(content == '' && filetype ==0)
		{
			$("#messageerror").show();
			$("#messageerror").html('备注与上传意见文件至少填写一个');
			istap=1;
			setTimeout(function(){
				$("#messageerror").hide();
				istap=0;	
			},2000);
			msg=false;
		}		
		if(msg == true)
		{
			$("#messageerror").html();
			 document.tagforms3.submit();
		}
	}
	</script>
</body>
</html>