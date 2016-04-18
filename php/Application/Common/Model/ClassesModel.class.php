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
    function getClassByName($name) {
        $where['name'] = array('like', '%'.$name.'%');
        $class = $this->where($where)->select();
        return $class;
    }
    function getOneByName ($name) {
        $class = $this->where(array('name'=>$name))->find();
        return $class;
    }
    function saveClass($id, $name) {
        $data = array(
            'name' => $name,
            'id' => $id
            );
        $result = $this -> data($data) -> save();
        if (!$result) {
            $this -> error('更新失败！');
        }
        return $result;
    }

    function removeClass ($id) {

        $result = D('Classes') -> where(array('id' => $id)) -> delete();
        return $result;
    }
}