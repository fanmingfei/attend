<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <title>Document</title>

    <link rel="stylesheet" type="text/css" href="http://www.dianming.com//static/Public/vendor/main.css" />
    <link rel="stylesheet" type="text/css" href="http://www.dianming.com//static/User/css/style.css" />
</head>

<body>
    <div class="main">
        <div class="container student">
            <div class="form-item">
                <input type="text" name="username" class="form-control" placeholder="姓名" />
            </div>
            <div class="form-item">
                <div class="weui_cell weui_cell_select form-control">
                    <div class="weui_cell_bd weui_cell_primary">
                        <select class="weui_select" name="classid">
                            <option selected value="0">请选择班级</option>
                            <?php if(is_array($classes)): $i = 0; $__LIST__ = $classes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-item">
                <input type="text" name="wename" class="form-control" placeholder="填写微信号" />
            </div>
            <input type="hidden" name="openid" value="<?php echo ($openid); ?>"/>
            <div class="form-item">
                <a href="javascript:;" class="weui_btn weui_btn_primary js-submit">绑定</a>
            </div>
        </div>
    </div>

<script src="http://www.dianming.com//static/Public/js/mod.js"></script>
<script type="text/javascript" src="http://www.dianming.com//static/User/js/script.js"></script>
<script src="http://www.dianming.com//static/Public/js/zepto.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    require('User/js/script.es6');
    </script>
</body>

</html>