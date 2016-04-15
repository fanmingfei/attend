<?php
namespace Home\Controller;
use Think\Controller;
class BackController extends Controller {
    public function _initialize(){
        $cookie = cookie('super');
        $session = session('super');
        $adminModel = M('Admin');
        if (!$session && !$cookie) {
            $this->error('请登录', '/?c=Login');
        } else if ($cookie) {
            $username = $cookie['username'];
            $password = $cookie['password'];

            $admin = $adminModel->where(array('username'=>$username))->find();
            if ($password == $admin['password']) {
                session('super', $admin);
                cookie('super', $admin, 30*24*60*60);
            }
        } else {
            $username = $session['username'];
            $password = $session['password'];

            $admin = $adminModel->where(array('username'=>$username))->find();
            if ($password == $admin['password']) {
                session('super', $admin);
                cookie('super', $admin, 30*24*60*60);
            }
        }
    }
}