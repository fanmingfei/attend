<?php
namespace Home\Controller;
use Think\Controller;
class LeaveNoteController extends Controller {
    // 请假条，所有人都可以看
    public function index(){
        $leaveId = I('id');
        $leaveNote = D('Leave') -> getLeaveById($leaveId);

        $this -> leave = $leaveNote;
        $this -> display('Leave:leaveNote');
    }
}