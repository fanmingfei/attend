<?php
namespace Home\Controller;
use Think\Controller;
class TermController extends Controller {
    public function setTerm()
    {
        $array = I('post.');
        $array['starttime'] = strtotime($array['starttime']);
        $array['endtime'] = strtotime($array['endtime']);
        $re = M('Term')->where(array('id'=>1))->data($array)->save();
        if ($re) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }

    }
}