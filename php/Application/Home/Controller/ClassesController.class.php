<?php
namespace Home\Controller;
use Think\Controller;
class ClassesController extends Controller {
    function addClass () {
        $this->display();
    }
    function postClass() {
        $name = I('name');
        if (D('Classes')->addClass($name)){
            echo '添加成功！';
        } else {
            echo '班级重复';
        }
    }
    function addStudent () {
        $classes = D('Classes')->getAllClasses();
        $this->classes = $classes;
        $this->display();
    }
    function postStudent () {
        $username = I('username');
        $classid = I('classid');
        $special = I('special');
        $arr = array('username'=>$username,'classid'=>$classid, 'special'=>$special);
        $re = D('Student')->data($arr)->add();
        if ($re) {
            echo '添加学生成功，需要学生在微信端注册';
        } else {
            echo '添加学生失败~';
        }
    }
}