<?php
function isWeixin () {
  if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
      return true;
  } 
  return false;
}
function ajax_return($data,$status,$msg){
    ob_clean();
    $r = array(
        'data'   =>$data,
        'status' =>$status,
        'msg'    =>$msg
        );
    header('Content-Type:application/json;charset=utf-8');
    exit(json_encode($r));
}


//判断是否为手机端
function isMobile(){
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    return true;

    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
    return true;
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    //找不到为flase,否则为true
    return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
    $clientkeywords = array(
    'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
    );
    //从HTTP_USER_AGENT中查找手机浏览器的关键字
    if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
    return true;
    }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
    if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
    return true;
    }
    }
    return false;
}

function isLogin() {
    if (cookie('user')) {
        return true;
    }
    return false;
}

function checkLogin () {
    if (!cookie('user')) {
        $jump = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
        cookie('attend.jump', $jump);
        if(I('state') == 't') {
            $url = C('DOMAIN_URL').'/?c=Wechat&a=wxLogin&type=t';
        } else {
            $url = C('DOMAIN_URL').'/?c=Wechat&a=wxLogin';
        }
        header('Location: '.$url);
    }
    return true;
}


/** 
* @desc 根据两点间的经纬度计算距离 
* @param float $lat 纬度值 
* @param float $lng 经度值 
*/
function getDistance($lat1, $lng1, $lat2, $lng2) 
{ 
    $earthRadius = 6367000; //approximate radius of earth in meters 
     
    /* 
    Convert these degrees to radians 
    to work with the formula 
    */
     
    $lat1 = ($lat1 * pi() ) / 180; 
    $lng1 = ($lng1 * pi() ) / 180; 
     
    $lat2 = ($lat2 * pi() ) / 180; 
    $lng2 = ($lng2 * pi() ) / 180; 
     
    /* 
    Using the 
    Haversine formula 
     
    http://en.wikipedia.org/wiki/Haversine_formula 
     
    calculate the distance 
    */
     
    $calcLongitude = $lng2 - $lng1; 
    $calcLatitude = $lat2 - $lat1; 
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2); 
    $stepTwo = 2 * asin(min(1, sqrt($stepOne))); 
    $calculatedDistance = $earthRadius * $stepTwo; 
     
    return round($calculatedDistance); 
} 


function getIds($arr) {

    $arr = explode(",", $arr);
    // 去掉第一个和最后一个空值
    array_shift($arr);
    array_pop($arr);

    return $arr;
}