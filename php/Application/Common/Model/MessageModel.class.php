<?php
namespace Common\Model;
use Think\Model;
class MessageModel extends Model {
    function getList($where=[], $page=null, $limit=null, $orderBy=null, $isCount=false) {
        $whereStr = ' 1 = 1 ';
        if(isset($where['title'])&&$where['title']) {
            $whereStr .= " and title like '%{$where['title']}%' ";
        }
        if($isCount) {
            return $this->where($whereStr)->count();
        } else {
            $list = $this->where($whereStr);
            if($page) {
                $this->page($page);
            }
            if($limit) {
                $this->limit($limit);
            }
            if($orderBy) {
                $this->order($orderBy);
            }
            return $this->select();
        }
    }
}