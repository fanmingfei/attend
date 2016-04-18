<?php
namespace Common\Model;
use Think\Model\RelationModel;
class CallModel extends RelationModel {
    protected $_link = array(
        'Teacher' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Teacher',
            'foreign_key'   => 'tid',
            'mapping_name'  => 'teacher',
        )
    );
    function addCall($cid, $title, $tcid, $tid, $longitude, $latitude) {
        $uid = session('user.id');
        if (session('user.special') == 1) {
            $info['sid'] = $uid;
            $info['tid'] = $tid;
        } else {
            $info['tid'] = $uid;
        }
        $data = array(
            'cid' => $cid,
            'title' => $title,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'time' => time(),
            'tcid' => $tcid
        );
        $data = array_merge($data, $info);
        $re = $this->data($data)->add();
        if ($re)
            return $re;
        else 
            return false;
    }

    function getCallById($id) {
        $call = $this->where(array('id'=>$id))->relation(true)->find();
        $call['endtime'] = intval($call['time']) + 600;

        $classesModel = D('Classes');

        $cids = getIds($call['cid']);

        foreach ($cids as $key => $value) {
            $cls = $classesModel->getClassById($value);
            $name = $cls['name'];
            $clses[] = $name;
        }
        $call['classes'] = implode('，', $clses);

        return $call;
    }

    function getCallsByClassId($cid, $page, $size) {

        $cid = '%,'.$cid.',%';
        $where['cid'] = array('like', $cid);

        $count = $this->where($where)->count();
        $pageCount = ceil($count / $size);

        $start = ($page - 1) * $size;

        $result = $this->where($where)->limit($start, $size)->order('id desc')->select();

        $data = array(
            'data' => $result,
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );

        return $data;
    }

    function getAllCalls ($page=1, $size=20) {
        $count = $this -> count();

        $pageCount = ceil($count / $size);
        $start = ($page - 1) * $size;

        $calls = $this -> limit($start, $size) -> order('time desc') -> select();

        $allCalls = D('Call') -> getCallDetail($calls);

        foreach ($allCalls as $key => $value) {
            $pageArr = array(
                'count' => $count,
                'size' => $size,
                'pageCount' => $pageCount,
                'page' => $page
            );
        }
        return array(
            'callList' => $allCalls,
            'page' => $pageArr
            );
    }

    function searchCall ($keyword) {

        $classes = D('Classes') -> getClassByName($keyword);
        $teacher = D('Teacher') -> getTeacherByName($keyword);
        if ($classes) {
            $cidArr = array();
            foreach ($classes as $key => $value) {
                array_push($cidArr, array(
                    'like', '%,'.$value['id'].',%'
                    ));
            }
            array_push($cidArr, 'or');
        }
        if ($teacher) {
            $tidArr = array();
            foreach ($teacher as $key => $value) {
                array_push($tidArr, array(
                    'eq', $value['id']
                    ));
            }
            array_push($tidArr, 'or');
        }

        $where['cid'] = $cidArr;
        $where['tid'] = $tidArr;
        $where['_logic'] = 'OR';

        $call = $this -> where($where) -> select();

        $allCalls = $this -> getCallDetail($call);

        return array(
            'callList' => $allCalls
            );

    }
    function getCallDetail ($calls) {


        foreach ($calls as $key => $value) {
            $cid = getIds($value['cid']);
            $class = D('Classes') -> getClassById($cid[0]);

            $calls[$key]['className'] = $class['name'];

            $tcid = $value['tcid'];
            $tcidTeacher = D('Teacher') -> getTeacherById($tcid);
            $calls[$key]['tcidTeacher'] = $tcidTeacher['username'];

            $tid = $value['tid'];
            $teacher = D('Teacher') -> getTeacherById($tid);
            $calls[$key]['teacher'] = $teacher['username'];

            $sid = $calls[$key]['sid'];
            if ($sid) {
                $startUser = D('Student') -> getStudentById($sid);
            }else {
                $startUser = $teacher;
            }

            $calls[$key]['startUser'] = $startUser['username']; 
            
            $data = D('Sign') -> getSignPeopleInfo($value['id']);
            $calls[$key]['data'] = $data;
        }

        return $calls;
    }


    function getTeacherCallListByTid ($page, $size, $uid) {
        $uid = $uid ? $uid : session('user.id');

        if (session('user.special') == 1) {
            $idName = 'sid';
            $where = array(
                $idName=>$uid
            );
        } else {
            $idName = 'tid';
            $where = array(
                $idName=>$uid,
                'tcid' => $uid,
                '_logic' => 'or'
            );
        }


        $signModel = D('Sign');
        $classesModel = D('Classes');


        $count = $this->where($where)->count();

        $pageCount = ceil($count / $size);

        $start = ($page - 1) * $size;
        $result = $this->where($where)->limit($start, $size)->order('id desc')->select();

        foreach ($result as $key => $value) {
            $result[$key]['signInfo'] = D('Sign')->getSignPeopleInfo($value['id']);

            $cids = getIds($result[$key]['cid']);
            $clses = array();
            foreach ($cids as $key1 => $cid1) {
                $cls = $classesModel->getClassById($cid1);
                $name = $cls['name'];
                $clses[] = $name;
            }
            $result[$key]['classes'] = implode('，', $clses);
        }
        
        return array(
            'data' => $result,
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );
    }

    function checkPost ($cid) {
        $cid = getIds($cid);
        $time = time() - 600;

        $classesModel = D('Classes');

        foreach ($cid as $key => $value) {
            $where['time'] = array('gt', $time);
            $where['cid'] = array('like', '%,'.$value.',%');

            $item = $this->where($where)->relation(true)->count();

            if ($item) {
                $cls = $classesModel->getClassById($value);
                $hasId[] = $cls['name'];
            }
        }
        return $hasId;
    }
    function getCurrentCall () {
        $sid = session('user.id');
        $cid = session('user.classid');
        $time = time() - 600;

        $where['time'] = array('gt', $time);
        $where['cid'] = array('like', '%,'.$cid.',%');

        $item = $this->where($where)->relation(true)->find();
        if ($item) {
            return $this->getCallById($item['id']);
        } else {
            return false;
        }

    }
    function setCallPs ($id, $ps) {
        return $this->where(array('id'=>$id))->data(array('ps'=>$ps))->save();
    }

}