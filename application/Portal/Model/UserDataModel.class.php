<?php
namespace Common\Model;
use Common\Model\CommonModel;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/25
 * Time: 13:12
 */
class UserDataModel extends CommonModel
{
    /*
     * 消费分期查询当前用户的全部账单
     * */
    public function SelUserData ($Uid, $Token)
    {
        $UserResult = $this->UserToken ($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            return USERNOTREGISTERED();
            exit;
        } else {
            $UserDataTable = M ("user_data");
            $UserBillResult = $UserDataTable->where ("uid = $Uid")->field ("did,uid,lid,dtype,data,uptime")->select ();
            if (empty($UserBillResult)) {
                return SONEERROR();
                exit;
            } else {
                return SONEOK($UserBillResult);
                exit;
            }
        }
    }

    /*
     * 将用户申请额度的审核材料入库
     * */
    public function ApplicationMaterials($data,$imgname){
        $imgname['name'] = $data["name"];
        $imgname['num']  = $data['num'];
        $UserDataTable = M ("user_data");
        $UserData['uid'] = $data['uid'];
        $UserData['dtype'] = 2;
        $UserData['data'] = json_encode($imgname);
        $UserData['uptime'] = date("Y-m-d H:i:s");
        if($UserDataTable->add($UserData)){
            return SONEOK();
            exit;
        }else{
            return SONEERROR();
            exit;
        }
    }
}

