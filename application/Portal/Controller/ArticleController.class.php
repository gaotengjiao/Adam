<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
/**
 * 文章内页
 */
namespace Portal\Controller;
use Common\Controller\AdminbaseController;
class ArticleController extends AdminbaseController {
    //文章内页
    public function index() {
        $id=intval($_GET['id']);

        // $article=sp_sql_post($id);
        // "SELECT * FROM s_term_relationships a INNER JOIN s_posts as b on a.object_id =b.id INNER JOIN s_users as c on b.post_author = c.id WHERE `status` = 1 AND `tid` = 6 LIMIT 1" 

        $Model = M('term_relationships');
        $article = $Model->join('s_posts ON s_term_relationships.object_id = s_posts.id')->join('s_users ON s_posts.post_author = s_users.id')->where("tid = $id and s_term_relationships.status = 1")->find();
        // dump($article);die;

        if(empty($article)){
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            if(sp_template_file_exists(MODULE_NAME."/404")){
                $this->display(":404");
            }
            return;
        }
        // echo $id;die;
        $termid=$article['term_id'];
        $term_obj= M("Terms");
        $term=$term_obj->where("term_id='$termid'")->find();
        
        $article_id=$article['object_id'];
        
        $posts_model=M("Posts");
        $posts_model->save(array("id"=>$article_id,"post_hits"=>array("exp","post_hits+1")));
        
        $article_date=$article['post_modified'];
        
        $join = "".C('DB_PREFIX').'posts as b on a.object_id =b.id';
        $join2= "".C('DB_PREFIX').'users as c on b.post_author = c.id';
        $rs= M("TermRelationships");
    	$next=$rs->alias("a")->join($join)->join($join2)->where(array("b.post_modified"=>array("egt",$article_date), "a.tid"=>array('neq',$id), "a.status"=>1,'a.term_id'=>$termid))->order("post_modified asc")->find();
    	$prev=$rs->alias("a")->join($join)->join($join2)->where(array("b.post_modified"=>array("elt",$article_date), "a.tid"=>array('neq',$id), "a.status"=>1,'a.term_id'=>$termid))->order("post_modified desc")->find();
    	
        $this->assign("next",$next);
        $this->assign("prev",$prev);
        
        $smeta=json_decode($article['smeta'],true);
        $content_data=sp_content_page($article['post_content']);
        $article['post_content']=$content_data['content'];

        $wid = $article['id']; // 文章id 
        // 查询评论
        $table = M('comments');
        $res = $table->where("post_id = $id and status = 1")->select();
        $len = count($res);
        // 查询用户的id 与 头像
        for($i=0; $i<$len; $i++){
            $uid = $res[$i]['uid'];
            $dets = M('member')->where("uid = $uid")->field('nickname, user_img')->find();
            $res[$i]['nickname'] = $dets['nickname'];
            $res[$i]['user_img'] = $dets['user_img'];
        }
        $this->assign('first', $res);

        $this->assign("page",$content_data['page']);
        $this->assign('list',$article);
        $this->assign("smeta",$smeta);
        $this->assign("term",$term);
        $this->assign("article_id",$article_id);
        // dump($article);
        // $tplname=$term["one_tpl"];
        // $tplname = sp_get_apphome_tpl($tplname, "article");
        $this->display("index");
    	// $this->display(":$tplname");
    }
    
    public function do_like(){
    	$this->check_login();
    	
    	$id=intval($_GET['id']);//posts表中id
    	
    	$posts_model=M("Posts");
    	
    	$can_like=sp_check_user_action("posts$id",1);
    	
    	if($can_like){
    		$posts_model->save(array("id"=>$id,"post_like"=>array("exp","post_like+1")));
    		$this->success("赞好啦！");
    	}else{
    		$this->error("您已赞过啦！");
    	}
    	
    }
}
