<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title>MetLife后台管理</title>

	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome必须的css -->
	<link rel="stylesheet" href="/public/ace/css/bootstrap.min.css" />
	<link rel="stylesheet" href="/public/font-awesome/css/font-awesome.min.css" />
        <link rel="stylesheet" href="/public/datetimepicker/jquery.datetimepicker.css" />
	<!-- 此页插件css -->

	<!-- ace的css -->
	<link rel="stylesheet" href="/public/ace/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<!-- IE版本小于9的ace的css -->
	<!--[if lte IE 9]>
	<link rel="stylesheet" href="/public/ace/css/ace-part2.min.css" class="ace-main-stylesheet" />
	<![endif]-->

	<!--[if lte IE 9]>
	<link rel="stylesheet" href="/public/ace/css/ace-ie.css" />
	<![endif]-->

	<link rel="stylesheet" href="/public/yfcmf/yfcmf.css" />
	<!-- 此页相关css -->

	<!-- ace设置处理的css -->

	<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

	<!--[if lte IE 8]>
	<script src="/public/others/html5shiv.min.js"></script>
	<script src="/public/others/respond.min.js"></script>
	<![endif]-->
    <!-- 引入基本的js -->
    <script type="text/javascript">
        var admin_ueditor_handle = "<?php echo U('Sys/upload');?>";
        var admin_ueditor_lang ='zh-cn';
    </script>
    <!--[if !IE]> -->
    <script src="/public/others/jquery.min-2.2.1.js"></script>
    <script src="/public/datetimepicker/jquery.datetimepicker.full.js"></script>
    <script src="/public/echart/echart.js"></script>
    <!-- <![endif]-->
    <!-- 如果为IE,则引入jq1.12.1 -->
    <!--[if IE]>
    <script src="/public/others/jquery.min-1.12.1.js"></script>
    <![endif]-->

    <!-- 如果为触屏,则引入jquery.mobile -->
    <script type="text/javascript">
        if('ontouchstart' in document.documentElement) document.write("<script src='/public/others/jquery.mobile.custom.min.js'>"+"<"+"/script>");
    </script>
    <script src="/public/others/bootstrap.min.js"></script>
</head>

<body class="no-skin">
<!-- 导航栏开始 -->
<div id="navbar" class="navbar navbar-default navbar-fixed-top">
	<div class="navbar-container" id="navbar-container">
		<!-- 导航左侧按钮手机样式开始 -->
		<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
			<span class="sr-only">Toggle sidebar</span>

			<span class="icon-bar"></span>

			<span class="icon-bar"></span>

			<span class="icon-bar"></span>
		</button><!-- 导航左侧按钮手机样式结束 -->
		<button data-target="#sidebar2" data-toggle="collapse" type="button" class="pull-left navbar-toggle collapsed">
			<span class="sr-only">Toggle sidebar</span>
			<i class="ace-icon fa fa-dashboard white bigger-125"></i>
		</button>
		<!-- 导航左侧正常样式开始 -->
		<div class="navbar-header pull-left">
			<!-- logo -->
			<a href="<?php echo U('Index/index');?>" class="navbar-brand">
				<small>
					<i class="fa fa-leaf"></i>
					MetLife
				</small>
			</a>
		</div><!-- 导航左侧正常样式结束 -->

		<!-- 导航栏开始 -->
		<div class="navbar-buttons navbar-header pull-right" role="navigation">
			<ul class="nav ace-nav">

				<li class="purple">
					<a data-info="确定要清理缓存吗？" class="confirm-rst-btn" href="<?php echo U('Sys/clear');?>">
						清除缓存
					</a>
				</li>

				<!-- 用户菜单开始 -->
				<li class="light-blue dropdown-modal">
					<a data-toggle="dropdown" href="#" class="dropdown-toggle">
						<?php if($_SESSION['admin_avatar'] != ''): ?><img class="nav-user-photo" src="/data/upload/avatar/<?php echo ($_SESSION['admin_avatar']); ?>" alt="<?php echo ($_SESSION['admin_username']); ?>" />
							<?php else: ?>
							<img class="nav-user-photo" src="/public/img/girl.jpg" alt="<?php echo ($_SESSION['admin_username']); ?>" /><?php endif; ?>
								<span class="user-info">
									<small>欢迎,</small>
									<?php echo ($_SESSION['admin_username']); ?>
								</span>

						<i class="ace-icon fa fa-caret-down"></i>
					</a>

					<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
						<li>
							<a href="<?php echo U('Sys/profile');?>">
								<i class="ace-icon fa fa-user"></i>
								会员中心
							</a>
						</li>

						<li class="divider"></li>

						<li>
							<a href="<?php echo U('Login/logout');?>"  data-info="你确定要退出吗？" class="confirm-btn">
								<i class="ace-icon fa fa-power-off"></i>
								注销
							</a>
						</li>
					</ul>
				</li><!-- 用户菜单结束 -->
			</ul>
		</div><!-- 导航栏结束 -->
	</div><!-- 导航栏容器结束 -->
</div><!-- 导航栏结束 -->

<!-- 整个页面内容开始 -->
<div class="main-container" id="main-container">
    <!-- 菜单栏开始 -->
    <div id="sidebar" class="sidebar responsive sidebar-fixed">
	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<!--左侧顶端按钮-->
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<a class="btn btn-success" href="<?php echo U('Member/member_list');?>" role="button" title="客户列表"><i class="ace-icon fa fa-signal"></i></a>
			<a class="btn btn-info" href="<?php echo U('Member/Member_add');?>" role="button" title="添加用户"><i class="ace-icon fa fa-pencil"></i></a>
<!--			<a class="btn btn-warning" href="<?php echo U('Member/member_list');?>" role="button" title="会员列表"><i class="ace-icon fa fa-users"></i></a>-->
<!--			<a class="btn btn-danger" href="<?php echo U('Sys/sys');?>" role="button" title="站点设置"><i class="ace-icon fa fa-cogs"></i></a>-->
                        <a class="btn btn-warning" href="<?php echo U('Member/member_product_list');?>" role="button" title="签单列表"><i class="ace-icon fa fa-signal"></i></a>
                        <a class="btn btn-danger" href="<?php echo U('Member/Member_product_add');?>" role="button" title="添加签单"><i class="ace-icon fa fa-pencil"></i></a>
		</div>
		<!--左侧顶端按钮（手机）-->
		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<a class="btn btn-success" href="<?php echo U('News/news_list');?>" role="button" title="文章列表"></a>
			<a class="btn btn-info" href="<?php echo U('News/news_add');?>" role="button" title="添加文章"></a>
			<a class="btn btn-warning" href="<?php echo U('Member/member_list');?>" role="button" title="会员列表"></a>
			<a class="btn btn-danger" href="<?php echo U('Sys/sys');?>" role="button" title="站点设置"></a>
		</div>
	</div>
	<!-- 菜单列表开始 -->
	<ul class="nav nav-list">
		<!--一级菜单遍历开始-->
		<?php if(is_array($menus)): foreach($menus as $key=>$v): if(!empty($v['_child'])): ?><li class="<?php if((count($menus_curr) >= 1) AND ($menus_curr[0] == $v['id'])): ?>open<?php endif; ?>">
			<a href="#" class="dropdown-toggle">
				<i class="menu-icon fa <?php echo ($v["css"]); ?>"></i>
				<span class="menu-text"><?php echo ($v["title"]); ?></span>
				<b class="arrow fa fa-angle-down"></b>
			</a>
			<ul class="submenu">
				<!--二级菜单遍历开始-->
				<?php if(is_array($v["_child"])): foreach($v["_child"] as $key=>$vv): if(!empty($vv['_child'])): ?><li class="<?php if((count($menus_curr) >= 2) AND ($menus_curr[1] == $vv['id'])): ?>active open<?php endif; ?>">
					<a href="#" class="dropdown-toggle">
						<i class="menu-icon fa fa-caret-right"></i>
						<?php echo ($vv["title"]); ?>
						<b class="arrow fa fa-angle-down"></b>
					</a>
					<b class="arrow"></b>
					<ul class="submenu">
						<!--三级菜单遍历开始-->
						<?php if(is_array($vv["_child"])): foreach($vv["_child"] as $key=>$vvv): ?><li class="<?php if((count($menus_curr) >= 3) AND ($menus_curr[2] == $vvv['id'])): ?>active<?php endif; ?>">
							<a href="<?php echo U($vvv['name']);?>">
								<i class="menu-icon fa fa-caret-right"></i>
								<?php echo ($vvv["title"]); ?>
							</a>
							<b class="arrow"></b>
							</li><?php endforeach; endif; ?><!--三级菜单遍历结束-->
					</ul>
					</li>
					<?php else: ?>
					<li class="<?php if((count($menus_curr) >= 2) AND ($menus_curr[1] == $vv['id'])): ?>active<?php endif; ?>">
					<a href="<?php echo U($vv['name']);?>">
						<i class="menu-icon fa fa-caret-right"></i>
						<?php echo ($vv["title"]); ?>
					</a>
					<b class="arrow"></b>
					</li><?php endif; endforeach; endif; ?><!--二级菜单遍历结束-->
			</ul>
			</li>
			<?php else: ?>
			<li class="<?php if((count($menus_curr) >= 1) AND ($menus_curr[0] == $v['id'])): ?>active<?php endif; ?>">
			<a href="<?php echo U($v['name']);?>">
				<i class="menu-icon fa fa-caret-right"></i>
				<?php echo ($v["title"]); ?>
			</a>
			<b class="arrow"></b>
			</li><?php endif; endforeach; endif; ?><!--一级菜单遍历结束-->
	</ul><!-- 菜单列表结束 -->

	<!-- 菜单栏缩进开始 -->
	<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
		<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
	</div><!-- 菜单栏缩进结束 -->
</div>
    <!-- 菜单栏结束 -->

    <!-- 主要内容开始 -->
    <div class="main-content">
        <div class="main-content-inner">
            <!-- 右侧主要内容页顶部标题栏开始 -->
            <div id="sidebar2" class="sidebar h-sidebar navbar-collapse collapse sidebar-fixed menu-min" data-sidebar="true" data-sidebar-scroll="true" data-sidebar-hover="true">
    <div class="nav-wrap-up pos-rel">
        <div class="nav-wrap">
            <ul class="nav nav-list">
                <?php if($id_curr != ''): if(is_array($menus_child)): foreach($menus_child as $key=>$k): ?><li>
                            <a href="<?php echo U(''.$k['name'].'');?>">
                                <o class="font12 <?php if($id_curr == $k['id']): ?>rigbg<?php endif; ?>"><?php echo ($k["title"]); ?></o>
                            </a>
                            <b class="arrow"></b>
                        </li><?php endforeach; endif; ?>
                    <?php else: ?>
                    <li>
                        <a href="<?php echo U('Index/index');?>">
                            <o class="font12">欢迎使用MetLife后台系统管理</o>
                        </a>
                        <b class="arrow"></b>
                    </li><?php endif; ?>
            </ul>
        </div>
    </div><!-- /.nav-list -->
</div>
            <!-- 右侧主要内容页顶部标题栏结束 -->

            <!-- 右侧下主要内容开始 -->
            
    <div class="page-content">
        <link rel="stylesheet" type="text/css" media="all" href="/public/sldate/daterangepicker-bs3.css" />
        <div class="row maintop">
            <?php if(($auth!=common)): ?><div class="col-xs-12 col-sm-1">
                <a href="<?php echo U('member_add');?>">
                    <button class="btn btn-xs btn-danger">
                        <i class="ace-icon fa fa-bolt bigger-110"></i>
                        添加客户
                    </button>
                </a>
            </div>
            <div class="col-xs-12 col-sm-1">
                <a href="<?php echo U('member_import');?>">
                    <button class="btn btn-xs btn-danger">
                        <i class="ace-icon fa fa-bolt bigger-110"></i>
                        表格批量导入
                    </button>
                </a>
            </div>
                <div class='col-sm-1'>
                    <select onchange="javascript:location.href=this.value;">
                        <option value="<?php echo U('member_list',array('status'=>0));?>" <?php if(($status==0)): ?>selected<?php endif; ?>>所有客户</option>
                        <option value="<?php echo U('member_list',array('status'=>1));?>" <?php if(($status==1)): ?>selected<?php endif; ?>>已分配客户</option>
                        <option value="<?php echo U('member_list',array('status'=>2));?>" <?php if(($status==2)): ?>selected<?php endif; ?>>未分配客户</option>
                    </select>
                </div>
            <div class="col-xs-12 col-sm-1">
                <a href="javascript:;" data-toggle="modal" class="assignBtn">
                    <button class="btn btn-xs btn-primary">
                        <i class="ace-icon fa fa-recycle bigger-110"></i>
                        分配客户
                    </button>
                </a>
            </div><?php endif; ?>
            <form name="admin_list_sea" class="form-search" method="post" action="<?php echo U('member_list');?>">
                <div class="col-xs-12 col-sm-2 hidden-xs btn-sespan">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i  class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                        </span>
                        <input type="text"  name="reservation" id="reservation" class="sl-date" value="<?php echo ($sldate); ?>" placeholder="点击选择日期范围"/>
                    </div>
                </div>
                <?php if(($auth!=common)): ?><div class="col-xs-12 col-sm-2 hidden-xs btn-sespan" style="z-index:  99;margin-left: 40px;">
                        <div class="input-group">
                            <input type="hidden" name="admin_id" value="<?php echo ($admin_id); ?>">
                            <input type="text" name="admin" class="completeInput" style="height: 30px" <?php if(($admin_id)): ?>value="<?php echo getField($admin_id,'admin','admin_realname','admin_id');?>(<?php echo getField($admin_id,'admin','admin_tel','admin_id');?>)"<?php endif; ?> placeholder="输入指派人姓名或手机"/>
                        </div>
                    </div><?php endif; ?>

                <div class="col-xs-12 col-sm-2">
                    
                    <div class="input-group" >
                        <span  class="input-group-addon">
                            <i  class="ace-icon fa fa-check"></i>
                        </span>
                        <input type="text" name="key" id="key" class="form-control search-query admin_sea" value="<?php echo ($val); ?>" placeholder="输入客户姓名或者手机号" />
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-xs  btn-purple">
                                <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                                搜索
                            </button>
                        </span>
                    </div>

                </div>
            </form>
            <div class="input-group-btn">
                <a href="<?php echo U('member_list');?>">
                    <button type="button" class="btn btn-xs  btn-purple">
                        <span class="ace-icon fa fa-globe icon-on-right bigger-110"></span>
                        显示全部
                    </button>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div>
                    <table class="table table-striped table-bordered table-hover" id="dynamic-table">
                        <thead>
                            <tr>
                                <?php if(($auth!=common)): ?><th style="width: 5%;" class="hidden-xs center">
                                    <label class="pos-rel">
                                        <input type="checkbox" class="ace"  id='chkAll' value="全选"/>
                                        <span class="lbl"></span>
                                    </label>
                                </th><?php endif; ?>
                                <th class="hidden-xs">ID</th>
                                <th>客户姓名</th>
                                <th>客户性别</th>
                                <th>手机号</th>
                                <th>客户生日</th>
                                <th>指派人</th>
                                <th>添加时间</th>
                                
                                <th style="border-right:#CCC solid 1px;">操作</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if(is_array($member_list)): foreach($member_list as $key=>$v): ?><tr>
                                <?php if(($auth!=common)): ?><td class="hidden-xs" align="center">
                                    <label class="pos-rel">
                                        <input name='id[]' id="navid" class="ace"  type='checkbox' value='<?php echo ($v["member_list_id"]); ?>'>
                                        <span class="lbl"></span>
                                    </label>
                                </td><?php endif; ?>
                                <td class="hidden-xs" height="28" ><?php echo ($v["member_list_id"]); ?></td>
                                <td><a href="javascript:;" class="showBtn" data-id="<?php echo ($v['member_list_id']); ?>" ><?php echo ($v["member_list_name"]); ?></a></td>

                                <td >
                            <?php if($v["member_list_sex"] == 1): ?>先生
                                <?php elseif($v["member_list_sex"] == 2): ?>女士
                                <?php else: ?>保密<?php endif; ?>
                            </td>
                            <td><?php echo ($v["member_list_tel"]); ?></td>
                            <td><?php echo (date('Y-m-d',$v["member_list_birth"])); ?></td>
                            <td><?php if(($v['admin_id'])): echo getField($v['admin_id'],'admin','admin_realname','admin_id'); else: ?>未指派<?php endif; ?></td>
                            <td ><?php echo (date('Y-m-d H:i:s',$v["member_list_addtime"])); ?></td>

                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a class="green"  href="<?php echo U('member_edit',array('member_list_id'=>$v['member_list_id']));?>" title="修改">
                                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                                    </a>
                                    <?php if(($auth!=common)): ?><a class="red confirm-rst-url-btn" href="<?php echo U('member_del',array('member_list_id'=>$v['member_list_id'],'p'=>I('p',1)));?>" data-info="你确定要删除吗？" title="删除">
                                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                    </a><?php endif; ?>
                                </div>
                                
                                <div class="hidden-md hidden-lg">
                                    <div class="inline position-relative">
                                        <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                            <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                            <li>
                                                <a href="<?php echo U('member_edit',array('member_list_id'=>$v['member_list_id']));?>" class="tooltip-success" data-rel="tooltip" title="" data-original-title="修改">
                                                    <span class="green">
                                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>
                                            <?php if(($auth!=common)): ?><li>
                                                <a href="<?php echo U('member_del',array('member_list_id'=>$v['member_list_id'],'p'=>I('p',1)));?>"  class="tooltip-error confirm-rst-url-btn" data-info="你确定要删除吗？" data-rel="tooltip" title="" data-original-title="删除">
                                                    <span class="red">
                                                        <i class="ace-icon fa fa-trash-o bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li><?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            </tr><?php endforeach; endif; ?>
                        <tr>
                            <td height="50" align="left"><button  data-toggle="modal" data-target="#myModal" class="btn btn-white btn-yellow btn-sm">导出到excel</button></td>
                            <td height="50" colspan="12" align="left"><?php echo ($page); ?></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div><!-- /.page-content -->

    <!-- 显示添加模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form class="form-horizontal" method="post" action="<?php echo U('member_export');?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">×
                        </button>
                        <h4 class="modal-title" id="myModalLabel">
                            选择导出范围
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 时间范围：  </label>
                                    <div class="col-sm-3 form_date" data-date-format="dd MM yyyy">
                                        <input type="text" name="start_time" class="col-xs-10 col-sm-12" required />

                                    </div>
                                    <div class="col-sm-1" style='line-height: 30px;'>
                                        ——
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" name="end_time" class="col-xs-10 col-sm-12" required />

                                    </div>
                                </div>
                                <div class="space-4"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            确认下载
                        </button>
                        <button class="btn btn-info" type="reset">
                            重置
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            关闭
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </form>
    </div><!-- /.modal -->

    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        指派分配的人员
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 选择人员：  </label>
                                <div class="col-sm-8" style="display: inline-block">
                                    <input type="text" name="admin"  class="complete col-xs-10 col-sm-12" required />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="submit" class="confirmBtn btn btn-primary">
                        确认指派
                    </a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        关闭
                    </button>
                </div>
            </div>
        </div>
    </div>

            <!-- 右侧下主要内容结束 -->
        </div>
    </div><!-- 主要内容结束 -->
    <!-- 页脚开始 -->
    <div class="footer">
    <div class="footer-inner">
        <div class="footer-content">
            <span class="bigger-120">
                后台管理系统 &copy; 2015-<?php echo date('Y');?>
            </span>
        </div>
    </div>
</div>

    <!-- 页脚结束 -->
    <!-- 返回顶端开始 -->
    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
    </a>
    <!-- 返回顶端结束 -->
</div><!-- 整个页面内结束 -->

<!-- ace的js,可以通过打包生成,避免引入文件数多 -->
<script src="/public/ace/js/ace.min.js"></script>
<!-- jquery.form、layer、yfcmf的js -->
<script src="/public/others/jquery.form.js"></script>
<script src="/public/others/maxlength.js"></script>
<script src="/public/layer/layer.js"></script>
<?php $t=time(); ?>
<script src="/public/yfcmf/yfcmf.js?<?php echo ($t); ?>"></script>
<!-- 此页相关插件js -->

    <script type="text/javascript" src="/public/sldate/moment.js"></script>
    <script type="text/javascript" src="/public/sldate/daterangepicker.js"></script>
    <script src="/public/bootcomplete/dist/jquery.bootcomplete.js"></script>
    <script>
        $(function () {
            var time = new Date();
            var year = time.getFullYear();
            var today = time.toLocaleDateString();
            $("input[name='start_time'],input[name='end_time']").datetimepicker({
                step: 10,
                format: "Y-m-d",
                timepicker: false,
                todayButton: true,
                yearStart: 1970,
                yearEnd: year,
                maxDate: today,
                scrollMonth: false,
            });
            $.datetimepicker.setLocale('ch');

            $('#reservation').daterangepicker(null, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });

            // 全选
            $("#chkAll").click(function () {
                if ($(this).prop('checked')) {
                    $('tbody input[type=checkbox]').prop('checked', true);
                } else {
                    $('tbody input[type=checkbox]').prop('checked', false);
                }
            });

            // 分配客户
            $(".assignBtn").click(function () {
                ids = [];
                $('tbody .pos-rel input[type=checkbox]').each(function (k, v) {
                    if ($(this).is(":checked")) {
                        ids.push($(this).val());
                    }
                });
                if (ids.length == 0) {
                    layer.msg("请选择要分配的客户");
                } else {
                    $(this).attr('data-target','#assignModal');
                    $(this).removeClass("assignBtn");
                    
                }
                
            });
            
            $(".complete").bootcomplete({
                url: "/Admin/Member/ajaxGetAdmins",
                method: "get",
                minLength: 1,
                menuClass:"bootMenu"
            });
            
            $(".completeInput").bootcomplete({
                url: "/Admin/Member/ajaxGetAdmins",
                method: "get",
                minLength: 1,
                menuClass:"bootMenu"
            });
            
            $(".confirmBtn").click(function(){
                var data = {};
                data['ids'] = ids;
                console.log(ids);
                data['admin_id'] = $("input[name='admin_id']").val();
                if(data['admin_id']){
                    $.ajax({
                        type:"post",
                        url:"/Admin/Member/member_assign",
                        data:data,
                        success:function(res){
                            if(res){
                                $("#assignModal").hide();
                                layer.msg("指派成功");
                                setTimeout(function(){
                                    location.reload();
                                },1500);
                            }
                        }
                    });
                }else{
                    layer.msg("请选择人员");
                }
            });
            
            $(".showBtn").click(function(){
                var id = $(this).attr("data-id");
                layer.open({
                    type: 2,
                    title: '客户详情',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['60%', '80%'],
                    content: '/Admin/Member/member_show?member_list_id='+id //iframe的url
                }); 
            });
            
        })
    </script>

<!-- 与此页相关的js -->
</body>
</html>