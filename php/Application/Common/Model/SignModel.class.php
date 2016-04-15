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
            'callid' => $callid,
            'sid' => $sid,
            'time' => time(),
            'status'=> 1
        );
        $re = $this->data($data)->add();
        if ($re)
            return true;
        else 
            return -2;
    }

    function setSignStatus ($callid, $sid, $status) {
        $condition = array('callid'=>$callid, 'sid'=>$sid);
        $has = $this->where($condition)->count();
        if($has == 0) {
            $re = $this->data(array('callid'=>$callid, 'sid'=>$sid, 'status'=>$status, 'time'=>time()))->add();
        } else {
            $re = $this->where($condition)->data(array('status'=>$status))->save();
        }
        return $re;
    }

    function getSignStatus ($callid, $sid) {
        $sid = $sid ? $sid: session('user.id');
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

        // 先获取当前点名所有的班级id
        $callItem = $callModel->getCallById($callid);
        $cidArray = getIds($callItem['cid']);

        $data = array();

        $classArray = array();
        // 取出班级里的学生和学生对应的 签到状态 放进数组 按照班级存放进二维数组
        foreach ($cidArray as $key => $classid) {
            $classInfo = $classesModel->getClassById($classid);
            $className = $classInfo['name'];

            // 创建以编辑名命名的数组元素
            $classArray[$className]['list'] = $studentModel->getStudentsByClassId($classid);
            $classArray[$className]['total'] = count($classArray[$className]['list']);
            $classArray[$className]['process'] = 0;
            $classArray[$className]['retroactive'] = 0;
            $classArray[$className]['leave'] = 0;
            $classArray[$className]['none'] = 0;
            $classArray[$className]['rate'] = 0;

            // 遍历当前班级学生，取出学生对应的状态 并且放入数组
            foreach ($classArray[$className]['list'] as $key2 => $user) {
                $sid = $user['id'];
                $status = $this->getSignStatus($callid, $sid);
                $classArray[$className]['list'][$key2]['status'] = $status;
                switch ($status) {
                    case '1':
                        $classArray[$className]['process'] ++;
                        break;
                    case '2':
                        $classArray[$className]['leave'] ++;
                        break;
                    case '3':
                        $classArray[$className]['process'] ++;
                        $classArray[$className]['retroactive'] ++;
                        break;
                    default:
                        $classArray[$className]['none'] ++;
                }
            }
            $classArray[$className]['rate'] = intval((intval($classArray[$className]['process']) / intval($classArray[$className]['total'])) * 100).'%';

        }
        return $classArray;

    }

    function getSignPeopleInfo ($callid) {
        $callModel = D('Call');
        $studentModel = D('Student');

        $call = $callModel->getCallById($callid);
        $ids = getIds($call['cid']);


        $cdt = array();
        foreach ($ids as $key => $value) {
            array_push($cdt, array(
                'eq',
                $value
            ));
        }
        array_push($cdt, 'or');
        $where['classid'] = $cdt;

        $total = $studentModel->where($where)->count();

        $where1 = array(
            'callid'=>$callid,
            'status' => 1
        );
        $active = $this->where($where1)->count();

        $where1['status'] = 3;

        $retroactive = $this->where($where1)->count();


        $where1['status'] = 2;
        
        $leave = $this->where($where1)->count();

        $process = $active + $retroactive;

        $none = $total - $process - $leave;

        $rate = intval(($process / $total)*100) . '%';

        $data = array(
            'total' => $total,
            'active' => $active,
            'leave' => $leave,
            'process' => $process,
            'none' => $none,
            'retroactive' => $retroactive,
            'rate' => $rate
        );

        return $data;



    }

    function getStudentSignListBySid ($page, $size, $sid) {
        $sid = $sid ? $sid: session('user.id');

        $studentModel = D('Student');
        $callModel = D('Call');

        // 取出班级id 
        $student = $studentModel->getStudentById($sid);
        $classid = $student['classid'];

        // 通过班级id获取点名列表
        $callList = $callModel->getCallsByClassId($classid, $page, $size);
        $callListInfo = $callList['data'];

        foreach ($callListInfo as $key => $value) {
            $signStatus = $this->getSignStatus($value['id']);
            $callList['data'][$key]['status'] = $signStatus;
        }

        return $callList;
    }

    function getStudentSignCountBySid ($sid) {

        $sid = $sid ? $sid : session('user.id');

        $term = M('Term')->find(1);
        $startTime = $term['starttime'];
        $where['time'] = array('EGT', $startTime);
        $where['sid'] = $sid;
        $all = $this->where($where)->count();

        $arrive = $this->where($where)->where(array('status'=>1))->count();
        $leave = $this->where($where)->where(array('status'=>2))->count();
        $retroactive = $this->where($where)->where(array('status'=>3))->count();

        $none = $all - $arrive - $leave - $retroactive;

        return array(
            'all' => $all,
            'arrive' => $arrive, 
            'leave' => $leave,
            'retroactive' => $retroactive,
            'none' => $none
        );

    }
}