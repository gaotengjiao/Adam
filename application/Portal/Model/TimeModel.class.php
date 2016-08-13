<?php
namespace Common\Model;
use Common\Model\CommonModel;
class TimeModel extends CommonModel {
	/*
		获取当天开始往后七天的时间
	*/
	public function DayTime(){
		$day1 = $this->getWeek(strtotime('+1 days'));
		$day2 = $this->getWeek(strtotime('+2 days'));
		$day3 = $this->getWeek(strtotime('+3 days'));
		$day4 = $this->getWeek(strtotime('+4 days'));
		$day5 = $this->getWeek(strtotime('+5 days'));
		$day6 = $this->getWeek(strtotime('+6 days'));
		$day7 = $this->getWeek(strtotime('+7 days'));
		$day['day1'] = $day1."</br>".date("m-d",strtotime("+1 day"));
		$day['day2'] = $day2."</br>".date("m-d",strtotime("+2 day"));
		$day['day3'] = $day3."</br>".date("m-d",strtotime("+3 day"));
		$day['day4'] = $day4."</br>".date("m-d",strtotime("+4 day"));
		$day['day5'] = $day5."</br>".date("m-d",strtotime("+5 day"));
		$day['day6'] = $day6."</br>".date("m-d",strtotime("+6 day"));
		$day['day7'] = $day7."</br>".date("m-d",strtotime("+7 day"));
		if(empty($day)){
			$error['error'] = 404;
			$error['error_mess'] = "网络错误或链接超时";
			$result = json_encode($error);
		}
		return $day;
	}

	
	/*
		获取工作时间
	*/
	public function Hourtime($DoctorId){
		$TimeTable = M("time");
		$TeacherTable = M("teacher");
		$UsersTable = M("users");
		$TimeLists = $TimeTable->select();
		$day = date("m-d",strtotime('+1 day'));
		$TimeLists = $TimeTable->field("id,t_time")->select();
		$TeacherLists = $TeacherTable->where("tid = $DoctorId and day = '$day'")->select();

		$Teacherlen = count($TeacherLists);
		$TimeLen = count($TimeLists);
		foreach($TeacherLists as $k=>$v){
			foreach($TimeLists as $ks=>$vs){
				if($v['time']==$vs['id']){
					$TimeLists[$ks]['trueor'] =1;
				}
			}
		}
		return $TimeLists;die;
	}

	public function Hourtimetwo($DoctorId,$dayId){
		$TimeTable = M("time");
		$TeacherTable = M("teacher");
		$TimeLists = $TimeTable->select();
		$day = date("m-d",strtotime('+'.$dayId.' day'));
		$TimeLists = $TimeTable->field("id,t_time")->select();
		$TeacherLists = $TeacherTable->where("tid = $DoctorId and day = '$day'")->select();
		$Teacherlen = count($TeacherLists);
		$TimeLen = count($TimeLists);
		foreach($TeacherLists as $k=>$v){
			foreach($TimeLists as $ks=>$vs){
				if($v['time']==$vs['id']){
					$TimeLists[$ks]['trueor'] =1;
				}
			}
		}
		return $TimeLists;die;
	}
	//php判断某一天是星期几的方法
	function getWeek($unixTime=''){
		$unixTime=is_numeric($unixTime)?$unixTime:time();
		$weekarray=array('日','一','二','三','四','五','六');
		return '周'.$weekarray[date('w',$unixTime)];
	}

	public function Userinfo($DoctorId){
		$UserTable = M("users");
		$Userinfo = $UserTable->where("id = $DoctorId")->field("id,user_login,user_nicename,user_url")->find();
		return $Userinfo;
	}

}