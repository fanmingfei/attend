<?php
namespace Home\Controller;
use Think\Controller;
class StudentController extends BackController {
    public function importStudent()
    {
        import("Vendor.Excel.PHPExcel");
        import("Vendor.Excel.PHPExcel.IOFactory");


        $name = time();
        $path = $_SERVER[DOCUMENT_ROOT].'/Uploads/';


        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('xls');// 设置附件上传类型
        $upload->rootPath  =     $path; // 设置附件上传根目录
        $upload->savePath  =     'Excel/'; // 设置附件上传（子）目录
        $upload->autoSub   =     false;
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
            exit();
        }
        $file = $_SERVER[DOCUMENT_ROOT].'/Uploads/Excel/'.$info['file_stu']['savename'];
        $objReader = new \PHPExcel_Reader_Excel5();
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);


        $objWorksheet = $objPHPExcel->getActiveSheet();

        $row = $objWorksheet->getHighestRow();
        // $col = $objWorksheet->getHighestColumn(); 
        $keyname = array('grade', 'major', 'studentid', 'username', 'phone', 'sex', 'idcard', 'political', 'address', 'bankid', 'father', 'fatherphone', 'mother', 'motherphone', 'dorm', 'room');
        $people = array();
        for ($r = 3; $r <= $row; $r++) {
            $c = 1;
            $people[$r-3] = array();
            foreach ($keyname as $key => $value) {
                $people[$r-3][$value] = $objWorksheet->getCellByColumnAndRow($c, $r)->getValue();
                $c ++;
            }
        }
        D('Student')->saveList($people);
        $this->success('添加成功！');
    }
    public function postStudent () {
        $user = I('post.');
        if ($user['id']) {
            $this->saveStudent($user);
            return false;
        }
        $has = D('Student')->where(array('username'=>$user['username'], 'classid'=>$user['classid']))->find();
        if ($has) {
            $this->error('该学生已存在');
        }
        if (!$user['username']) {
            $this->error('请填写姓名');
        }
        if (!$user['classid']) {
            $this->error('请选择班级');
        }
        $re = D('Student')->data($user)->add();
        if ($re) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }
    public function saveStudent ($user) {
        $re = D('Student')->where(array('id'=>$user['id']))->data($user)->save();
        if ($re) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

}