<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {
    public $openId;
    public function wxLogin(){

        $weObj = new \Home\Controller\WechatController();
        $openId = $weObj->getUserOpenId();

        if (!$openId) {
            $isLogin = checkLogin();
        }
        if ($isLogin) {
            $this->show('已经登陆，请勿重复绑定！');
            return;
        }

        $this->openId = $openId;

        $studentModel = D('Student');
        $teacherModel = D('Teacher');


        $student = $studentModel->getStudentByOpenId($openId);
        $teacher = $teacherModel->getTeacherByOpenId($openId);

        if(!$student && !$teacher) {
            if (I('state') == 't') {
                $this->teacherReg();
            } else {
                $this->studentReg();
            }
            return 1;
        }


        $time = 30*24*60*60;
        if ($teacher) {
            cookie('user', $teacher, $time);
        } else {
            cookie('user', $student, $time);
        }

        $jump = cookie('attend.jump');
        cookie('attend.jump', null);
        header('Location:'.urldecode($jump));

    }

    public function registerStudent() {
        $username = I('username');
        $wename = I('wename');
        $classid = I('classid');
        $openid = I('openid');

        if (!$username) {
            ajax_return(null, -1, '请填写姓名');
        }
        if (!$wename) {
            ajax_return(null, -1, '请填写微信号');
        }
        if (!$classid) {
            ajax_return(null, -1, '请选择班级');
        }
        if (!$openid) {
            ajax_return(null, -1, '错误，请重新进入');
        }

        $studentModel = D('Student');
        if ($studentModel->regStudent($username, $wename, $classid, $openid)) {
            ajax_return(null, 0, '绑定成功');
        } else {
            ajax_return(null, -1, '对应班级没有此学生，请联系老师');
        }

    }

    public function registerTeacher () {
        $username = I('username');
        $phone = I('phone');
        $openid = I('openid');

        if (!$username) {
            ajax_return(null, -1, '请填写姓名');
        }
        if (!$phone) {
            ajax_return(null, -1, '请填写手机');
        }
        if (!$openid) {
            ajax_return(null, -1, '错误，请重新进入');
        }

        $teacherModel = D('Teacher');
        
        if ($teacherModel->regTeacher($username, $phone, $openid)) {
            ajax_return(null, 0, '绑定成功');
        } else {
            ajax_return(null, -1, '绑定出现问题能够，请重试或联系管理员');
        }

    }

    public function bindSuccess () {
        $this->display();
    }

    private function teacherReg () {
        $this->openid = $this->openId;
        $this->display('teacherReg');
    }
    private function studentReg () {
        $this->openid = $this->openId;
        $this->assign('classes', D('Classes')->getAllClasses());
        $this->display('studentReg');
    }

}