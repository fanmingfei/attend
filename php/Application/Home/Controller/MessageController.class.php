<?php
namespace Home\Controller;
use Think\Controller;
class MessageController extends BackController {
    var $httpsqs;

    public function _initialize() {
        parent::_initialize();
        import("Vendor.httpsqs.httpsqs");
        $httpConfig = C('httpsqs');
        $this->httpsqs = new \httpsqs($httpConfig['host'], $httpConfig['port'], $httpConfig['auth'] , $httpConfig['charset']);
    }

    public function getList() {
        $page = I('page', '1');
        $size = I('size', '10');
        $offset = ($page-1) * $size;
        $where = [];
        if(I('title')) {
            $where['title'] = I('title');
        }

        $msgModel = D('Message');
        $total = $msgModel->getList($where, null, null, null, true);
        $msgList = $msgModel->getList($where, $page, $size, 'id desc');
        $data = [
            'data' => $msgList,
            'pageInfo' => [
                'currentPage' => $page,
                'size' => $size,
                'offset' => $offset,
                'total' => $total,
                'pages' => ceil( $total / $size ),
            ]
        ];
        ajax_return($data, 0, '获取成功');
    }

    //单发学生
    public function addStuMsg()
    {
        $title = I('title', '');
        $content = I('content', '');
        $url = I('url', '');
        $remark = I('remark', '');
        $userIds = I('ids', '');

        if($title=='') {
            ajax_return([], -1, '标题不能为空');
        }
        if($content=='') {
            ajax_return([], -1, '消息内容不能为空');
        }
        if($userIds=='') {
            ajax_return([], -1, '选取的用户不能为空');
        }
        //处理输入的用户id数据
        $userIds = explode(',', $userIds);
        $arr = array();
        foreach ($userIds as $val) {  
            if (empty($val)) {  
                continue;  
            }  
            $arr[] = $val;  
        }
        array_unique($arr);
        $userIds = implode(',', $arr);
        $map['id']  = array('in',$userIds);
        $users = D('Student')->where($map)->select();
        $arr = [];
        foreach($users as $user) {
            array_push($arr, $user['id']);
        }
        $userIds = implode(',', $arr);
        if($userIds=='') {
            ajax_return([], -1, '选取的用户不能为空');
        }
        //插入数据
        $data = [
            'type' => 1,
            'sid' => $userIds,
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'remark' => $remark,
            'status' => 0,
            'time' => time(),
        ];
        $insertId = D('Message')->data($data)->add();
        //加入到发送队列
        $httpsqs = $this->httpsqs;
        $message = $httpsqs->put('tpl_message', $insertId);
        ajax_return($insertId, 0, '创建模板消息成功，正在发送中');
    }

    //单发老师
    public function addTMsg() {
        $title = I('title', '');
        $content = I('content', '');
        $url = I('url', '');
        $remark = I('remark', '');
        $userIds = I('ids', '');

        if($title=='') {
            ajax_return([], -1, '标题不能为空');
        }
        if($content=='') {
            ajax_return([], -1, '消息内容不能为空');
        }
        if($userIds=='') {
            ajax_return([], -1, '选取的用户不能为空');
        }
        //处理输入的用户id数据
        $userIds = explode(',', $userIds);
        $arr = array();
        foreach ($userIds as $val) {  
            if (empty($val)) {  
                continue;  
            }  
            $arr[] = $val;  
        }
        array_unique($arr);
        $userIds = implode(',', $arr);
        if($userIds[0]==''){
            ajax_return([], -1, '选取的用户不能为空');
        }
        $map['id']  = array('in',$userIds);
        $users = D('Teacher')->where($map)->select();
        $arr = [];
        foreach($users as $user) {
            array_push($arr, $user['id']);
        }
        $userIds = implode(',', $arr);
        if($userIds=='') {
            ajax_return([], -1, '选取的用户不能为空');
        }
        //插入数据
        $data = [
            'type' => 2,
            'tid' => $userIds,
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'remark' => $remark,
            'status' => 0,
            'time' => time(),
        ];
        $insertId = D('Message')->data($data)->add();
        //加入到发送队列
        $httpsqs = $this->httpsqs;
        $message = $httpsqs->put('tpl_message', $insertId);
        ajax_return($insertId, 0, '创建模板消息成功，正在发送中');
    }

    //群发学生
    public function addStuGroupMsg()
    {
        $title = I('title', '');
        $content = I('content', '');
        $url = I('url', '');
        $remark = I('remark', '');
        $groupType = I('grouptype', 0);//默认0全体，1按班级, 2按年级
        $classIds = I('classids', '');
        $gradeIds = I('gradeids', '');

        if($title=='') {
            ajax_return([], -1, '标题不能为空');
        }
        if($content=='') {
            ajax_return([], -1, '消息内容不能为空');
        }
        if($groupType!=0 && $groupType!=1 && $groupType !=2) {
            ajax_return([], -1, '错误的群发类型');
        }
        if($groupType==1&&$classIds=='') {
            ajax_return([], -1, '你没有选择班级');
        }
        if($groupType==2&&$gradeIds=='') {
            ajax_return([], -1, '你没有选择年级');
        }

        $remarkTxt = '';
        switch ($groupType) {
            case 0:
                $all = D('Student')->select();
                $arr = [];
                foreach ($all as $user) {
                    array_push($arr, $user['id']);
                }
                $userIds = implode(',', $arr);
                if($remark == '') {
                    $remarkTxt = '群发全体学生';
                }
                break;
            case 1:
                //处理输入的班级id数据
                $classIds = explode(',', $classIds);
                if($classIds[0]==''){
                    ajax_return([], -1, '你没有选择班级');
                }
                $arr = array();
                foreach ($classIds as $val) {  
                    if (empty($val)) {  
                        continue;  
                    }  
                    $arr[] = $val;  
                }
                array_unique($arr);
                $classIds = implode(',', $arr);
                $map['id']  = array('in',$classIds);
                $classes = D('Classes')->where($map)->select();
                $arr = [];
                $classNames = [];
                foreach($classes as $class) {
                    array_push($classNames, $class['name']);
                    //获取某个班级的所有学生id, 并存入arr数组
                    $users = D('Student')->where(['classid'=>$class['id']])->select();
                    foreach($users as $user) {
                        array_push($arr, $user['id']);
                    }
                }
                $userIds = implode(',', $arr);
                if($remark == '') {
                    $remarkTxt = '群发指定班级: '.implode(',', $classNames);
                }
                break;
            case 2:
                $all = D('Student')->where(['grade'=>['in',$gradeIds]])->select();
                $arr = [];
                foreach ($all as $user) {
                    array_push($arr, $user['id']);
                }
                $userIds = implode(',', $arr);
                if($remark == '') {
                    $remarkTxt = '群发指定年级: '.$gradeIds;
                }
                break;
        }

        if($userIds=='') {
            ajax_return([], -1, '选取的用户不能为空');
        }

        //插入数据
        $data = [
            'type' => 1,//学生
            'sid' => $userIds,
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'remark' => $remark ? $remark : $remarkTxt,
            'status' => 0,
            'time' => time(),
        ];
        $insertId = D('Message')->data($data)->add();
        //加入到发送队列
        $httpsqs = $this->httpsqs;
        $message = $httpsqs->put('tpl_message', $insertId);
        ajax_return($insertId, 0, '创建模板消息成功，正在发送中');
    }

    //获取班级列表
    public function getAllClass() {
        $classes = D('Classes')->select();
        ajax_return($classes, 0, '获取班级成功');
    }

    //获取学生
    public function getSingleStu() {
        $keyword = I('keyword', '');

        if(empty($keyword)) {
            ajax_return([], -1, '关键词不能为空');
        }

        $user = D('Student')->searchStudents($keyword);
        ajax_return($user, 0, '搜索完毕');
    }

    //获取老师
    public function getSingleT() {
        $keyword = I('keyword', '');

        if(empty($keyword)) {
            ajax_return([], -1, '关键词不能为空');
        }

        $user = D('Teacher')->getTeacherByName($keyword);
        ajax_return($user, 0, '搜索完毕');
    }
}