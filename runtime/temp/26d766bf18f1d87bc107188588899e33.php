<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\xampp\htdocs\tplay\public/../app/admin\view\mand\add.html";i:1551753029;}*/ ?>
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
        <li><a href="<?php echo url('admin/mand/ckjd'); ?>" class="a_menu">进度管理</a></li>
        <li class="layui-this">添加进度</li>
      </ul>
    </div>
    <div style="margin-top: 20px;">
    </div>
    <form class="layui-form" id="admin">
      
<!--       <div class="layui-form-item">
        <label class="layui-form-label">状态</label>
        <div class="layui-input-inline">
          <input name="name" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" type="text" value="">
        </div>
      </div> -->
      状态:<select name="status">
          <option value="1" selected>待处理</option>
          <option value="2">已接单</option>
          <option value="3">处理中</option>
          <option value="4">已完成</option>
      </select>
      <br>
      <br>
      <br>

      <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block" style="max-width:500px;">
          <textarea placeholder="请输入内容" class="layui-textarea" name="content"></textarea>
        </div>
      </div>
      
      <input type="hidden" name="id" value="">
      <div class="layui-form-item">
        <div class="layui-input-block">
          <button class="layui-btn" lay-submit lay-filter="admin">立即提交</button>
          <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
      </div>
      
    </form>


    <script src="/static/public/layui/layui.js"></script>
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
    <script>
      layui.use(['layer', 'form'], function() {

          var layer = layui.layer,
              $ = layui.jquery,
              form = layui.form;
              // alert('layer');
          $(window).on('load', function() {
            // alert('layer');
              form.on('submit(admin)', function(data) {
                  $.ajax({
                      url:"<?php echo url('admin/mand/add'); ?>",
                      data:$('#admin').serialize(),
                      type:'post',
                      async: false,
                      success:function(res) {
                        // alert(res);
                          if(res == 1) {
                              layer.alert(res.msg, function(index){
                                location.href = res.url;
                              })
                          } else {
                            // alert('wqeqwewq');
                            
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