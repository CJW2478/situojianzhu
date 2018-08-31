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
			<li class="active"><a href="javascript:;">账号管理</a></li>	
			<li><a href="<?php echo U('member/add');?>">新增</a></li>				
		</ul>
		<form class="js-ajax-form" action="" method="post">
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th style="min-width: 50px;text-align: center;">ID</th>
						<th style="min-width: 100px;text-align: center;">姓名</th>
						
						<th style="min-width: 100px;text-align: center;">职务</th>
						<th style="min-width: 100px;text-align: center;">手机号</th>
						<th style="min-width: 80px;text-align: center;">添加时间</th>
						<th style="min-width: 100px;text-align: center;">操作</th>
					</tr>
				</thead>
				<?php if(is_array($member)): foreach($member as $key=>$vo): ?><tr>
                    <td style="text-align: center;"><b><?php echo ($vo["id"]); ?></b></td>
					<td style="text-align: center;"><?php echo ($vo["user_name"]); ?></td>
					
					<td style="text-align: center;"><?php if($vo['user_duty'] != ''): echo ($vo["user_duty"]); else: ?>--<?php endif; ?></td>
					<td style="text-align: center;"><?php echo ($vo["mobile"]); ?></td>
					<td style="text-align: center;"><?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?></td>
					<td style="text-align: center;">
						<a href="<?php echo U('Member/edit',array('id'=>$vo['id']));?>" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">编辑</a>
						<a href="javascript:;" onclick="delete_post('<?php echo ($vo["id"]); ?>')" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">删除</a>  						
					</td>
				</tr><?php endforeach; endif; ?>				
			</table>
			<div class="pagination" style="float: right;"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/company/public/js/common.js"></script>
	<script type="text/javascript">
		function delete_post(id){
         $.dialog({id: 'popup', lock: true,icon:"question", content: "是否确认删除该用户？",cancel: true, ok: function () {
            $.ajax({
                url: "<?php echo U('Member/delete');?>",
                type: 'POST',
                data: {id:id},
                dataType:"json",
                success:function (res) {
                    if(res.status == 0){                       
                            location.href='<?php echo U("Member/index");?>';                       
                    } else {
                        $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                    }
                }
            });
        }})
    }
	</script>
</body>
</html>