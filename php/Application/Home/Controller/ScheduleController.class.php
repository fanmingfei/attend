<?php
namespace Home\Controller;
use Think\Controller;
class ScheduleController extends Controller {
    public function postSchedule() {
        $id = I('id');
        $name = I('name');
        $day = I('day');
        $classid = I('classid');
        $teacherid = I('teacherid');
        $lessionnums = ','.I('lessionnums').',';
        $weeks = ','.I('weeks').',';

        $result = D('Schedule')->postSchedule($id, $name, $day, $classid, $lessionnums, $weeks, $teacherid);
        if ($result) {
            ajax_return($result, 0, '成功');
        } else {
            ajax_return(null, -1, '失败');
        }
    }
    public function clearAll()
    {
        $re = D('Schedule')->clearAll();
        if ($re) {
            ajax_return($re, 0, '删除成功');
        } else {
            ajax_return(null, -1, '删除失败');
        }
    }
    public function delete() {
        $id = I('id');
        $re = D('Schedule')->deleteById($id);
        if(re) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }

    }

}