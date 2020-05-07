<?php use think\Collection;
use think\Paginator;

if (!defined('THINK_PATH')) exit(); /*a:2:{s:72:"D:\phpstudy_pro\WWW\warehouse\public/../app/admin\view\client\index.html";i:1588838258;s:61:"D:\phpstudy_pro\WWW\warehouse\app\admin\view\public\foot.html";i:1546481687;}*/ ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>layui</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="/static/public/layui/css/layui.css"  media="all">
  <link rel="stylesheet" href="/static/public/font-awesome/css/font-awesome.min.css" media="all" />
  <link rel="stylesheet" href="/static/admin/css/admin.css"  media="all">
  <style type="text/css">

    /* tooltip */
    #tooltip{
      position:absolute;
      border:1px solid #ccc;
      background:#333;
      padding:2px;
      display:none;
      color:#fff;
    }
</style>
</head>
<body style="padding:10px;">
  <div class="tplay-body-div"> 

  <div class="layui-tab">
    <ul class="layui-tab-title">
      <li class="layui-this">客户列表</li>
      <li><a href="<?php echo url('admin/client/publish'); ?>" class="a_menu">添加新客户</a></li>
    </ul>
  </div>
  <form class="layui-form serch" action="<?php echo url('admin/admin/index'); ?>" method="post">
    <div class="layui-form-item" style="float: left;">
      <div class="layui-input-inline">
        <input type="text" name="keywords" lay-verify="title" autocomplete="off" placeholder="请输入关键词" class="layui-input layui-btn-sm">
      </div>
      <div class="layui-input-inline">
        <div class="layui-inline">
          <select name="admin_cate_id" lay-search="">
            <option value="">角色</option>
            <option value="">---</option>
          </select>
        </div>
      </div>
      <div class="layui-input-inline">
        <div class="layui-inline">
          <div class="layui-input-inline">
            <input type="text" class="layui-input" id="create_time" placeholder="创建时间" name="create_time">
          </div>
        </div>
      </div>
      <button class="layui-btn layui-btn-danger layui-btn-sm" lay-submit="" lay-filter="serch">立即提交</button>
    </div>
  </form>
  <form class="layui-form" id="admin">

    <table class="layui-table" lay-size="sm">
      <colgroup>
        <col width="50">
        <col width="100">
        <col width="100">
        <col width="150">
        <col width="150">
        <col width="150">
        <col width="150">
        <col width="150">
        <col width="150">
      </colgroup>
      <thead>
        <tr>
          <th>ID</th>
          <th>姓名</th>
          <th>性别</th>
          <th>电话</th>
          <th>微信</th>
          <th>所在地</th>
          <th>创建时间</th>
          <th>最后修改时间</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(is_array($data) || $data instanceof Collection || $data instanceof Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
          <tr>
            <td><?php echo $vo['client_id']; ?></td>
            <td><span style="font-weight:500;"><?php echo $vo['client_name']; ?></span><?php echo $vo['client_name']; ?></td>
            <td>
              <?php if($vo['client_sex'] == '0'): ?>未知<?php endif; if($vo['client_sex'] == '1'): ?>男<?php endif; if($vo['client_sex'] == '2'): ?>女<?php endif; ?>
            </td>
            <td><?php echo $vo['client_phone']; ?></td>
            <td><?php echo $vo['client_wechat']; ?></td>
            <td><?php echo $vo['client_position_id']; ?></td>
            <td><?php echo $vo['create_time']; ?></td>
            <td><?php echo $vo['update_time']; ?></td>
            <td class="operation-menu">
              <div class="layui-btn-group">
                <a href="<?php echo url('admin/client/publish',['client_id'=>$vo['client_id']]); ?>" class="layui-btn layui-btn-xs a_menu layui-btn-primary" style="margin-right: 0;font-size:12px;"><i class="layui-icon"></i></a>
                <a href="javascript:" class="layui-btn layui-btn-xs layui-btn-primary delete" client_id="<?php echo $vo['client_id']; ?>" style="margin-right: 0;font-size:12px;"><i class="layui-icon"></i></a>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
      </tbody>
    </table>
<!--  <button class="layui-btn layui-btn-sm" lay-submit lay-filter="admin">更新排序</button>-->
  </form>
    <div style="padding:0 20px;"><?php echo $data->render(); ?></div>
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

  $('.delete').click(function(){
    var client_id = $(this).attr('client_id');
    layer.confirm('确定要删除?', function(index) {
      $.ajax({
        url:"<?php echo url('admin/client/delete'); ?>",
        data:{client_id:client_id},
        success:function(res) {
          layer.msg(res.msg);
          if(res.code == 1) {
            setTimeout(function(){
              location.href = res.url;
            },1500)
          }
        }
      })
    })
  })
  </script>
</div>
</body>
</html>
