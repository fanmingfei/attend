<?php
namespace Common\Model;
use Think\Model;
class StudentModel extends Model {
    function getStudentByOpenId ($openid) {
        $user = $this->where(array('openid'=>$openid))->find();
        return $user;
    }
    function regStudent ($username, $wename, $classid, $openid) {
        $conditions = array('username'=>$username, 'classid'=>$classid);
        $user = $this->where($conditions)->find();
        if ($user) {
            $data = array('openid'=>$openid, 'wename'=>$wename);
            $this->where($conditions)->data($data)->save();
            return true;
        } else {
            return false;
        }
    }
    function getStudentsByClassId ($cid) {
        return $this->where(array('classid'=>$cid))->select();
    }
    function getStudentById ($id) {
        return $this->where(array('id'=>$id))->find();
    }

}