<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
    public function add(){

        // $user = D('Admin')->getUser();
        // 
        $user = 123;
        $this->user = $user;
        $this->display();
    }
}