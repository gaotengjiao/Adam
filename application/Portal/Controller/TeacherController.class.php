<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 预约医生
 */
//header("Access-Control-Allow-Origin: *");
class TeacherController extends HomebaseController {
    /*
     * 预约医生
     * */
    public function SubDoctor(){
        $SubDoctor = I("request.");
        $TeacherModel = D("Teacher");
        $result = $TeacherModel->GenerateReservationRecord($SubDoctor);
        $this->ajaxReturn($result);
    }
}