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
        )
    );
    /**
     * 通过id获取课程信息
     */
    function getScheduleById ($id) {

    }

    /**
     * 添加课程信息
     */
    function postSchedule ($name, $day, $classid, $lessionnums, $weeks, $teacherid) {


    }

    /**
     * 设置状态
     * @param [type] $where [description]
     * @param [type] $data  [description]
     */
    function setSchedule ($where, $data) {

    }
    



}