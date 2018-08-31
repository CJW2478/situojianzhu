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
			<li class="active"><a href="javascript:;">结构专业</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Structural/index');?>">	
			项目： 
			<select name="project_id" style="width: 150px;" onchange="dochangetype(this.value)">
                <?php if(count($project) != 0): if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$va): $mod = ($i % 2 );++$i;?><option value="<?php echo ($va["id"]); ?>" <?php if($formget['project_id'] == $va['id']): ?>selected<?php endif; ?>><?php echo ($va["project_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>  
				<?php else: ?>
				<option>暂未分配项目</option><?php endif; ?>                          
            </select>
		</form>
		<ul class="nav nav-tabs pointUl">
			<li><a href="<?php echo U('Structural/index',array('project_id'=>$info['id']));?>">平面图</a></li>
			<li class="active"><a href="<?php echo U('Structural/zyinfolist',array('project_id'=>$info['id']));?>">各专业条件图</a></li>
			<li><a href="<?php echo U('Structural/bzysglist',array('project_id'=>$info['id']));?>">本专业施工图</a></li>
			<li><a href="<?php echo U('Structural/nbmessagelist',array('project_id'=>$info['id']));?>">施工图审查意见（内审、外审）<?php if($nbncount > 0): ?><span class="pointred">.</span><?php endif; ?></a></li>
			<li><a href="<?php echo U('Structural/doinglist',array('project_id'=>$info['id']));?>">进度动态</a></li>
			<li><a href="<?php echo U('Structural/allzylist',array('project_id'=>$info['id']));?>">各专业人员基本信息</a></li>
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
					<label class="control-label" style="margin-left: 15px;">各专业条件图</label>
			</div>
			<div class="control-group" style="margin-bottom: 0px;">
					<label class="control-label">&nbsp;</label>
					<div class="controls" style="margin-bottom: 5px;">	
						<?php if($count1 > 0): ?><a href="javascript:upload_onezyfile('文件上传','#thumb','file','','','<?php echo ($info["id"]); ?>','结构专业');">							
							<span class="btn" style="margin-left: -88px;background:#1abc9c">上传</span>
						</a>
						<?php else: ?>
						<a href="javascript:;">							
							<span class="btn" style="margin-left: -88px;background:#ccc;">上传</span>
						</a><?php endif; ?>	
						<span style="margin-left: 20px;color:#ccc;">注：请上传本专业条件图</span>												
					</div>
				</div>
			<table class="table table-hover table-bordered" style="width: 800px;margin-left:90px;">
				<thead>
					<tr>
						<th style="text-align: center;min-width: 80px;">所属专业</th>
						<th style="text-align: center;min-width: 80px;">上传时间</th>						
						<th style="text-align: center;min-width: 120px;">文件名</th>
						<th style="text-align: center;">上传人姓名</th>
						<th style="text-align: center;">操作</th>
					</tr>
				</thead>
				<tbody id="zytiaojianlist">
					<?php if(is_array($files)): $i = 0; $__LIST__ = $files;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
						<td style="text-align: center;"><?php echo ($val["role_name"]); ?></td>
						<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$val["createtime"])); ?></td>						
						<td style="text-align: center;"><?php echo ($val["filename"]); ?></td>
						<td style="text-align: center;"><?php echo ($val["user_name"]); ?></td>						
						<td style="text-align: center;"><a href="<?php echo U('Structural/downloadzy',array('id'=>$val['id']));?>" class="btn" style="background:#1abc9c">下载</a></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(count($files) == 0): ?><tr id="nonumber"><td colspan="5" style="text-align: center;">暂无专业条件图</td>
					</tr><?php endif; ?>
				</tbody>
			</table>
			<div class="pagination" style="margin-left: 90px;"><?php echo ($page); ?></div>
			
		</fieldset>
		</form>
	</div>
	<script src="/public/js/common.js"></script>	
	<script type="text/javascript">
	function  dochangetype(val)
	{
		location.href = '<?php echo U("Structural/index");?>'+'&project_id='+val;
	}
	</script>
</body>
</html>