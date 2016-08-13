<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/*
*注明修改原因，谁改的，什么时间
*王鑫磊：2016-06-20 10:40 修改登陆方法
*/

/**
 * 登陆、注册
 */
class LoginController extends HomebaseController {
    /*
    验证码
    通过获取的用户手机号信息向用户手机发送一个四位数随机验证码
    */
    public function Register(){
        $phone = I("request.phone");
        $UserModel = D("Users");
        $result = $UserModel->Register($phone);
        $this->ajaxReturn($result);
    }

    /*
     * 验证用户唯一性
     * $Uname 用户手机号
     * */
    public function SoleUname(){
        $Uname      = I("request.phone");
        $UserModel  = D("Users");
        if(empty($Uname)){
            $result["resultnum"]    = "101";
            $result['result_mess']  = "提交错误";
            $this->ajaxReturn($result);die;
        }
        if(mb_strlen($Uname)<11 || mb_strlen($Uname)>11){
            $result["resultnum"]    = "110";
            $result['result_mess']  = "手机号格式不正确";
            $this->ajaxReturn($result);die;
        }else{
            $result = $UserModel->SoleName($Uname);
            $this->ajaxReturn($result);
        }

    }
    /*
    注册
    需要再次发端验证码的准确性
    如果二次验证成功测注册用户
    否侧注册失败，重新获取验证码
    */
    public function RegisterPost(){
        $phone = I("request.phone");
        $code  = I("request.code");
        $rtype = I("request.type");
        $pass  = I("request.pass");
        if($phone=="" | $code=="" | $rtype=="" | $pass==""){
            $returnmess['resultnum'] = "101";
            $returnmess['result_mess'] = "提交错误";
            $this->ajaxReturn($returnmess);
        }
        $codepgone = $_SESSION['code'."$phone"];
        if($codepgone!=$code){
            $returnmess['resultnum'] = "103";
            $returnmess['result_mess'] = "验证码错误";
            $returnmess['result'] = "";
            $this->ajaxReturn($returnmess);
        }else{
            $UserModel = D("Users");
            $result = $UserModel->RegisterAdd($phone,$code,$rtype,$pass);
            $this->ajaxReturn($result);
        }
    }

    /*
     * 使用第三方登陆的用户绑定手机号
     * */
    public function UserBound(){
        $phone = I("request.phone");
        $Uid   = I("request.uid");
        $Token = I("request.token");
        $code  = I("request.code");
        $pass  = I("request.pass");
        if($phone=="" | $Uid=="" | $Token=="" | $code==""){
            $returnmess['resultnum'] = "101";
            $returnmess['result_mess'] = "提交错误";
            $returnmess['result'] = "";
            $this->ajaxReturn($returnmess);
        }
        $codepgone = $_SESSION['code'."$phone"];
        if($codepgone!=$code){
            $returnmess['resultnum'] = "103";
            $returnmess['result_mess'] = "验证码错误";
            $returnmess['result'] = "";
            $this->ajaxReturn($returnmess);
        }else{
            $UserModel = D("Users");
            $result = $UserModel->BoundUserPhone($phone,$Uid,$Token,$pass);
            $this->ajaxReturn($result);
        }
    }

    /*
     * 用户登陆
     * */
    public function Login(){
        $UserInfo = I("request.");
        $UserModel = D("Users");
        $phone = I("request.phone");
        $code  = I("request.code");
        $rtype = I("request.type");
        $pass  = I("request.pass");
        $codeDate['phone'] = $phone;
        $codeDate['code']  = $code;
        $codeDate['type']  = $rtype;
        if ($rtype==1) {
            if ($phone=="") {
                $returnmess['resultnum'] = "112";
                $returnmess['result_mess'] = "手机号未输入";
                $this->ajaxReturn($returnmess);
            }
            if ($code=="") {
                $returnmess['resultnum'] = "114";
                $returnmess['result_mess'] = "验证码未输入";
                $this->ajaxReturn($returnmess);
            }
        } else {
            if ($phone=="") {
                $returnmess['resultnum'] = "112";
                $returnmess['result_mess'] = "手机号未输入";
                $this->ajaxReturn($returnmess);
            }
            if ($pass=="") {
                $returnmess['resultnum'] = "115";
                $returnmess['result_mess'] = "密码未输入";
                $this->ajaxReturn($returnmess);
            }
        }
        if($rtype==1){
            $result = $UserModel->UserCodeLogin($codeDate);
            $this->ajaxReturn($result);
        }else{
            $result = $UserModel->UserLogin($UserInfo);
            $this->ajaxReturn($result);
        }
    }
    /*
     * 退出登录
     */
    public function outLogin(){
        $uid = I('request.uid', '');
        $token = I('request.token', '');
        if(empty($uid) && empty($token)){
            $result = SONEERROR();
            $this->ajaxReturn($result);exit;
        }
        $unset = session($uid.$token, null);
        if($unset){
            $this->ajaxReturn($unset);exit;
        }else{
            $error = SONEERROR();
            $this-> ajaxReturn($error);
            exit;
        }
    }

}