<?php
namespace Common\Model;
use Common\Model\CommonModel;
/*
 * 王鑫磊  2016-06-21  增加预约单Model
 * */
class ShoppingModel extends CommonModel{
    /*
     * 添加预约单信息
     * $uid         用户ID
     * $token       用户唯一签名
     * $project     项目ID
     * */
    public function ShoppingAdd($Uid,$Token,$Project){
        $UserResult = $this->UserToken($Uid,$Token);
        if($UserResult['resultnum']!=0){
            $result['resultnum'] = "119";
            $result['result_mess'] = "用户未注册";
            $result['result'] = "";
            return $result;exit;
        }else{
            $ShoppingTable = M("shopping");
            $SelShopping   = $ShoppingTable->where("uid = $Uid and gid = $Project and state = 0")->find();
            if(!empty($SelShopping)){
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = "";
                return $result;exit;
            }
            $ShoppingData['uid']    = $Uid;
            $ShoppingData['gid']    = $Project;
            $ShoppingData['time']   = time();
            $ShoppingData['type']   = 1;
            $ShoppingData['state']  = 0;
            $resultAdd = $ShoppingTable->add($ShoppingData);
            if($resultAdd>0){
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = "";
                return $result;exit;
            }else{
                $result['resultnum'] = "1";
                $result['result_mess'] = "失败";
                $result['result'] = "";
                return $result;exit;
            }
        }
    }
    /*
     * 删除预约单信息
     * $uid         用户ID
     * $token       用户唯一签名
     * $project     项目ID
     * */
    public function ShoppingDelete($Uid,$Token,$Project){
        $UserResult = $this->UserToken($Uid,$Token);
        if($UserResult['resultnum']!=0){
            $result['resultnum'] = "119";
            $result['result_mess'] = "用户未注册";
            $result['result'] = "";
            return $result;exit;
        }else{
            $ShoppingTable = M("shopping");
            $ShoppingData['state']  = 2;
            $resultDel = $ShoppingTable->where("gid in ($Project) and uid = $Uid and state = 0")->save($ShoppingData);
            if($resultDel>0){
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = "";
                return $result;exit;
            }else{
                $result['resultnum'] = "1";
                $result['result_mess'] = "失败";
                $result['result'] = "";
                return $result;exit;
            }
        }
    }
    /*
   * 查询预约单信息
   * $uid         用户ID
   * $token       用户唯一签名
   * */
    public function ShoppingSelect($Uid,$Token){
        $UserResult = $this->UserToken($Uid,$Token);
        if($UserResult['resultnum']!=0){
            $result['resultnum']    = "119";
            $result['result_mess']  = "用户未注册";
            $result['result']       = "";
            return $result;exit;
        }else{
            $ShoppingTable = M("shopping");
            $GoodsTable    = M("goods");
            $GoodsType     = M("goods_type");
            $resultSel = $ShoppingTable->where("uid = $Uid and state = 0")->field("id,gid,uid,state")->select();
            if(empty($resultSel)){
                $result['resultnum']    = "404";
                $result['result_mess']  = "未查询到数据";
                $result['result']       = "";
                return $result;exit;
            }
            $GoodsCount = count($resultSel);
            for($g=0;$g<$GoodsCount;$g++){
                $Gid = $resultSel[$g]['gid'];
                $Goods[$g] = $GoodsTable->where("id = $Gid")->field("img,gname,id,about,keyword,aid,gtype")->find();
                $tid = $Goods[$g]['gtype'];
                $typename = $GoodsType->where("id = $tid")->field("id,typename")->find();
                $Goods[$g]['typename'] = $typename['typename'];
            }
            if(!empty($Goods)){
                $result['resultnum']    = "0";
                $result['result_mess']  = "成功";
                $result['result']       = $Goods;
                return $result;exit;
            }else{
                $result['resultnum']    = "1";
                $result['result_mess']  = "失败";
                $result['result']       = "";
                return $result;exit;
            }
        }
    }

    /*
     * 生成订单之后修改预约单内项目的状态
     * */
    public function ModifyReservationForm($Project,$uid){
       $GoodsCount = count($Project);
        $str = "";
        for($i=0;$i<$GoodsCount;$i++){
            $Goods[$i] = $Project[$i]->id;
            $str.= $Goods[$i].",";
        }
        $str = rtrim($str,",");
        $ShoppingTable = M("shopping");
        $status["state"] = 1;
        $Result = $ShoppingTable->where("uid = $uid and gid in ($str) and state = 0")->save($status);
        return $Result;exit;
    }
}
