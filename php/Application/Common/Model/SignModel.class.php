<?php
namespace Common\Model;
use Think\Model;
class SignModel extends Model {
    function addSign($callid) {
        $sid = session('user.id');

        $condition = array('callid'=>$callid, 'sid'=>$sid);
        $count = $this->where($condition)->count();
        if ($count > 0) {
            return -1;
        }
        $data = array(
            'callid' => $cid,
            'sid' => $sid,
            'time' => time()
        );
        $re = $this->data($data)->add();
        if ($re)
            return true;
        else 
            return -2;
    }

    function setSignStatus ($callid, $sid, $status) {
        $condition = array('callid'=>$callid, 'sid'=>$sid);
        $has = $this->where($condition)->find();
        if(!$has) {
            $re = $this->data(array('callid'=>$callid, 'sid'=>$sid, 'status'=>$status))->add();
        } else {
            $re = $this->where($condition)->data(array('status'=>$status))->save();
        }
        return $re;
    }

    function getSignStatus ($callid, $sid) {
        $condition = array('callid'=>$callid, 'sid'=>$sid);
        $has = $this->where($condition)->find();
        if ($has) {
            return $has['status'];
        } else {
            return false;
        }

    }

    /**
     * 根据callid获取 当前点名的学生列表
     * @param  [type] $callid [description]
     * @return [type]         [description]
     */
    function getStudentsByCallid($callid) {
        $callModel = D('Call');
        $classesModel = D('Classes');
        $studentModel = D('Student');

        $condition = array('callid'=>$callid);


        // 先获取当前点名所有的班级id
        $callItem = $callModel->where($condition)->find();
        $cidArray = explode(",", $callItem['cid']);


        // 去掉第一个和最后一个空值
        array_shift($cidArray);
        array_pop($cidArray);


        $classArray = array();
        // 取出班级里的学生和学生对应的 签到状态 放进数组 按照班级存放进二维数组
        foreach ($cidArray as $key => $classid) {
            $classInfo = $classesModel->getClassById($classid);
            $className = $classInfo['name'];

            // 创建以编辑名命名的数组元素
            $classArray[$className] = $studentModel->getStudentsByClassId($classid);
            // 遍历当前班级学生，取出学生对应的状态 并且放入数组
            foreach ($classArray[$className] as $key2 => $user) {
                $sid = $user['id'];
                $status = $this->getSignStatus($sid, $classid);
                if ($status) {
                    $classArray[$className][$key2]['status'] = $status;
                }
            }
        }

        return $classArray;

    }

    function getStudentSignStatus ($callid, $sid) {
        $sid = $sid || session('user.id');
    }

    function getStudentSignListBySid ($page, $size, $sid) {
        $sid = $sid || session('user.id');

        $studentModel = D('Student');
        $callModel = D('Call');

        // 取出班级id 
        $student = $studentModel->getStudentById($sid);
        $classid = $student['classid'];

        // 通过班级id获取点名列表
        $callList = $callModel->getCallsByClassId($classid, $page, $size);
        $callListInfo = $callList['data'];

        foreach ($callListInfo as $key => $value) {
            $signStatus = $this->getStudentSignStatus($value['id']);
            $callList['data'][$key]['status'] = $signStatus;
        }

        return $callList;
    }
}