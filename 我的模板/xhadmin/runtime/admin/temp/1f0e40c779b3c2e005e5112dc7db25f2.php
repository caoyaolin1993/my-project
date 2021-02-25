<?php /*a:4:{s:42:"E:\xhadmin\app\admin\view\index\index.html";i:1588229006;s:42:"E:\xhadmin\app\admin\view\common\_tab.html";i:1602926514;s:44:"E:\xhadmin\app\admin\view\common\_right.html";i:1602637969;s:44:"E:\xhadmin\app\admin\view\common\_theme.html";i:1579182374;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo config('xhadmin.site_title'); ?></title>
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/css/animate.css" rel="stylesheet">
    <link href="/static/css/style.css?v=<?php echo rand(1000,9999)?>" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
    <div id="wrapper">

        <!--左侧导航开始-->
        	<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="nav-close"><i class="fa fa-times-circle"></i>
    </div>
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span><img alt="image" class="img-circle" src="<?php echo config('xhadmin.site_logo'); ?>" width="80" height="80"/></span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                       <span class="block m-t-xs"><?php echo session('admin.role_name'); ?>-<?php echo session('admin.name'); ?></span>
                       
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="<?php echo url('admin/login/out'); ?>?id=<?php echo session('admin.userid'); ?>">安全退出</a>
                        </li>
                    </ul>
                </div>
                <div class="logo-element">
                </div>
            </li>
            
            <?php if(is_array($menus) || $menus instanceof \think\Collection || $menus instanceof \think\Paginator): $i = 0; $__LIST__ = $menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pmenu): $mod = ($i % 2 );++$i;if(empty($pmenu['sub']) || (($pmenu['sub'] instanceof \think\Collection || $pmenu['sub'] instanceof \think\Paginator ) && $pmenu['sub']->isEmpty())): if(in_array($pmenu['access_url'],session('admin.nodes')) || session('admin.role_id') == 1): ?>
					<li>
						<a class="J_menuItem" href="<?php echo $pmenu['url']; ?>" name="tabMenuItem">
							<i class="<?php echo $pmenu['icon']; ?>"></i>
							<span class="nav-label"><?php echo $pmenu['title']; ?></span>
						</a>
					</li>
					<?php endif; else: if(in_array($pmenu['access_url'],session('admin.nodes')) || session('admin.role_id') == 1): ?>
				<li>
					<a href="#">
						<i class="fa <?php echo $pmenu['icon']; ?>"></i>
						<span class="nav-label"><?php echo $pmenu['title']; ?></span>
						<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level">
						<?php if(is_array($pmenu['sub']) || $pmenu['sub'] instanceof \think\Collection || $pmenu['sub'] instanceof \think\Paginator): $i = 0; $__LIST__ = $pmenu['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;if(empty($menu['sub']) || (($menu['sub'] instanceof \think\Collection || $menu['sub'] instanceof \think\Paginator ) && $menu['sub']->isEmpty())): if(in_array($menu['access_url'],session('admin.nodes')) || session('admin.role_id') == 1): ?>
								 <li>
									<a class="J_menuItem" href="<?php echo $menu['url']; ?>" name="tabMenuItem">
										<i class="<?php echo $menu['icon']; ?> nav-icon"></i>
										<span class="nav-label"><?php echo $menu['title']; ?></span>
									</a>
								 </li>
								<?php endif; else: if(in_array($menu['access_url'],session('admin.nodes')) || session('admin.role_id') == 1): ?>
								<li>
									<a href="J_menuItem"><?php echo $menu['title']; ?> <span class="fa arrow"></span>
									<i class="<?php echo $menu['icon']; ?> nav-icon"></i>
									</a>
									<ul class="nav nav-third-level" style="padding-left:20px;">
										<?php if(is_array($menu['sub']) || $menu['sub'] instanceof \think\Collection || $menu['sub'] instanceof \think\Paginator): $i = 0; $__LIST__ = $menu['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$thirdmenu): $mod = ($i % 2 );++$i;if(in_array($thirdmenu['access_url'],session('admin.nodes')) || session('admin.role_id') == 1): ?>
											<li><a class="J_menuItem" href="<?php echo $thirdmenu['url']; ?>" name="tabMenuItem"><?php echo $thirdmenu['title']; ?><i class="<?php echo $thirdmenu['icon']; ?> nav-icon"></i></a></li>
											<?php endif; ?>
										<?php endforeach; endif; else: echo "" ;endif; ?>
										
									</ul>
								</li>
								<?php endif; ?>
							<?php endif; ?>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
                </li>
				<?php endif; ?>
				<?php endif; ?>
            <?php endforeach; endif; else: echo "" ;endif; ?>
			

        </ul>
    </div>
</nav>
        <!--左侧导航结束-->
        
        <!--右侧部分开始-->
        	<div id="page-wrapper" class="gray-bg dashbard-1">
	<div class="row border-bottom" style="position:relative">
		<nav class="navbar navbar-static-top page-tab J_menuTabs" role="navigation" style="margin-bottom: -15px;">
			<ul class="nav navbar-top-links pull-left" style="position:absolute; z-index:2;background:#fff;">
				<li style="padding:0">
					<a class="navbar-minimalize" title="关闭左侧导航"><i class="fa fa-bars"></i></a>
				</li>
				<li style="padding:0; margin-right:2px">
					<a class="roll-nav roll-left J_tabLeft hidden-xs"><i class="fa fa-backward"></i></a>
				</li>
			</ul>
			<ul  class="nav navbar-top-links pull-left page-tabs-content hidden-xs">
				<li style="margin-left:80px;" class="active J_menuTab" data-id="<?php echo url('admin/Index/main'); ?>">
					<a class="top-menu">后台首页 <i class="fa fa-times-circle"></i></a>
				</li>
			</ul>
		</nav>
		<ul class="nav navbar-top-links navbar-right" style="position:absolute; right:0; top:0; z-index:9999; background:#fff">
				<li class="dropdown hidden-xs" onclick="fullScreen()">
					<a class="count-info" href="#" title="全屏">
						<i class="glyphicon glyphicon-fullscreen"></i>
					</a>
				</li>
				<li class="dropdown hidden-xs" id="cache">
					<a class="count-info" href="#" title="清除缓存">
						<i class="glyphicon glyphicon-trash"></i>
					</a>
				</li>
				<li class="dropdown">
					<div data-toggle="dropdown" data-hover="dropdown" style="cursor:pointer;">
					<img src="<?php echo config('xhadmin.site_logo'); ?>" width="28" height="28" style="border-radius:50%; margin-right:5px;">
						<?php echo session('admin.username'); ?> <span class="caret"></span>
					</div>
					<ul class="dropdown-menu animated fadeInRight m-t-xs" style="margin-top:10px;">
						<li><a href="<?php echo url('admin/Base/password'); ?>" class="J_menuItem"><i class="fa fa-lock"></i> 修改密码</a></li>
						<li><a href="<?php echo url('admin/Login/out'); ?>"><i class="fa fa-sign-out"></i> 安全退出</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a class="right-sidebar-toggle" aria-expanded="false">
						<i class="fa fa-tasks"></i>
					</a>
				</li>
			</ul>
	</div>

	<div class="row J_mainContent" id="content-main">
		<iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="<?php echo url('admin/Index/main'); ?>" frameborder="0" data-id="<?php echo url('admin/Index/main'); ?>" seamless></iframe>
	</div>
	<div class="footer gray-bg">
		<div class="pull-right">&copy; 2018-2020 <a href="http://www.xhadmin.com" target="_blank">xhadmin</a></div>
	</div>
</div>
        <!--右侧部分结束-->
        
        <!--右侧边栏开始-->
        	<div id="right-sidebar">
	<div class="sidebar-container">

		<ul class="nav nav-tabs navs-3">
			<li class="active"><a data-toggle="tab" href="#tab-1"> <i
					class="fa fa-gear"></i> 主题
			</a></li>
		</ul>

		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				<div class="sidebar-title">
					<h3>
						<i class="fa fa-comments-o"></i> 主题设置
					</h3>
					<small><i class="fa fa-tim"></i>
						你可以从这里选择和预览主题的布局和样式，这些设置会被保存在本地，下次打开的时候会直接应用这些设置。</small>
				</div>
				<div class="skin-setttings">
					<div class="title">主题设置</div>
					<div class="setings-item">
						<span>收起左侧菜单</span>
						<div class="switch">
							<div class="onoffswitch">
								<input type="checkbox" name="collapsemenu"
									class="onoffswitch-checkbox" id="collapsemenu"> <label
									class="onoffswitch-label" for="collapsemenu"> <span
									class="onoffswitch-inner"></span> <span
									class="onoffswitch-switch"></span>
								</label>
							</div>
						</div>
					</div>
					<div class="setings-item">
						<span>固定顶部</span>

						<div class="switch">
							<div class="onoffswitch">
								<input type="checkbox" name="fixednavbar"
									class="onoffswitch-checkbox" id="fixednavbar"> <label
									class="onoffswitch-label" for="fixednavbar"> <span
									class="onoffswitch-inner"></span> <span
									class="onoffswitch-switch"></span>
								</label>
							</div>
						</div>
					</div>
					<div class="setings-item">
						<span> 固定宽度 </span>

						<div class="switch">
							<div class="onoffswitch">
								<input type="checkbox" name="boxedlayout"
									class="onoffswitch-checkbox" id="boxedlayout"> <label
									class="onoffswitch-label" for="boxedlayout"> <span
									class="onoffswitch-inner"></span> <span
									class="onoffswitch-switch"></span>
								</label>
							</div>
						</div>
					</div>
					<div class="title">皮肤选择</div>
					<div class="setings-item default-skin nb">
						<span class="skin-name "> <a href="#" class="s-skin-0">
								默认皮肤 </a>
						</span>
					</div>
					<div class="setings-item blue-skin nb">
						<span class="skin-name "> <a href="#" class="s-skin-1">
								蓝色主题 </a>
						</span>
					</div>
					<div class="setings-item yellow-skin nb">
						<span class="skin-name "> <a href="#" class="s-skin-3">
								黄色/紫色主题 </a>
						</span>
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
        <!--右侧边栏结束-->
       
    </div>

   <!-- 全局js -->
    <script src="/static/js/jquery.min.js?v=2.1.4"></script>
    <script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
	<script src="/static/js/common/Feng.js"></script>
    <script src="/static/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/static/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/static/js/plugins/layer/layer.min.js"></script>

    <!-- 自定义js -->
    <script src="/static/js/hplus.js?v=<?php echo rand(1000,9999)?>"></script>
    <script type="text/javascript" src="/static/js/contabs.js"></script>

    <!-- 第三方插件 -->
    <script src="/static/js/plugins/pace/pace.min.js"></script>
	<script>
		$(function(){
			$("#cache").click(function(){
				Feng.confirm("是否删除缓存？", function () {
					$.ajax({
						url: '<?php echo url("admin/Base/clearData"); ?>',
						success:function(data){
							if(data.status == '00'){
								layer.msg(data.msg, {
								  icon: 1,
								  time: 1000
								});
							}else{
								layer.msg(data.msg, {
								  icon: 2,
								  time: 1000
								});
							}
						}
					})
				});	
			})
		
		})
		
		
	</script>
</body>

</html>
