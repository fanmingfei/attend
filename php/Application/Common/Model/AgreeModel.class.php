<?php
namespace Common\Model;
use Think\Model\RelationModel;
class AgreeModel extends RelationModel {
    protected $_link = array(
        'Teacher' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Teacher',
            'foreign_key'   => 'teacherid',
            'mapping_name'  => 'teacher',
        ),
        'Leave' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Leave',
            'foreign_key'   => 'leaveid',
            'mapping_name'  => 'leave',
        ),
    );

    function getAgreeByLeaveId($id) {
        $where = array('leaveid'=>$id);
        return $this->relation(true)->where($where)->select();
    }
    function getAgreesByTeacher($page=1, $size=20, $tid) {
        $tid = $tid ? $tid : session('user.id');

        if (session('user.tzz') == 1) {
            $count = $this ->group('leaveid') -> count();
        } else {
            $count = $this -> where(array('teacherid' => $tid))-> count();
        }


        $pageCount = ceil($count / $size);
        $start = ($page - 1) * $size;

        if (session('user.tzz') == 1) {
            $agrees = $this -> relation(true)-> limit($start, $size) -> order('addtime desc') ->group('leaveid')-> select();
        } else {
            $agrees = $this -> relation(true) -> limit($start, $size) -> where(array('teacherid' => $tid)) -> order('addtime desc') -> select();
        }
        $studentModel = D('Student');
        foreach ($agrees as $key => $value) {
            $agrees[$key]['student'] = $studentModel->getStudentById($value['leave']['sid']);
        }
        $pageArr = array(
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );
        return array(
            'agreeList' => $agrees,
            'page' => $pageArr
        );

    }
}