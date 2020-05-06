<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\Project\xiaofang\public/../app/admin\view\user\publish.html";i:1553937635;}*/ ?>
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
</head>
<body style="padding:10px;">
  <div class="tplay-body-div">
    <div class="layui-tab">
      <ul class="layui-tab-title">
        <li><a href="<?php echo url('admin/user/index'); ?>" class="a_menu">服务管理</a></li>
        <li class="layui-this">新增服务</li>
      </ul>
    </div> 
    <div style="margin-top: 20px;">
    </div>
    <form class="layui-form" id="admin">
      
      <div class="layui-form-item">
        <label class="layui-form-label">姓名</label>
        <div class="layui-input-block" style="max-width:600px;">
          <input name="name" lay-verify="name" autocomplete="off" placeholder="请输入姓名" class="layui-input" type="text" <?php if(!(empty($goods['name']) || (($goods['name'] instanceof \think\Collection || $goods['name'] instanceof \think\Paginator ) && $goods['name']->isEmpty()))): ?>value="<?php echo $goods['name']; ?>"<?php endif; ?>>
        </div>
      </div>


      <div class="layui-form-item">
        <label class="layui-form-label">电话</label>
        <div class="layui-input-block" style="max-width:600px;">
          <input name="tel" lay-verify="tel" autocomplete="off" placeholder="请输入手机号码" class="layui-input" type="text" <?php if(!(empty($goods['tel']) || (($goods['tel'] instanceof \think\Collection || $goods['tel'] instanceof \think\Paginator ) && $goods['tel']->isEmpty()))): ?>value="<?php echo $goods['tel']; ?>"<?php endif; ?>>
        </div>
      </div>

      <div class="layui-form-item">
        <label class="layui-form-label">省份</label>
        <div class="layui-input-block" style="max-width:600px;">
          <input name="pro" lay-verify="pro" autocomplete="off" placeholder="请输入身份证号" class="layui-input" type="text" <?php if(!(empty($goods['pro']) || (($goods['pro'] instanceof \think\Collection || $goods['pro'] instanceof \think\Paginator ) && $goods['pro']->isEmpty()))): ?>value="<?php echo $goods['pro']; ?>"<?php endif; ?>>
        </div>
      </div>
<!--
      <div class="layui-form-item">
        <label class="layui-form-label">申请借款金额</label>
        <div class="layui-input-block" style="max-width:600px;">
          <input name="pay" lay-verify="pay" autocomplete="off" placeholder="请输入借款金额" class="layui-input" type="text" <?php if(!(empty($goods['pay']) || (($goods['pay'] instanceof \think\Collection || $goods['pay'] instanceof \think\Paginator ) && $goods['pay']->isEmpty()))): ?>value="<?php echo $goods['pay']; ?>"<?php endif; ?>>
        </div>
      </div>
    -->




      <?php if(!(empty($goods) || (($goods instanceof \think\Collection || $goods instanceof \think\Paginator ) && $goods->isEmpty()))): ?>
      <input type="hidden" name="id" value="<?php echo $goods['id']; ?>">
      <?php endif; ?>
      <div class="layui-form-item">
        <div class="layui-input-block">
          <button class="layui-btn" lay-submit lay-filter="admin">立即提交</button>
          <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
      </div>
      
    </form>


    <script src="/static/public/layui/layui.js"></script>
    <script src="/static/public/jquery/jquery.min.js"></script>
    <!-- <script>
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
    </script> -->

    <script>
      layui.use(['layer', 'form'], function() {
          var layer = layui.layer,
              $ = layui.jquery,
              form = layui.form;
          $(window).on('load', function() {
              form.on('submit(admin)', function(data) {
                  $.ajax({
                      url:"<?php echo url('admin/user/publish'); ?>",
                      data:$('#admin').serialize(),
                      type:'post',
                      async: false,
                      success:function(res) {
                          if(res.code == 1) {
                              layer.alert(res.msg, function(index){
                                location.href = res.url;
                              })
                          } else {
                              layer.msg(res.msg);
                          }
                      }
                  })
                  return false;
              });
          });
      });
    </script>

   
  </div>
</body>
</html>