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


    public function postLeaveToTeacher($sid, $tid, $lid)
    {
        $student = D('Student')->getStudentById($sid);
        $teacher = D('Teacher')->getTeacherById($tid);
        $leave = D('Leave')->getLeaveById($lid);

        if (C('debug')) {
            $tempid = 'dSmvXDvXJhPMWXnVWZC23WmHO-GwWP6uSeOKWBDULJ4';
        } else {
            $tempid = 'XCHTCIRvxwEnDKhFuSEv2hiZXVg0uddIBsQes_UPpks';
        }

        $msg = array(
            "touser" => $teacher['openid'],
            "template_id" => $tempid,
            "url" => C('DOMAIN_URL').'/?c=Leave&a=leaveDetail&id='.$lid,
            "topcolor" => "#FF0000",
            "data" => array(
                "first"=> array(
                    "value" => '请假审批结果',
                    // "color" => "#173177"
                ),
                "childName"=> array(
                    "value" => $student['username'],
                ),
                "time"=> array(
                    "value" => date('Y-m-d H:I', $leave['starttime']). ' ~ ' .date('Y-m-d H:I', $leave['endtime']),
                    // "color" => "#173177"
                ),
                'score'=>array(
                    "value" => $leave['description'],
                ),
                'remark' => array(
                    "value"=> date('Y-m-d H:i:s',time()),
                    "color" => "#173177"
                )
            )
        );

        $a = $this->weObj->sendTemplateMessage($msg);
    }
    public function postLeaveToStudent($lid)
    {
        $leave = D('Leave')->getLeaveById($lid);

        switch ($leave['status']) {
            case 1:
                $status = '同意';
                break;
            case 2:
                $status = '拒绝';
                break;
            case 3:
                $status = '撤回';
                break;
            case 0:
                $status = '等待';
                break;
            
            default:
                # code...
                break;
        }

        if (C('debug')) {
            $tempid = '23cN2_nxkgD3oRRi13NoebY6wzBBtmEa7CGaakir_nQ';
        } else {
            $tempid = 'DfyPPKqWtQ-BopP9hG2wMmTm8MODXLkVctIxCvDtt18';
        }

        $msg = array(
            "touser" => $leave['student']['openid'],
            "template_id" => $tempid,
            "url" => C('DOMAIN_URL').'/?c=Leave&a=leaveDetail&id='.$lid,
            "topcolor" => "#FF0000",
            "data" => array(
                "first"=> array(
                    "value" => $leave['description'],
                    // "color" => "#173177"
                ),
                "keyword3"=> array(
                    "value" => date('Y-m-d H:I', $leave['starttime']).' ~ '.date('Y-m-d H:I', $leave['endtime']),
                ),
                'keyword2'=>array(
                    "value" => session('user.username'),
                ),
                'keyword1'=>array(
                    "value" => $status,
                ),
                'remark' => array(
                    "value"=> date('Y-m-d H:i:s',time()),
                    "color" => "#173177"
                )
            )
        );
        $a = $this->weObj->sendTemplateMessage($msg);
    }
    public function postCallToTeacher($cid)
    {

        $call = D('Call')->getCallById($cid);
        $teacherModel = D('Teacher');
        $tc = $teacherModel->getTeacherById($call['tcid']);
        $t = $teacherModel->getTeacherById($call['tid']);

        if (C('debug')) {
            $tempid = 'Ob6GRNreJLPrtrUx_ozMi0U0IHuEiQvRcHJi9ohV86E';
        } else {
            $tempid = 'NXbwy061HFikgswYbkDzXPj5x91Fx4oRXQqSAhTCBcc';
        }

        $msg = array(
            "touser" => $tc['openid'],
            "template_id" => $tempid,
            "url" => C('DOMAIN_URL').'/?c=Call&a=callDetail&id='.$cid,
            "topcolor" => "#FF0000",
            "data" => array(
                "first"=> array(
                    "value" => '发起了点名',
                    // "color" => "#173177"
                ),
                "keyword3"=> array(
                    "value" => '发起点名',
                ),
                'keyword2'=>array(
                    "value" => $call['classes'],
                ),
                'keyword1'=>array(
                    "value" => date('Y-m-d H:i:s',$call['time']),
                ),
                'remark' => array(
                    "value"=> date('Y-m-d H:i:s',time()),
                    "color" => "#173177"
                )
            )
        );
        $a = $this->weObj->sendTemplateMessage($msg);

        $msg['touser'] = $t['openid'];
        $a = $this->weObj->sendTemplateMessage($msg);
        
    }

}