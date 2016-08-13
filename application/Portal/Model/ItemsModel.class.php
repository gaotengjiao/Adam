<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ItemsModel extends CommonModel {
	/*
	生成订单
	*/
	public function GenerateTheReservationRecord($DataPost,$Project){
       // print_r($DataPost);die;
		$ItemsTable = M("items");
        $ShoppingTable = M("shopping");
        $ProjectCount = count($Project);
        for($i=0;$i<$ProjectCount;$i++){
            $str[$i]=$Project[$i]->aid;
        }
        $actiove = array_unique($str);
        unset($actiove[array_search("0",$actiove)]);//array_search("Cat",$a)按元素值返回键名。去除后保持索
        $actiove = implode(",",$actiove);
		$OrderReference = $this->GenerateReservationNumber();
        $Data['uid'] = $DataPost['uid'];
        $Data['eid'] = $actiove;
        $Data['name'] = json_encode($Project);
        $Data['time'] = $DataPost['time'];
        $Data['subtime'] = time();
        $Data['audit'] = 0;
        $Data['i_num'] = $OrderReference;
        $result = $ItemsTable->add($Data);
        if($result>0){
            $return['resultnum'] = 0;
            $return['num'] = $OrderReference;
            $return['status'] = 0;
            $uid = $ItemsTable->where("id = $result")->field("uid")->find();
            $uid = $uid['uid'];
            $userMember = M("member")->where("uid = $uid")->field("name,iphone,sex")->find();
            $status = $return['status'];
            // $resultMess = $this->SendMessage($userMember,$status);
            // $return['send'] = $resultMess;
        }else{
            $return['resultnum'] = 105;
            $return['resultmess'] = "预约信息未能生成";
        }
        return $return;
	}
	/*
	发送信息
	*/
	public function  SendMessage($userMember,$status){
		// $code = rand(100000,999999);
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$hour = date("H:i:s");
		$name = $userMember['name'];
		$phone = $userMember['iphone'];
		if($userMember['sex']==1){
			$sex="帅锅";
		}else{
			$sex="美眉";
		}
		if($status==0){
			$data ="亲爱的".$name.$sex."：恭喜您申请预约已成功，会有“思美丽妍”客服与您联系，请保持电话畅通。";
		}elseif($status==1){
			$data = " 亲爱的".$name.$sex."：您的预约呼声已经送达，请耐心等待专家天使回复；时刻关注手机短信或微信，抢鲜知道。".$year."年".$month."月".$day."日".$hour;
		}else{
			$data = "亲爱的".$name.$sex."：抱歉哈，该项目正在升级规划中，请随时关注我们微信公告新闻。";
		}
		//$_SESSION['code'] = $code;
		$post_data = array();
		$post_data['account'] = iconv('GB2312', 'GB2312',"yanqiuping_jy");
		$post_data['pswd'] = iconv('GB2312', 'GB2312',"Jy123456");
		$post_data['mobile'] ="$phone";
		$post_data['msg']=mb_convert_encoding("$data",'UTF-8', 'UTF-8');
		$post_data['needstatus']='true';
		$url='http://222.73.117.156/msg/HttpBatchSendSM?'; 
		$parse = parse_url($url);
		//var_dump($parse);
		for($i=0;$i<10;$i++)
		//echo "<br />";
		$o="";
		foreach ($post_data as $k=>$v)
		{
		   $o.= "$k=".urlencode($v)."&";
		}
		$post_data=substr($o,0,-1);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($ch) ;
		$pos = strpos($result,',');
		//return  $result;
		//用于截取判断状态码
		$co=substr($result,15,1);
		if($co == '0'){
			echo $co;
		}else{
			$res = substr($result,15,3);
		}
		return $res;
	}

    /*
     * 根据预约编号查询项目信息
     * $num = 项目编号
     * */
    public function SelSubNumGoods($num){
        $ItemsTable = M("items");
        $GoodsTable = M("goods");
        $result = $ItemsTable->where("i_num = '$num'")->field("name,audit")->find();
        if(empty($result)){
            $result['resultnum']    = "111";
            $result['result_mess']  = "未查询到订单信息";
            return $result;die;
        }
        $goods = json_decode($result['name']);
        $GoodsCount = count($goods);
        for($i=0;$i<$GoodsCount;$i++){
            $GoodsID[$i] = $goods[$i]->id;
            $goodsData['goods'][$i] = $GoodsTable->where("id =$GoodsID[$i]")->field("id,gname,humbimg,about,keyword")->find();
        }
        $goodsData['status'] = $result['audit'];
        return $goodsData;die;
    }

    /*
     * 查询预约项目的详细信息
     * $Uid         用户ID
     * $Token       用户唯一签名
     * $num         订单号
     * */
    public function ItemsBookingDetails($Uid,$Token,$num){
        $UserResult = $this->UserToken($Uid,$Token);
        if($UserResult['resultnum']!=0){
            $result['resultnum']    = "119";
            $result['result_mess']  = "用户未注册";
            $result['result']       = "";
            return $result;exit;
        }else{
            $ItemsTable     = M("items");
            $GoodsTable     = M("goods");
            $ResultItems    = $ItemsTable->where("uid = $Uid and i_num = '$num'")->field("id,name,uid,time,i_num,audit")->find();
            if(empty($ResultItems)){
                $result['resultnum']    = "101";
                $result['result_mess']  = "请求错误";
                $result['result']       = "";
                return $result;exit;
            } else {
                $Goods = $ResultItems['name'];
                $Goods = json_decode($Goods);
                $GoodsCount = count($Goods);
                for ($i = 0; $i < $GoodsCount; $i++) {
                    $id[$i] = $Goods[$i]->id;
                    $ResultGoods[$i] = $GoodsTable->where("id = $id[$i]")->field("id,aid,img,gname,about,keyword")->find();
                }
                $ResultData['num']      = $ResultItems['i_num'];
                $ResultData['status']   = $ResultItems['audit'];
                $ResultData['time']     = $ResultItems['time'];
                $ResultData['project']  = $ResultGoods;
                $NewResultArray = array();
                $NewResultArray = $ResultData;
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = $NewResultArray;
                return $result;
                exit;
            }
        }
    }
    /*
    * 删除项目
    * $num  订单编号
    * $uid  用户ID
    * $type 类型
    * */
    public function CancelSubItems($num,$uid,$type){
        $ItemsTable = M("items");
        $HaveItems = $ItemsTable->where("i_num = '$num' and uid = '$uid'")->find();
        if(empty($HaveItems)){
            $result['resultnum']    = "404";
            $result['result_mess']  = "未查询到订单";
            $result['result']       = "";
            return $result;exit;
        }else{
            if($type=="itemsdelete"){
                $audit['audit'] = "99";
            }else{
                $audit['audit'] = "5";
            }
            $CancelItems = $ItemsTable->where("i_num = '$num' and uid = '$uid'")->save($audit);
            return $CancelItems;exit;
        }
    }
}