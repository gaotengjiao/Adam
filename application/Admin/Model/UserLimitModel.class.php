<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UserLimitModel extends CommonModel
{
    /*
     * 查询所有额度信息
     * */
    public function SelAllUser($type){
        if($type==1){
            $where = '';
            $UserLimit = $this->SelAllUserLimit($where);
            return $UserLimit;exit;
        }elseif($type==2){
            $where = 'status=1';
            $Result = $this->SelAllUserLimit($where);
            return $Result;exit;
        }elseif($type==3){
            $where = 'status!=1';
            $Result = $this->SelAllUserLimit($where);
            return $Result;exit;
        }elseif($type==4){
            $where = 'status=4';
            $Result = $this->SelAllUserLimit($where);
            return $Result;exit;
        }else{
            $Result = $this->UserUpLimit();
            return $Result;exit;
        }
    }

    /*
     * 搜索
     * */
    public function SeachUserLimit($where){
        $UserLimitTable = M("user_limit");
        $starttime      = $where["starttime"];
        $endtime        = $where["endtime"];
        $phone          = $where["phone"];
        $type           = $where["type"];
        $status = "";
        if($type=="AllLimit"){
            $status.= "";
        }elseif($type=="HaveLimit"){
            $status.= "status = 1 and ";
        }elseif($type=="NotLimit"){
            $status.= "status != 1 and ";
        }else{
            $status.= "status = 4 and ";
        }
        if($starttime !="" & $endtime!=""){
            $what = $status." createDate between '$starttime' and '$endtime'";
        }elseif($starttime !="" | $endtime ==""){
            $what = $status." createDate > '$starttime'";
        }elseif($endtime !="" | $starttime ==""){
            $what = $status." createDate < '$endtime'";
        }elseif($starttime =="" & $endtime ==""){
            $what = $status."";
        }else{
            echo '错误';
        }
       // $UserLimitMess = $UserLimitTable->where($what)->field("id,uid,limit,createDate,status")->select();
        $count      = $UserLimitTable->where($what)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $UserLimitMess = $UserLimitTable->where($what)->field("id,uid,limit,createDate,status")->limit($Page->firstRow.','.$Page->listRows)->select();
        $userLimitCount = count($UserLimitMess);
        $userScore = $this->ScoreMess($UserLimitMess,$userLimitCount);
        $userMess  = $this->UserMess($userScore,$userLimitCount);
        if($phone==""){
            $result = $this->HtmlResult($userMess);
           return $result;exit;
        }else{
            $Result = $this->PhoneWhere($phone,$userMess,$userLimitCount);
            $result = $this->HtmlResult($Result);
            return $result;exit;
        }
    }

    /*
     *搜索返回值
     * */
    public function HtmlResult($Result){
        $data = "";
        foreach($Result as $k=>$v){
            $data.=  "<tr id='searchresult'>";
            $data.= "<td> <input type='checkbox' name='chk_list[]' value='".$v['id']."' class='checkas'></td>";
            $data.= "<td>".$v['id']."</td>";
            $data.= "<td>".$v['nickname']."</td>";
            $data.= "<td>".$v['iphone']."</td>";
            $data.= "<td>".$v['score']."</td>";
            $data.= "<td>".$v['limit']."</td>";
            $data.= "<td>".$v['createdate']."</td>";
            $data.= "<td>";
            if ($v['status'] == 1){
                $data.= " <font color='#a9a9a9'>已授权</font>";
            }elseif($v['status'] == 2){
                $data.= "<a href='javascript:void(0)' class='shou' dd='".$v['id']."'>授权</a>/<a href='javascript:void(0)' class='btn6' dd='".$v['id']."' uid='".$v['uid']."'>驳回</a>";
            }elseif($v['status'] == 0){
                $data.= "<a href='javascript:void(0)' class='shou' dd='".$v['id']."'>授权</a>/<a href='javascript:void(0)'  class='btn6' dd='".$v['id']."' uid='".$v['uid']."'>驳回</a>";
            }elseif($v['status'] == 3){
                $data.= "<font color='red'>已拒回</font>";
            }elseif($v['status'] == 4){
                $data.= "<font color='red'>评分不足，不允许授权</font>";
            }elseif($v['status'] == 6){
                $data.= "<a href='javascript:void(0)' class='shou' dd='".$v['id']."'>授权</a>/<a href='javascript:void(0)'  class='btn6' dd='".$v['id']."' uid='".$v['uid']."'>驳回</a>";
            }else{
                $data.= "<a href='javascript:void(0)' class='shou' dd='".$v['id']."'>授权</a>/<a href='javascript:void(0)'  class='btn6' dd='".$v['id']."' uid='".$v['uid']."'>驳回</a>";
            };
            $data.= "</td>";
            $data.= "<td>";
            if ($v['status'] == 1){
                $data.= "<a href='javascript:void(0)' dd='".$v['id']."'><font color='red'>已申请</font></a>";
            }elseif($v['status'] == 2){
                $data.= "<font color='#a9a9a9'>未申请</font>";
            }elseif($v['status'] == 0){
                $data.= "<font color='#a9a9a9'>未申请</font>";
            }elseif($v['status'] == 3){
                $data.= "<a href='javascript:void(0)'class='jujue' dd='".$v['id']."'><font color='#006400'>查看拒回原因</font></a>";
            }elseif($v['status'] == 4){
                $data.= "<font color='red'>评分不足，不允许授权</font>";
            }elseif($v['status'] == 6){

            }else{
                $data.= "<a href='javascript:void(0)' dd='".$v['id']."'><font color='red'>已申请</font></a>";
            };
            $data.= "</td>";
            $data.= "</tr>";
        }
        return $data;exit;
    }
    /*
     * 搜索手机号
     * */
    public function PhoneWhere($phone,$userMess,$userLimitCount){
       for($s=0;$s<$userLimitCount;$s++){
            if($userMess[$s]['iphone']!=$phone){
                unset($userMess[$s]);
            }
       }
        return $userMess;exit;
    }
    /*
     * 提额用户
     * */
    public function UserUpLimit(){
        $UserLimitTable = M("user_uplimit");
        $count      = $UserLimitTable->field("id")->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $UserLimitTable->field("id,limitid,uid,UpLimitSize,upLimitDate,status,number,approver,limittype")->order('upLimitDate desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $userLimitCount = count($list);
        $userScore = $this->ScoreMess($list,$userLimitCount);
        $userMess  = $this->UserMess($userScore,$userLimitCount);
        $Result['data']    =$this->UserHaveMoney($userMess);
        $Result['page']    =$show;
        return $Result;exit;
    }

    /*
     * 查询用户的原有额度
     * $$userMess       用户信息
     * */
    public function UserHaveMoney($userMess){
        $UserLimitTable = M("user_limit");
        $UserUpLimitCount = count($userMess);
        for($i=0;$i<$UserUpLimitCount;$i++){
            $uid = $userMess[$i]['uid'];
            $userinfo = $UserLimitTable->where("uid = $uid")->field("limit")->find();
            $userMess[$i]['userlimit'] = $userinfo['limit'];
        }
        return $userMess;exit;
    }
    /*
     * 查询用户额度
     * */
    public function SelAllUserLimit($where){
        $UserLimitTable = M("user_limit");
        $count      = $UserLimitTable->where($where)->field("id")->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $userLimit = $UserLimitTable->where($where)->field("id,uid,limit,createDate,status")->order('createDate desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $userLimitCount = count($userLimit);
        $userScore = $this->ScoreMess($userLimit,$userLimitCount);
        $userMess  = $this->UserMess($userScore,$userLimitCount);
        $Result['data']    = $userMess;
        $Result['page']    = $show;
        return $Result;exit;
    }
    /*
     * 查询用户评分
     * $userLimitCount      用户个数
     * $userLimit           用户额度信息
     * */
    public function ScoreMess($userLimit,$userLimitCount){
        $ScoreTable = M("score");
        for($i=0;$i<$userLimitCount;$i++){
            $uid = $userLimit[$i]['uid'];
            $score = $ScoreTable->where("uid = $uid")->field("fraction")->find();
            $userLimit[$i]['score'] = $score['fraction'];
        }
        return $userLimit;exit;
    }
    /*
    * 查询用户信息
    * $userLimitCount      用户个数
    * $userScore           用户额度信息
    * */
    public function UserMess($userScore,$userLimitCount){
        $MemberTable = M("member");
        for($i=0;$i<$userLimitCount;$i++){
            $uid = $userScore[$i]['uid'];
            $usermess = $MemberTable->where("uid = $uid")->field("iphone,nickname")->find();
            $userScore[$i]['nickname'] = $usermess['nickname'];
            $userScore[$i]['iphone'] = $usermess['iphone'];
        }
        return $userScore;exit;
    }

    /*
     *授权
     * $id      授权id
     * */
    public function Authorization($id,$money,$type){
        $UserLimitTable = M("user_limit");
        if($type=="editmoney"){
            $data['limit'] = $money;
        }else{
            $data['status'] = 1;
            $data['user_balance'] = $money.".00";
            $data['approver'] = $_SESSION['ADMIN_ID'];
        }
        $Result = $UserLimitTable->where("id = $id")->save($data);
        if($Result==1){
            return 1;exit;
        }else{
            return 0;exit;
        }
    }

    /*
     * 拒绝授权
     * $id      额度ID
     * $refuse  拒绝原因
     * */
    public function RefuseAuthorization($id,$refuse,$uid){
        $UserLimitTable = M("user_limit");
        $data['status'] = 3;
        $Result = $UserLimitTable->where("id = $id")->save($data);
        if($Result==1){
            $ReasonsTable = M("reasons");
            $Data['uid'] = $uid;
            $Data['lid'] = $id;
            $Data['reasone'] = $refuse;
            $Data['reasonetime'] = time();
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

    /*
     * 查询拒绝原因
     * $id  额度ID
     * */
    public function ReasonsForRefusal($id){
        $ReasonsTable = M("reasons");
        $Result = $ReasonsTable->where("lid = $id")->field("reasone")->find();
        $Return = $Result['reasone'];
        return $Return;exit;
    }
}