<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UserUplimitModel extends CommonModel
{
    /*
     * 提额授权
     * $id  
     * */
    public function Authorization($id,$uid){
        $UserUplimitTable = M("user_uplimit");
        $UserUplimit = $UserUplimitTable->where("id = $id")->field("upLimitSize,uid")->find();
        $status['status'] = 1;
        $status['approver'] = $_SESSION['ADMIN_ID'];
        $status['isuplimit'] = 1;
        $Result = $UserUplimitTable->where("id = $id")->save($status);
        if($Result==1){
            $UserLimitTable = M("user_limit");
            $UserLimit = $UserLimitTable->where("uid = $uid")->field("limit")->find();
            $Money['limit'] = $UserLimit['limit'] + $UserUplimit['uplimitsize'];
            $Money['approver'] = $_SESSION['ADMIN_ID'];
            $Result = $UserLimitTable->where("uid = $uid")->save($Money);
            if($Result==1){
                return 1;exit;
            }else{
                return 0;exit;
            }
        }else{
            return 0;exit;
        }
    }

    /*
     * 拒绝授权
     * */
    public function RefuseAuthorization($id,$refuse,$uid){
        $UserLimitTable = M("user_uplimit");
        $data['status'] = 3;
        $Result = $UserLimitTable->where("id = $id")->save($data);
        if($Result==1){
            $ReasonsTable = M("reasons");
            $Data['uid'] = $uid;
            $Data['lid'] = $id;
            $Data['reasone'] = $refuse;
            $Data['reasonetime'] = time();
            $Data['isuplimit'] = 1;
            $Result = $ReasonsTable->add($Data);
            if($Result>0){
                return 1;exit;
            }else{
                return 0;exit;
            }
        }else{
            return 0;exit;
        }
    }
}