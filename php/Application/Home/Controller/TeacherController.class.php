<?php
namespace Home\Controller;
use Think\Controller;
class TeacherController extends Controller {
    public function addTeacher()
    {
        $name = I('name');
        $re = M('Teacher')->data(array('username'=>$name))->add();
        if ($re) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }
    public function delete()
    {
        $id = I('id');
        $re = D('Teacher')->deleteById($id);
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}