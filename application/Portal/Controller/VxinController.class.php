<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 王鑫磊  2016-06-21  增加预约单Controller
 */
header("Content-type: text/html; charset=utf-8");
class VxinController extends HomebaseController {

    /*
     * 微信登陆获取微信用户的个人基本信息
     * */
    public function OpenIdVxin(){
        $type = I("request.type");
        $TripartiteModel = D("Tripartite");
        $url = "http://www.buruwo.com/web/project/denglu.html";
        if($type=="vx"){
            $state = I("request.state");
            $result = $TripartiteModel->GetCode($state);
            $this->ajaxReturn($result);
        }elseif($type=="vxlog"){
            $result = $TripartiteModel->GetOpenId();
            header("Location:".$url);
        }else{
            echo "出错啦";
        }
    }

    public function UserInfo(){
        $time = I("request.state");
        $TripartiteModel = D("Tripartite");
        $result = $TripartiteModel->GetTimeUserInfo($time);
        $this->ajaxReturn($result);
    }
    /*
     * 生成随机字符串
     * */
    public function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }
    /*
     * 获取sha1加密串
     * */
    public function ShaGet(){
        if(IS_GET){
            $str = I("request.GetShaStr");
            if(empty($str)){
                $resultmess['resultnum']    = "101";
                $resultmess['result_mess']  = "提交失败";
                $resultmess['result']       = "";
                $this->ajaxReturn($resultmess);
            }else{
                $str = sha1($str);
                $resultmess['resultnum']    = "0";
                $resultmess['result_mess']  = "成功";
                $resultmess['result']       = $str;
                $this->ajaxReturn($resultmess);
            }
        }else{
            $resultmess['resultnum']    = "131";
            $resultmess['result_mess']  = "提交失败";
            $resultmess['result']       = "";
            $this->ajaxReturn($resultmess);
        }
    }

    /*
    * 获取jsapi_ticket执行入库操作
    * */
    public function Getjsapiticket(){
        $VxJsapiTable = M("vx_jsapi");
        $OKResult = $VxJsapiTable->where("status = 1")->find();
        if($OKResult==""){
            $url = "http://www.buruwo.com/wx/sample.php";
            $data = file_get_contents($url);
            $AddData['jsapi_ticket']    = $data['jsapi_ticket'];
            $AddData['noncestr']         = $data['nonceStr'];
            $AddData['tamp']             = $data['timestamp'];
            $AddData['url']              = $data['url'];
            $AddData['signature']       = $data['signature'];
            $AddData['rawString']       = $data['rawString'];
            if($VxJsapiTable->add($AddData)){
                $return = SONEOK($AddData);
                $this->ajaxReturn($return);exit;
            }else{
                $return = SONEERROR();
                $this->ajaxReturn($return);exit;
            }
        }else{
            $return = SONEOK( $OKResult);
            $this->ajaxReturn($return);exit;
        }
    }
    //根据serverid从微信服务器获得图片并存储
    public function upload(){
        $userInfo = $_POST['userinfo'];
        $data = json_decode($userInfo,true);
        $uid = $data['uid'];
        $ServiceId = $data['serverId'];
        $content = $this->get_media($ServiceId);
        $dir = "data/userimg/$uid/";
        if(is_dir($dir)){
        }else{
            mkdir($dir);
        }
        $ContentCount = count($content);
        for($i=0;$i<$ContentCount;$i++){
            $imgnames = $this->getRandChar(7).'.jpg';
            file_put_contents(realpath("$dir") . '/'.$imgnames, $content[$i]);
            $imgname[] = $imgnames;
        }
        $UserModel = D("UserData");
        $result = $UserModel->ApplicationMaterials($data,$imgname);
        $this->ajaxReturn($result);
    }
    //从微信服务器上获取媒体文件
    public function get_media($ServiceId){
        $Access_Token = S("accesstoken");
        if($Access_Token==""){
            $access_token = $this->GetToken();
            S('accesstoken',$access_token,3600);
        }else{
            $access_token = $Access_Token;
        }
        $MediaCount = count($ServiceId);
        for($i=0;$i<$MediaCount;$i++){
            $media_id = $ServiceId[$i];
            $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$media_id;
            $content = file_get_contents($url);
            $arr[] = $content;

        }
        return $arr;
    }

    /*
  * 获取token
  * */
    public function GetToken(){
        $appid  = "wxaf6078a557ceeeec";
        $secret = "4290895a40612599370221ab71995c91";
        $time = time();
        $length = 8;
        $rand = $this->getRandChar($length);
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        $data = file_get_contents($url);
        $date = json_decode($data);
        $result['token'] = $date->access_token;
        $resultmess['resultnum']    = "0";
        $resultmess['result_mess']  = "成功";
        $resultmess['result']       = $result;
        return $result['token'];die;
        $this->ajaxReturn($resultmess);
    }
}