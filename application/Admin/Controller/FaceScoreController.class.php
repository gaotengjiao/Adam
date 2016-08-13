<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class FaceScoreController extends AdminbaseController
{
    /*
    * 订单管理
    * 查询所有用户订单信息
    * */
    public function OrderManagement(){
        $this->display();
    }
    /*
    * 账单管理页面
    * 查询所有用户账单
    * */
    public function BillManagement(){
        $this->display();
    }
    /*
    * 分期管理页面
    * 查询所有用户分期信息
    * */
    public function StageManagement(){
        $this->display();
    }
    /*
    * 利率管理页面
    * 查询所有利率
    * */
    public function InterestRateManagement(){
        $this->display();
    }
    /*
    * 付款管理页面
    * 查询所有付款方式以及用户的付款信息
    * */
    public function PaymentManagement(){
        $this->display();
    }
    /*
    * 逾期管理页面
    * 查询逾期用户的账单信息
    * */
    public function OverdueManagement(){
        $this->display();
    }
}
