<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function _initialize(){
        checkLogin();
        $user = cookie('user');
        if ($user['usertype'] == 1) {
            $user = D('Student')->getStudentById($user['id']);
        } else {
            $user = D('Teacher')->getTeacherById($user['id']);
        }
        if(!$user) {
            cookie('user', null);
            checkLogin();
        }
        session('user', $user);
    }
}