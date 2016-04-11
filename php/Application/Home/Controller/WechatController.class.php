<?php

//微信接口
//
//@author Edire 
//@date 2016-01-18
namespace Home\Controller;
use Think\Controller;
class WechatController extends BaseController {
    public $weObj;
    public function _initialize()
    {
        import("Vendor.Weixin.Wx");
        $options = C('wechat');
        $this->weObj = new \TPWechat($options);

    }

//http://www.dianming.com/?c=Wechat&a=wxLogin&type=t&cate=bbb
    public function wxLogin()
    {
        $type = I('type','s');
        $state = $type;
        $authname = 'user_token'. $accessToken['appid'];
        S($authname, null);
        // if (!S($authname) && isWeixin()) {
        $url = $this->weObj->getOauthRedirect(C('DOMAIN_URL').'/?c=User&a=wxLogin', $state, 'snsapi_base');
        header('Location:'.$url);
        // }
    }
    public function getUserOpenId()
    {
        $accessToken = $this->weObj->getOauthAccessToken();
        $expire = $accessToken['expires_in'] ? intval($accessToken['expires_in'])-100 : 3600;
        $authname = 'user_token'. $accessToken['appid'];
        S($authname, $accessToken['access_token'], $expire);
        return $accessToken['openid'];
    }

    public function getSign () {
        $url = urldecode(I('url'));
        $timestamp = I('timestamp');
        $noncestr = I('noncestr');
        $tiket = $this->weObj->getJsSign($url, $timestamp, $noncestr);
        ajax_return($tiket, 0, '获取成功');

    }
}