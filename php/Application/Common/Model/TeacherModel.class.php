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
        $result = $this->data($data)->add();
        if ($result) {
            return true;
        } else {
            return false;
        }

    }
}