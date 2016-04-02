<?php
namespace Common\Model;
use Think\Model;
class ClassesModel extends Model {
    function getClassById ($id) {
        return $this->where(array('id'=>$id))->find();
    }
    function getAllClasses () {
        return $this->select();
    }
}