<?php
namespace Home\Controller;
use Think\Controller;
class LeaveBackController extends BackController {
    public function postLeave()
    {
        $leave = I('post.');

        $user = D('Student')->getStudentByNameAndClass($leave['username'],$leave['classid']);
        if (!$user) {
            $this->error('没有找到学生');

        }


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

        $leave['sid'] = $user['id'];
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
        $this->success('添加成功');

    }
}