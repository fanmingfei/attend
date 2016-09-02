<?php
namespace Home\Controller;
use Think\Controller;
class AdminLeaveController extends BackController {
    public function leave ()
    {

        $keyword = I('keyword');
        $page = I('page', 1);
        $size = I('size', 20);

        if ($keyword) {
            $result = D('Leave') -> searchLeaves($keyword);
        } else {
            $result = D('Leave') -> getAllLeaves($page, $size);
        }

        $this->leaveNav = 'active';
        $this->keyword = $keyword;
        $this -> assign($result);
        $this -> display();

        
    }
    public function deleteLeave()
    {

        $id = I('id');
        if ($id) {
            $re = D('Leave')->where(array('id'=>$id))->delete();
        }
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function leaveDetail(){
        $id = I('id');
        $this->leaveNav = 'active';
        $this -> leaveDetail = D('Leave') -> getLeaveById($id);
        $this -> display();
    }

    public function setLeaveAgree () {

        $id = I('id');
        $leaveRe = M('Leave')->where(array('id'=>$id))->data(array('status'=>1))->save();
        $agreeRe = M('Agree')->where(array('leaveid'=>$id))->data(array('status'=>1))->save();
        if ($leaveRe && $agreeRe) {
            $this->success('已同意');
        } else {
            $this->error('出现问题');
        }
    }
    
    public function setLeaveRefuse () {

        $id = I('id');
        $re = M('Leave')->where(array('id'=>$id))->data(array('status'=>2))->save();
        $agreeRe = M('Agree')->where(array('leaveid'=>$id))->data(array('status'=>2))->save();
        if ($re) {
            $this->success('已拒绝');
        } else {
            $this->error('出现问题');
        }
    }
}