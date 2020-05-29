<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:70:"D:\phpstudy_pro\WWW\warehouse\public/../app/admin\view\main\index.html";i:1589968684;s:61:"D:\phpstudy_pro\WWW\warehouse\app\admin\view\public\foot.html";i:1546481687;}*/ ?>
<!DOCTYPE html>
<html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>tplay_main</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <script src="/static/public/echarts/echarts.min.js"></script>
  <link rel="stylesheet" href="/static/public/layui/css/layui.css" media="all">
  <link rel="stylesheet" href="/static/public/font-awesome/css/font-awesome.min.css" media="all">
  <link rel="stylesheet" href="/static/admin/css/admin-1.css" media="all">
<body class="layui-layout-body" style="overflow-y:visible;">
    <div class="layadmin-tabsbody-item layui-show"><div class="layui-fluid">
  <div class="layui-row layui-col-space15">
    <div class="layui-col-md8">
      <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
          <div class="layui-card">
            <div class="layui-card-header">网站数据</div>
            <div class="layui-card-body">

              <div class="layui-carousel layadmin-carousel layadmin-backlog" lay-anim="" lay-indicator="inside" lay-arrow="none" style="width: 100%; height: 280px;">
                <div carousel-item="">
                  <ul class="layui-row layui-col-space10 layui-this">
                    <li class="layui-col-xs6" style="cursor:pointer;">
                      <a data-url="<?php echo url('admin/admin/index'); ?>" class="layadmin-backlog-body admin_user">
                        <h3>管理员</h3>
                        <p><cite><?php echo $web['user_num']; ?></cite></p>
                      </a>
                    </li>
                    <li class="layui-col-xs6" style="cursor: pointer">
                      <a data-url="<?php echo url('admin/WarehouseGood/index'); ?>?good_arr=<?php echo (isset($good_warn) && ($good_warn !== '')?$good_warn:''); ?>" title="库存警告" class="layadmin-backlog-body good_warn">
                        <h3>库存不足</h3>
                        <p><cite><?php echo $good_warn_count; ?></cite></p>
                      </a>
                    </li>
                    <li class="layui-col-xs6" style="cursor: pointer">
                      <a data-url="<?php echo url('admin/WarehouseGood/index'); ?>?good_arr=<?php echo (isset($good_warn_day) && ($good_warn_day !== '')?$good_warn_day:''); ?>" class="layadmin-backlog-body good_warn_day">
                        <h3>库存积压</h3>
                        <p><cite><?php echo $good_warn_day_count; ?></cite></p>
                      </a>
                    </li>
                    <li class="layui-col-xs6" style="cursor: pointer">
                      <a data-url="<?php echo url('admin/Client/index'); ?>" class="layadmin-backlog-body client">
                        <h3>客户</h3>
                        <p><cite><?php echo $client_count; ?></cite></p>
                      </a>
                    </li>
                  </ul>
                </div>
              <button class="layui-icon layui-carousel-arrow" lay-type="sub"></button><button class="layui-icon layui-carousel-arrow" lay-type="add"></button></div>
            </div>
          </div>
        </div>
        <div class="layui-col-md12">
          <div class="layui-card">
            <div class="layui-card-header">管理员登录</div>
            <div class="layui-card-body" id="main" style="height: 450px;">

            </div>
          </div>
        </div>
      </div>
    </div>
    

  </div>
  </div>
  </div>


        <script src="/static/public/layui/layui.js" charset="utf-8"></script>
    <script src="/static/public/jquery/jquery.min.js"></script>
    <script>
            var message;
            layui.config({
                base: '/static/admin/js/',
                version: '1.0.1'
            }).use(['app', 'message'], function() {
                var app = layui.app,
                    $ = layui.jquery,
                    layer = layui.layer;
                //将message设置为全局以便子页面调用
                message = layui.message;
                //主入口
                app.set({
                    type: 'iframe'
                }).init();
            });
        </script> 
    <script type="text/javascript">
    $(function(){
      var x = 10;
      var y = 20;
      $(".tooltip").mouseover(function(e){ 
        var tooltip = "<div id='tooltip'><img src='"+ this.href +"' alt='产品预览图' height='200'/>"+"<\/div>"; //创建 div 元素
        $("body").append(tooltip);  //把它追加到文档中             
        $("#tooltip")
          .css({
            "top": (e.pageY+y) + "px",
            "left":  (e.pageX+x)  + "px"
          }).show("fast");    //设置x坐标和y坐标，并且显示
        }).mouseout(function(){  
        $("#tooltip").remove();  //移除 
        }).mousemove(function(e){
        $("#tooltip")
          .css({
            "top": (e.pageY+y) + "px",
            "left":  (e.pageX+x)  + "px"
          });
      });
    })
    </script>
    <script type="text/javascript">
    $('.a_menu').click(function(){
      var url = $(this).attr('href');
      var id = $(this).attr('id');
      var a = true;
      if(id) {
        $.ajax({
          url:url
          ,async:false
          ,data:{id:id}
          ,success:function(res){
            if(res.code == 0) {
              layer.msg(res.msg);
              a = false;
            }
          }
        })
      } else {
        $.ajax({
          url:url
          ,async:false
          ,success:function(res){
            if(res.code == 0) {
              layer.msg(res.msg);
              a = false;
            }
          }
        })
      }
      return a;
    })
    </script>
    <script>
    layui.use('laydate', function(){
      var laydate = layui.laydate;
      
      //常规用法
      laydate.render({
        elem: '#create_time'
      });
    });
    </script>
<script type="text/javascript">
var a = "<?php echo $web['date_string']; ?>";
var date = a.split(",");

var b = "<?php echo $web['login_sum']; ?>";
var login_sum = b.split(",");


var myChart = echarts.init(document.getElementById('main'));

option = {
    tooltip: {
        trigger: 'axis',
        position: function (pt) {
            return [pt[0], '10%'];
        }
    },
    grid: {
        top: 50,
        bottom: 70,
        left:40,
        right:50
    },
    toolbox: {
        feature: {
            dataZoom: {
                yAxisIndex: 'none'
            },
            restore: {},
            saveAsImage: {}
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: date
    },
    yAxis: {
        type: 'value',
        boundaryGap: [0, '100%']
    },
    dataZoom: [{
        type: 'inside',
        start: 0,
        end: 100
    }, {
        start: 0,
        end: 100,
        handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
        handleSize: '100%',
        handleStyle: {
            color: '#fff',
            shadowBlur: 3,
            shadowColor: '#009688',
            shadowOffsetX: 2,
            shadowOffsetY: 2
        }
    }],
    series: [
        {
            name:'管理员登录',
            type:'line',
            smooth:true,
            symbol: 'none',
            sampling: 'average',
            itemStyle: {
                normal: {
                    color: '#009688'
                }
            },
            areaStyle: {
                normal: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                        offset: 0,
                        color: '#009688'
                    }, {
                        offset: 1,
                        color: '#009688'
                    }])
                }
            },
            data: login_sum
        }
    ]
};
myChart.setOption(option);


$(document).on('click','.good_warn,.admin_user,.good_warn_day,.client',function () {
  var url = $(this).data('url')
  var title = $(this).attr('title')
  layer.open({
    type: 2
    ,title: title
    ,content:url
    ,area: ['90%', '90%']
    ,btn: ['关闭']
    ,yes: function(index, layero){
      layer.close(index)
    }
  })
})
</script>
</body>
</html>
