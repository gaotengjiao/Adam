<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 颜值圈首页
 */
//header("Access-Control-Allow-Origin: *");
class LevelsController extends HomebaseController
{
    /*
     * 颜值圈
     */
    public function index(){
        $LevelsTable = D('Levels');
            $id = I("request.id", '');
            $result = $LevelsTable -> SelFaceQuery($id);
            $this->ajaxReturn($result);
    }
    /*
     * 点赞功能
     */
    public function clickGood(){
        $click = D('Levels');
        $uid = I("request.uid", '');
        $aid = I("request.aid", '');
        //$table_name = I("request.tname", '');
        $table_name = "levels";
        $click -> click_Zambia($uid, $aid, $table_name);
        $upd = $click -> upLevelClick($aid);
        $this->ajaxReturn($upd);
    }
    /*
     * 颜值圈文章详情页评论点赞
     */
    public function articleCommentClick(){
        $click = D('Levels');
        $uid = I("request.uid", '');
        $aid = I("request.aid", '');
        $table_name = "comments";
        $result = $click -> click_Zambia($uid, $aid, $table_name);
        $this->ajaxReturn($result);
    }
    /*
     * 发布文章，
     * 发布者的ID，关键词标签，发布的内容，上传的图片，
     */
    public function addArticle(){
        $levels = D('Levels');
        if(IS_POST){
            if(!empty($_POST)){
                $data['level_author'] = I('post.uid');
                $data['level_date'] = date("Y-m-d h:i:s", time());
                $keywords = I('post.keywords');
                $data['level_keywords'] = implode(',', $keywords);
                $data['level_content'] = I('post.content');
                $data['level_status'] = 0;
                if(!empty($_FILES)){
                    $files = $_FILES;
                }
                $result = $levels -> addArtShow($data, $files);
                $this->ajaxReturn($result);
            }
        }
    }
    /*
     * 发布评论
     *评论内容ID，发布评论的用户ID，被评论的用户ID,评论内容，
     */
    public function addComment(){
        $comment = D('Levels');
        if(IS_POST){
            $post_id = I('post.post_id', '');
            $user_id    = I('psot.uid', '');
            $to_userid  = I('post.to_uid', '');
            $content    = I('post.content', '');
            $result = $comment -> addComment($post_id, $user_id, $to_userid, $content);
            $this->ajaxReturn($result);
        }
    }
    /*
     * 点击头像查看个人文章
     */
    public function showPersonal(){
        $uid = I('uid', '');
        if(empty($uid)){
            $return = COMMITERROR();
            $this->ajaxReturn($return);
        }
        $me = D('Levels');
        $result = $me -> selPersonal($uid);
        $this->ajaxReturn($result);
    }
    /*
     * 点击头像查看美问答
     */
    public function showQuestion(){
        $uid = I('request.uid', '');
        if(empty($uid)){
            $return = COMMITERROR();
            $this->ajaxReturn($return);exit;
        }
        $guest = D('Guestbook');
        $result = $guest->SelBeautyAsk($uid);
        $this->ajaxReturn($result);
    }
    /*
     * 添加标签
     */
    public function addTag(){
        if(IS_POST){
            $Uid = I('post.uid', '');
            $KeyWords = I('post.keywords', '');
            $KeyWord = implode(',', $KeyWords);
            if(!empty($uid)){
                $result = AddUserLabel($Uid,$KeyWord);
                $this->ajaxReturn($result);
            }
        }
    }

}