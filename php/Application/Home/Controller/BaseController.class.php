<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function _initialize(){
        checkLogin();
        session('user', cookie('user'));
        session('user.type', cookie('user.type'));
    }
}