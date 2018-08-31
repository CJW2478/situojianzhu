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
			<li class="active"><a href="<?php echo U('rbac/index');?>">角色管理</a></li>
			<li><a href="<?php echo U('rbac/roleadd');?>">角色添加</a></li>
		</ul>
		<form action="<?php echo U('Rbac/listorders');?>" method="post">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						
						<th style="text-align:center;min-width:100px;"><?php echo L('ROLE_NAME');?></th>					
						<th style="text-align:center;min-width:80px;">添加时间</th>
						<th style="text-align:center;"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($roles)): foreach($roles as $key=>$vo): if($vo['id'] != 1): ?><tr>						
						<td style="text-align:center;"><?php echo ($vo["name"]); ?></td>						
						<td style="text-align:center;"><?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?>
						</td>
						<td style="text-align:center;">	
							<?php if($vo['id'] > 12): ?><a href="<?php echo U('Rbac/authorize',array('id'=>$vo['id']));?>" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;"><?php echo L('ROLE_SETTING');?></a>
							<a href="<?php echo U('Rbac/roleedit',array('id'=>$vo['id']));?>" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;"><?php echo L('EDIT');?></a>
							<a class="btn btn-primary js-ajax-delete" href="<?php echo U('Rbac/roledelete',array('id'=>$vo['id']));?>" style="padding: 2px 15px;color: white;background-color: #1dccaa;"><?php echo L('DELETE');?></a><?php endif; ?>							
						</td>
					</tr><?php endif; endforeach; endif; ?>
				</tbody>
			</table>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>