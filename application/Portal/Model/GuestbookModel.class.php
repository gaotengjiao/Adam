<?php
namespace Common\Model;
use Common\Model\CommonModel;
class GuestbookModel extends CommonModel {
    /*
     * 查询美问答内容
     * */
    public function SelBeautyAsk($where,$uid){
        $whereSearch = "";
        if($where == ""){
            $whereSearch.="status = 1 and audit_status = 1";
        }else{
            $whereSearch.="status = 1 and audit_status = 1 and msg like '%$where%'";
        }
        if($uid==""){
            $whereSearch = $whereSearch;
        }else{
            $whereSearch.= " and uid = $uid";
        }
        $GuestbookTable = M("guestbook");
        $GuestBookResult = $GuestbookTable->where("$whereSearch")->order("createtime desc")->field("id,uid,msg,createtime,views")->select();

        $UserResult = $this->SelUser($GuestBookResult);
        $Result = $this->SelDoctorReply($UserResult);

        $Result = $this->SelUsersComment($Result);
        foreach($Result as $key=>$val){
            $Result[$key]['replytime'] = TIMESHOW($val['replytime']);
            $Result[$key]['createtime'] = TIMESHOW($val['createtime']);
        }
        if($Result==""){
            return NOTFOUND();exit;
        }else{
            return SONEOK($Result);exit;
        }
    }


    /*
   * 查询用户评论信息
   * */
    public function SelUsersComment($Result){
        $SelUserGuestBookCount = count($Result);
        $GuestbookReply = M("guestbook_reply");
        $MemberTable = M("member");
        for($i=0;$i<$SelUserGuestBookCount;$i++){
            $Posts_id = $Result[$i]['id'];
            $USER[$i]['usercomment'] = $GuestbookReply->where("isadmin = 0 and guestbookid = $Posts_id and status = 1")->select();
            $Result[$i]['count'] = count( $USER[$i]['usercomment']);
        }
        return $Result;exit;
    }

    /*
    * 查询项目详情
    * */
    public function SelQueryDetails($Posts_id, $uid){
        $GuestbookTable = M("guestbook");
        $click = M('click');
        $GuestBook = $GuestbookTable->where("id = $Posts_id")->field("id,uid,msg,createtime,views")->find();
        $data['details']['click_guestbook'] = $click ->where("table_name = 'guestbook' AND pid = ".$GuestBook['id'])->count();
        $zan = $click-> where("table_name = 'guestbook' AND pid = ".$Posts_id." AND uid = ".$uid)->find();
        if($zan){
            $data['details']['click_status'] = 1;
        }else{
            $data['details']['click_status'] = 0;
        }
        $GuestBookResult[] = $GuestBook;
        $UserResult = $this->SelUser($GuestBookResult);
        $Result = $this->SelDoctorReply($UserResult);
        $UserResult = $this->SelUserComment($Posts_id);
        $data['details'] = $Result[0];
        $data['details']['createtime'] = TIMESHOW($data['details']['createtime']);
        $data['details']['replytime'] = TIMESHOW($data['details']['replytime']);
        $data['usercommnet'] = $UserResult;
        foreach($data['usercommnet'] as $kk=>$vv){
            $data['usercommnet'][$kk]['replytime'] = TIMESHOW($vv['replytime']);
        }
        if($data['details']==""){
            return NOTFOUND();exit;
        }else{
            return SONEOK($data);exit;
        }
    }
    /*
     * 查询用户评论信息
     * */
    public function SelUserComment($Posts_id){
        $GuestbookReply = M("guestbook_reply");
        $MemberTable = M("member");
        $GuestBookResult = $GuestbookReply->where("isadmin = 0 and guestbookid = $Posts_id and status = 1")->select();
        $UserResult = $this->SelUser($GuestBookResult);
        $UserResult['count'] = count($GuestBookResult);
        return $UserResult;exit;
    }
    /*
     * 查询用户信息
     * */
    public function SelUser($GuestBookResult){
        $MemberTable = M("member");
        $GuestBookResultCount = count($GuestBookResult);
        for($i=0;$i<$GuestBookResultCount;$i++){
            $uid = $GuestBookResult[$i]['uid'];
            $UserMember = $MemberTable->where("uid = $uid")->field("nickname,user_img")->find();
            $GuestBookResult[$i]['user_nickname'] = $UserMember['nickname'];
            $GuestBookResult[$i]['user_img'] = $UserMember['user_img'];
        }
        return $GuestBookResult;exit;
    }

    /*
     * 查询医生是否已经回答
     * */
    public function SelDoctorReply($UserResult){
        $GuestbookReply = M("guestbook_reply");
        $UsersTable = M("users");
        $UserResultCount = count($UserResult);
        for ($i=0;$i<$UserResultCount;$i++){
            $GuestId = $UserResult[$i]["id"];
            $Result = $GuestbookReply->where("isadmin = 1 and guestbookid = $GuestId and status = 1")->field("uid,isadmin,replycontent,replytime")->find();
            if($Result!=""){
                $uid = $Result['uid'];
                $RResult = $UsersTable->where("id = $uid")->field("user_login,user_url")->find();
                $UserResult[$i]['doctor'] = $RResult["user_login"];
                $UserResult[$i]['doctor_url'] = $RResult["user_url"];
                $UserResult[$i]['isreply'] = 1;
                $UserResult[$i]['replytime'] = $Result['replytime'];
                $UserResult[$i]['replycontent'] = $Result['replycontent'];
            }else{
                $UserResult[$i]['isreply'] = 0;
            }
        }
        //$UserResult['count'] = $UserResultCount;
        return $UserResult;exit;
    }

    /*
     * 查询条件
     * */
    public function SelWhere(){
        $GuestBook = $this->SelGuestBookWhere();
        $Post = $this->SelBeautyEncyclopedia();
        if($GuestBook=="" | $Post==""){
            return NOTFOUND();exit;
        }else{
            $data['type1'] = $GuestBook;
            $data['type2'] = $Post;
            return SONEOK($data);exit;
        }
    }

    /*
     * 查询有问必答
     * */
    public function SelGuestBookWhere(){
        $GuestbookTable = M("guestbook");
        $GuestBookResult = $GuestbookTable->where("recommend = 1")->field("id,msg")->select();
        $GuestBookResultCount = count($GuestBookResult);
        for($i=0;$i<$GuestBookResultCount;$i++){
            $Contetn = $GuestBookResult[$i]['msg'];
            $GuestBookResult[$i]['content'] = mb_substr("$Contetn", 0, 15, 'utf-8')."...";
            unset($GuestBookResult[$i]["msg"]);
        }
        return $GuestBookResult;exit;
    }
    /*
    * 查询美容百科内容
    * */
    public function SelBeautyEncyclopedia(){
        $PostsTable  = M("posts");
        $Posts  = $PostsTable->where("recommended = 1")->field("post_keywords")->select();
        foreach ($Posts as $v){
            $v=join(',',$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[]=$v;
        }
        $post = implode(",",$temp);
        $post = explode(",",$post);
        $post = array_unique($post);
        return $post;exit;
    }
}