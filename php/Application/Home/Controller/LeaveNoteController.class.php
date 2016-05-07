<?php
namespace Home\Controller;
use Think\Controller;
class LeaveNoteController extends Controller {
    
    public function index(){
        $leaveId = I('id');
        $leaveNote = D('Leave') -> getLeaveById($leaveId);

        $this -> leave = $leaveNote;
        $this -> display('Leave:leaveNote');
    }
}