<?php
namespace Common\Model;
use Common\Model\CommonModel;
/*
     * 查询颜值圈
     * $Posts_id    文章ID
     * */
class LevelsModel extends CommonModel {
    /*
     * 判断是首页还是详情页
     */
    public function SelFaceQuery($id){
        //return SONEOK('123');exit;
        if(empty($id)){
           $return = $this->allArticle();
            if(empty($return)){
                return SONEERROR();
            }else{
                return SONEOK($return);exit;
            }
        }else{
            $return1 = $this->findArticle($id);
            if(empty($return1)){
                return SONEERROR();
            }else{
                return SONEOK($return1);exit;
            }
        }
    }
    /*
     *颜值圈首页
     */
    public function allArticle(){
        $levels = M('levels');
        $member = M('member');
        $level_photo = M('levels_photo');
        $comment = M('comments');
        $click = M('click');
        $art = $levels ->where("level_status = 1")->order("level_date DESC")-> select();
        foreach($art as $k=>$v){
            $where['uid'] = $v['level_author'];
            $art[$k]['user_mation'] = $member -> where($where)->field('uid,user_img,nickname')->find();
            $photo = $level_photo -> where('lid = '.$v['id']) -> select();
            foreach($photo as $kk=>$vv){
                if(empty($vv['after_img'])){
                    $art[$k]['photo_status'] = 2;
                    $before1_img = explode(',',$photo[$kk]['before_img']);
                    $photo[$kk]['before_img'] = $before1_img;
                }else{
                    $photo[$kk]['before_img'] = array($photo[$kk]['before_img']);
                    $photo[$kk]['after_img'] = array($photo[$kk]['after_img']);
                    $art[$k]['photo_status'] = 1;
                }
            }
            $art[$k]['user_photo'] = $photo;
            $art[$k]['user_comment'] = $comment -> where("post_table = 'levels' AND post_id = ".$v['id'])->count();
            $art[$k]['user_click'] = $click ->where("table_name = 'levels' AND pid = ".$v['id'])->count();
            $art[$k]['level_date'] = TIMESHOW($v['level_date']);
            $click_zan = $click ->where("table_name = 'levels' AND pid = ".$v['id'])->find();
            if($click_zan){
                $art[$k]['user_click_status'] = 1;
            }else{
                $art[$k]['user_click_status'] = 0;
            }
        }

        return $art;exit;
    }
    /*
     * 颜值圈详情页
     */
    public function findArticle($id){
        $comment = M('comments');
        $member = M('member');
        $where['post_id'] = $id;
        $where['post_table'] = 'levels';
        $level = M('levels');
        $click = M('click');
        $findClick = $level->where("id = ".$id)->find();
        $data['level_hits'] = $findClick['level_hits'] +1;
        $clicks = $level->where("id = ".$id)->add($data);
        if(!$clicks){
            return SONEERROR();exit;
        }
        $comments = $comment ->where($where)->order('createtime DESC')->select();
        foreach($comments as $k=>$v){
            $com = $member -> where("uid = ".$v['uid'])->field("id,uid,nickname,user_img") ->find();
            $comments[$k]['user_uid'] = $com['uid'];
            $comments[$k]['user_name'] = $com['nickname'];
            $comments[$k]['user_img'] = $com['user_img'];
            $comments[$k]['createtime'] = TIMESHOW($v['createtime']);
            $comments[$k]['comment_count'] = $click->where("table_name = 'comments' AND pid = ".$v['id'])->count();
            $click_comement = $click ->where("table_name = 'comments' AND pid = ".$v['id'])->find();
            if($click_comement){
                $comments[$k]['user_comment_status'] = 1;
            }else{
                $comments[$k]['user_comment_status'] = 0;
            }
        }
        //$article  = $this->allArticle();
        
        $level_photo = M('levels_photo');

        $art = $level ->where("level_status = 1 AND id = ".$id)->order("level_date DESC")-> select();
        //return $art;die;
        foreach($art as $k=>$v){
            $where['uid'] = $v['level_author'];
            $art[$k]['user_mation'] = $member -> where($where)->field('uid,user_img,nickname')->find();
            $photo = $level_photo -> where('lid = '.$v['id']) -> select();
            $art[$k]['level_date'] = TIMESHOW($v['level_date']);
            foreach($photo as $kk=>$vv){
                if(empty($vv['after_img'])){
                    $art[$k]['photo_status'] = 2;
                    $before1_img = explode(',',$photo[$kk]['before_img']);
                    $photo[$kk]['before_img'] = $before1_img;
                }else{
                    $photo[$kk]['before_img'] = array($photo[$kk]['before_img']);
                    $photo[$kk]['after_img'] = array($photo[$kk]['after_img']);
                    $art[$k]['photo_status'] = 1;
                }
            }
            $art[$k]['user_photo'] = $photo;
            $art[$k]['user_comment'] = $comment -> where("post_table = 'levels' AND post_id = ".$v['id'])->count();
            $art[$k]['user_click'] = $click ->where("table_name = 'levels' AND pid = ".$v['id'])->count();
            $click_zan = $click ->where("table_name = 'levels' AND pid = ".$v['id'])->find();
            if($click_zan){
                $art[$k]['user_click_status'] = 1;
            }else{
                $art[$k]['user_click_status'] = 0;
            }
        }
        
        $data = array(
            'article' => $art,
            'comment' => $comments
        );
        return $data;exit;
    }
    /*
     * 点赞接口
     * $uid|点赞的用户ID,$aid|点赞的文章的ID,$tname|点赞的表的名字
     */
    public function click_Zambia($uid, $aid, $tname){
        if(empty($uid) || empty($aid) || empty($tname)){
            return COMMITERROR();exit;
        }
        $data['table_name'] = $tname;
        $data['pid'] = $aid;
        $data['uid'] = $uid;
        $click = M('click');
        $result = $click->add($data);
        if($result){
            return SONEOK($result);exit;
        }else{
            return SONEERROR();exit;
        }
    }
    /*
     * 颜值表点赞数
     * $aid|点赞的文章的ID
     */
    public function upLevelClick($aid){
            $level = M('levels');
            $where['id'] = $aid;
            $sel = $level -> where($where)->find();
            $data['level_like'] = $sel['level_like'] +1;
            $result = $level -> where($where)->save($data);
            if($result){
                return SONEOK($result);exit;
            }else {
                return SONEERROR();
                exit;
            }
    }
    /*
     * 个人文章
     */
    public function selPersonal($uid){
        $level = M('levels');
        $comment = M('comments');
        $photo = M('levels_photo');
        $return = $level -> where("level_author = ".$uid." AND level_status in (1,2)") -> order("level_date DESC")-> select();
        if(empty($return)){
            return SONEOK($return);exit;
        }
        foreach($return as $k=>$v){
            $where['post_table'] = 'levels';
            $where['post_id'] = $v['id'];
            $return[$k]['comment_count'] = $comment -> where($where)->count();
            $return[$k]['levels_photo'] = $photo -> where('lid = '.$v['id'])->select();
        }
        return SONEOK($return);
    }
    /*
     * 发布颜值圈
     */
    public function addArtShow($data, $files = ''){
        $levels = M('levels');
        $levels_photo = M('levels_photo');
        $return = $levels -> add($data);
        $photo['before_img'] = json_encode($files['before']);
        $photo['after_img'] = json_encode($files['after']);
        $photo['lid'] = $return;
        $photo_result = $levels_photo ->add($photo);
        if($return && $photo_result){
            return SONEOK($return);exit;
        }else{
            return SONEERROR();exit;
        }
    }
    /*
     * 发布颜值圈评论
     * 评论文章ID，发布评论的用户ID，被评论的用户ID,评论内容，
     */
    public function addComment($post_id, $user_id, $to_userid, $content){
        if(!empty($post_id) || !empty($user_id) || !empty($to_userid) || !empty($content)){
            return SONEERROR();exit;
        }
        //$member = M('member');
        $comment = M('comments');
        $data['post_table'] = 'levels';
        $data['post_id'] =$post_id;
        $data['uid'] = $user_id;
        $data['to_uid'] = $to_userid;
        $data['content'] = $content;
        $data['createtime'] = date('Y-m-d h:i:s', time());
        $result = $comment -> add($data);
        if($result){
            return SONEOK($result);exit;
        }else{
            return SONEERROR();exit;
        }
    }
    /*
     * 修改推送设置
     */
    public function saveSocket($uid,$status){
        $where['uid'] = $uid;
        $data['status'] = $status;
        $table = M('member');
        if($table->where($where)->save($data)){
            return SONEOK();
            exit;
        }else{
            return SONEERROR();exit;
        }
    }

}