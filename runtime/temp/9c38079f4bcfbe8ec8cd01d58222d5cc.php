<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:60:"D:\Project\xiaofang\public/../app/admin\view\user\index.html";i:1553939088;s:51:"D:\Project\xiaofang\app\admin\view\public\foot.html";i:1546481687;}*/ ?>
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
   
    </div> 
  
    <table class="layui-table" lay-size="sm">
      <colgroup>
        <col width="50">
        <col width="300">
        <col width="50">
        <col width="100">
        <col width="100">
        <col width="150">
        <col width="100">
        <col width="150">
        <col width="50">
        <col width="50">
        <col width="100">
      </colgroup>
      <thead>
        <tr>
          <th>ID</th>
          <th>姓名</th>
          <th>联系电话</th>
          <th>省份</th>
           <th>学历</th> 
           <th>工作年限</th>
           <th>报名时间</th>      
          <th>审核</th>
          
          <th>操作</th>
        </tr> 
      </thead>
      <tbody>
        <?php if(is_array($goods) || $goods instanceof \think\Collection || $goods instanceof \think\Paginator): $i = 0; $__LIST__ = $goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <tr>
          <td><?php echo $vo['id']; ?></td>
          <td><?php echo $vo['name']; ?></td>
          <td><?php echo $vo['tel']; ?></td>
          <td><?php echo $vo['pro']; ?></td>
          <td>
<?php switch($vo['edu']): case "1": ?>中专/高中<?php break; case "2": ?>大专<?php break; case "3": ?>本科<?php break; case "4": ?>研究生<?php break; case "5": ?>硕士<?php break; case "6": ?>博士<?php break; default: ?>未填
           <?php endswitch; ?>
          </td>
          <td>
<?php switch($vo['years']): case "1": ?>1年<?php break; case "2": ?>2年<?php break; case "3": ?>3年<?php break; case "4": ?>4年<?php break; case "5": ?>5年<?php break; case "6": ?>6年<?php break; default: ?>未填
           <?php endswitch; ?>

          </td>
          <td><?php echo date('Y-m-d H:i:s',$vo['createtime']); ?></td>
          <td><a href="javascript:;" style="font-size:18px;" class="status" data-id="<?php echo $vo['id']; ?>" data-val="<?php echo $vo['status']; ?>"><?php if($vo['status'] == '1'): ?><i class="fa fa-toggle-on"></i><?php else: ?><i class="fa fa-toggle-off"></i><?php endif; ?></a></td>
          <td class="operation-menu"> 
            <div class="layui-btn-group">
              <!--
              <a href="<?php echo url('admin/user/publish',['id'=>$vo['id']]); ?>" class="layui-btn layui-btn-xs a_menu layui-btn-primary" style="margin-right: 0;font-size:12px;"><i class="layui-icon"></i></a>
             -->
              <a href="javascript:;" class="layui-btn layui-btn-xs layui-btn-primary delete" id="<?php echo $vo['id']; ?>" style="margin-right: 0;font-size:12px;"><i class="layui-icon"></i></a>
            </div>
          </td>          
        </tr>
        <?php endforeach; endif; else: echo "" ;endif; ?>
      </tbody>
    </table>
    <div style="padding:0 20px;"><?php echo $goods->render(); ?></div> 
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
      var id = $(this).attr('id');
      layer.confirm('确定要删除?', function(index) {
        $.ajax({
          url:"<?php echo url('admin/user/delete'); ?>",
          data:{id:id},
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
    <script type="text/javascript">




    $('.status').click(function(){

      var val = $(this).attr('data-val');
      var id = $(this).attr('data-id');
      var i = $(this).find('i');
      var the = $(this);
      if(val == 1){
        
        var status = 0;
      } else {
        
        var status = 1;
      }
      $.ajax({
        type:"post",
        url:"<?php echo url('admin/user/status'); ?>",
        data:{status:status,id:id},
        success:function(res){
          
          if(res.code == 1) {

            tostatus();
          } else {
            layer.msg(res.msg);
          }
        }
      })

      function tostatus(){
        if(val == 1){
          i.attr("class","fa fa-toggle-off");
          the.attr('data-val',0);
        } else {
          i.attr("class","fa fa-toggle-on");
          the.attr('data-val',1);
        }
      }
    })
    </script>
  </div>
</body>
</html>
