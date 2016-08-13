<?php
namespace Common\Model;
use Common\Model\CommonModel;
class PostsModel extends CommonModel {
	/*
	 * 表结构
	 * id:post的自增id
	 * post_author:用户的id
	 * post_date:发布时间
	 * post_content
	 * post_title
	 * post_excerpt:发表内容的摘录
	 * post_status:发表的状态,可以有多个值,分别为publish->发布,delete->删除,...
	 * comment_status:
	 * post_password
	 * post_name
	 * post_modified:更新时间
	 * post_content_filtered
	 * post_parent:为父级的post_id,就是这个表里的ID,一般用于表示某个发表的自动保存，和相关媒体而设置
	 * post_type:可以为多个值,image->表示某个post的附件图片;audio->表示某个post的附件音频;video->表示某个post的附件视频;...
	 */
	//post_type,post_status注意变量定义格式;
	
	protected $_auto = array (
		array ('post_date', 'mGetDate', 1, 'callback' ), 	// 增加的时候调用回调函数
		//array ('post_modified', 'mGetDate', 2, 'callback' ) 
	);
	// 获取当前时间
	function mGetDate() {
		return date ( 'Y-m-d H:i:s' );
	}
	
	protected function _before_write(&$data) {
		parent::_before_write($data);
	}

	/*
	 * 引导页
	 * */
	public function WhatKeyWord(){
		$PostsTable  = M("posts");
		$Posts  = $PostsTable->field("post_keywords")->select();
		$PostsCount = count($Posts);
		for($i=0;$i<$PostsCount;$i++){
			$Data[$i] = explode(",",$Posts[$i]['post_keywords']);
			$DataCOunt = count($Data[$i]);
			for($j=0;$j<$DataCOunt;$j++){
				$date[$i][$j]['keyword'] = $Data[$i][$j];
			}
		}
		$arr2 = array();
		foreach ($date as $k => $v) {
			foreach ($v as $m => $n) {
				$arr2[] = $n;
			}
		}
		if($arr2==""){
			return NOTFOUND();exit;
		}else{
			return SONEOK($arr2);exit;
		}
	}

	/*
	 * 根据用户的喜好查询相关的文章
	 * uid          用户ID
     * token        用户本站唯一标识
     * keyword      关键词
     * type         分类
     * rand         临时用户随机名
	 * */
	public function QueryArticles($uid,$token,$KeyWord,$type,$time){
		if($uid=="" && $token=="" && $time==""){
			return COMMITERROR();exit;
		}
		if($time!=""){
			$AddResult = $this->AddUserLabel($time,$KeyWord,$type);
			$SelPostsResult = $this->SelPostsResult($KeyWord,$type);
			return SONEOK($SelPostsResult);exit;
		}elseif($uid!="" && $token!=""){
			$AddMemberResult = $this->SaveUserLabel($uid,$KeyWord,$type);
			$SelPostsResult = $this->SelPostsResult($KeyWord,$type);
			//print_r($SelPostsResult['type']);die;
			return SONEOK($SelPostsResult);exit;
		}else{
			return COMMITERROR();exit;
		}
	}
	/*
	 * 根据条件查询丽妍会-推荐
	 * */
	public function SelPostsResult($KeyWord,$type){
		$TypeResult = $this->SelTermsType();
		$PostsTable = M("posts");
		$keyword = explode(",",$KeyWord);
		$type = explode(",",$type);
		if($keyword!="" && $type!=""){
			$where = array_merge($keyword,$type);
		} elseif($keyword !="" | $type==""){
			$where = $keyword;
		} else{
			$where = $type;
		}
		$where = array_unique($where);
		$WhereCount = count($where);
		$wherew = "";
		for($w=0;$w<$WhereCount;$w++){
			$wherew.= "post_keywords like '%$where[$w]%' OR ";
		}
		$wherew = trim($wherew," OR ");
		$arr = $PostsTable->where("$wherew and post_status=1 and recommended = 1")->field("id,post_keywords,post_source,post_content,post_title,post_excerpt,post_modified,post_hits,post_like,istop,recommended")->select();
		$data['type'] = $TypeResult;
		$TermsPosts = $this->SelPostsTerms($arr);
		$data['data'] = $TermsPosts;
		return $data;EXIT;
	}
	/*
	 * 查询文章分类
	 * */
	public function SelPostsTerms($data){
		$DataCOunt = count($data);
		$TermRelationshipsTable = M("term_relationships");
		for($i=0;$i<$DataCOunt;$i++){
			$id = $data[$i]['id'];
			$typeid = $TermRelationshipsTable->where("object_id = $id")->field("object_id,term_id,status")->find();
			$data[$i]['typeid'] = $typeid['term_id'];
			$data[$i]['status'] = $typeid['status'];
			if($data[$i]['status'] == 0){
				unset($data[$i]);
			}
		}
		array_multisort($data,SORT_DESC);
		return $data;exit;
	}
	/*查询分类*/
	public function SelTermsType(){
		$TermsTable = M("terms");
		$PostsType = $TermsTable->where("status = 1")->field("term_id,name,parent")->select();
		$CarouslResultCount = count($PostsType);
		$SlideTable = M("slide");
		for($i=0;$i<$CarouslResultCount;$i++){
			$slideid = $PostsType[$i]['term_id'];
			$PostsType[$i]['slide'] = $SlideTable->where("slide_cid = $slideid and slide_status = 1")->field("slide_id,slide_name,slide_pic")->select();
		}
		return $PostsType;exit;
	}
	/*
	 * 更新用户喜欢的便签盒分类
	 * */
	public function SaveUserLabel($uid,$KeyWord,$type){
		$MemberTable = M("member");
		$data['label'] = $KeyWord;
		$data['type']  = $type;
		return $MemberTable->where("uid = $uid")->save($data);
	}
	/*
	 * 临时用户添加临时标签和分类
	 * */
	public function AddUserLabel($time,$KeyWord,$type){
		$TemporaryUserLabelTable = M("temporary_user_label");
		$data['user']   = $time;
		$data['label']  = $KeyWord;
		$data['type']   = $type;
		return $TemporaryUserLabelTable->add($data);exit;
	}

	/*
	 * 查询美容百科内容
	 * */
	public function SelBeautyEncyclopedia($where){
		$whereSearch = "";
		if($where == ""){
			$whereSearch.="post_status = 1";
		}else{
			$whereSearch.="post_status = 1 and post_keywords like '%$where%'";
		}
		$PostsTable  = M("posts");
		$Posts  = $PostsTable->where("$whereSearch")->field("id,post_title,post_excerpt,post_modified,post_hits,istop,recommended,smeta")->select();
		$PostsCount = count($Posts);
		for($i=0;$i<$PostsCount;$i++){
			$img = $Posts[$i]['smeta'];
			$img = json_decode($img);
			$Posts[$i]['thumb'] = "data/upload/".$img->thumb;
			unset($Posts[$i]['smeta']);
		}
		foreach($Posts as $k=>$v){
			$Posts[$k]['post_modified'] = TIMESHOW($v['post_modified']);
		}
		if($Posts==""){
			return NOTFOUND();exit;
		}else{
			return SONEOK($Posts);exit;
		}
	}

	/*
	 * 查询项目详情
	 * */
	public function SelQueryDetails($Posts_id){
		$PostsTable  = M("posts");
		$result = $PostsTable->where("id = $Posts_id and post_status = 1")->field("post_author,post_keywords,post_modified,post_source,post_content,post_title,post_excerpt,post_hits,post_like")->find();
		$result['post_modified'] = TIMESHOW($result['post_modified']);
		if($result==""){
			return NOTFOUND();exit;
		}else{
			return SONEOK($result);exit;
		}
	}

}