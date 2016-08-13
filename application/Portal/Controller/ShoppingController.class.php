<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 王鑫磊  2016-06-21  增加预约单Controller
 */
class ShoppingController extends HomebaseController {
    /*
     * 此方法实现项目预约单的增、删、查
     * uid         用户ID
     * token       用户唯一签名
     * project     项目ID
     * type        类型
     * */
    public function ReservationList(){
        $Data       = I("request.");
        $Type       = $Data['type'];
        $Uid        = $Data['uid'];
        $Token      = $Data['token'];
        $Project    = $Data['project'];
        if($Uid=="" | $Token==""){
            $result['resultnum'] = "118";
            $result['result_mess'] = "用户未登录";
            $result['result'] = "";
            $this->ajaxReturn($result);
        }

        if($Type!="select"){
            if($Project==""){
                $result['resultnum'] = "101";
                $result['result_mess'] = "请求错误";
                $result['result'] = "";
                $this->ajaxReturn($result);
            }
        }
        if($Type==""){
            $result['resultnum'] = "113";
            $result['result_mess'] = "类型未输入";
            $result['result'] = "";
            $this->ajaxReturn($result);
        }else{
            $ShoppingModel = D("Shopping");
            if($Type=="add"){
                if($Project<1){
                    $result['resultnum'] = "101";
                    $result['result_mess'] = "请求错误";
                    $result['result'] = "";
                    $this->ajaxReturn($result);
                }else{
                    $result = $ShoppingModel->ShoppingAdd($Uid,$Token,$Project);
                }
            }elseif($Type=="delete"){
                $result = $ShoppingModel->ShoppingDelete($Uid,$Token,$Project);
            }else{
                $result = $ShoppingModel->ShoppingSelect($Uid,$Token);
            }
            $this->ajaxReturn($result);
        }

    }
}