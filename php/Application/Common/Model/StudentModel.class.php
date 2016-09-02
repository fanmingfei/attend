<?php
namespace Common\Model;
use Think\Model\RelationModel;
class StudentModel extends RelationModel {
    protected $_link = array(
        'Classes' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Classes',
            'foreign_key'   => 'classid',
            'mapping_name'  => 'classes'
        )
    );
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
        return $this->where(array('id'=>$id))->relation(true)->find();
    }

    function saveList($people) {
        $classesModel = D('Classes');

        foreach ($people as $key => $value) {
            if ($value['major']){
                $class = $classesModel->getOneByName($value['major']);
                if (!$class) {
                    $classid = $classesModel->addClass($value['major']);
                } else {
                    $classid = $class['id'];
                }
            }

            $value['classid'] = $classid;
            $this->data($value)->add();
        }
    }
    public function searchStudents($keyword, $order)
    {
        $classes = D('Classes')->getClassByName($keyword);


        if ($classes) {
            $cls = array();
            foreach ($classes as $key => $value) {
                array_push($cls, array(
                    'eq',
                    $value['id']
                ));
            }
            array_push($cls, 'or');
        }

        $map['classid'] = $cls;
        $map['username'] = array('like', '%'.$keyword.'%');
        $map['studentid'] = array('like', '%'.$keyword.'%');
        $map['idcard'] = array('like', '%'.$keyword.'%');
        $map['grade'] = array('like', '%'.$keyword.'%');
        $map['wename'] = array('like', '%'.$keyword.'%');

        $map['_logic'] = 'OR';
        $students = $this->where($map)->relation(true)->select();
        $students = $this->filterStudents($students);

        if($order == 2) {
            uasort($students, 'studentSortByNone');
        } else {
            uasort($students, 'studentSortByLeave');
        }

        return $students;
    }

    function filterStudents($students) {
        $signModel = D('Sign');

        foreach ($students as $key => $value) {
            $callInfo = $signModel->getStudentSignCountBySid($value['id']);
            $students[$key]['leave'] = $callInfo['leave'];
            $students[$key]['retroactive'] = $callInfo['retroactive'];
            $students[$key]['none'] = $callInfo['none'];
        }

        return $students;
    }

    function getStudentByNameAndClass($name, $cls) {
        return $this->where(array('username'=>$name, 'classid'=>$cls))->find();
    }


}