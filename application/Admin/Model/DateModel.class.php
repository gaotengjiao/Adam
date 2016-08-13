<?php
namespace Common\Model;
use Common\Model\CommonModel;
class DateModel extends CommonModel {
    /*
        添加第七天时间
     *      */
    public function TimeAdd($day7){
        $DateTable = M("date");
        $DoctorTime = $DateTable->where("tid=2")->select();
        $DoctorTimeCount = count($DoctorTime);
        $day = $this->DayTime();
        //print_r($day);die;
        if($DoctorTimeCount==0){
            for($i=1;$i<=7;$i++){
                $Add[$i]['tid'] = 2;
                $Add[$i]['date'] = $day[$i]['date'];
                $Add[$i]['state'] = 1;
                $Return = $DateTable->add($Add[$i]);
            }
        }elseif($DoctorTimeCount>=7){
                $IS_DAY7 = $DateTable->where("tid=2")->order("id desc")->field("date")->find();
                if($IS_DAY7['date'] == $day7){
                   $date = $DateTable->where("tid=2")->order("id desc")->limit(0,7)->field("id,date,false")->select();
                   $dateorder = asort($date,$date['date']);
                   return $date;die;
                }else{
                    $Add['tid'] = 2;
                    $Add['date'] = $day7;
                    $Add['state'] = 1;
                    $Return = $DateTable->add($Add);
                    $date = $DateTable->where("tid=2")->order("id desc")->limit(0,7)->field("id,date,false")->select();
                    $dateorder = asort($date,$date['date']);
                    return $date;die;
                }
        }
    }
     
    
    /*
        获取七天的时间
     *      */
    public function DayTime(){
           for($i=1;$i<8;$i++){
                $day[$i]['date'] = date('Y-m-d', strtotime('+'.$i.' day'));
           }
           return $day;
    }
}