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
}