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
        $keyname = array('grade', 'major', 'studentid', 'username', 'phone', 'sex', 'idcard', 'political', 'address', 'bankid', 'father', 'fatherphone', 'mother', 'motherfphone', 'dorm', 'room');
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
}