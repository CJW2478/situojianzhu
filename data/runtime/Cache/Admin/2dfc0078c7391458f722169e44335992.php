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
			<li class="active"><a href="<?php echo U('UserManage/index');?>">用户管理</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('userManage/index');?>">
            <input type="text" name="user_info" style="width: 150px;" value="<?php  echo trim($_REQUEST['user_info'])?>" placeholder="请输入联系方式/姓名">
			<select id="navcid_select" name="company_id">
				<option selected="selected" value="">全部</option>
				<?php if(is_array($company)): foreach($company as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if($vo['id'] == $company_id): ?>selected<?php endif; ?>><?php echo ($vo["company_name"]); ?></option><?php endforeach; endif; ?>
			</select>
            <input type="submit" class="btn btn-primary" value="查询" />
            <a class="btn btn-danger" href="<?php echo U('userManage/index');?>">清空</a>
        </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th  style="min-width: 50px;text-align: center;">用户姓名</th>
					<th  style="min-width: 50px;text-align: center;"><?php echo L('COMPANY');?></th>
					<th  style="min-width: 50px;text-align: center;"><?php echo L('DUTY');?></th>
					<th  style="min-width: 50px;text-align: center;"><?php echo L('MOBILE');?></th>
					<th  style="min-width: 50px;text-align: center;"><?php echo L('CREATE_TIME');?></th>
					<th  style="min-width: 50px;text-align: center;"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php if(is_array($account)): foreach($account as $key=>$vo): ?><tr>
					<td style="text-align: center;"><?php echo ($vo["user_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["company_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["user_duty"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["mobile"]); ?></td>
					<td style="text-align: center;"><?php echo (date('Y-m-d H:i',$vo["create_time"])); ?></td>
					<td style="text-align: center;">
						<?php if($vo['status'] == 1): ?><a onclick="change_status('<?php echo ($vo["id"]); ?>',2)" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">冻结</a>
							<?php else: ?>
							<a onclick="change_status('<?php echo ($vo["id"]); ?>',1)" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">启用</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
<script src="/public/js/common.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
	function change_status(id,status) {
		if(status == 2)
		{
			$.dialog({id: 'popup', lock: true,icon:"question", content: "是否确认冻结该用户？",cancel: true, ok: function () {
				$.ajax({
					url: "<?php echo U('UserManage/change_status');?>",
					type: 'POST',
					data:{id:id,status:status},
					dataType:"json",
					success:function (res) {
						if(res.status == 0){
							$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});							
							location.href='<?php echo U("UserManage/index");?>';							
						} else {
							$.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
						}
					}
				});
			}})
		}else{
			$.dialog({id: 'popup', lock: true,icon:"question", content: "是否确认启用该用户？",cancel: true, ok: function () {
				$.ajax({
					url: "<?php echo U('UserManage/change_status');?>",
					type: 'POST',
					data:{id:id,status:status},
					dataType:"json",
					success:function (res) {
						if(res.status == 0){
							$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});							
							location.href='<?php echo U("UserManage/index");?>';							
						} else {
							$.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
						}
					}
				});
			}})
		}		
			
    }
</script>
</body>
</html>