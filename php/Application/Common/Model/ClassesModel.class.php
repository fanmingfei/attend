<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ClassesModel extends RelationModel {
    function getClassById ($id) {
        return $this->where(array('id'=>$id))->find();
    }
    function getAllClasses () {
        return $this->select();
    }
}