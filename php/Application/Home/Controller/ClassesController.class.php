<?php
namespace Home\Controller;
use Think\Controller;
class ClassesController extends BackController {
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

}