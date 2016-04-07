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
    function addClass($name) {
        $arr = array('name'=> $name);
        $has = $this->where($arr)->count();
        if ($has > 0) {
            return false;
        } else {
            return $this->data(array('name'=> $name))->add();
        }
    }
}