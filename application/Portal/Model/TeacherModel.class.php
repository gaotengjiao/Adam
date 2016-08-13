<?php
namespace Common\Model;
use Common\Model\CommonModel;
class TeacherModel extends CommonModel {
	/*
	查询当前用户的医生预约记录
	*/
	public function SubDoctor($UserID){
		if(empty($UserID)){
	  		$error['error'] = 101;
			$error['error_mess'] = "请求错误";
			return $error;die;
	  	}
	  	$TeacherLists = M("teacher")
	  	->where("uid = $UserID")
	  	->field("tid,name,uid,subtime,day,time,audit,t_num")
	  	->select();
	  	$TeacherCount = count($TeacherLists);
	  	for($i=0;$i<=$TeacherCount;$i++){
	  		
	  	}
	  	print_r($TeacherCount);die;
	}
	/*/*
	生成预约记录
	*/
	public function GenerateReservationRecord($SubDoctor){
        if($SubDoctor['uid']!="" | $SubDoctor['tid']!="" | $SubDoctor['day']!="" | $SubDoctor['time']!=""){
            $uid = $SubDoctor['uid'];
            $tid = $SubDoctor['tid'];
            $time = $SubDoctor['time']+1;
            $day = $SubDoctor['day'];
            $day = substr($day,6);
            $TeacherTable = M("teacher");
            $IsDaySub = $TeacherTable->where("tid = $tid and day='$day' and uid='$uid'")->find();
            if(!empty($IsDaySub)){
                $result['resultnum'] = "117";
                $result['result_mess'] = "您已经预约过此医生";
                return $result;die;
            }
            $resultSub = $TeacherTable->where("tid = $tid and day='$day' and time = $time")->find();
            if($resultSub==""){
                $T_num = $this->GenerateReservationNumber();
                $TeacherDate['uid'] = $uid;
                $TeacherDate['tid'] = $tid;
                $TeacherDate['day'] = $day;
                $TeacherDate['time'] = $SubDoctor['time']+1;
                $TeacherDate['subtime'] = time();
                $TeacherDate['audit'] = 0;
                $TeacherDate['t_num'] = $T_num;
                $AddTeacherIndent = $TeacherTable->add($TeacherDate);
                if($AddTeacherIndent>0){
                    $result['resultnum'] = "0";
                    $result['t_num'] = $TeacherDate['t_num'];
                    $result['result_mess'] = "订单生成";
                }
            }else{
                $result['resultnum'] = "106";
                $result['result_mess'] = "当前时间段已经被预约";
            }
        } else {
            $result['resultnum'] = "101";
            $result['result_mess'] = "请求错误";
        }
		return $result;die;
	}
/*
	查询用户历史预约医生数据
*/
	public function HistoryRecord($uid){
        $TeacherTable   = M("teacher");
        $ItemsTable     = M("items");
        $TeacherList    = $TeacherTable->where("uid = $uid")->order('subtime desc')->field("id,time,day,t_num,audit")->select();
        $ItemsList      = $ItemsTable->where("uid = $uid")->order('subtime desc')->field("id,time,i_num,audit")->select();
        $TeacherCount = count($TeacherList);
        $ItemsCount   = count($ItemsList);
        $SubResult = array();
        for($i=0;$i<$TeacherCount;$i++){
            $SubResultTeacher[$i]['day']       = date("Y-").$TeacherList[$i]['day'];
            $SubResultTeacher[$i]['num']       = $TeacherList[$i]['t_num'];
            $SubResultTeacher[$i]['id']        = $TeacherList[$i]['id'];
            $SubResultTeacher[$i]['status']    = $TeacherList[$i]['audit'];
            $SubResultTeacher[$i]['subtype']   = 1;
        }
        $SubResultCount = count($SubResult);
        for($j=$SubResultCount;$j<$ItemsCount+$SubResultCount;$j++){
            $SubResultItems[$j]['day']         = $ItemsList[$j]['time'];
            $SubResultItems[$j]['num']         = $ItemsList[$j]['i_num'];
            $SubResultItems[$j]['id']          = $ItemsList[$j]['id'];
            $SubResultItems[$j]['status']      = $ItemsList[$j]['audit'];
            $SubResultItems[$j]['subtype']     = 2;
        }
        if(!empty($SubResultTeacher) && !empty($SubResultItems)){
            $SubResult = array_merge($SubResultTeacher,$SubResultItems);
            foreach ($SubResult as $key=>$value)
            {
                if ($value['status'] == 99)
                    unset($SubResult[$key]);
            }
            array_multisort($SubResult);
            $lists = array_slice($SubResult, -3, 3);
        }elseif(!empty($SubResultTeacher)){
                foreach ($SubResultTeacher as $key=>$value)
                {
                    if ($value['status'] == 99)
                        unset($SubResultTeacher[$key]);
                }
                array_multisort($SubResultTeacher);
                $lists = array_slice($SubResultTeacher, -3, 3);

            }
            else{
                foreach ($SubResultItems as $key=>$value)
                {
                    if ($value['status'] == 99)
                        unset($SubResultItems[$key]);
                }
                array_multisort($SubResultItems);
                $lists = array_slice($SubResultItems, -3, 3);
            }
        if(empty($lists)){
            $result['resultnum'] = "404";
            $result['result_mess'] = "当前用户还未预约";
            $result['result'] = "";
            return $result;die;
        }else{
            $result['resultnum'] = "0";
            $result['result_mess'] = "成功";
            $result['result'] = $lists;
            return $result;die;
        }
    }
    /*
    * 历史预约
    * */
    public function HistoryToMakeAnAppointment($UserResult){
        $TeacherTable   = M("teacher");
        $ItemsTable     = M("items");
        $DoctorTable    = M("users");
        $GoodsTable     = M("goods");
        $uid = $UserResult['uid'];
        $TeacherList    = $TeacherTable->where("uid = $uid")->order('subtime desc')->field("id,tid,time,day,t_num,audit")->select();
        $ItemsList      = $ItemsTable->where("uid = $uid")->order('subtime desc')->field("id,name,time,i_num,audit")->select();
        $TeacherCount = count($TeacherList);
        $ItemsCount   = count($ItemsList);
        $SubResult = array();
        for($i=0;$i<$TeacherCount;$i++){
            $Tid                               = $TeacherList[$i]['tid'];
            $strtime                           = date("Y-").$TeacherList[$i]['day'];
            $day                               = $this->getWeek($strtime);
            $SubResultTeacher[$i]['day']       = $day.$TeacherList[$i]['day'];
            $SubResultTeacher[$i]['num']       = $TeacherList[$i]['t_num'];
            $SubResultTeacher[$i]['id']        = $TeacherList[$i]['id'];
            $SubResultTeacher[$i]['status']    = $TeacherList[$i]['audit'];
            $content = $DoctorTable->where("id = $Tid")->field("signature")->find();
            $SubResultTeacher[$i]['content']   = $content['signature'];
            $SubResultTeacher[$i]['subtype']   = 1;
            if ($SubResultTeacher[$i]['status'] == 99){
                unset($SubResultTeacher[$i]);
            }
        }
        $SubResultCount = count($SubResult);
        for($j=$SubResultCount;$j<$ItemsCount+$SubResultCount;$j++){
            $strtime                           = date("Y-").$ItemsList[$j]['time'];
            $day                               = $this->getWeek($strtime);
            $ttime = $ItemsList[$j]['time'];
            $time =  substr($ttime,5);
            $SubResultItems[$j]['day']         = $day.$time;
            $SubResultItems[$j]['num']         = $ItemsList[$j]['i_num'];
            $SubResultItems[$j]['id']          = $ItemsList[$j]['id'];
            $Goods = json_decode($ItemsList[$j]['name']);
            $Goodsid = $Goods[0]->id;
            $GoodsContent = $GoodsTable->where("id = $Goodsid")->field("about")->find();
            $SubResultItems[$j]['status']      = $ItemsList[$j]['audit'];
            $SubResultItems[$j]['content']     = $GoodsContent['about'];
            $SubResultItems[$j]['subtype']     = 2;
            if ($SubResultItems[$j]['status'] == 99){
                unset($SubResultItems[$j]);
            }
        }
        if(!empty($SubResultTeacher) && !empty($SubResultItems)){
            $SubResult = array_merge($SubResultTeacher,$SubResultItems);
            rsort($SubResult);
        }elseif(!empty($SubResultTeacher)){
            rsort($SubResultTeacher);
        }else{
            rsort($SubResultItems);
        }
        if(!empty($SubResult)){
            $lists = $SubResult;
        }elseif(!empty($SubResultTeacher)){
            $lists = $SubResultTeacher;
        }else{
            $lists = $SubResultItems;
        }
        if(empty($lists)){
            $result['resultnum'] = "404";
            $result['result_mess'] = "当前用户还未预约";
            $result['result'] = "";
            return $result;die;
        }else{
            $result['resultnum'] = "0";
            $result['result_mess'] = "成功";
            $result['result'] = $lists;
            return $result;die;
        }
    }

    /*
     * $uid/$day/$time/$num
     * 根据四个参数对用户订单信息进行修改
     * */
    public function EditSubDoctorTime($num,$day,$time,$uid){
        $TeacherTable = M("teacher");
        $data['day'] = $day;
        $data['time'] = $time+1;
        $result = $TeacherTable->where("uid = '$uid' and t_num = '$num'")->save($data); // 根据条件更新记录
        return $result;die;
    }
    /*
     * 删除/取消医生预约
     * $num  订单编号
     * $uid  用户ID
     * $type 类型
     * */
    public function CancelSubDoctor($num,$uid,$type){
        $TeacherTable = M("teacher");
        $HaveTeacher = $TeacherTable->where("t_num = '$num' and uid = '$uid'")->find();
        if(empty($HaveTeacher)){
            $result['resultnum']    = "404";
            $result['result_mess']  = "未查询到订单";
            $result['result']       = "";
            return $result;exit;
        }else{
            if($type=="doctordelete"){
                $audit['audit'] = "99";
            }else{
                $audit['audit'] = "5";
            }
            $DeleteTeacher = $TeacherTable->where("t_num = '$num' and uid = '$uid'")->save($audit);
            return $DeleteTeacher;exit;
        }
    }

    /*
     * 查询预约医生的详细信息
     * $Uid         用户ID
     * $Token       用户唯一签名
     * $num         订单号
     * */
    public function TeacherBookingDetails($Uid,$Token,$num){
        $UserResult = $this->UserToken($Uid,$Token);
        if($UserResult['resultnum']!=0){
            $result['resultnum']    = "119";
            $result['result_mess']  = "用户未注册";
            $result['result']       = "";
            return $result;exit;
        }else{
            $TeacherTable   = M("teacher");
            $UsersTable     = M("users");
            $TimeTable      = M("time");
            $ResultTeacher  = $TeacherTable->where("uid = '$Uid' and t_num = '$num'")->field("id,tid,time,t_num,day,audit")->find();
            if(empty($ResultTeacher)){
                $result['resultnum']    = "101";
                $result['result_mess']  = "请求错误";
                $result['result']       = "";
                return $result;exit;
            }else{
                $Tid            = $ResultTeacher['tid'];
                $ResultUsers    = $UsersTable->where("id = $Tid")->field("id,user_login,user_url,user_nicename")->find();
                $Time           = $ResultTeacher['time'];
                $ResultTime     = $TimeTable->where("id = $Time")->field("t_time")->find();
                $ResultData['num']      = $ResultTeacher['t_num'];
                $ResultData['day']      = date("Y-").$ResultTeacher['day'];
                $ResultData['status']   = $ResultTeacher['audit'];
                $ResultData['time']     = $ResultTime['t_time'];
                $ResultData['img']      = $ResultUsers['user_url'];
                $ResultData['username'] = $ResultUsers['user_login'];
                $ResultData['jobtitle'] = $ResultUsers['user_nicename'];
                $ResultData['oid']      = $ResultTeacher['id'];
                $NewResultArray         = array();
                $NewResultArray[0]      = $ResultData;
                $result['resultnum']    = "0";
                $result['result_mess']  = "成功";
                $result['result']       = $NewResultArray;
                return $result;exit;
            }
        }
    }
}