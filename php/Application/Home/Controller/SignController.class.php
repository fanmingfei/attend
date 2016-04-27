<?php
namespace Home\Controller;
use Think\Controller;
class SignController extends BaseController {

    function postSign () {
        $callModel = D('Call');
        $callInfo = $callModel->getCurrentCall();

        if (!$callInfo) {
            ajax_return(null, -1, '签到失败，签到可能已过期');
        }
        $callid = $callInfo['id'];

        $longitude = I('longitude');
        $latitude = I('latitude');

        if (!$longitude || !$latitude) {
            $msg = '没有获取到地理位置';
            ajax_return(null, -1, $msg);
        }


        $distance = getDistance($callInfo['latitude'], $callInfo['longitude'], $latitude, $longitude);

        if ($distance > 500) {
            ajax_return(null, -1, '距离太远，或定位错误！');
        }

        $signAddResult = D('Sign')->addSign($callid);
        if ($signAddResult === true) {
            ajax_return(null, 0, '签到成功');
        } else if ($signAddResult === -1) {
            ajax_return(null, -1, '已签到');
        } else {
            ajax_return(null, -1, '签到失败');
        }
    }


    function signStart () {
        if(session('user.usertype') == 2) {
            $this->show('你没有权限签到');
            exit();
        }
        $call = D('Call')->getCurrentCall();
        if ($call) {
            $this->assign('call', $call);
            $this->display();
        } else {
            $this->display('noCall');
        }

    }

    function signList () {
        $signModel = D('Sign');

        $page = I('page', 1);
        $size = I('size', 20);


        $signList = $signModel->getStudentSignListBySid($page, $size);

        if ($signList['page'] == $signList['pageCount']) {
            $signList['isEnd'] = 1;
        }
        if ($signList['page'] == 1) {
            $signList['isFirst'] = 1;
        }
        if ($signList['pageCount'] != 1 && !$signList['isEnd']){
            $signList['nextPage'] = $signList['page'] + 1;
        }
        if (!$signList['isFirst']){
            $signList['prevPage'] = $signList['page'] - 1;
        }


        $this->assign('signList', $signList);
        $this->display();
    }

    function postSignSuccess () {
        $this->display();
    }
}