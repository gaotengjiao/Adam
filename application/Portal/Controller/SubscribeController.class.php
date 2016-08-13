<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
/**
 * 预约项目
 */
 //header("Access-Control-Allow-Origin: *");
class SubscribeController extends HomebaseController {
  	/*
		预约时间
  	*/
	public function Subscribetime(){
		$DoctorId  = I("request.doctorid");
		$TimeModel = D("Time");
		$Daytime   = $TimeModel->DayTime();
		$Hourtime  = $TimeModel->Hourtime($DoctorId);
		$Usermess  = $TimeModel->Userinfo($DoctorId);
		$Date['day']  = $Daytime;
		$Date['time'] = $Hourtime;
		$Date['user'] = $Usermess;
		if(empty($TimeLists)){
			$error['error'] 	 = 404;
			$error['error_mess'] = "未能查询到数据";
			$result = json_encode($error);
		}
		$result	= json_encode($Date);
		print_r($result);die;
	}

	/*点击二次时间获取某一天的时间*/
	public function Subscribetimetwo(){
		$DoctorId   = I("request.doctorid");
		$dayId      = I("request.dayid");
		$TimeModel  = D("Time");
		$Hourtime   = $TimeModel->Hourtimetwo($DoctorId,$dayId);
			$Date['time'] = $Hourtime;
			if(empty($TimeLists)){
				$error['error']         = 404;
				$error['error_mess']    = "未能查询到数据";
				$result = json_encode($error);
			}
		$result = json_encode($Date);
		print_r($result);die;
	}

	/*
  	通过预约类型ID查询相关的信息
  	*/
  	public function Doctor(){
  		$UsersModel  = D("Users");
  		$TypeID      = I("request.typeid");
  		$Doctorlists = $UsersModel->SelDoctor($TypeID);
  		$result	     = json_encode($Doctorlists);
		print_r($result);die;
  	}

  	/*
	通过用户当前登陆的ID去查询这个用户的医生预约记录
  	*/
	public function Subrecord(){
	  	$UserID         = I("request.userid");
	  	$TeacherModel   = D("Teacher");
	  	$TeacherSubLists = $TeacherModel->SubDoctor($UserID);
	  	print_r($TeacherSubLists);die;
	}

	/*
	生成预约信息
	*/
	public function AddSubpost(){
		$DataPost       = I("request.");
		$TeacherModel   = D("Teacher");
		$SubResult      = $TeacherModel->GenerateReservationRecord($DataPost);
		$result	        = json_encode($SubResult);
		print_r($result);die;
	}

	/*
	预约历史记录
	*/
	public function Historyrecord(){
		$uid = I("request.uid");
        if(empty($uid)){
            $error['resultnum']     = 101;
            $error['result_mess']   = "请求错误";
            $error['result']        = "";
            $this->ajaxReturn($error);die;
        }
		$TeacherModel = D("Teacher");
        $result = $TeacherModel->HistoryRecord($uid);
        $this->ajaxReturn($result);die;
	}

    /*
     * 查询用户所有的预约记录
     * uid   用户ID
     * token 用户的唯一凭证
     * */
    public function HistoryToMakeAnAppointment(){
        $uid    = I("request.uid");
        $token  = I("request.token");
        if($uid=="" | $token==""){
            $error['resultnum']     = 118;
            $error['result_mess']   = "用户未登陆";
            $error['result']        = "";
            $this->ajaxReturn($error);die;
        }
        $TeacherModel   = D("Teacher");
        $UserModel      = D("Users");
        $UserResult = $UserModel->SelUserMess($uid,$token);
        if($UserResult==1){
            $error['resultnum']     = 119;
            $error['result_mess']   = "未查询到此用户,请重新登陆或注册";
            $error['result']        = "";
            $this->ajaxReturn($error);die;
        }else{
            $TeacherResult = $TeacherModel->HistoryToMakeAnAppointment($UserResult);
            $this->ajaxReturn($TeacherResult);die;
        }
    }

	/*
	预约项目入口
	*/
	public function SalesSupport(){
		$DataPost   = I("request.");
		$project    = $DataPost['projectid'];
        $uid        = $DataPost['uid'];
        $time       = $DataPost['time'];
        if($time=="" | $uid=="" | $project==""){
            $returnmess['resultnum']    = "101";
            $returnmess['result_mess']  = "提交错误";
            $returnmess['result']       = "";
            $this->ajaxReturn($returnmess);
        }else{
            $DataPost['projectid'] = htmlspecialchars_decode($project);
            $Project = "[".$DataPost['projectid']."]";
            $Project = json_decode($Project);
            if(empty($DataPost)){
                $returnmess['resultnum']    = "101";
                $returnmess['result_mess']  = "提交错误";
                $returnmess['result']       = "";
                $this->ajaxReturn($returnmess);
            }else{
                $ProjectModel = D("Items");
                $result = $ProjectModel->GenerateTheReservationRecord($DataPost,$Project);
                if($result["resultnum"]==0){
                    $ShoppingModel = D("Shopping");
                    $ResultShopping = $ShoppingModel->ModifyReservationForm($Project,$uid);
                    if($ResultShopping>1){
                        $this->ajaxReturn($result);exit;
                    }else{
                        $result['resultnum']    = "1";
                        $result['result_mess']  = "失败";
                        $result['result']       = "";
                        $this->ajaxReturn($result);exit;
                    }

                }else{
                    $returnmess['resultnum']    = "500";
                    $returnmess['result_mess']  = "网络错误";
                    $returnmess['result']       = "";
                    $this->ajaxReturn($returnmess);exit;
                }
            }
        }
	}

    /*
     * 立即预约二
     * 根据项目编号查询项目
     * */

    public function SelSubPro(){
        $num        = I("request.num");
        $ItemsModel = D("items");
        if(empty($num)){
            $error['resultnum']     = 101;
            $error['result_mess']   = "请求错误";
            $result = json_encode($error);
            print_r($result);die;
        }
        $result = $ItemsModel->SelSubNumGoods($num);
        $this->ajaxReturn($result);die;
    }
	/*
	服务流程
	*/
	public function Service(){
		$Projectid = I("request.");
		if(empty($Projectid)){
			$error['resultnum']     = 101;
			$error['result_mess']   = "请求错误";
			$result = json_encode($error);
			print_r($result);die;
		}
		$TeacherModel = D("Goods");
		$ServiceModel = D("Service");
		$GoodsServiceId = $TeacherModel->SelGoodsService($Projectid);
		$ServiceResult  = $ServiceModel->SelService($GoodsServiceId);
		if(empty($ServiceResult)){
				$error['resultnum']     = 404;
				$error['result_mess']   = "未能查询到数据";
				$result = json_encode($error);
		}
		$result['resultnum']    = "0";
		$result['result_mess']  = $ServiceResult;
		$result	= json_encode($result);
		print_r($result);die;
	}

	/*
		添加订单信息
	*/

	public function ReservationList(){
		$Projectid = I("request.projectid");
        if($Projectid==0){
            $result['resultnum']    = "101";
            $result['result_mess']  = "请求错误";
            $this->ajaxReturn($result);
        }
		if($Projectid==""){
			$error['resultnum']     = 101;
			$error['result_mess']   = "请求错误";
			$result = json_encode($error);
			print_r($result);die;
		}
		$GoodsModel = D("Goods");
		$return = $GoodsModel->SelGoodsReservation($Projectid);
		$result = json_encode($return);
		print_r($result);die;
	}

    /*
     * 个人预约
     * */

    public function PersonalAppointmentNum(){
        $Uid = I("request.uid");
        if(empty($Uid)){
            $error['resultnum']     = 101;
            $error['result_mess']   = "请求错误";
            $this->ajaxReturn($error);die;
        }
    }

    /*
     * 医生预约（修改预约时间）
     * */

    public function EditSubDoctor(){
        $num  = I("request.num");
        $day  = I("request.day");
        $time = I("request.time");
        $uid  = I("request.uid");
        if($num=="" | $day=="" | $time=="" | $uid==""){
            $returnmess['resultnum']    = "101";
            $returnmess['result_mess']  = "提交错误";
            $this->ajaxReturn($returnmess);die;
        }
        $TeacherModel = D("Teacher");
        $result = $TeacherModel->EditSubDoctorTime($num,$day,$time,$uid);
        if($result>0){
            $returnmess['resultnum']    = 0;
            $returnmess['result_mess']  = "修改成功";
            $this->ajaxReturn($returnmess);
        }else{
            $returnmess['resultnum']    = 404;
            $returnmess['result_mess']  = "修改失败";
            $this->ajaxReturn($returnmess);
        }
    }
    /*
     * 预约医生/项目（取消）
     * */
    public function CancelSub(){
        $num    = I("num");
        $uid    = I("uid");
        $token  = I("token");
        $type   = I("type");
        if($uid=="" | $token==""){
            $result['resultnum']    = "118";
            $result['result_mess']  = "用户未登录";
            $result['result']       = "";
            $this->ajaxReturn($result);
        }
        if($num==""){
            $result['resultnum']    = "101";
            $result['result_mess']  = "提交错误";
            $result['result']       = "";
            $this->ajaxReturn($result);
        }
        $UserModel      = D("Users");
        $UserResult = $UserModel->SelUserMess($uid,$token);
        if($UserResult==1){
            $error['resultnum']     = 119;
            $error['result_mess']   = "未查询到此用户,请重新登陆或注册";
            $error['result']        = "";
            $this->ajaxReturn($error);die;
        }else{
            $TeacherModel = D("Teacher");
            $ItemsModel   = D("Items");
            if($type=="doctorcancel"){
                $result = $TeacherModel->CancelSubDoctor($num,$uid,$type);
            }
            if($type=="itemscancel"){
                $result = $ItemsModel->CancelSubItems($num,$uid,$type);
            }
            if($type=="doctordelete"){
                $result = $TeacherModel->CancelSubDoctor($num,$uid,$type);
            }
            if($type=="itemsdelete"){
                $result = $ItemsModel->CancelSubItems($num,$uid,$type);
            }
            if($result>0){
                $returnmess['resultnum']    = 0;
                $returnmess['result_mess']  = "修改成功";
                $returnmess['result']  = "";
                $this->ajaxReturn($returnmess);
            }else{
                $returnmess['resultnum']    = 1;
                $returnmess['result_mess']  = "修改失败";
                $returnmess['result']  = "";
                $this->ajaxReturn($returnmess);
            }
        }
    }

    /*
     * 个人预约详情
     * */
    public function BookingDetails(){
        $num    = I("request.num");
        $Type   = I("request.type");
        $Token  = I("request.token");
        $Uid    = I("request.uid");
        if($Uid=="" | $Token==""){
            $result['resultnum']    = "118";
            $result['result_mess']  = "用户未登录";
            $result['result']       = "";
            $this->ajaxReturn($result);
        }
        if($num==""){
            $result['resultnum']    = "101";
            $result['result_mess']  = "请求错误";
            $result['result']       = "";
            $this->ajaxReturn($result);
        }
        if($Type==""){
            $result['resultnum']    = "113";
            $result['result_mess']  = "类型未输入";
            $result['result']       = "";
            $this->ajaxReturn($result);
        }else{
            $TeacherModel   = D("Teacher");
            $ItemsModel     = D("Items");
            if($Type==1){
                $result = $TeacherModel->TeacherBookingDetails($Uid,$Token,$num);
            }else{
                $result = $ItemsModel->ItemsBookingDetails($Uid,$Token,$num);
            }
            $this->ajaxReturn($result);
        }
    }
}