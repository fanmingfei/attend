<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function sign(){
        $type = session('user.usertype');
        $spacial = session('user.special');
        if ($spacial == 1) {
            $this->display('Sign/selectType');
            exit();
        }
        if($type == 1) {
            header('Location: /?c=Sign&a=signStart');
        } else {
            header('Location: /?c=Call&a=CallBegin');
        }
    }
    public function index2() {
    }
}