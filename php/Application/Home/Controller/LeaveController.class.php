<?php
namespace Home\Controller;
use Think\Controller;
class LeaveController extends BaseController {
    public function leaveBegin()
    {
        $headerTeachers = D('Teacher')->getAllLeaderTeacher();
        $this->headerTeachers = $headerTeachers;
        $teachers = D('Teacher')->getAllTeachers();
        $this->teachers = $teachers;
        $this->display();
    }
    public function getLeaveTeachers()
    {
        $start = I('start');
        $end = I('end');
        $start = strtotime($start);
        $end = strtotime($end);
        $schedule = D('Schedule')->getSchedulesByRange($start,$end);
        if ($schedule[0]) {
            ajax_return($schedule, 0, '获取成功');
        } else {
            ajax_return(null, -1, '没有课程');
        }
    }
    public function leaveDetail() {
        $id = I('id');
        $leaveModel = D('Leave');
        $agreeModel = D('Agree');
        $leave = $leaveModel->getLeaveById($id);
        $agree = $agreeModel->getAgreeByLeaveId($id);

        $this->agree = $agree;
        $this->leave = $leave;
        $this->user = session('user');
        dump($leave);
        $this->display();
    }
    public function leaveList() {
        if(session('user.usertype') == 2) {
            header('Location: '.C('DOMAIN_URL').'/?c=Leave&a=teacherList');
        }
        $leaveModel = D('Leave');
        $page = I('page',1);
        $size = I('size',20);
        $leaves = $leaveModel->getStudentLeaves($page, $size);
        $this->assign($leaves);
        $this->display();
    }
    public function teacherList() {
        if(session('user.usertype') == 1) {
            header('Location: '.C('DOMAIN_URL').'/?c=Leave&a=leaveList');
        }
        $agreeModel = D('Agree');
        $page = I('page',1);
        $size = I('size',20);
        $agrees = $agreeModel->getAgreesByTeacher($page, $size);
        $this->user = session('user');
        $this->assign($agrees);
        $this->display();
    }
    public function leaveCancel () {
        $id = I('id');
        $leave = D('Leave')->getLeaveById($id);
        if ($leave['sid'] != session('user.id')) {
            $this->error('非本人无法撤回');
        }
        $re = M('Leave')->where(array('id'=>$id))->data(array('status'=>3))->save();
        if ($re) {
            $this->success('取消成功');
        } else {
            $this->error('取消出现问题');
        }
    }

    public function leaveSuccess()
    {
        $id = I('id');
        $this->leave = M('Leave')->find($id);
        $this->display();
    }
    public function postLeave()
    {
        $leave = I('post.');


        $teachers = htmlspecialchars_decode($leave['teachers']);

        unset($leave['teachers']);


        $path = $_SERVER[DOCUMENT_ROOT].'/Uploads/';
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     1024000 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'jpeg', 'gif', 'png');// 设置附件上传类型
        $upload->rootPath  =     $path; // 设置附件上传根目录
        $upload->savePath  =     'Leave/'; // 设置附件上传（子）目录
        $upload->autoSub   =     false;
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info && $upload->getError() !== '没有文件被上传！') {// 上传错误提示错误信息
            $this->error($upload->getError());
            exit();
        }else if ($info['leave_file']['savename']) {
            $file = 'Uploads/Leave/'.$info['leave_file']['savename'];
        }


        $data = array();

        $leave['sid'] = session('user.id');
        $leave['image'] = $file;
        $leave['addtime'] = time();

        $leave['starttime'] = strtotime($leave['starttime']);
        $leave['endtime'] = strtotime($leave['endtime']);

        $id = M('Leave')->data($leave)->add();
        if (!$id) {
            $this->error('失败！请重试');
        }
        $teachers = json_decode($teachers, true);

        $weObj = new \Home\Controller\WechatController();

        foreach ($teachers as $key => $value) {
            $item['addtime'] = time();
            $item['leaveid'] = $id;
            $item['teacherid'] = $value;
            M('agree')->data($item)->add();
            $weObj->postLeaveToTeacher(session('user.id'), $value, $id);
        }
        Header('Location: '. C('DOMAIN_URL').'/?c=Leave&a=leaveSuccess&id='.$id);

    }
    function setAgree () {

        $id = I('id');
        $re = M('Agree')->where(array('leaveid'=>$id,'teacherid'=>session('user.id')))->data(array('status'=>1))->save();
        if ($re) {
            $this->success('已同意');
        } else {
            $this->error('出现问题');
        }
    }
    function setRefuse () {

        $id = I('id');
        $re = M('Agree')->where(array('leaveid'=>$id,'teacherid'=>session('user.id')))->data(array('status'=>2))->save();
        if ($re) {
            $this->success('已拒绝');
        } else {
            $this->error('出现问题');
        }
    }
    function setStatus() {
        $id = I('id');
        $type = I('type');
        $re = M('Leave')->where(array('id'=>$id))->data(array('status'=>$type))->save();
        if ($re) {
            $weObj = new \Home\Controller\WechatController();
            $weObj->postLeaveToStudent($id);
            $this->success('操作成功');
        } else {
            $this->error('出现问题');
        }
    }
    function toLeaveList () {
        $type = session('user.type');
        $special = session('user.special');
        if ($type == 1) {
            header('Location: /?c=Leave&a=teacherList');
        } else if ($special == 1) {
            header('Location: /?c=Leave&a=selectType');
        } else {
            header('Location: /?c=Leave&a=leaveList');
        }
    }
    function leaveClassList() {
        $leaves = D('Leave')->getLeaveListByClassId();
        $this->assign($leaves);
        $this->display();
    }
    function setLeaveNote(){
        $leaveId = I('id');
        $leaveNote = I('note');
        $user = session('user');
        if($user['usertype'] != 2 && $user['tzz'] != 1){
            $this -> error('你没有权限修改！');
        }
        $re = M('leave') -> where(array('id'=>$leaveId)) -> data(array('note'=>$leaveNote)) -> save();
        if($re){
            $this -> success('修改成功！');
        }else {
            $this -> error('出现问题！');
        }
    }

}