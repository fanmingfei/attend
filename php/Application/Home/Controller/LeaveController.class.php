<?php
namespace Home\Controller;
use Think\Controller;
class LeaveController extends BaseController {
    public function leaveBegin()
    {
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
        var_dump($agree);

        $this->agree = $agree;
        $this->leave = $leave;

        $this->display();
    }
    public function leaveList() {
        $this->display();
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




        $lessions = htmlspecialchars_decode($leave['lessions']);

        unset($leave['lessions']);



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
            $lessions = json_decode($lessions, true);
        foreach ($lessions as $key => $value) {
            $value['addtime'] = time();
            $value['leaveid'] = $id;
            M('agree')->data($value)->add();
        }
        Header('Location: '. C('DOMAIN_URL').'/?c=Leave&a=leaveSuccess&id='.$id);

    }
}