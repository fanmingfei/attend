<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends BackController {

    public function index(){
        D('Schedule')->getSchedulesByRange(1461034800, 1461049200);
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

        $this->nameNav = 'active';
        $this->keyword = $keyword;
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

    public function classes () {

        $className = I('name');

        $class = D('Classes') -> getClassByName($className);

        $this->classesNav = 'active';
        $this -> assign('classes', $class);
        $this -> display();
    }

    public function addClasses () {

        $id = I('id');
        $className = I('name');

        $classes = D('Classes') -> getClassById($id);
        if ($className && $id) {
            $existClass = D('Classes') -> getOneByName($className);
            if ($existClass) {
                $this -> error('班级名称已存在！');
            }
            $result = D('Classes') -> saveClass($id, $className);

            if ($result) {
                $this -> success('修改成功！', U('Admin/classes'));
            }
        }
        if ($className && !$id) {
            $newClass = D('Classes') -> addClass($className);

            if ($newClass) {
                $this -> success('添加成功！', U('Admin/classes'));
            }
        }

        $this -> assign('classes', $classes);
        $this -> display();
    }

    public function removeClass() {
        $id = I('id');

        $result = D('Classes') -> removeClass($id);
        if ($result) {
            $this -> success('删除成功！');
        }else {
            $this -> error('删除失败！');
        }
    }
    public function deleteCall () {
        $id = I('id');
        if ($id) {
            $re = D('Call')->deleteCall($id);
        }
        if ($re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}