<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 王鑫磊  2016-06-21  增加预约单Controller
 */
class CeController extends HomebaseController {
    public function Ce(){
        $TeacherTable  = M("teacher");
        $ItemsTable    = M("items");
        $ShoppingTable = M("shopping");
        $TeacherResult  = $TeacherTable->select();
        $ItemsResult    = $ItemsTable->select();
        $ShoppingResult = $ShoppingTable->select();
        $this->assign("teacher",$TeacherResult);
        $this->assign("items",$ItemsResult);
        $this->assign("shopping",$ShoppingResult);
        $this->display("index");

    }

    public function delete(){
        $id = I("post.");
        $id = $id['name'];
        print_r($id);die;
        $ShoppingTable = M("shopping");
        $ShoppingResult = $ShoppingTable->where("id in ($id)")->delete();
        echo $ShoppingTable->getLastSql();die;
        $id = exception($id,",");
        print_r($id);exit;
    }
}