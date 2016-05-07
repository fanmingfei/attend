<?php
namespace Common\Model;
use Think\Model\RelationModel;
class LeaveModel extends RelationModel {

    protected $_link = array(
        'Student' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Student',
            'foreign_key'   => 'sid',
            'mapping_name'  => 'student',
        )
    );

    function getLeaveById($id) {
        $leave = $this->relation(true)->find($id);
        $class = D('Classes')->getClassById($leave['student']['classid']);
        $leave['student']['classname'] = $class['name'];
        return $leave;
    }
    function getStudentLeaves ($page=1, $size=20, $uid) {
        $uid = $uid ? $uid : session('user.id');
        $count = $this -> where(array('sid' => $uid))-> count();

        $pageCount = ceil($count / $size);
        $start = ($page - 1) * $size;

        $leaves = $this -> limit($start, $size) -> where(array('sid' => $uid)) -> order('addtime desc') -> select();


        $pageArr = array(
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );
        return array(
            'leaveList' => $leaves,
            'page' => $pageArr
        );
    }
    function getAllLeaves ($page=1, $size=20) {
        $count = $this -> count();

        $pageCount = ceil($count / $size);
        $start = ($page - 1) * $size;

        $leaves = $this -> limit($start, $size) -> order('addtime desc') -> relation(true) -> select();

        $pageArr = array(
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );
        return array(
            'leaveList' => $leaves,
            'page' => $pageArr
            );
    }
    function searchLeaves($keyword) {
        $sts = D('Student')->where(array('username'=>array('like','%'.$keyword.'%')))->select();

        $sidArr = array();
        if($sts) {
            foreach ($sts as $key => $value) {
                array_push($sidArr, array(
                    'like', $value['id']
                ));
            }
            array_push($sidArr, 'or');
            $condition['sid'] = $sidArr;
        }

        $condition['id'] = $keyword;
        $condition['_logic'] = 'OR';

        $leaves = $this -> relation(true) -> where($condition) -> select();

        return array(
            'leaveList' => $leaves,
        );

    }
}