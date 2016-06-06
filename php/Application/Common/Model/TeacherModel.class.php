<?php
namespace Common\Model;
use Think\Model\RelationModel;
class TeacherModel extends RelationModel {
    function getTeacherByOpenId ($openid) {
        $user = $this->where(array('openid'=>$openid))->find();
        return $user;
    }
    function regTeacher ($username, $phone, $openid) {
        $data = array('username'=>$username, 'openid'=>$openid, 'phone'=>$phone);
        $user = $this->where(array('username'=>$username))->find();
        if ($user) {
            $result = $this->where(array('username'=>$username))->data($data)->save();
        } else {
            $result = $this->data($data)->add();
        }
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    function getTeacherById($id) {
        $user = $this->where(array('id'=>$id))->find();
        return $user;
    }

    function getAllTeachers () {
        return $this->select();
    }

    function getTeacherByName ($username) {
        $where['username'] = array('like', '%'.$username.'%');
        $teacher = $this->where($where)->select();
        return $teacher;
    }
    function deleteById($id) {
        return $this->where(array('id'=>$id))->delete();
    }
    function getAllLeaderTeacher()
    {
        return $user = $this->where(array('lead'=>1))->select();
    }
}