<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends BackController {

    public function index(){
        $this->indexNav = 'active';
        $this->display();
    }
    public function course()
    {
        $keyword = I('keyword');
        $scheduleMode = D('Schedule');
        if ($keyword){
            $this->lession = $scheduleMode->searchSchedule($keyword);
        } else {
            $this->lession = $scheduleMode->getAllSchedules();
        }
        $this->courseNav = 'active';
        $this->keyword = $keyword;
        $this->display();
    }
    public function addCourse()
    {
        $id = I('id');
        if ($id) {
            $this->schedule = D('Schedule')->getScheduleById($id);
            $this->id = $id;
        }
        $this->teachers = D('Teacher')->getAllTeachers();
        $this->classes = D('Classes')->getAllClasses();
        $this->courseNav = 'active';
        $this->display();
    }
    public function user () {
        $this->userNav = 'active';

        $keyword = I('keyword');
        $order = I('order');

        if ($keyword) {
            $studentModel = D('Student');
            
            $this->students = $studentModel->searchStudents($keyword, $order);
            $this->keyword = $keyword;
        }

        $this->display();
    }
    public function addTeacher()
    {
        $id = I('id');
        if ($id) {
            $this->teacher = D('Teacher')->getTeacherById($id);
        }
        $this->userNav = 'active';
        $this->display();
        
    }

    public function teacherList()
    {
        $this->userNav = 'active';
        $this->teachers = D('Teacher')->getAllTeachers();
        $this->display();
    }
    public function termset()
    {
        $this->termNav = 'active';

        $term = M('Term')->find(1);
        $term['starttime'] = date('Y-m-d', $term['starttime']);
        $term['endtime'] = date('Y-m-d', $term['endtime']);
        $this->term = $term;

        $this->display();
    }

    public function logout()
    {
        cookie('super', null);
        session('super', null);
        $this->success('退出成功！', '/?c=Login');
    }
    public function addStudent () {
        $id = I('id');
        if ($id) {
            $this->student = D('Student')->getStudentById($id);
        }
        $classes = D('Classes')->getAllClasses();
        $this->userNav = 'active';
        $this->classes = $classes;
        $this->display();
    }

    public function name () {

        $keyword = I('keyword');
        $page = I('page', 1);
        $size = I('size', 20);

        if ($keyword) {
            $result = D('Call') -> searchCall($keyword);
        } else {
            $result = D('Call') -> getAllCalls($page, $size);
        }

        $this -> assign($result);
        $this -> display();
    }

    public function nameDetail () {
        $callid = I('id');
        $signModel = D('Sign');
        $callModel = D('Call');
        $classes = $signModel->getStudentsByCallid($callid);
        $call = $callModel->getCallById($callid);

        $this->assign(array(
            'signs'=> $classes,
            'call'=> $call
        ));
        $this->display();
    }

    function setStatus () {
        $sid = I('sid');
        $callid = I('callid');
        $status = I('status');

        $signModel = D('Sign');

        $re = $signModel->setSignStatus($callid, $sid, $status);
        if ($re || $re == 0) {
            ajax_return(null, 0, '设置成功');
        } else {
            ajax_return(null, -1, '设置失败');
        }
    }
}