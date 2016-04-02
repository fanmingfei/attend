<?php
namespace Common\Model;
use Think\Model;
class CallModel extends Model {
    function addCall($cid, $title, $longitude, $latitude) {
        $tid = session('user.id');
        $data = array(
            'tid' => $tid,
            'cid' => $cid,
            'title' => $title,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'time' => time()
        );
        $re = $this->data($data)->add();
        if ($re)
            return true;
        else 
            return false;
    }

    function getCallById($id) {
        return $this->where(array('id'=>$id))->find();
    }

    function getCallsByClassId($cid, $page, $size) {

        $cid = ','.$cid.',';
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

    function getTeacherCallListByTid ($page, $size, $tid) {
        $tid = $tid || session('user.id');
        
        $where = array('tid'=>$tid);

        $count = $this->where($where)->count()
        $pageCount = ceil($count / $size);

        $start = ($page - 1) * $size;
        $result = $this->where($where)->limit($start, $size)->order('id desc')->select();

        return array(
            'data' => $result,
            'count' => $count,
            'size' => $size,
            'pageCount' => $pageCount,
            'page' => $page
        );
    }
}