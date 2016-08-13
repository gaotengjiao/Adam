<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 王鑫磊  2016-07-29  增加颜值圈Controller
 */
class ColorCircleController extends HomebaseController {
    /*
     * 颜值圈引导页
     * */
    public function KeyWord(){
        $PostsModel = D("Posts");
        $result = $PostsModel->WhatKeyWord();
        $this->ajaxReturn($result);
    }
    /*
     * 判断用户首次进入丽颜汇还是多次
     */
    public function user_open(){
        $uid = I("request.uid", '');
        $member = M("member")->where("uid = ".$uid)->field("user_status")->find();
        if($member){
            $return = SONEOK($member['user_status']);
        }else{
            $return = NOTFOUND();
        }
        $this->ajaxReturn($return);
    }

    /*
	 * 引导页-分类页
	 * */
    public function TypeHtml(){
        $GoodsTypeModel = D("GoodsType");
        $result = $GoodsTypeModel->SelTypeAll();
        $this->ajaxReturn($result);
    }

    /*
    * 颜值圈首页（推荐页）
    * uid          用户ID
    * token        用户本站唯一标识
    * keyword      关键词
    * type         分类
    * mac          mac地址
    * rand         临时用户随机名
    * */
    public function FaceRecommend(){
        $uid        = I("request.uid");
        $token      = I("request.token");
        $KeyWord    = I("request.keyword");
        $type       = I("request.type");
        $time       = I("request.time");
        $TermsModel = D("Posts");
        $Result = $TermsModel->QueryArticles($uid,$token,$KeyWord,$type,$time);
        $this->ajaxReturn($Result);exit;
    }
    /*
     * 推荐页的轮播图
     * keywords  关键词
     * type      分类
     */
    public function showPhoto(){
        $slide = D('Favorable');
        $return = $slide->pagePhoto();
        $this->ajaxReturn($return);
    }
    /*
     * 推荐页的问答显示
     */
    public function showProblem(){
        $problem = D('Favorable');
        $keywords = I('request.keyword', '');
        $type = I('request.type', '');
        $result = $problem -> selAnswer($keywords, $type);
        $this->ajaxReturn($result);
    }
    /*
     * 手术前后的对比
     */
    public function contrast(){
        $contrast = D('Favorable');
        $keywords = I('request.keyword', '');
        $type = I('request.type', '');
        $result = $contrast -> selContrast($keywords, $type);
        $this->ajaxReturn($result);
    }
    /*
     * 查询最近两个小时发布的文章数
     */
    public function newArticle(){
        $level = D('Favorable');
        $result = $level->selNew();
        $this->ajaxReturn($result);
    }
    /*
     * 优惠的活动（推荐和置顶的显示）
     */
    public function selDiscount(){
        $favorable = D('Favorable');
        $result = $favorable->showPhoto();
        $this->ajaxReturn($result);
    }

    /*
     * 美问答-搜索
     * */
    public function SearchBeautyAsk(){
        $GuestbookModel = D("Guestbook");
        $Result= $GuestbookModel->SelWhere();
        $this->ajaxReturn($Result);exit;
    }
    /*
     * 美容百科
     * */
    public function BeautyEncyclopedia(){
        $TermsModel = D("Posts");
        $where = I("request.where", '');
        $Result = $TermsModel->SelBeautyEncyclopedia($where);
        $this->ajaxReturn($Result);exit;
    }
    /*
     * 美问答
     * */
    public function BeautyAsk(){
        $GuestbookModel = D("Guestbook");
        $where = I("request.where");
        $Result= $GuestbookModel->SelBeautyAsk($where);
        $this->ajaxReturn($Result);exit;
    }

    /*
     * 查询文章的详细信息
     * */
    public function QueryDetails(){
        $Posts_id   = I("request.id");
        $Type       = I("request.type");
        $uid        = I("request.uid", '');
        if($Posts_id=="" | $Type=="" | $uid == ""){
            $Result = COMMITERROR();
        }else{
            if($Type==1){
                $GuestbookModel = D("Guestbook");
                $Result = $GuestbookModel->SelQueryDetails($Posts_id, $uid);
            }elseif($Type==2){
                $PostsModel = D("Posts");
                $Result = $PostsModel->SelQueryDetails($Posts_id);
            }else{
                $Result = COMMITERROR();
            }
        }
        $this->ajaxReturn($Result);exit;
    }
    /*
     * 美容百科点赞
     */
    public function click_beauty(){
        $uid = I("request.uid", '');
        $pid = I("request.pid", '');
        $status = I("request.status", '');
        $tname = "posts";
        $level = D("Levels");
        $result = $level ->click_Zambia($uid, $pid, $tname, $status);
        $this->ajaxReturn($result);
        }
    /*
     * 美问答 添加接口
     */
    public function addBeauty(){
        $uid = I("request.uid", '');
        $content = I("request.msg", '');
        $createtime = date("Y-m-d h:i:s", time());
        $photo = I("request.photo", '');
    }
}