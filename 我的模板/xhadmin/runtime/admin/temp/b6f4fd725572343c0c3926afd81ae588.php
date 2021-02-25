<?php /*a:2:{s:41:"E:\xhadmin\app\admin\view\index\main.html";i:1600703058;s:49:"E:\xhadmin\app\admin\view\common\_container2.html";i:1600572028;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit"/><!-- 让360浏览器默认选择webkit内核 -->

    <!-- 全局css -->
    <!-- 全局css -->
    <link href="/static/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="/static/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
     <link href="/static/css/style.css?v=1.0.0" rel="stylesheet">
    <link rel="stylesheet" href="/static/js/plugins/layui/css/layui.css?ver=170803"  media="all">
	<link href="/static/css/plugins/webuploader/webuploader.css" rel="stylesheet">
    <script src="/static/js/jquery.min.js?v=2.1.4"></script>
    <script src="/static/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/static/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
	<script src="/static/js/plugins/validate/bootstrapValidator.min.js"></script>
    <script src="/static/js/plugins/validate/zh_CN.js"></script>
    <script src="/static/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
    <script src="/static/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
    <script src="/static/js/plugins/layer/layer.min.js"></script>
    <script src="/static/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/static/js/common/ajax-object.js?v=<?php echo rand(1000,9999)?>"></script>
    <script src="/static/js/common/bootstrap-table-object.js"></script>
    <script src="/static/js/common/Feng.js"></script>
	<script src="/static/js/plugins/webuploader/webuploader.min.js"></script>
	<script type="text/javascript" src="/static/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/static/js/ueditor/ueditor.all.min.js"> </script>
	<script type="text/javascript" src="/static/js/xheditor/xheditor-1.2.2.min.js"></script>
	<script type="text/javascript" src="/static/js/xheditor/xheditor_lang/zh-cn.js"></script>
	 <script type="text/javascript">
		<?php
			$domains = config('app.domain_bind');
			$app = app('http')->getName();
			if(in_array($app,$domains)){			
				$ctxPathUrl = request()->domain();
			}else{
				$ctxPathUrl = request()->domain().'/'.getKeyByVal(config('app.app_map'),$app);
			}
		?>
        Feng.addCtx("<?php echo $ctxPathUrl;?>");
        Feng.sessionTimeoutRegistry();
    </script>
</head>

<body  class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
	
<style>
.ibox-content{
	background-color: #ffffff;
    color: inherit;
    padding: 15px 20px 20px 20px;
    border-color: #e7eaec;
    -webkit-border-image: none;
    -o-border-image: none;
    border-image: none;
    border-style: solid solid none;
    border-width: 1px 0px;
}
</style>
 <div class="row">
	<div class="col-sm-3">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>业绩收入</h5>
			</div>
			<div class="ibox-content">
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-info pull-right">当日</span>1524</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-success pull-right">当月</span>75445</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-warning pull-right">总计</span>544545</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>完成订单</h5>
			</div>
			<div class="ibox-content">
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-info pull-right">当日</span>1524</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-success pull-right">当月</span>75445</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-warning pull-right">总计</span>544545</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>销售套餐卡</h5>
			</div>
			<div class="ibox-content">
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-info pull-right">当日</span>1524</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-success pull-right">当月</span>75445</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-warning pull-right">总计</span>544545</h1>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>vip用户</h5>
			</div>
			<div class="ibox-content">
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-info pull-right">当日</span>1524</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-success pull-right">当月</span>75445</h1>
				<h1 class="no-margins"><span style="margin-top:8px;" class="label label-warning pull-right">总计</span>544545</h1>
			</div>
		</div>
	</div>
</div>

<div class="row">
            <div class="col-sm-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>当月业绩折线图</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="graph_flot.html#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="graph_flot.html#">选项1</a>
                                </li>
                                <li><a href="graph_flot.html#">选项2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="echarts" id="echarts-line-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>年度业绩树状图</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="graph_flot.html#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="graph_flot.html#">选项1</a>
                                </li>
                                <li><a href="graph_flot.html#">选项2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="echarts" id="echarts-bar-chart"></div>
                    </div>
                </div>
            </div>
        </div>
		


 <!-- ECharts -->
    <script src="/static/js/echarts-all.js"></script>
	<script>
	$(function () {
    var lineChart = echarts.init(document.getElementById("echarts-line-chart"));
    var lineoption = {
        title : {
            text: '当月业绩折线图'
        },
        tooltip : {
            trigger: 'axis'
        },
        grid:{
            x:40,
            x2:40,
            y2:24
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]
            }
        ],
        yAxis : [
            {
                type : 'value',
            }
        ],
        series : [
            {
                name:'业绩',
                type:'line',
                data:[0,0,0,0,0,126,246,452,45,36,479,488,434,9,18,27,18,88,45,0,0,0,0,0,0,0,0,0,0,0],
                markPoint : {
                    data : [
                        {type : 'max', name: '最大值'}
                    ]
                },
                markLine : {
                    data : [
                        {type : 'average', name: '平均值'}
                    ]
                }
            }
        ]
    };
    lineChart.setOption(lineoption);
    $(window).resize(lineChart.resize);

    var barChart = echarts.init(document.getElementById("echarts-bar-chart"));
    var baroption = {
        title : {
            text: '年度业绩表'
        },
        tooltip : {
            trigger: 'axis'
        },
        grid:{
            x:30,
            x2:40,
            y2:24
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                data : ['9月','10月','11月','12月']
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'业绩',
                type:'bar',
                data:[7589, 0, 0, 0, 0],
                markPoint : {
                    data : [
                        {type : 'max', name: '最大值'},
                        {type : 'min', name: '最小值'}
                    ]
                },
                markLine : {
                    data : [
                        {type : 'average', name: '平均值'}
                    ]
                }
            }
        ]
    };
    barChart.setOption(baroption);
    window.onresize = barChart.resize;
});

	</script>

</div>
<script src="/static/js/content.js?v=1.0.0"></script>

</body>
</html>
