<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:59:"D:\xampp\htdocs\tplay\public/../app/index\view\.\index.html";i:1551426592;s:55:"D:\xampp\htdocs\tplay\app\index\view\common\header.html";i:1551494392;s:55:"D:\xampp\htdocs\tplay\app\index\view\common\footer.html";i:1551506912;}*/ ?>
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
                <?php if(empty(session('homeUserInfo'))): ?>
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
<!---中间内容-->
<div class="wrap">
	<div class="fullSlide">
        <div class="bd">
            <ul>
                <li><a target="_blank" href=""><img src="/static/home/images/banner.jpg" /></a></li>
                <li><a target="_blank" href=""><img src="/static/home/images/banner.jpg" /></a></li>
                <li><a target="_blank" href=""><img src="/static/home/images/banner.jpg" /></a></li>
                <li><a target="_blank" href=""><img src="/static/home/images/banner.jpg" /></a></li>
            </ul>
        </div>
        <div class="hd"><ul></ul></div>       
    </div>
    <div class="si-kuai">
    	<div class="content">
        	<ul class="sikuai-list fix">
            	<li>
                	<img src="/static/home/images/sikuai-1.png" />
                    <p>原创设计</p>
                </li>
                <li>
                	<img src="/static/home/images/sikuai-2.png" />
                    <p>精准分析</p>
                </li>
                <li>
                	<img src="/static/home/images/sikuai-3.png" />
                    <p>担保交易</p>
                </li>
                <li>
                	<img src="/static/home/images/sikuai-4.png" />
                    <p>终身售后</p>
                </li>
            </ul>
        </div>
    </div>
    <div class="content">
        <!-- 行业顾问 -->
        <div class="ind-boxa">
            <div class="title fix">
            	<h3>行业顾问</h3>
                <ul class="title-tab fix">
                	<li class="on">设计</li>
                    <li>开发</li>
                    <li>推广</li>
                    <li>其它服务</li>
                </ul>            
            </div>
            <div class="switch">
            	<div class="switch-box" style="display:block;">
                	<ul class="fix">
                    	<li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李设计</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李设计</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李设计</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李设计</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                    </ul>
                </div>
                
                <div class="switch-box" style="display:none;">
                	<ul class="fix">
                    	<li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李开发</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李开发</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李开发</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李开发</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                    </ul>
                </div>
                
                <div class="switch-box" style="display:none;">
                	<ul class="fix">
                    	<li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李推广</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李推广</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李推广</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李推广</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                    </ul>
                </div>
                
                <div class="switch-box" style="display:none;">
                	<ul class="fix">
                    	<li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">其它服务</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">其它服务</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">李推广</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                        <li>
                        	<div class="img-box"><img src="/static/home/images/tx.png"></div>
                            <div class="name">其它服务</div>
                            <div class="introduce">5年设计经验</div>
                            <a href="">立即咨询</a>
                        </li>
                    </ul>
                </div>
            </div>        
        </div>
         <!- 行业顾问 -->
        <div class="ind-boxa">
            <div class="title fix">
            	<h3>热门服务</h3>
                <ul class="title-tab fix">
                	<li class="on">设计</li>
                    <li>开发</li>
                    <li>推广</li>
                    <li>其它服务</li>
                </ul>            
            </div>
            <div class="switch">
            	<div class="switch-fw fix" style="display:block;">                   
                    <div class="switch-fw-fr">
                    	<ul class="ind-switch-ul" style="display:block;">
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
   
                        </ul> 
                    </div> 
                </div> 
            	<div class="switch-fw fix" style="display:none;">                   
                    <div class="switch-fw-fr">
                    	<ul class="ind-switch-ul" style="display:block;">
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌2设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
                            <li>                                
                                <div class="img-box"><img src="/static/home/images/img-box.png"></div>
                                <div class="txt-box">
                                    <div class="fix">
                                        <div class="txt">品牌设计</div>
                                        <div class="shu">购买（682）</div>
                                    </div>
                                    <div class="price fix">
                                        <p><span>￥880</span>起</p>                                        
                                    	<a href="">购买</a>
                                    </div>
                                </div>
                            </li>
   
                        </ul> 
                    </div> 
                </div> 
				
			</div>
		</div>
        <div class="ind-boxb fix">
        	<div class="ind-boxb-fl fl">
            	<div class="title fix">
                    <h3>赚佣金</h3>  
                </div>
                <table class="ind-boxb-fl-table" width="100%" border="0" cellspacing="2" cellpadding="0">
                    <tr>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xq">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xy">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="fh">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xq">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xy">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="fh">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xq">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xy">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="fh">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xq">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="xy">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                        <td>
                        	<div class="tx"><img src="/static/home/images/tx.png" /></div>
                            <div class="txt">
                            	<div class="fix">
	                            	<h4>赵伟</h4>
                                    <span class="fh">喜鹊</span>
                                </div>
                                <p>加入时间：2010年7月11号</p>
                                <p>近 3个月：20万</p>
                            </div>
                        </td>
                    </tr>
				</table>
            </div>
            <div class="ind-boxb-fr fr">
            	<div class="ind-boxb-fr-tab">
                	<p class="on">最新入群</p><span>|</span><p>最新收入</p>
                </div>
                <div class="ind-boxb-tab" style="display:block;">
                	<table class="ind-boxb-tab-table" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tr>
                            <th scope="col">级别</th>
                            <th scope="col">ID</th>
                            <th scope="col">昵称</th>
                            <th scope="col">加入时间</th>
                        </tr>
					</table>
					<div id="A1" style="height:370px;">
	                    <div id="A2">
                        	<table class="ind-boxb-tab-table" width="100%" border="0" cellspacing="2" cellpadding="0">
                                <tr>
                                    <td><span class="xq">喜鹊</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="fh">凤凰</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="xy">小丫</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="xq">喜鹊</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="fh">凤凰</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="xy">小丫</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="xq">喜鹊</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="fh">凤凰</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td><span class="xy">小丫</span></td>
                                    <td>245785</td>
                                    <td>大胡子</td>
                                    <td>2018-02-02</td>
                                </tr>
                            </table>
						</div>
					</div>                    
                </div>
                
                <div class="ind-boxb-tab" style="display:none;">
                    <table class="ind-boxb-tab-table" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tr>
                            <th scope="col">昵称</th>
                            <th scope="col">订单金额</th>
                            <th scope="col">佣金</th>
                            <th scope="col">时间</th>
                        </tr>
                    </table>
                    <div id="A3" style="height:370px;">
                        <div id="A4">
                            <table class="ind-boxb-tab-table" width="100%" border="0" cellspacing="2" cellpadding="0">
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                                <tr>
                                    <td>沙漠骆驼</td>
                                    <td>1000</td>
                                    <td><span class="yj">200</span></td>
                                    <td>2018-02-02</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        
        </div>
        
        <div class="ind-boxc">
        	<div class="title fix">
                <h3>常见问题</h3>  
            </div>
            <div class="ind-boxc-list fix">
                <div class="item">
                	<h4>购买服务</h4>
                    <ul>
                    	<li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">新用户如何付款</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">新用户 如何付款</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                    </ul>
                </div>
                
                <div class="item">
                	<h4>咨询顾问</h4>
                    <ul>
                    	<li><a href="">有专业问题如何</a></li>
                        <li><a href="">有专业问题如何咨询</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">有专业问题如何咨询</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                    </ul>
                </div>
                
                <div class="item">
                	<h4>售后服务</h4>
                    <ul>
                    	<li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">新用户如何付款</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">新用户 如何付款</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                    </ul>
                </div>
                <div class="item">
                	<h4>技术分享</h4>
                    <ul>
                    	<li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">新用户如何付款</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                        <li><a href="">新用户 如何付款</a></li>
                        <li><a href="">新用户如何购买服务</a></li>
                    </ul>
                </div>
                <div class="item">
                	<h4>如何赚佣金</h4>
                    <ul>
                    	<li><a href="">佣金奖励机制</a></li>
                        <li><a href="">如何开展业务</a></li>
                        <li><a href="">佣金奖励机制</a></li>
                        <li><a href="">如何开展业务</a></li>
                        <li><a href="">佣金奖励机制</a></li>
                    </ul>
                </div>
            </div>
        </div>
	</div>
</div>
<!-- 中间-end -->

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
<!---底部-end-->

<!---底部悬浮-->
<div id="light" class="white-content">
    <div class="content po-r">
    	<a class="gb" href="javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'"><img src="/static/home/images/gb.png" /></a>
         <div class="ind-xf">
            <form class="fix" action="/index/mend/insert" method="post">            
                <span>选择：</span>
                <select name="cid" class="text text-san">
                    <?php foreach($cate as $k => $v): if($v['pid'] == 0): foreach($cate as $ka => $va): if($v['id'] == $va['pid']): foreach($cate as $kb=>$vb): if($vb['pid'] == $va['id']): ?>
                    <option value="<?php echo $vb['id']; ?>"><?php echo $vb['name']; ?></option>   
                    <?php endif; endforeach; endif; endforeach; endif; endforeach; ?>
                </select>
                <span>手机号：</span>
                <input name="tel" class="text" type="text" />
                <input name="" type="submit" class="sub" value="立即发布" />            
            </form>
        </div>    
	</div>
</div>
<!---右侧悬浮-->   
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

<!-轮播-->
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

