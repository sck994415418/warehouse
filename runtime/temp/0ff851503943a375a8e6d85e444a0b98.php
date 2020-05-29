<?php use think\Collection;
use think\Paginator;

if (!defined('THINK_PATH')) exit(); /*a:2:{s:72:"D:\phpstudy_pro\WWW\warehouse\public/../app/admin\view\client\index.html";i:1590732882;s:61:"D:\phpstudy_pro\WWW\warehouse\app\admin\view\public\foot.html";i:1546481687;}*/ ?>
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
  <form class="layui-form serch" action="<?php echo url('admin/client/index'); ?>" method="post">
    <div class="layui-form-item" style="float: left;">
      <div class="layui-input-inline">
        <input type="text" name="keywords" lay-verify="title" autocomplete="off" placeholder="用户名/电话/微信" class="layui-input layui-btn-sm">
      </div>
      <div class="layui-input-inline province_div" style="width: 100px">
        <select name="client_position_id" lay-verify="" class="province" lay-filter="province">
          <option value="">请选择省</option>
          <?php if(!empty($View_address)): if(is_array($View_address) || $View_address instanceof Collection || $View_address instanceof Paginator): if( count($View_address)==0 ) : echo "" ;else: foreach($View_address as $key=>$v): ?>
          <option value="<?php echo $v['address_id']; ?>" ><?php echo $v['address_name']; ?></option>
          <?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </select>
      </div>
      <input type="hidden" name="num" value="">
      <div class="layui-input-inline city_div" style="width: 100px">

      </div>
      <div class="layui-input-inline qu_div" style="width: 100px">

      </div>
      <div class="layui-input-inline street_div" style="width: 100px">

      </div>
<!--      <div class="layui-input-inline">-->
<!--        <div class="layui-inline">-->
<!--          <div class="layui-input-inline">-->
<!--            <input type="text" class="layui-input" id="create_time" placeholder="创建时间" name="create_time">-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
      <div class="layui-inline">
        <label class="layui-form-label">日期范围</label>
        <div class="layui-input-inline">
          <input type="text" name="time" class="layui-input" id="test6" placeholder=" - ">
        </div>
      </div>
      <button class="layui-btn layui-btn-danger layui-btn-sm" lay-submit="" onclick="layer.load()" lay-filter="serch">立即提交</button>
    </div>
  </form>
  <form class="layui-form" id="admin">

    <table class="layui-table" lay-size="sm">
      <colgroup>
        <col width="45">
        <col width="90">
        <col width="120">
        <col width="45">
        <col width="">

        <col width="120">
        <col width="130">

        <col width="90">
        <col width="100">
        <col width="120">
      </colgroup>
      <thead>
        <tr>
          <th>ID</th>
          <th>负责人姓名</th>
          <th>单位名称</th>
          <th>性别</th>
          <th>备注</th>

          <th>所在地</th>
          <th>详细地址</th>

          <th>创建时间</th>
          <th>最后修改时间</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php if(is_array($data) || $data instanceof Collection || $data instanceof Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
          <tr>
            <td><?php echo $vo['client_id']; ?></td>
            <td><?php echo $vo['client_name']; ?></td>
            <td><?php echo (isset($vo['client_company']) && ($vo['client_company'] !== '')?$vo['client_company']:''); ?></td>
            <td>
              <?php if($vo['client_sex'] == '0'): ?>未知<?php endif; if($vo['client_sex'] == '1'): ?>男<?php endif; if($vo['client_sex'] == '2'): ?>女<?php endif; ?>
            </td>
            <td><?php echo (isset($vo['client_desc']) && ($vo['client_desc'] !== '')?$vo['client_desc']:''); ?></td>
            <td><?php echo (isset($vo['address']['name2']) && ($vo['address']['name2'] !== '')?$vo['address']['name2']:''); ?><?php echo (isset($vo['address']['name3']) && ($vo['address']['name3'] !== '')?$vo['address']['name3']:''); ?><?php echo (isset($vo['address']['name4']) && ($vo['address']['name4'] !== '')?$vo['address']['name4']:''); ?><?php echo (isset($vo['address']['name5']) && ($vo['address']['name5'] !== '')?$vo['address']['name5']:''); ?></td>
            <td><?php echo (isset($vo['client_position_details']) && ($vo['client_position_details'] !== '')?$vo['client_position_details']:'暂无'); ?></td>
            <td><?php echo (isset($vo['create_time']) && ($vo['create_time'] !== '')?$vo['create_time']:''); ?></td>
            <td><?php echo (isset($vo['update_time']) && ($vo['update_time'] !== '')?$vo['update_time']:''); ?></td>
            <td class="operation-menu">
              <div class="layui-btn-group">
                <a href="javascript:" title="查看客户" class="layui-btn layui-btn-xs layui-btn-primary client_details" data-url="<?php echo url('admin/client/client_details'); ?>?client_id=<?php echo $vo['client_id']; ?>">
                  &nbsp;<i class="fa fa-user">&nbsp;</i>
                </a>
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
</div>
</body>
</html>
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
<script>
  layui.use(['form', 'layer'], function () {
    $ = layui.jquery;
    var form = layui.form;
    //选择省时触发事件获取其下市数据
    form.on('select(province)', function(data){
      var item = $(this).parents('.layui-form-item')
      // console.log(item)
      $('.city_div').html('')
      $('.qu_div').html('')
      $('.street_div').html('')
      address(data,item)
    });
    form.on('select(city)', function(data){
      var item = $(this).parents('.layui-form-item')
      // console.log(item)
      $('.qu_div').html('')
      $('.street_div').html('')
      address_qu(data,item)
    });
    form.on('select(qu)', function(data){
      var item = $(this).parents('.layui-form-item')
      // console.log(item)
      $('.street_div').html('')
      address_street(data,item)
    });
    form.on('select(street)', function(data){
      $('input[name=num]').val(9)
    });
    // $(window).on('load', function() {
    //   var item = $(this)
    //   var data=[];
    //   data.value="<?php echo (isset($data['user_sheng']) && ($data['user_sheng'] !== '')?$data['user_sheng']:''); ?>";
    //   address(data,item)
    // });

    function address(data,item){
      layer.load()
       address_id = data.value
      $.getJSON("<?php echo url('admin/Address/address'); ?>?address_id="+address_id, function(data){
        if(data.code == '1'){
          var str= '          <select lay-verify="" name="client_position_id" class="city" lay-filter="city">\n' +
                  '            <option value="'+address_id+'" selected>请选择市</option>\n' +
                  '          </select>\n';
          $('.city_div').html(str)
          var optionstring = "";
          $.each(data.data, function(i,item ){
            optionstring += "<option value=\"" + item.address_id + "\">" + item.address_name + "</option>";
          });
          $(".city").html('<option value="'+address_id+'"></option>' + optionstring);
          $('input[name=num]').val(3)
          form.render('select');
          layer.closeAll()
        }else{
          layer.closeAll()
          // layer.msg(data.msg);
        }

      });
    }
    function address_qu(data,item){
      layer.load()
       address_id = data.value
      $.getJSON("<?php echo url('admin/Address/address_qu'); ?>?address_id="+address_id, function(data){
        if(data.code == '1'){
          var str='          <select lay-verify="" name="client_position_id" class="qu" lay-filter="qu">\n' +
                  '            <option value="'+address_id+'" selected>请选择区/县</option>\n' +
                  '          </select>\n';
          $('.qu_div').html(str)
          var optionstring = "";
          $.each(data.data, function(i,item ){
            optionstring += "<option value=\"" + item.address_id + "\">" + item.address_name + "</option>";
          });
          $(".qu").html('<option value="'+address_id+'"></option>' + optionstring);
          $('input[name=num]').val(4)
          form.render('select');
          layer.closeAll()
        }else{
          layer.closeAll()
          // layer.msg(data.msg);
        }

      });
    }

    function address_street(data,item){
      layer.load()
       address_id = data.value
      $.getJSON("<?php echo url('admin/Address/address_street'); ?>?address_id="+address_id, function(data){
        if(data.code == '1'){
          var str='          <select lay-verify="" name="client_position_id" class="street" lay-filter="street">\n' +
                  '            <option value="'+address_id+'" selected>请选择乡镇/街道</option>\n' +
                  '          </select>\n';
          $('.street_div').html(str)
          var optionstring = "";
          $.each(data.data, function(i,item ){
            optionstring += "<option value=\"" + item.address_id + "\">" + item.address_name + "</option>";
          });
          $(".street").html('<option value="'+address_id+'"></option>' + optionstring);
          $('input[name=num]').val(6)
          form.render('select')
          layer.closeAll()
        }else{
          layer.closeAll()
          // layer.msg(data.msg);
        }

      });
    }
  });
</script>
<script>
  layui.use('laydate', function(){
    var laydate = layui.laydate;
    //日期范围
    laydate.render({
      elem: '#test6'
      ,range: true
    });

  });
  $(document).on('click','.client_details',function () {
    var url = $(this).data('url')
    layer.open({
      type: 2
      ,title: '客户详情'
      ,content:url
      ,area: ['50%', '90%']
      ,btn: ['关闭']
      ,yes: function(index, layero){
        layer.close(index)
      }
    })
  })
</script>