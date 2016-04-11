<?php
namespace Common\Model;
use Think\Model\RelationModel;
class CallModel extends RelationModel {
    protected $_link = array(
        'Teacher' => array(
            'mapping_type'  => self::BELONGS_TO,
            'class_name'    => 'Teacher',
            'foreign_key'   => 'tid',
            'mapping_name'  => 'teacher',
        )
    );
    function addCall($cid, $title, $longitude, $latitude) {
        if (session('user.special') == 1) {
            $idName = 'sid';
        } else {
            $idName = 'tid';
        }
        $uid = session('user.id');
        $data = array(
            $idName => $uid,
            'cid' => $cid,
            'title' => $title,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'time' => time()
        );
        $re = $this->data($data)->add();
        if ($re)
            return $re;
        else 
            return false;
    }

    function getCallById($id) {
        $call = $this->where(array('id'=>$id))->relation(true)->find();
        $call['endtime'] = intval($call['time']) + 600;

        $classesModel = D('Classes');

        $cids = getIds($call['cid']);

        foreach ($cids as $key => $value) {
            $cls = $classesModel->getClassById($value);
            $name = $cls['name'];
            $clses[] = $name;
        }
        $call['classes'] = implode('，', $clses);

        return $call;
    }

    function getCallsByClassId($cid, $page, $size) {

        $cid = '%,'.$cid.',%';
        $where['cid'] = array('like', $cid);

        $count = $this->where($where)->count();
        $pageCount = ceil($count / $size);

        $start = ($page - 1) * $size;

        $result = $this->where($where)->limit($start, $size)->order('id desc')->select();

        $data = array(
            'data' => $result,
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );

        return $data;
    }


    function getTeacherCallListByTid ($page, $size, $uid) {
        $uid |= session('user.id');

        if (session('user.special') == 1) {
            $idName = 'sid';
        } else {
            $idName = 'tid';
        }

        $signModel = D('Sign');
        $classesModel = D('Classes');

        $where = array($idName=>$uid);

        $count = $this->where($where)->count();

        $pageCount = ceil($count / $size);

        $start = ($page - 1) * $size;
        $result = $this->where($where)->limit($start, $size)->order('id desc')->select();


        foreach ($result as $key => $value) {
            $result[$key]['signInfo'] = D('Sign')->getSignPeopleInfo($value['id']);

            $cids = getIds($result[$key]['cid']);
            $clses = array();
            foreach ($cids as $key1 => $cid1) {
                $cls = $classesModel->getClassById($cid1);
                $name = $cls['name'];
                $clses[] = $name;
            }
            $result[$key]['classes'] = implode('，', $clses);
        }
        
        return array(
            'data' => $result,
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );
    }

    function checkPost ($cid) {
        $cid = getIds($cid);
        $time = time() - 600;

        $classesModel = D('Classes');

        foreach ($cid as $key => $value) {
            $where['time'] = array('gt', $time);
            $where['cid'] = array('like', '%,'.$value.',%');

            $item = $this->where($where)->relation(true)->count();

            if ($item) {
                $cls = $classesModel->getClassById($value);
                $hasId[] = $cls['name'];
            }
        }
        return $hasId;
    }
    function getCurrentCall () {
        $sid = session('user.id');
        $cid = session('user.classid');
        $time = time() - 600;

        $where['time'] = array('gt', $time);
        $where['cid'] = array('like', '%,'.$cid.',%');

        $item = $this->where($where)->relation(true)->find();
        if ($item) {
            return $this->getCallById($item['id']);
        } else {
            return false;
        }

    }
}