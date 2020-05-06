<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:60:"D:\xampp\htdocs\tplay\public/../app/index\view\login\zc.html";i:1551429587;s:55:"D:\xampp\htdocs\tplay\app\index\view\common\header.html";i:1551494122;s:55:"D:\xampp\htdocs\tplay\app\index\view\common\footer.html";i:1551427365;}*/ ?>
<!-- 头部公共文件 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">    
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="author" content="南篱ღ">    
    <title>叮咚鸟</title>    
    <link href="/static/home/css/reset.css" rel="stylesheet">    
    <link href="/static/home/css/style.css" rel="stylesheet" type="text/css" />
<!--     <link href="/static/home/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/static/home/css/toastr.css" rel="stylesheet" type="text/css" /> -->
</head>
<body>
<div class="head">
    <div class="top">
        <div class="content fix">
            <div class="welcome">你好，尊敬的用户
                <a href="index/login/dl" style="color:red; "><?php echo \think\Request::instance()->session('homeUserInfo')['name']; ?>&nbsp</a>叮咚鸟为您服务！
            </div>
            <div class="top-fr fix">
                <?php if(empty(session('homeFlag'))): ?>
                <div class="dlzc">
                    <a href="index/login/dl">登录</a>/<a href="index/login/index">免费注册</a>
                </div>
                
                <div class="dlzc">
                    <a href="index/login/dl">登录</a>/<a href="index/login/index">免费注册</a>
                </div>
                <?php else: ?>
                <div class="dlzc">
                    <a href="index/login/loginout">退出登录</a>
                </div>
                <?php endif; ?>
                <ul class="nav-fr fl">            	
                    <li class="nav-er">
                        <span>我的鸟窝</span>
                        <div class="nav-tab">
                            <a href="">我的鸟窝1</a>
                            <a href="">我的鸟窝2</a>
                            <a href="">我的鸟窝3</a>
                        </div>
                    </li>
                  
                    <li class="nav-er bang">
                        <span>帮助中心</span>
                        <div class="nav-tab">
                            <a href="">帮助中心1</a>
                            <a href="">帮助中心2</a>
                            <a href="">帮助中心3</a>
                        </div>
                    </li>
                </ul>        
            </div>
        </div>
    </div>
    <div class="nav-search">
    	<div class="content fix">
        	<h1 class="logo"><a href=""><img src="/static/home/images/logo.png"></a></h1>
            <div class="search">
            	<form class="fix">                
                    <input class="text textixa" type="text" name="" list="list" placeholder="找顾问">                  
                    <input class="butt" name="" type="submit" value="搜索">
                </form>
                <p class="remen fix">                	
                	<a href="">LOGO</a>
                    <a href="">商标注册</a>
                    <a href="">首页设计</a>
                    <a href="">画册设计</a>
                    <a href="">网站开发</a>
                    <a href="">流量推广</a>
                    <a href="">工业设计</a>
                    <a href="">漫画设计</a>
                </p>
            </div>
        </div>
    </div>
    
    <div class="nav-box">
    	<div class="content fix">
        	<div class="recycling">
            	<span class="jjhs"><img src="/static/home/images/fl.png" />全部分类</span>
                <div class="recycling-show">
                	<ul class="recycling-show-ul">
                    <?php foreach($cate as $k=>$v): if($v['pid'] == 0): ?>
                        <li>
                        	<div class="li-top fix">
                            	<p><i class="icon-sj"></i><?php echo $v['name']; ?></p>
                            	<span class="san"></span>
                            </div>
                            <?php foreach($cate as $ka=>$va): if($va['pid'] == $v['id']): ?>
                            <div class="show-fix fix">
                                <a href=""><?php echo $va['name']; ?></a>
                            </div>        
                            <?php endif; endforeach; ?>
                            <div class="recycling-white fix">
                            	<div class="recycling-white-fl fl">
                                    <?php foreach($cate as $ka=>$va): if($va['pid'] == $v['id']): foreach($cate as $kb=>$vb): if($vb['pid'] == $va['id']): ?>
                                	<div class="recyclingfl-list fix">
        	                        	<a href="" class="pinming"><?php echo $va['name']; ?></a>
                                        <ul class="fix">
                                        	<li><a href=""><?php echo $vb['name']; ?></a></li>
                                        </ul>
                                        <a href="" class="more">更多&nbsp;></a>		                                    
                                    </div>
                                    <?php endif; endforeach; endif; endforeach; ?>

                                    <a href="" class="more-pp">更多设计</a>
                                </div>
                                <div class="recycling-white-fr fl">
                                	<img src="/static/home/images/fr.png" />
                                </div>
                            </div>
                        </li>
                        <?php endif; endforeach; ?>                        

                    </ul>
                </div>            
            </div>
            <!---导航---->
        	<ul class="nav-box-ul">
            	<li class="on"><a href="">首页</a></li>
                <li><a href="">找人才</a></li>
                <li><a href="">案例库</a></li>
				<li><a href="">买服务</a></li>
				<li><a href="">工单</a></li>
                <li><a href="">赚佣金</a></li>
                <li><a href="">常见问题</a></li>
            </ul>
            <!---扫一扫---->
            <div class="nav-ma fr">
            	<h3>扫一扫</h3><br />
                <p>手机服务更方便</p>
                <div class="ma">
                	<img src="/static/home/images/timg.jpg" />
                    <span>扫一扫关注</span>
                </div>
            </div>            
        </div>    
    </div>
</div>
<!---顶部end---->
<!---中间内容---->
<div class="wrap" style="background:#f5f5f5;">
	<div class="content">
    	<div class="zc-box-pa">
        	<div class="zc-box">
            	<div class="zc-box-auto">
                	<h2>新用户注册</h2>
                	<form action="/index/user/install" method="post">
                    	<div class="shu-ru">
                        	<input name="name" type="text" class="text" placeholder="用户名" />
                        </div>
                        
                        <div class="shu-ru">
                        	<input name="email" type="email" class="text" placeholder="email" />
                        </div>
                    	<div class="shu-ru">
                        	<input name="password" type="password" class="text" placeholder="密码" />
                        </div>
                        <div class="shu-ru">
                        	<input name="repw" type="password" class="text" placeholder="确认密码" />
                        </div>
                        <div class="shu-ru">
                        	<input name="tel" type="text" class="text" placeholder="手机" />
                        </div>

                     <!--    <div class="shu-ru">
                            请选择您的身份:
                            <input name="auth" type="radio" value="1" />用户
                            <input name="auth" type="radio" value="2" />技术
                            <input name="auth" type="radio" value="3" />推广
                        </div> -->
                        
                        <div class="shu-ru fix">
                        	<input name="yzm" id="pwd" type="text" size="10" class="text text-yan" placeholder="验证码" />
                            <span><img src="<?php echo captcha_src(); ?>" class="verify" onclick="javascript:this.src='<?php echo captcha_src(); ?>?rand='+Math.random()" ></span>
                        </div>
                        <br>
                        <br>
                        <br>
                        <!-- <label class="label"><input name="Fruit" type="checkbox" value="1" />我已看过并接受《<a href="">用户协议</a>》</label>  -->
                        <button class="butt">注册</button>
                    </form>
                    <div class="disan">
                    	<h4>使用第三方帐号登录</h4>
                        <ul class="fix">
                        	<li><a href=""><img src="/static/home/images/zc-qq.jpg" /></a></li>
                        	<li><a href=""><img src="/static/home/images/zc-wb.jpg" /></a></li>
                            <li><a href=""><img src="/static/home/images/zc-zfb.jpg" /></a></li>
                            <li><a href=""><img src="/static/home/images/zc-wx.jpg" /></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!---中间-end-->
<script type="text/javascript" src="/static/home/js/jquery.js"></script>
<script type="text/javascript" src="/static/home/js/toastr.js"></script>
    
    
</script>
<!-- 底部固定区域 -->
<!----底部---->
<div class="foot">
	<div class="wu-kuai">
    	<div class="content">
            <ul class="fix">
                <li><img src="/static/home/images/wu-1.png" />定制开发</li>
                <li><img src="/static/home/images/wu-2.png" />原创设计</li>
                <li><img src="/static/home/images/wu-3.png" />终身售后</li>
                <li><img src="/static/home/images/wu-4.png" />精准分析</li>
                <li><img src="/static/home/images/wu-5.png" />赚佣金</li>
            </ul>
        </div>
    </div>
    <div class="foot-list">
    	<div class="content fix">
        	<div class="foot-list-item">
            	<h3>关于我们</h3>
                <a href="">联系我们</a>
                <a href="">加入我们</a>
            </div>
            <div class="foot-list-item foot-mfl90">
            	<h3>帮助中心</h3>
                <a href="">服务条款</a>
                <a href="">投诉建议</a>
            </div>
            <div class="foot-list-sao fix">
            	<img src="/static/home/images/timg.jpg" />
                <div class="txt">扫一扫<br />关注我们</div>
            </div>
            <div class="foot-list-kf">
            	<h5>客服电话</h5>
                <p><span>400-123-1234</span>周一至周日 09:00—19:00</p>            
            </div>        
        </div>
    </div>
	<div class="foot-bei">
    	<div class="content">
        	<ul>
            	<li><img src="/static/home/images/360.png" /></li>
                <li><img src="/static/home/images/ren.png" /></li>
                <li><img src="/static/home/images/kx.png" /></li>
            </ul>
        	<p>**8版权所有ICP备案号：京ICP备12003479号-3京公网安备 11010802020522号</p>
        </div>
    </div>
</div>
<!---底部-end--->

<!---底部悬浮--->
<div id="light" class="white-content">
    <div class="content po-r">
    	<a class="gb" href="javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'"><img src="/static/home/images/gb.png" /></a>
         <div class="ind-xf">
            <form class="fix">            
                <span>选择：</span>
                <select name="" class="text text-san">
                    <option value="0">网页设计1</option>
                    <option value="1">平面设计2</option>
                    <option value="2">网页设计3</option>
                    <option value="3">平面设计4</option>                
                </select>
                <span>手机号：</span>
                <input name="" class="text" type="text" />
                <input name="" type="button" class="sub" value="立即发布" />            
            </form>
        </div>    
	</div>
</div>
<!---右侧悬浮--->   
<div class="float">
	<ul>
    	<li>
        	<img src="/static/home/images/wx.png" />
            <div class="float-wx">
            	<img src="/static/home/images/timg.jpg" />
            </div>        
        </li>
        <li><a href=""><img src="/static/home/images/kf.png" /></a></li>
        <li><a href="#top"><img src="/static/home/images/fh-top.png" /></a></li>    
    </ul>
</div>


<!---jquery1.8.3库--->
<script type="text/javascript" src="/static/home/js/jquery-1.8.3.min.js"></script>

<!---------轮播-------->
<script type="text/javascript" src="/static/home/js/jquery.SuperSlide.2.1.1.js"></script>
<script type="text/javascript">
    /* 控制左右按钮显示 */
    jQuery(".fullSlide").hover(function(){ jQuery(this).find(".prev,.next").stop(true,true).fadeTo("show",0.9) },function(){ jQuery(this).find(".prev,.next").fadeOut() });

    /* 调用SuperSlide */
    jQuery(".fullSlide").slide({ titCell:".hd ul", mainCell:".bd ul", effect:"fold",  autoPlay:true, autoPage:true, trigger:"click",
        startFun:function(i){
            var curLi = jQuery(".fullSlide .bd li").eq(i); /* 当前大图的li */
            if( !!curLi.attr("_src") ){
                curLi.css("background-image",curLi.attr("_src")).removeAttr("_src") /* 将_src地址赋予li背景，然后删除_src */
            }
        }
    });
</script>

<script>
	$('.title-tab li').click(function(){
	var el_index=$(this).parent().parent().find('li').index(this);
	$(this).parent().parent().find('li').removeClass('on');
	$(this).addClass('on');
	$(this).parent().parent().parent().find(".switch-box").hide();
	$(this).parent().parent().parent().find(".switch-box").eq(el_index).show();
	
	});
	
	$('.title-tab li').click(function(){
	var el_index=$(this).parent().parent().find('li').index(this);
	$(this).parent().parent().find('li').removeClass('on');
	$(this).addClass('on');
	$(this).parent().parent().parent().find(".switch-fw").hide();
	$(this).parent().parent().parent().find(".switch-fw").eq(el_index).show();
	
	});
	
	$('.switch-fl-tab li').click(function(){
	var el_index=$(this).parent().parent().find('li').index(this);
	$(this).parent().parent().find('li').removeClass('on');
	$(this).addClass('on');
	$(this).parent().parent().parent().find(".ind-switch-ul").hide();
	$(this).parent().parent().parent().find(".ind-switch-ul").eq(el_index).show();
	
	});
	
	
	$('.ind-boxb-fr-tab p').click(function(){
	var el_index=$(this).parent().parent().find('p').index(this);
	$(this).parent().parent().find('p').removeClass('on');
	$(this).addClass('on');
	$(this).parent().parent().parent().find(".ind-boxb-tab").hide();
	$(this).parent().parent().parent().find(".ind-boxb-tab").eq(el_index).show();
	
	});	
</script>
<script type="text/javascript">
    
</script>
<script language=JavaScript>
	<!--
	function _InitScroll(_S1,_S2,_W,_H,_T){
		return "var marqueesHeight"+_S1+"="+_H+";var stopscroll"+_S1+"=false;var scrollElem"+_S1+"=document.getElementById('"+_S1+"');with(scrollElem"+_S1+"){style.width="+_W+";style.height=marqueesHeight"+_S1+";style.overflow='hidden';noWrap=true;}scrollElem"+_S1+".onmouseover=new Function('stopscroll"+_S1+"=true');scrollElem"+_S1+".onmouseout=new Function('stopscroll"+_S1+"=false');var preTop"+_S1+"=0; var currentTop"+_S1+"=0; var stoptime"+_S1+"=0;var leftElem"+_S2+"=document.getElementById('"+_S2+"');scrollElem"+_S1+".appendChild(leftElem"+_S2+".cloneNode(true));setTimeout('init_srolltext"+_S1+"()',"+_T+");function init_srolltext"+_S1+"(){scrollElem"+_S1+".scrollTop=0;setInterval('scrollUp"+_S1+"()',50);}function scrollUp"+_S1+"(){if(stopscroll"+_S1+"){return;}currentTop"+_S1+"+=1;if(currentTop"+_S1+"==(marqueesHeight"+_S1+"+1)) {stoptime"+_S1+"+=1;currentTop"+_S1+"-=1;if(stoptime"+_S1+"=="+_T/50+") {currentTop"+_S1+"=0;stoptime"+_S1+"=0;}}else{preTop"+_S1+"=scrollElem"+_S1+".scrollTop;scrollElem"+_S1+".scrollTop +=1;if(preTop"+_S1+"==scrollElem"+_S1+".scrollTop){scrollElem"+_S1+".scrollTop=0;scrollElem"+_S1+".scrollTop +=1;}}}";
	}
	eval(_InitScroll("A1","A2",550,19*6,1000));
	eval(_InitScroll("A3","A4",550,19*6,1000));
</script>

</body>
</html>

