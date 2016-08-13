<?php
namespace Common\Model;
use Common\Model\CommonModel;
class FavorableModel extends CommonModel {
    /*
     * 优惠-首页
     * */
    public function SelAllFavorable(){
        $FavorableTable = M('favorable');
        $Result = $FavorableTable->where("status = 0")->field("id,img,istop")->order("istop desc")->select();
        if($Result==""){
            return NOTFOUND();exit;
        }else{
            return SONEOK($Result);exit;
        }
    }
    /*
     * 推荐页
     * 轮播图显示
     */
    public function pagePhoto(){
        $slide = M('slide');
        $where['slide_cid'] = 1;
        $where['slide_status'] = 1;
        $return  = $slide -> where($where) -> select();
        if($return){
            return SONEOK($return);exit;
        }else{
            return NOTFOUND();exit;
        }
    }
    /*
     * 等待回答的问题显示
     * $return['reply'] = 1:有医师回复；0:没有回复
     */
    public function selAnswer($keywords, $type){
        $guestbook = M('guestbook');
        $member = M('member');
        $reply = M('guestbook_reply');
        $return = $guestbook -> where("msg like '%".$keywords."%' AND msg like '%".$type."%' AND audit_status=1 AND status=1")->order("createtime DESC")->field("id,uid,msg,views,createtime")->limit(1)->find();
        if(empty($return)){
            return NOTFOUND();
            exit;
        }
        $return['user'] = $member -> where('uid = '.$return['uid'])->field("uid,nickname,user_img")->find();
        $sel_reply = $reply -> where("guestbookid = ". $return['id']." AND isadmin = 1")->select();
        $return['createtime'] = TIMESHOW($return['createtime']);
        if($sel_reply){
            $return['reply'] = 1;
        }else{
            $return['reply'] = 0;
        }
        return SONEOK($return);
    }
    /*
     * 查询手术对比图
     */
    public function selContrast($keywords, $type){
        $level = M('levels');
        $photo = M("levels_photo");
        $return = $level -> where("level_keywords like '%".$keywords."%' AND level_keywords like '%".$type."%' AND level_status = 1")->order("level_date DESC")->field("id")->select();
        if(!$return){
            return NOTFOUND();exit;
        }
        foreach($return as $k=>$v){
            $arr[] =$v['id'];
        }
        $arr = implode(',', $arr);
        $result = $photo ->where("lid in(".$arr.") AND after_img is not null")->limit(2)->select();
        if($result){
            return SONEOK($result);exit;
        }else{
            return NOTFOUND();exit;
        }

    }
    /*
     * 查询最近两个小时发表的文章数
     */
    public function selNew(){
        $level = M('levels');
        $member = M('member');
        $now = date("Y-m-d H:i:s", time());
        $before = date("Y-m-d H:i:s", time()-7200);
        $where['level_date'] = array(between, array($before,$now ));
        //return SONEOK($now."||".$before);exit;
        $result = $level ->where($where)->order("id DESC")-> field('level_author')-> limit(4)->select();
        foreach($result as $k=>$v){
           $result[$k]['user_photo'] = $member -> where("uid = ".$v['level_author']) -> field("id,user_img")->select();
        }
        $result['count'] = $level ->where($where)->count();

        if($result['count'] == '0'){
            return NOTFOUND();
            exit;
        }else{
            return SONEOK($result);
            exit;
        }
    }
    /*
     * 显示优惠活动
     */
    public function showPhoto(){
        $favorable = M('favorable');
        $where['status'] = 0;
        $status = $favorable->where($where)->order("(istop AND isrecommend) DESC")->select();

        foreach($status as $k=>$v){
            $now_date = time();
            $act_date = strtotime($v['end_time']);
            if($act_date < $now_date){
                unset($status[$k]);
                $data['status'] = 2;
                if(!($favorable->where("id = ".$v['id'])->save($data))){
                    return SONEERROR();
                    exit;
                }
            }
        }
        if($status){
            return SONEOK($status);
            exit;
        }else{
            return NOTFOUND();
            exit;
        }
    }
}
