<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index() {
        $this->display('Admin/login');
    }
    public function login()
    {
        $username = I('username');
        $password = I('password');
        $admin = M('Admin')->where(array('username'=>$username, 'password'=>$password))->count();
        if ($admin) {
            session('super', $admin);
            cookie('super', $admin, 30*24*60*60);
            $this->success('登陆成功', '/?c=Admin');
        } else {
            $this->error('用户信息不正确！');
        }
    }
}