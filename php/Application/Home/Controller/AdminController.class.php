<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
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
        $this->display();
    }


}