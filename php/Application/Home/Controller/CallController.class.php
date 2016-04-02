<?php
namespace Home\Controller;
use Think\Controller;
class CallController extends BaseController {
    function postCall () {
        $cid = ','.I('cid').',';
        $title = I('title');
        $longitude = I('longitude');
        $latitude = I('latitude');

        if (!$cid || !$title || !$longitude || !$latitude) {
            $msg = '参数不完整';
            ajax_return(null, -1, $msg);
        }

        $callModel = D('Call');
        $result = $callModel->addCall($cid, $title, $longitude, $latitude);

        if ($result) {
            ajax_return(null, 0, '点名成功');
        } else {
            ajax_return(null, -1, '点名失败');
        }
    }

    function postSign () {
        $callid = I('callid');
        $longitude = I('longitude');
        $latitude = I('latitude');

        if (!$callid || !$longitude || !$latitude) {
            $msg = '参数不完整';
            ajax_return(null, -1, $msg);
        }

        $callModel = D('Call');
        $callInfo = $callModel->getCallById($callid);

        $distance = getDistance($callInfo['latitude'], $callInfo['longitude'], $latitude, $longitude);

        if ($distance > 100) {
            ajax_return(null, -1, '距离太远，或定位错误！');
        }

        $signAddResult = D('Sign')->addSign($callid);
        if ($signAddResult === true) {
            ajax_return(null, 0, '签到成功');
        } else if ($signAddResult === -1) {
            ajax_return(null, 0, '已签到');
        } else {
            ajax_return(null, 0, '签到失败');
        }
    }

    function setSignStatus () {
        $callid = I('callid');
        $sid = I('sid');
        $status = I('status');

        $re = D('Sign')->setSignStatus($callid, $sid, $status);
        if ($re) 
            ajax_return(null, 0, '设置成功');
        else 
            ajax_return(null, -1, '设置失败');
    }

    function getStudentsStatus () {
        $callid = I('callid');
        $signModel = D('Sign');
        $classes = $signModel->getStudentsByCallid($callid);
        ajax_return($classes, 0, '获取成功');
    }

    function getStudentSignList () {
        $page = I('page', '1');
        $size = I('size', '10');
        
        $signModel = D('Sign');

        return $signModel->getStudentSignListBySid($page, $size);
    }

    function getTeacherCallList () {
        $page = I('page', '1');
        $size = I('size', '10');

        $callModel = D('Call');

        return $callModel->getTeacherCallListByTid($page, $size);
    }
}