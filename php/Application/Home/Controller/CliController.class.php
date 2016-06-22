<?php
namespace Home\Controller;
use Think\Controller;
class CliController extends BaseController {
    var $httpsqs;

    public function _initialize() {
        parent::_initialize();
        import("Vendor.httpsqs.httpsqs");
        $httpConfig = C('httpsqs');
        $this->httpsqs = new \httpsqs($httpConfig['host'], $httpConfig['port'], $httpConfig['auth'] , $httpConfig['charset']);
    }

    public function getTmplMsg() {
        while(1) {
            $msgId = $this->httpsqs->get('tpl_message');
            if($msgId!='HTTPSQS_GET_END' && $msgId != false) {
                $msg = D('Message')->where(['id'=>['eq',$msgId]])->find();
                if($msg) {
                    //获取用户列表
                    if($msg['type']==1) {
                        $users = explode(',', $msg['sid']);
                    } else if($msg['type']==2) {
                        $users = explode(',', $msg['tid']);
                    }
                    //待发送信息
                    $title = $msg['title'];
                    $content = $msg['content'];
                    $url = $msg['url'];
                    //循环发送
                    foreach($users as $user) {
                        //发送模板消息
                        try {
                            $this->SendTmplMsg($user, $title, $content, $url);
                        } catch (Exception $e) {
                            //ToDo: 新建任务去定时处理失败的消息
                            $data = [
                                'msgid' => $msgId,
                                'type' => $msg['type'],
                                'uid' => $user['id'],
                                'status' => 0,
                                'created_at' => time(),
                            ];
                            D('Failmessage')->data($data)->add();
                        }
                    }
                    //更新发送消息状态
                    $msgModel = M('Message');
                    $msgModel->where(['id'=>['eq',$msgId]])->save(['status'=>1]);
                }
            }
            sleep(5);
        }
    }

    private function SendTmplMsg($user, $title, $content, $url) {
        if(C('debug')) {
            $tmpl_id = 'PT0X_93niu22Ti3CYqEL0bXPkflJ06zUK5Yt3_KCF_g';
            $user_id = 'oUeGNtz41L35y49a_xqXGjWeBazU';
            $appid = 'wx54602a12c477c961';
            $secret = 'dde1ceb8fb98cb3ec0a70e9f0849221b';
        } else {
            $tmpl_id = 'GUYSPZTiKtBR6CTJVMXvRqzduppyHpt0Q14lDNx3Kwk';
            $wechat = C('Wechat');
            $appid = $wechat['appid'];
            $secret = $wechat['appsecret'];
            $user_id = 'oUeGNtz41L35y49a_xqXGjWeBazU';// $user['openid'] ? $user['openid'] : '';
        }

        $access_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
        $tmpl_url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=';

        //get access token
        $access_token = S('test_access_token');
        if(!$access_token){
            $r = file_get_contents($access_url);
            $r = json_decode($r, true);
            $r = $r['access_token'];
            S('test_access_token', $r, 7000);
            $access_token = $r;
        }

        //send
        $send_url = $tmpl_url . $access_token;
        $json = '{
            "touser":"'.$user_id.'",
            "template_id":"'.$tmpl_id.'",
            
            "url":"'.$url.'",
            "topcolor":"#FF0000",
            "data":{
                "first": {
                    "value":"'.$title.'",
                    "color":"#173177"
                },
                "keyword1": {
                    "value":"这是一条提醒消息，请点击查看",
                    "color":"#173177"
                },
                "remark":{
                    "value":"'.$content.'",
                    "color":"#173177"
                }
            }
        }';
        $options = array(
            'http' => 
                array(
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                    "Content-Length: ".strlen($json)."\r\n".
                    "User-Agent:MyAgent/1.0\r\n",
                    'method'  => 'POST',
                    'content' => $json
                )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($send_url, false, $context);
        return $result;
    }
}