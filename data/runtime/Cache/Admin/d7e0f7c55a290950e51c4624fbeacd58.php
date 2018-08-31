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
		display:none;position: fixed;  top: 10%;border-radius: 3px;  left: 28%; width: 27%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
	}
	.row-fluid-edit{
		display:none;position: fixed;  top: 10%;border-radius: 3px;  left: 28%; width: 27%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
	}
	#bg{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}
	#bg2{ display: none;  position: fixed;  top: 0%;  left: 0%;  width: 100%;  height: 100%;  background-color: black;  z-index:1001;  -moz-opacity: 0.7;  opacity:.70;  filter: alpha(opacity=70);}

	.table tr th,.table tr td{
		text-align: center;
	}
	.add-btn{
		margin-left: 22px;
	}
	#addmessage{text-align:center;color:red;}
	#editmessage{text-align:center;color:red;}
	table tbody{line-height: 45px;}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('user/index');?>">管理员</a></li>
		</ul>
		<form class="form-horizontal">
		<div class="controls">
			<a onclick="show_add()">
				<span class="btn" style="margin-left: -180px;background:#3b97d7;width: 80px;height: 25px;border-radius: 6px;margin-bottom: 10px;">添加管理员</span>
			</a>
		</div>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th style="min-width: 50px;text-align: center;">姓名</th>
					<th style="min-width: 50px;text-align: center;">角色</th>
					<th style="min-width: 50px;text-align: center;">电话</th>
					<th style="min-width: 50px;text-align: center;">职务</th>
					<th style="min-width: 50px;text-align: center;">qq号</th>
					<th style="min-width: 50px;text-align: center;">微信号</th>
					<th style="min-width: 50px;text-align: center;">添加时间</th>
					<th style="min-width: 50px;text-align: center;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php $user_statuses=array("0"=>L('USER_STATUS_BLOCKED'),"1"=>L('USER_STATUS_ACTIVATED'),"2"=>L('USER_STATUS_UNVERIFIED')); ?>
				<?php if(is_array($users)): foreach($users as $key=>$vo): ?><tr>
					<td style="text-align: center;"><?php echo ($vo["user_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["mobile"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["user_duty"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["qq_no"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["wx_no"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["create_time"]); ?></td>
					<td style="text-align: center;">
						<?php if($vo['user_id'] == sp_get_current_admin_id()): ?><font class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #cccccc;">编辑</font>
							<font class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #cccccc;">删除</font>
						<?php else: ?>
							<a onclick="show_edit('<?php echo ($vo["id"]); ?>')" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">编辑</a>
							<a onclick="delete_post('<?php echo ($vo["id"]); ?>')" class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">删除</a><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
		<div class="control-group" id="category-list">
			<div class="row-fluid" id="company_add" style="display: none">
				<div style="margin-bottom: 5px">
					<div>
					<div style="margin-bottom: 5px">
					<div style="padding:10px 0 0">
						<table style="margin:0 auto;">
							<tr>
								<td>手机号：</td>
								<td>
									<input type="text" name="mobile" maxlength="11"  style="width: 160px;" value="" id="mobile"/>
								</td>
							</tr>
							<tr>
								<td >登录密码：</td>
								<td>
									<input type="password" name="user_pass" value=""  style="width: 160px;" maxlength='20' id="user_pass" placeholder="6~20位数字或字母"/>
								</td>
							</tr>
							<tr>
								<td >真实姓名：</td>
								<td>
									<input type="text" name="user_name" value=""  style="width: 160px;" maxlength='10' id="user_name" placeholder="不超过10个字"/>
								</td>
							</tr>
							<tr>
								<td >职务：</td>
								<td>
									<input type="text" name="user_duty" value=""  style="width: 160px;" maxlength='6' id="user_duty" placeholder="不超过6个字"/>
								</td>
							</tr>
							<tr>
								<td >QQ号：</td>
								<td>
									<input type="text" name="qq_no"  value=""  style="width: 160px;" id="qq_no" />
								</td>
							</tr>
							<tr>
								<td >微信号：</td>
								<td>
									<input type="text" name="wx_no" value=""  style="width: 160px;" id="wx_no"/>
								</td>
							</tr>
							<tr>
								<td>角色：</td>
								<td>
									<select name="role_id" id="role_id"  style="width: 160px;">
										<?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
									</select>
								</td>
							</tr>
						</table>
					</div>
					</div>
				</div>
				</div>
				<div id="addmessage" style="display:none;"></div>
				<div style="height: 5px;border-bottom: 1px solid #ccc;"></div>
				<div style="text-align: center;margin-top: 10px;">
					<a href="javascript:;" class="btn btn-primary" onclick="close_div2()">取消</a>&nbsp;&nbsp;&nbsp;
					<div class="btn btn-primary" id="buttomstr" onclick="add_post()">确认</div>
				</div>
				<div class="row" id="page-info">
				</div>
			</div>

			<div class="row-fluid-edit" id="company_edit" style="display: none">
				<div style="margin-bottom: 5px">
					<div>
						<div style="margin-bottom: 5px">
							<div style=" padding:10px 0 0">
								<table style="margin: 0 auto">
									<tr>
										<td>手机号：</td>
										<td>
											<input type="text" name="mobile" maxlength="11" value=""  style="width: 160px;" id="edit_mobile"/>
											<input type="hidden" name="oldmobile" maxlength="11" value="" id="edit_oldmobile"/>
										</td>
									</tr>
									<tr>
										<td>登录密码：</td>
										<td>
											<input type="password" name="user_pass" value="" maxlength='20'  style="width: 160px;" id="edit_user_pass" placeholder="6~20位数字或字母"/>
										</td>
									</tr>
									<tr>
										<td>真实姓名：</td>
										<td>
											<input type="text" name="user_name" value="" maxlength='10'  style="width: 160px;" id="edit_user_name" placeholder="不超过10个字"/>
										</td>
									</tr>
									<tr>
										<td>职务：</td>
										<td>
											<input type="text" name="user_duty" value="" maxlength='6' style="width: 160px;" id="edit_user_duty" placeholder="不超过6个字"/>
										</td>
									</tr>
									<tr>
										<td>QQ号：</td>
										<td>
											<input type="text" name="qq_no"  value="" id="edit_qq_no"  style="width: 160px;"/>
										</td>
									</tr>
									<tr>
										<td>微信号：</td>
										<td>
											<input type="text" name="wx_no" value="" id="edit_wx_no" style="width: 160px;"/>
										</td>
									</tr>
									<tr>
										<td>角色：</td>
										<td>
											<select name="role_id" id="edit_role_id"  style="width: 160px;">
												<?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
											</select>
											<input type="hidden" name="oldrole_id" id="oldrole_id" value="0">
										</td>
									</tr>
								</table>
							</div>
							<input type="hidden" id="id" value="">
							<input type="hidden" id="olduser_pass" value="">
							</div>
						</div>
						</div>
					<div id="editmessage" style="display:none;"></div>
					<div style="height: 5px;border-bottom: 1px solid #ccc;"></div>
					<div style="text-align: center;margin-top: 10px;">
					<a href="javascript:;" class="btn btn-primary" onclick="close_div()">取消</a>&nbsp;&nbsp;&nbsp;
					<div class="btn btn-primary" id="buttomstr2" onclick="edit_post()">确认</div>
				</div>
				<div class="row">
				</div>
			</div>
		</div>
	</div>
	<div id="bg" onclick="close_div()"></div>
	<div id="bg2" onclick="close_div2()"></div>
<script src="/public/js/common.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
	var istap=0;
    function close_div() {		
		$("#editmessage").hide();      
        $('.row-fluid-edit').css('display','none');
        $('#bg').css('display','none');
    }
	function close_div2() {
		$("#mobile").val('');
		$("#user_pass").val('');
		$("#user_name").val('');
		$("#user_duty").val('');
		$("#qq_no").val('');
		$("#wx_no").val('');
		$("#addmessage").hide();		
        $('.row-fluid').css('display','none');       
        $('#bg2').css('display','none');
    }
    function show_add() {
        $("#bg2").css('display','block');
        $('#company_add').css('display','block');
    }

    function add_post(){
		if(istap==1)
		{
			return;
		}
        var mobile = $('#mobile').val();
        var user_pass = $('#user_pass').val();
        var role_id = $('#role_id option:selected').val();
        var user_name = $('#user_name').val();
        var user_duty = $('#user_duty').val();
        var qq_no = $('#qq_no').val();
        var wx_no = $('#wx_no').val();
		if(mobile == '' || user_name=='' || user_pass=='' || user_duty=='' || qq_no=='' || wx_no=='')
		{			
			$("#addmessage").show();
            $("#addmessage").html('请填写完整信息');
			istap=1;			
			setTimeout(function(){
				$("#addmessage").hide();
				istap=0;	
			},2000)
		}else{
			
			$.ajax({
				url: "<?php echo U('User/add_post');?>",
				type: 'POST',
				data: {mobile:mobile,user_pass:user_pass,role_id:role_id,user_name:user_name,user_duty:user_duty,qq_no:qq_no,wx_no:wx_no},
				dataType:"json",
				success:function (res) {
					console.log(res);
					if(res.status == 0){
						close_div2();
						$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});                   
						location.href='<?php echo U("User/index");?>';                    
					} else {
						$("#addmessage").show();
						$("#addmessage").html(res.msg);
						istap=1;
						setTimeout(function(){
							$("#addmessage").hide();
							istap=0;
						},2000)
					}
				}
			});
		}
    }

    function show_edit(id) {
        $.ajax({
            url: "<?php echo U('User/edit');?>",
            type: 'POST',
            data: {id:id},
            dataType:"json",
            success:function (res) {
                if(res.status == 0){
                    $("#bg").css('display','block');
                    $('#company_edit').css('display','block');
                    document.getElementById('edit_mobile').value = res.data.mobile;
					document.getElementById('edit_oldmobile').value = res.data.mobile;
                    document.getElementById('edit_user_pass').value = res.data.user_pass;
					document.getElementById('olduser_pass').value = res.data.user_pass;
                    document.getElementById('edit_user_name').value = res.data.user_name;
                    document.getElementById('edit_user_duty').value = res.data.user_duty;
                    document.getElementById('edit_qq_no').value = res.data.qq_no;
                    document.getElementById('edit_wx_no').value = res.data.wx_no;
                    document.getElementById('edit_role_id').value = res.data.role_id;
					document.getElementById('oldrole_id').value = res.data.role_id;
                    document.getElementById('id').value = id;
                } else {
                    $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                }
            }
        });
    }

    function edit_post(){
        if(istap==1)
		{
			return;
		}
        var mobile = $('#edit_mobile').val();
		var oldmobile = $('#edit_oldmobile').val();
        var user_pass = $('#edit_user_pass').val();
		var olduser_pass = $('#olduser_pass').val();
        var role_id = $('#edit_role_id option:selected').val();
		var oldrole_id =$("#oldrole_id").val();
        var user_name = $('#edit_user_name').val();
        var user_duty = $('#edit_user_duty').val();
        var qq_no = $('#edit_qq_no').val();
        var wx_no = $('#edit_wx_no').val();
        var id = $('#id').val();
		if(mobile == '' || user_name=='' || user_pass=='' || user_duty=='' || qq_no=='' || wx_no=='')
		{			
			$("#editmessage").show();
            $("#editmessage").html('请填写完整信息');
			istap=1;
			setTimeout(function(){
				$("#editmessage").hide();
				istap=0;
			},2000)
		}else{
			$.ajax({
				url: "<?php echo U('User/edit_post');?>",
				type: 'POST',
				data: {mobile:mobile,oldmobile:oldmobile,user_pass:user_pass,olduser_pass:olduser_pass,role_id:role_id,oldrole_id:oldrole_id,user_name:user_name,user_duty:user_duty,qq_no:qq_no,wx_no:wx_no,id:id},
				dataType:"json",
				success:function (res) {
					if(res.status == 0){
						close_div();
						$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});                   
						location.href='<?php echo U("User/index");?>';                    
					} else {
						$("#editmessage").show();
						$("#editmessage").html(res.msg);
						istap=1;
						setTimeout(function(){
							$("#editmessage").hide();
							istap=0;
						},2000)
					}
				}
			});
		}
    }

    function delete_post(id){
         $.dialog({id: 'popup', lock: true,icon:"question", content: "是否确认删除该管理员？",cancel: true, ok: function () {
            $.ajax({
                url: "<?php echo U('User/delete');?>",
                type: 'POST',
                data: {id:id},
                dataType:"json",
                success:function (res) {
                    if(res.status == 0){                       
                        location.href='<?php echo U("User/index");?>';                       
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