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
		display:none;position: fixed;  top: 10%;border-radius: 3px;  left: 28%; width: 40%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
	}
	.row-fluid-edit{
		display:none;position: fixed;  top: 10%;border-radius: 3px;  left: 28%; width: 40%; overflow:hidden; overflow-y: auto;  padding: 8px;  border: 1px solid #E8E9F7;  background-color: white;  z-index:10003;
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
	table tbody{line-height:45px;}
</style>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="<?php echo U('Project/index');?>">项目管理</a></li>
		</ul>
        <form class="well form-search" method="post" action="<?php echo U('Project/index');?>">
            <input type="text" name="project_info" style="width: 300px;" value="<?php echo ((isset($formget["project_info"]) && ($formget["project_info"] !== ""))?($formget["project_info"]):''); ?>" placeholder="请输入项目编号或项目名称">
            <input type="submit" class="btn btn-primary" value="查询" />
            <a class="btn btn-danger" href="<?php echo U('Project/index');?>">清空</a>
        </form>
		<form class="form-horizontal">
		<div class="controls">
			<a onclick="show_add()">
				<span class="btn" style="margin-left: -180px;background:#3b97d7;width: 80px;height: 25px;border-radius: 6px;margin-bottom: 10px;">添加</span>
			</a>
		</div>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th  style="min-width: 50px;text-align: center;">项目名称</th>
					<th  style="min-width: 50px;text-align: center;">项目编号</th>
					<th  style="min-width: 50px;text-align: center;">创建日期</th>
					<th  style="min-width: 50px;text-align: center;">所属公司</th>
					<th  style="min-width: 50px;text-align: center;">甲方项目负责人</th>
					<th  style="min-width: 50px;text-align: center;">职务</th>
					<th  style="min-width: 50px;text-align: center;">联系电话</th>
					<th  style="min-width: 50px;text-align: center;">QQ号</th>
					<th  style="min-width: 50px;text-align: center;">微信号</th>
					<th  style="min-width: 50px;text-align: center;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php if(is_array($project)): foreach($project as $key=>$vo): ?><tr>
					<td style="text-align: center;"><?php echo ($vo["project_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["project_no"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["create_time"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["company_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["principal_name"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["duty"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["mobile"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["qq"]); ?></td>
					<td style="text-align: center;"><?php echo ($vo["wx"]); ?></td>
					<td style="text-align: center;">
						<a onclick="show_edit('<?php echo ($vo["id"]); ?>')"  class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #1dccaa;">编辑</a>
						<a onclick="delete_post('<?php echo ($vo["id"]); ?>')"  class="btn btn-primary" style="padding: 2px 15px;color: white;background-color: #990000;">删除</a>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
			<div class="control-group" id="category-list">
				<div class="row-fluid" id="company_add" style="display: none">
					<div style="margin-bottom: 5px">
						<div style="padding:10px 0 0">
							<table style="margin:0 auto;">
								<tr>
									<td>项目名称：</td>
									<td>
										<input type="text" name="project_name" value="" maxlength="30" id="project_name" placeholder="不超过三十个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>项目编号：</td>
									<td>
										<input type="text" name="project_no" value="" maxlength="16" id="project_no" placeholder="不超过十六个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
								<td>所属公司：</td>
								<td>
									<select name="company_id" id="company_id">
										<option value="0">选择公司</option>
										<?php if(is_array($company)): foreach($company as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["company_name"]); ?></option><?php endforeach; endif; ?>
									</select>
								</td>
								</tr>
								<tr>
									<td>甲方项目负责人：</td>
									<td>
										<input type="text" name="principal_name" value="" maxlength="6" id="principal_name" placeholder="不超过六个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>甲方负责人职务：</td>
									<td>
										<input type="text" name="duty" value="" id="duty" maxlength="10" placeholder="不超过十个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
								<td>甲方负责人电话：</td>
								<td>
									<input type="text" name="mobile" value="" maxlength="11" id="mobile" style="width: 250px"/>
								</td>
								</tr>
								<tr>
								<td>甲方负责人QQ：</td>
								<td>
									<input type="text" name="qq" value="" id="qq" style="width: 250px"/>
								</td>
								</tr>
								<tr>
								<td>甲方负责人微信：</td>
								<td>
									<input type="text" name="wx" value="" id="wx" style="width: 250px"/>
								</td>
								</tr>
							</table>
						</div>						
					</div>
					<div id="addmessage" style="display:none;"></div>
					<div style="height: 5px;border-bottom: 1px solid #ccc;"></div>
					<div style="text-align: center;margin-top: 10px;">
						<a href="javascript:;" class="btn btn-primary" onclick="close_div2()">取消</a>&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" class="btn btn-primary" onclick="add_post()">确认</a>
					</div>
					<div class="row" id="page-info">
					</div>
				</div>

				<div class="row-fluid-edit" id="company_edit" style="display: none">
					<div style="margin-bottom: 5px">
						<div style="padding:10px 0 0">
							<table style="margin:0 auto">
								<tr>
									<td>项目名称：</td>
									<td>
										<input type="text" name="project_name" value="" maxlength="30" id="edit_project_name" placeholder="不超过三十个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>项目编号：</td>
									<td>
										<input type="text" name="project_no" value="" id="edit_project_no" maxlength="16" placeholder="不超过十六个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>所属公司：</td>
									<td>
										<select name="company_id" id="edit_company_id">
											<option value="0">选择公司</option>
											<?php if(is_array($company)): foreach($company as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["company_name"]); ?></option><?php endforeach; endif; ?>
										</select>
									</td>
								</tr>
								<tr>
									<td>甲方项目负责人：</td>
									<td>
										<input type="text" name="principal_name" value="" maxlength="6" id="edit_principal_name" placeholder="不超过六个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>甲方负责人职务：</td>
									<td>
										<input type="text" name="duty" value="" id="edit_duty" maxlength="10" placeholder="不超过十个字符" style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>甲方负责人电话：</td>
									<td>
										<input type="text" name="mobile" value="" maxlength="11" id="edit_mobile" style="width: 250px"/>
										<input type="hidden"  value="" maxlength="11" id="edit_oldmobile" />
									</td>
								</tr>
								<tr>
									<td>甲方负责人QQ：</td>
									<td>
										<input type="text" name="qq" value="" id="edit_qq"  style="width: 250px"/>
									</td>
								</tr>
								<tr>
									<td>甲方负责人微信：</td>
									<td>
										<input type="text" name="wx" value="" id="edit_wx"  style="width: 250px"/>
									</td>
								</tr>
								<input type="hidden" id="project_id" value="">
							</table>
						</div>						
					<div id="editmessage" style="display:none;"></div>
					<div style="height: 5px;border-bottom: 1px solid #ccc;"></div>
					<div style="text-align: center;margin-top: 10px;">
						<a href="javascript:;" class="btn btn-primary" onclick="close_div()">取消</a>&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" class="btn btn-primary" onclick="edit_post()">确认</a>
					</div>
					<div class="row">
					</div>
				</div>
			</div>
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<div id="bg" onclick="close_div()"></div>
	<div id="bg2" onclick="close_div2()"></div>
<script src="/public/js/common.js"></script>
<script src="/public/js/artDialog/artDialog.js"></script>
<script type="text/javascript">
		var istap=0;
        function close_div() {
			$("#addmessage").hide();
			$("#editmessage").hide();
            $('.row-fluid').css('display','none');
            $('.row-fluid-edit').css('display','none');
            $('#bg').css('display','none');
        }
		 function close_div2() {
			$("#project_name").val('');
			$("#project_no").val('');
			$("#company_id").val(0);
			$("#principal_name").val('');
			$("#duty").val('');
			$("#mobile").val('');
			$("#qq").val('');
			$("#wx").val('');
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
            var project_name = $('#project_name').val();
            var project_no = $('#project_no').val();
            var company_id = $('#company_id option:selected').val();
            var principal_name = $('#principal_name').val();
            var duty = $('#duty').val();
            var mobile = $('#mobile').val();
            var qq = $('#qq').val();
            var wx = $('#wx').val();
			if(mobile == '' || project_name=='' || project_no=='' || duty=='' || principal_name=='' || company_id==0)
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
					url: "<?php echo U('Project/add_post');?>",
					type: 'POST',
					data: {project_name:project_name,project_no:project_no,company_id:company_id,principal_name:principal_name,duty:duty,mobile:mobile,qq:qq,wx:wx},
					dataType:"json",
					success:function (res) {
						if(res.status == 0){
							close_div2();
							$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});                       
								location.href='<?php echo U("Project/index");?>';                       
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
                url: "<?php echo U('Project/edit');?>",
                type: 'POST',
                data: {id:id},
                dataType:"json",
                success:function (res) {
                    if(res.status == 0){
                        $("#bg").css('display','block');
                        $('#company_edit').css('display','block');
						
                        document.getElementById('edit_project_name').value = res.data.project_name;
                        document.getElementById('edit_project_no').value = res.data.project_no;
                        document.getElementById('edit_company_id').value = res.data.company_id;
                        document.getElementById('edit_principal_name').value = res.data.principal_name;
                        document.getElementById('edit_duty').value = res.data.duty;
                        document.getElementById('edit_mobile').value = res.data.mobile;
						document.getElementById('edit_oldmobile').value = res.data.mobile;
                        document.getElementById('edit_qq').value = res.data.qq;
                        document.getElementById('edit_wx').value = res.data.wx;
                        document.getElementById('project_id').value = res.data.id;
                    } else {
                        $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                    }
                }
            });
        }

        function edit_post(){
            if(istap==1){return;}
            var project_name = $('#edit_project_name').val();
            var project_no = $('#edit_project_no').val();
            var company_id = $('#edit_company_id option:selected').val();
            var principal_name = $('#edit_principal_name').val();
            var duty = $('#edit_duty').val();
            var mobile = $('#edit_mobile').val();
			var oldmobile = $('#edit_oldmobile').val();
            var qq = $('#edit_qq').val();
            var wx = $('#edit_wx').val();
            var project_id = $('#project_id').val();
			if(mobile == '' || project_name=='' || project_no=='' || duty=='' || principal_name=='' || company_id==0)
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
					url: "<?php echo U('Project/edit_post');?>",
					type: 'POST',
					data: {project_name:project_name,project_no:project_no,company_id:company_id,principal_name:principal_name,duty:duty,mobile:mobile,oldmobile:oldmobile,qq:qq,wx:wx,id:project_id},
					dataType:"json",
					success:function (res) {
						if(res.status == 0){
							close_div();
							$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});                       
							location.href='<?php echo U("Project/index");?>';                      
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
            $.dialog({id: 'popup', lock: true,icon:"question", content: "是否确认删除该项目？",cancel: true, ok: function () {
                $.ajax({
                    url: "<?php echo U('project/delete');?>",
                    type: 'POST',
                    data: {id:id,isdel:1},
                    dataType:"json",
                    success:function (res) {
                        if(res.status == 0){     
							$.dialog({id: 'popup', lock: true,icon:"succeed", content: res.msg, time: 2});   
                            location.href='<?php echo U("Project/index");?>';                            
                        } else {
                            $.dialog({id: 'popup', lock: true,icon:"warning", content: res.msg, time: 2});
                        }
                    }
                });
            }});
        }
	</script>
</body>
</html>