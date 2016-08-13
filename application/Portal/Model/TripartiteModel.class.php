<?php
namespace Common\Model;
use Common\Model\CommonModel;
class TripartiteModel extends CommonModel {
    /*
     * 获取code
     * */
    public function GetCode($state){
        $time = $state;
        $UserTable = M("user");
        $appid = "wxaf6078a557ceeeec";
        $weburl = "http%3a%2f%2fwww.buruwo.com%2findex.php%3fg%3dPortal%26m%3dVxin%26a%3dOpenIdVxin%26type%3dvxlog";
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$weburl.'&response_type=code&scope=snsapi_userinfo&state='.$time.'#wechat_redirect';
        header("Location:".$url);
    }

    /*
     * 获取openid
     * */
    public function GetOpenId(){
        $TripartiteTable    = M("tripartite");
        $MemberTable        = M("member");
        $UserTable          = M("user");
        $appid  = "wxaf6078a557ceeeec";
        $secret = "4290895a40612599370221ab71995c91";
        $code   = I("request.code");
        $time   = I("request.state");
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $ch     = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_token_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res    = curl_exec($ch);
        curl_close($ch);
        $json_obj       = json_decode($res,true);
        $access_token   = $json_obj['access_token'];
        $openid         = $json_obj['openid'];
        echo $access_token;
        echo "<br/>";
        echo $openid;
        print_r($json_obj);
        if(empty($openid)){
            $result['resultnum']    = "00110";
            $result['result_mess']  = "登陆失败";
            $result['result']       = "";
            return $result;exit;
        }else{
            $openidd['openid'] = $openid;
            $UserOpenid = $UserTable->where("openid = '$openid'")->find();
            print_r($UserOpenid);
            $uid = $UserOpenid['uid'];
            if(empty($UserOpenid['openid'])){
                $UserTable        = M("user");
                $data['time'] = $time;
                $uid = $UserTable->add($data);
                if($uid>0){
                    $UserInfo = $this->GetUserInfo($access_token,$openid,$time,$uid);
                    return $UserInfo;exit;
                }else{
                    $result['resultnum']    = "00110";
                    $result['result_mess']  = "登陆失败";
                    $result['result']       = "";
                    return $result;exit;
                }
            }else{
                $UserInfo = $this->GetHaveUserInfo($openid,$uid,$time);
                return $UserInfo;exit;
            }
        }
    }

    /*
     * 获取用户的基本信息
     * $openid   用户唯一openid
     * $access_token  获取用户的基本信息必须条件
     * */
    public function GetUserInfo($access_token,$openid,$time,$uid){
        $get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$get_user_info_url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        $userinfo = json_decode($res);
        $UserTable      = M("user");
        $MemberTable    = M("member");
        $UserRegister['token'] =rand(1000000,9999999);
        $UserRegister['openid'] = $userinfo->openid;
        $UserRegister['time']   = $time;
        $result = $UserTable->where("uid = $uid")->save($UserRegister);
        if($result>0){
            $MemeberInfo['nickname']    = $userinfo->nickname;
            $MemeberInfo['uid']         = $uid;
            $MemeberInfo['sex']         = $userinfo->sex==1 ?0:1;
            $MemeberInfo['user_img']    = $userinfo->headimgurl;
            $MemeberInfo['starttime']   = date("Y-m-d H:i:s");
            $MemeberInfo['openid']      = $UserRegister['openid'];
            $result = $MemberTable->add($MemeberInfo);
            if($result>0){
                $resulinfo = $UserTable->where("uid = $uid")->find();
                $memberin  = $MemberTable->where("id = $result")->find();
                $memberin['token'] = $resulinfo['token'];
                $resultmess['resultnum']    = "0";
                $resultmess['result_mess']  = "成功";
                $resultmess['result']       = $memberin;
                return $resultmess;exit;
            }else{
                $resultmess['resultnum']    = "1";
                $resultmess['result_mess']  = "失败";
                $resultmess['result']       = "";
                return $resultmess;exit;
            }
        }
    }
    /*
     * 获取用户信息
     * $openid  用户唯一凭证
     * */
    public function GethaveUserInfo($uid,$openid,$time){
        $MemberTable        = M("member");
        $UserTable          = M("user");
        $userresult = $UserTable->where("openid = '$uid' and uid = $openid")->find();
        $data['time'] = $time;
        $SaveTime = $UserTable->where("uid = $openid")->save($data);
        if(empty($userresult)){
            $resultmess['resultnum']    = "101";
            $resultmess['result_mess']  = "失败";
            $resultmess['result']       = "";
            return $resultmess;exit;
        }else{
            $uid = $userresult['uid'];
            $result = $MemberTable->where("uid = $uid")->find();
            $result['token'] = $userresult['token'];
            if(empty($result)){
                $resultmess['resultnum']    = "1";
                $resultmess['result_mess']  = "失败";
                $resultmess['result']       = $result;
                return $resultmess;exit;
            }else{
                $resultmess['resultnum']    = "0";
                $resultmess['result_mess']  = "成功";
                $resultmess['result']       = $result;
                return $resultmess;exit;
            }
        }
    }

    public function GetTimeUserInfo($time){
        $MemberTable        = M("member");
        $UserTable          = M("user");
        $userresult = $UserTable->where("time = '$time'")->find();
        if(empty($userresult)){
            $resultmess['resultnum']    = "101";
            $resultmess['result_mess']  = "失败";
            $resultmess['result']       = "";
            return $resultmess;exit;
        }else{
            $uid = $userresult['uid'];
            $result = $MemberTable->where("uid = $uid")->find();
            $result['token'] = $userresult['token'];
            if(empty($result)){
                $resultmess['resultnum']    = "1";
                $resultmess['result_mess']  = "失败";
                $resultmess['result']       = $result;
                return $resultmess;exit;
            }else{
                $resultmess['resultnum']    = "0";
                $resultmess['result_mess']  = "成功";
                $resultmess['result']       = $result;
                return $resultmess;exit;
            }
        }
    }
}