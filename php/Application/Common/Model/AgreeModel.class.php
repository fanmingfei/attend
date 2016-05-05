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
        'Schedule' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Schedule',
            'foreign_key'   => 'scheduleid',
            'mapping_name'  => 'schedule',
        ),
    );

    function getAgreeByLeaveId($id) {
        $where = array('leaveid'=>$id);
        return $this->relation(true)->where($where)->select();
    }
}