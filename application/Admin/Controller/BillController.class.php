<?php
/* * 
 * 账单管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class BillController extends AdminbaseController {
    /*
     * 账单 add添加 edit查看
     */
    public function index(){
        $billinfo = D("Records");//实例化Model
        $billinfoo = $billinfo->bill();//调用Model中方法
        $this->assign('lists',$billinfoo['1']);//赋值数据集
        $this->assign('page',$billinfoo['2']);//赋值分页输出
        $this->display('index');
    }
    /*  查看   */
    public function check(){
        $id = I("get.");
        $billinfo = D("Records");//实例化Model
        $billinfoo = $billinfo->check($id);//调用Model中方法
//        print_r($billinfoo);die;
        $this->assign('lists',$billinfoo);//赋值数据集
        $this->display('edit'); 
    }
}
?>