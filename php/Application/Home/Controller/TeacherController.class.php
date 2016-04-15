<?php
namespace Home\Controller;
use Think\Controller;
class TeacherController extends Controller {
    public function addTeacher()
    {

        $id = I('id');
        $name = I('name');
        $lead = I('lead');
        if ($id) {
            $re = M('Teacher')->where(array('id'=>$id))->data(array('username'=>$name,'lead'=>$lead))->save();
        } else {
            $re = M('Teacher')->data(array('username'=>$name,'lead'=>$lead))->add();
        }
        if ($re) {
            $this->success('成功');
        } else {
            $this->error('失败');
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