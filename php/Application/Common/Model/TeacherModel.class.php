<?php
namespace Common\Model;
use Think\Model;
class TeacherModel extends Model {
    function getTeacherByOpenId ($openid) {
        $user = $this->where(array('openid'=>$openid))->find();
        return $user;
    }
    function regTeacher ($username, $openid) {
        $data = array('username'=>$username,'openid'=>$openid);
        $result = $this->data($data)->add();
        if ($result) {
            return true;
        } else {
            return false;
        }

    }
}