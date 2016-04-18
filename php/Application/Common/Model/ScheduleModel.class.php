<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ScheduleModel extends RelationModel {

    protected $_link = array(
        'Teacher' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Teacher',
            'foreign_key'   => 'teacherid',
            'mapping_name'  => 'teacher',
        ),
        'Classes' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Classes',
            'foreign_key'   => 'classid',
            'mapping_name'  => 'classes'
        )
    );
    /**
     * 通过id获取课程信息
     */
    function getScheduleById ($id) {
        return $this->where(array('id'=>$id))->relation(true)->find();
    }

    function getAllSchedules() {
        return $this->relation(true)->order('id desc')->select();
    }

    /**
     * 添加课程信息
     */
    function postSchedule ($id, $name, $day, $classid, $lessionnums, $weeks, $teacherid) {
        $data = array(
            'name' => $name,
            'day' => $day,
            'classid' => $classid,
            'classid' => $classid,
            'lessionnums' => $lessionnums,
            'weeks' => $weeks,
            'teacherid' => $teacherid,
        );
        if ($id) {
            return $this->where(array('id'=>$id))->data($data)->save();
        } else {
            return $this->data($data)->add();
        }
    }

    /**
     * 设置状态
     * @param [type] $where [description]
     * @param [type] $data  [description]
     */
    function setSchedule ($where, $data) {

    }

    /**
     * 删除全部
     */
    function clearAll () {
        return $this->where(1)->delete();
    }

    function deleteById ($id) {
        return $this->where(array('id'=>$id))->delete();
    }

    function searchSchedule($keyword)
    {
        $classes = D('Classes')->getClassByName($keyword);
        $teachers = D('Teacher')->getTeacherByName($keyword);


        if ($classes) {
            $cls = array();
            foreach ($classes as $key => $value) {
                array_push($cls, array(
                    'eq',
                    $value['id']
                ));
            }
            array_push($cls, 'or');
        }

        if ($teachers) {
            $tcs = array();
            foreach ($teachers as $key => $value) {
                array_push($tcs, array(
                    'eq',
                    $value['id']
                ));
            }
            array_push($tcs, 'or');
        }


        $map['name'] = array('like', '%'.$keyword.'%');
        $map['classid'] = $cls;
        $map['teacherid'] = $tcs;
        $map['_logic'] = 'OR';

        return $this->where($map)->relation(true)->select();
    }
    

    function getCurrentSchedule() {
        $type = session('user.usertype');
        if ($type == 1) {
            return $this->getCurrentScheduleBySid();
        } else {
            return $this->getCurrentScheduleByTid();
        }
    }

    function getCurrentScheduleBySid($sid) {
        $sid = $sid ? $sid : session('user.id');

        $classid = session('user.classid');

        $termModel = D('Term');
        $currLession = $termModel->getCurrentLessionNum();
        if ($currLession == 0) {
            return false;
        }
        $currentWeek = $termModel->getCurrentWeek();
        if (!$currentWeek) {
            return false;
        }
        $day = date('w');
        $schedule = $this->where(array(
            'classid' => $classid,
            'lessionnums' => array('like', '%,'.$currLession.',%'),
            'weeks'=>array('like', '%,'.$currentWeek.',%'),
            'day'=>array('like', $day)
        ))->find();

        return $schedule;

    }
    function getCurrentScheduleByTid($tid) {
        $tid = $tid ? $tid : session('user.id');

        $termModel = D('Term');
        $currLession = $termModel->getCurrentLessionNum();
        if ($currLession == 0) {
            return false;
        }
        $currentWeek = $termModel->getCurrentWeek();
        if (!$currentWeek) {
            return false;
        }
        $day = date('w');
        $schedule = $this->where(array(
            'teacherid' => $tid,
            'lessionnums' => array('like', '%,'.$currLession.',%'),
            'weeks'=>array('like', '%,'.$currentWeek.',%'),
            'day'=>array('like', $day)
        ))->find();

        return $schedule;
    }

    function getSheduleByTime($sid, $time)
    {
        $sid = $sid ? $sid : session('user.id');

        $classid = session('user.classid');

        $termModel = D('Term');

        $currLession = $termModel->getCurrentLessionNum(date('H:i', $time));
        if ($currLession == 0) {
            return false;
        }
        $currentWeek = $termModel->getCurrentWeek($time);
        if (!$currentWeek) {
            return false;
        }
        $day = date('w', $time);
        $schedule = $this->where(array(
            'classid' => $classid,
            'lessionnums' => array('like', '%,'.$currLession.',%'),
            'weeks'=>array('like', '%,'.$currentWeek.',%'),
            'day'=>array('like', $day)
        ))->relation(true)->find();

        return $schedule;
        
    }

    function getSchedulesByRange($start, $end) {
        $termModel = D('Term');

        $lessionsTime = $termModel->getLessionTimeByRange($start, $end);

        $array = array();

        foreach ($lessionsTime as $key => $value) {
            $schedule = $this->getSheduleByTime(null, $value);
            if ($schedule) {
                $schedule['date'] = date('m月d号', $value);
                $array[] = $schedule;
            }
        }
        return $array;
    }

}