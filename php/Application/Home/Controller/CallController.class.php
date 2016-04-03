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

        $hasCls = $callModel->checkPost($cid);

        if ($hasCls) {
            ajax_return($hasCls, -2, '改班级在点名列表中');
        }

        $result = $callModel->addCall($cid, $title, $longitude, $latitude);

        if ($result) {
            ajax_return($result, 0, '点名成功');
        } else {
            ajax_return(null, -1, '点名失败');
        }
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


    function callBegin () {
        if(session('user.usertype') == 1 && session('user.special') == 0) {
            $this->show('你没有权限点名');
            exit();
        }
        $this->assign('classes', D('Classes')->getAllClasses());
        $this->display();
    }


    function postCallSuccess () {
        $id = I('id');
        $call = D('Call')->getCallById($id);
        $this->assign('call', $call);
        $this->display();
    }


    function callDetail () {
        $callid = I('id');
        $signModel = D('Sign');
        $callModel = D('Call');
        $classes = $signModel->getStudentsByCallid($callid);
        $call = $callModel->getCallById($callid);
        $this->assign(array(
            'signs'=> $classes,
            'call'=> $call
        ));
        $this->display();
    }

    function callList () {
        $callModel = D('Call');
        $page = I('page', 1);
        $size = I('size', 20);
        $callList = $callModel->getTeacherCallListByTid($page, $size);
        

        if ($callList['page'] == $callList['pageCount']) {
            $callList['isEnd'] = 1;
        }
        if ($callList['page'] == 1) {
            $callList['isFirst'] = 1;
        }
        if ($callList['pageCount'] != 1 && !$callList['isEnd']){
            $callList['nextPage'] = $callList['page'] + 1;
        }
        if (!$callList['isFirst']){
            $callList['prevPage'] = $callList['page'] - 1;
        }



        $this->assign('callList', $callList);
        $this->display();
    }
    function setStatus () {
        $sid = I('sid');
        $callid = I('callid');
        $status = I('status');

        $signModel = D('Sign');


        if(session('user.usertype') != 2 && session('user.special') != 1) {
            ajax_return(null, -1, '您没有修改权限');
        }

        $re = $signModel->setSignStatus($callid, $sid, $status);
        if ($re || $re == 0) {
            ajax_return(null, 0, '设置成功');
        } else {
            ajax_return(null, -1, '设置失败');
        }


    }
}