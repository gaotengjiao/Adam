<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UsersModel extends CommonModel {
    /*
        根据ID值查询医生或项目或酒店的信息
    */
    public function SelDoctor($TypeID){
        $UsersTable = M("users");
        $GoodsTypeTable = M("goods_type");
        if(empty($TypeID)){
            $UsersLists['resultnum'] = "101";
            $UsersLists['result_mess'] = "请求错误";
            return $UsersLists;die;
        }
        if($TypeID!=2){
            $UsersLists['resultnum'] = "101";
            $UsersLists['result_mess'] = "请求错误";
            return $UsersLists;die;
        }
        $UsersLists = $UsersTable->where("is_doctor = 1")->field("id,user_login,user_nicename,user_url,recommend,rold")->select();
        if(empty($UsersLists)){
            $UsersLists['resultnum'] = "404";
            $UsersLists['result_mess'] = "为查询到数据";
            return $UsersLists;die;
        }
        return $UsersLists;
    }
    /*
     * 用户唯一性验证
     * */
    public function SoleName($Uname){
        $name = md5($Uname);
        $UserTable = M("user");
        $MemberTable = M("member");
        $SoleName = $UserTable->where("uname = '$name'")->field('uname')->find();
        if(!empty($SoleName)){
            $result['resultnum'] = 109;
            $result['result_mess'] = "用户名重复";
            return $result;die;
        }else{
            $MemberResult = $MemberTable->where("iphone = '$Uname'")->find();
            if(empty($MemberResult)){
                $result['resultnum'] = 0;
                $result['result_mess'] = "用户名可用";
                return $result;die;
            }else{
                $result['resultnum'] = 0;
                $result['result_mess'] = "用户名可用";
                $result['result'] = $MemberResult;
                return $result;die;
            }
        }
    }

    /*
        用户在前端验证成功后，需把验证码发送至后端，后端会进行二次验证，验证账号唯一性
        验证正确无误后则执行注册动作
    */
    public function RegisterAdd($phone,$code,$rtype,$pass){
        $UserTable = M("user");
        $MemberTable = M("member");
        $UserRegister['token'] =rand(1000000,9999999);
        $UserRegister['uname'] =  md5($phone);
        $solePhone = $UserRegister['uname'];
        $PhoneLast = $this->PhoneLast($solePhone);
        if($PhoneLast['resultnum']!=0){
            return $PhoneLast;exit;
        }else{
            $UserRegister['password'] = md5($pass.md5($UserRegister['token']));
            $result = $UserTable->add($UserRegister);
            if($result>1){
                $MemberRegister['uid'] = $result;
                $MemberRegister['iphone'] = $phone;
                $MemberRegister['starttime'] = date("Y-m-d H:i:s");
                $memberresult = $MemberTable->add($MemberRegister);
                if($memberresult>1){
                    $Userinfo = $UserTable->where("uid = $result")->find();
                    $member = $MemberTable->where("uid = $result")->find();
                    $member['token'] = $Userinfo['token'];
                    $returnmess['resultnum'] = "0";
                    $returnmess['result_mess'] = "注册成功";
                    $returnmess['result'] = $member;
                    return $returnmess;die;
                }else{
                    $returnmess['resultnum'] = "104";
                    $returnmess['result_mess'] = "注册失败";
                    return $returnmess;die;
                }
            }else{
                $returnmess['resultnum'] = "104";
                $returnmess['result_mess'] = "注册失败";
                return $returnmess;die;
            }
        }

    }

    /*
     * 验证用户手机号是否重复
     * $phone   用户手机号
     * */
    public function PhoneLast($solePhone){
        $UserTable = M("user");
        $Solephone = $UserTable->where("uname = '$solePhone'")->find();
        if(!empty($Solephone)){
            $result['resultnum'] = 109;
            $result['result_mess'] = "用户名重复";
        }else{
            $result['resultnum'] = 0;
            $result['result_mess'] = "可用";
        }
        return $result;exit;
    }
    /*
        用户登录
        status = 1   为密码登陆
        status = 2 	 为验证码登陆
    */
    public function UserLogin($UserInfo){
        $pass = $UserInfo['pass'];
        // print_r($UserInfo);die;
        $UserTable = M("user");
        $UserMemberTable = M("member");
        $QuotaTable = M("quota");
        $name = md5($UserInfo['phone']);
        $status = $UserInfo["type"];
        $UserMess = $UserTable->where("uname = '$name'")->find();
        if(empty($UserMess)){
            $error['resultnum'] = "108";
            $error['result_mess'] = "用户不存在或用户名错误";
            return $error;die;
        }else{
            $Token = $UserMess['token'];
            $UserID = $UserMess['uid'];

            $password = md5($pass.md5($Token));
            if($password==$UserMess['password']){
                $member = $UserMemberTable->where("uid = $UserID")->field("uid,name,number,iphone,points,level,influence,openid,starttime,nickname,user_img,usergroup,isface")->find();
                $member['token'] = $Token;
                $result['resultnum'] = 0;
                $result['result_mess'] = "登陆成功";
                $result['result'] = $member;
                return $result;die;
            }else{
                $result['resultnum'] = "107";
                $result['result_mess'] = "密码错误";
                $result['result'] = "";
                return $result;die;
            }
        }
    }

    /*
     * 用户验证码的路
     * */
    public function UserCodeLogin($codeDate){
        $phone = $codeDate['phone'];
        $code  = $codeDate['code'];
        $Ycode = $_SESSION['code'."$phone"] = $code;
        if($Ycode!=$code){
            $member['resultnum'] = 116;
            $member['result_mess'] = "验证码错误";
            return $member;die;
        }else{
            $UserMemberTable = M("member");
            $UserTable = M("user");
            $name = md5($codeDate['phone']);
            $status = $codeDate["type"];
            $UserMess = $UserTable->where("uname = '$name'")->find();
            if(!empty($UserMess)){
                $UserID = $UserMess['uid'];
                $member = $UserMemberTable->where("uid = $UserID")->field("uid,name,number,iphone,points,level,influence,openid,starttime,nickname,user_img,usergroup,isface")->find();
                $member['token'] = $UserMess['token'];
                $result['resultnum'] = 0;
                $result['result_mess'] = "登陆成功";
                $result['result'] = $member;
                return $result;exit;
            }else{
                $phone = $codeDate['phone'];
                $UserRegister['token'] =rand(1000000,9999999);
                $UserRegister['uname'] =  md5($phone);
                $result = $UserTable->add($UserRegister);
                $MemberRegister['uid'] = $result;
                $MemberRegister['iphone'] = $phone;
                $MemberRegister['starttime'] = date("Y-m-d H:i:s");
                $memberresult = $UserMemberTable->add($MemberRegister);
                $member = $UserMemberTable->where("uid = $memberresult")->field("uid,name,number,iphone,points,level,influence,openid,starttime,nickname,user_img,usergroup,isface")->find();
                $member['token'] = $UserRegister['token'];
                $result['resultnum'] = 0;
                $result['result_mess'] = "登陆成功";
                $result['result'] = $member;
                return $result;exit;
            }
        }
    }
    /*
    用户注册发送验证码
    根据获得到的手机号向此手机号发送验证码
    */
    public function Register($phone){
        session_start();
        if(empty($phone)){
            $returnmess['resultnum'] = '101';
            $returnmess['result_mess'] = "提交错误";
        }else{
            $code = rand(1000,9999);
            $_SESSION['code'."$phone"] = $code;
            if(!empty($code)){
                $returnmess['resultnum'] = '0';
                $returnmess['result_mess'] = "验证码已发送";
                $returnmess['code']		 = $code;
                return $returnmess;die;
            }
            $data ="您好，您的验证码是" . $code ;

//            $_SESSION[$phone] = $code;
//            print_r($_SESSION);
//            print_r( $_SESSION[$phone]);die;
            $post_data = array();
            $post_data['account'] = iconv('GB2312', 'GB2312',"jiesenxiu001");
            $post_data['pswd'] = iconv('GB2312', 'GB2312',"Jsx888123");
            $post_data['mobile'] = $phone;
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
            //echo $result;
            //用于截取判断状态码
            $co=substr($result,15,1);
            if($co == '0'){
                $returnmess['resultnum'] = '0';
                $returnmess['result_mess'] = "验证码已发送";
                $returnmess['code']		 = "$code";
            }else{
                $returnmess['resultnum'] = '1';
                $returnmess['result_mess'] = "验证码发送失败";
            }
        }
        return $returnmess;
    }

    /*
     * 绑定第三方登陆用户的手机号
     * $phone     用户手机号
     * $uid       用户iD
     * $token     用户唯一标识
     * */
    public function BoundUserPhone($phone,$Uid,$Token,$pass){
        $UserResult = $this->UserToken($Uid,$Token);
        if($UserResult['resultnum']!=0){
            return $UserResult;exit;
        }else{
            $openid = $UserResult["result"]['openid'];
            $UserTable = M("user");
            $MemberTable = M("member");
            $User['uname'] = md5($phone);
            $User['password'] =  md5($pass.md5($Token));
            $UserResult = $UserTable->where("uid = $Uid")->save($User);
            if($UserResult>0){
                $OldUser = $MemberTable->where("iphone = '$phone'")->find();
                $uid = $OldUser['uid'];
                $MemberTable->where("iphone = '$phone'")->delete();
                $UserTable->where("uid = '$uid'")->delete();
                $data['iphone'] = $phone;
                $data['openid'] = $openid;
                $resultMember = $MemberTable->where("uid = $Uid")->save($data);
                if($resultMember>0){
                    $userMember = $MemberTable->where("uid = $Uid")->find();
                    $userMember['token'] = $Token;
                    $returnmess['resultnum'] = "0";
                    $returnmess['result_mess'] = "成功";
                    $returnmess['result'] = $userMember;
                    return $returnmess;exit;
                }else{
                    $returnmess['resultnum'] = "1";
                    $returnmess['result_mess'] = "绑定失败";
                    $returnmess['result'] = "";
                    return $returnmess;exit;
                }
            }else{
                $returnmess['resultnum'] = "1";
                $returnmess['result_mess'] = "绑定失败";
                $returnmess['result'] = "";
                return $returnmess;exit;
            }
        }
    }

    /*
     * 查询用户信息
     * uid  token
     * */
    public function SelUserMess($uid,$token){
        $UserTable = M("user");
        $UserMess = $UserTable->where("uid = $uid")->field("uid,token")->find();
        if($UserMess==""){
            return 1;die;
        }else{
            if($UserMess['token']==$token){
                return $UserMess;die;
            }else{
                return 1;die;
            }
        }
    }
}