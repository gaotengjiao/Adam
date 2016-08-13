<?php
/*
 * 颜值圈
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LevelsController extends AdminbaseController 
{
	protected $posts_model;
	protected $term_relationships_model;
	protected $terms_model;

	function _initialize() {
		parent::_initialize();
		$this->posts_model = D("Portal/Posts");
		$this->terms_model = D("Portal/Terms");
		$this->term_relationships_model = D("Portal/TermRelationships");
	}
	/*
	 * 颜值圈展示
	 * 搜索关键字 | level_keywords
	 * 标题       | level_title
	 * 审核状态   | level_status
	 * 评论状态   | comment_status
	 * 点击量     | level_hits
	 * 点赞数     | level_like
	 * 推荐       | recommended
	 */
	public function index()
	{
		$table = D('Levels');
		$result = $table -> show_score();
		$this->assign('show', $result);
		$this->display();
	}
	/*
	 * 添加颜值圈内容
	 * 标题| 内容 | 手术前照片 | 手术后照片 | 评论状态 | 是否推荐
	 */
	public function add(){
		$this->display();
	}
	/*
	 * 添加数据入库
	 */
	public function add_post(){
		if(IS_POST){
			//print_R($_POST);die;
			$data['level_keywords'] = I("post.levels_label", '');
			$data['recommended'] = I("post.recommended", '');
			$data['level_content'] = I("post.levels_content", '');
			if(empty($_POST['levels_label']) || empty($_POST['levels_content'])){
				$this->error('请填写内容');
			}
			$before = $_POST['img_url'][0];
			$after  = $_POST['img1_url'][0];
			if(empty($before) || empty($after)){
				$this->error("请上传对比图");
				exit;
			}
			$table = D('Levels');
			$add = $table -> add_con($data);
			if(!empty($before) || !empty($after)){
				$upload = $table -> uploads($before,$after,$add);
				if($upload <= 0){
					$this->error("添加失败");exit;
				}
			}
			if($add > 0){
					$this-> success('添加成功');
				}else{
					$this->error('添加失败');
			}

		}
	}

	//选中
	function check(){
		$table = M('levels');
		if(isset($_POST['ids']) && $_GET["check"]){
			$data["level_status"]=1;

			$tids=join(",",$_POST['ids']);
			$objectids=$table->where("id in ($tids)")->select();
			$ids=array();
			foreach ($objectids as $id){
				$ids[]=$id["id"];
			}
			$ids=join(",", $ids);
			if ( $table->where("id in ($ids)")->save($data)!==false) {
				$this->success("审核成功！");
			} else {
				$this->error("审核失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["uncheck"]){

			$data["level_status"]=0;
			$tids=join(",",$_POST['ids']);

			$objectids= $table->where("id in ($tids)")->field("id")->select();
			$ids=array();
			foreach ($objectids as $id){
				$ids[]=$id["id"];
			}
			$ids=join(",", $ids);
			if ( $table->where("id in ($ids)")->save($data)) {
				$this->success("取消审核成功！");
			} else {
				$this->error("取消审核失败！");
			}
		}
	}

	function recommend(){
		$table = M('levels');
		if(isset($_POST['ids']) && $_GET["recommend"]){
			$data["recommended"]=1;

			$tids=join(",",$_POST['ids']);
			$objectids=$table->field("id")->where("id in ($tids)")->select();
			$ids=array();
			foreach ($objectids as $id){
				$ids[]=$id["id"];
			}
			$ids=join(",", $ids);
			if ( $table->where("id in ($ids)")->save($data)!==false) {
				$this->success("推荐成功！");
			} else {
				$this->error("推荐失败！");
			}
		}
		if(isset($_POST['ids']) && $_GET["unrecommend"]){

			$data["recommended"]=0;
			$tids=join(",",$_POST['ids']);
			$objectids=$table->field("id")->where("id in ($tids)")->select();
			$ids=array();
			foreach ($objectids as $id){
				$ids[]=$id["id"];
			}
			$ids=join(",", $ids);
			if ( $table->where("id in ($ids)")->save($data)) {
				$this->success("取消推荐成功！");
			} else {
				$this->error("取消推荐失败！");
			}
		}
	}
}