<?php
/*
     优惠
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class FavorableController extends AdminbaseController {
     /*
          优惠页
      */
     public function index(){
          $table = M('favorable');
          $time = time();
          // 自动执行的方法
          $dema = $table->where('status = 0')->field('id, end_time')->select();
          for ($i=0; $i < count($dema); $i++) { 
               $time1 = $dema[$i]['end_time'];
               $id    = $dema[$i]['id'];
               $time2 = strtotime($time1);
               if($time2 < $time){
                    $table->where("id = $id")->save(array('status' => 2));
               }
          }
          $where = "status = 0";
          $post = I('post.');
          if(!empty($post)){
               $red = $this->search($post);
               $where = $red[1];
               if(!empty($where)){
                    $where .= " and status = 0";
               }else{
                    $where = 'status = 0';
               }
          }
          
          $count = $table->where($where)->count();
          $page = new \Think\Page($count, 10);
          $show = $page->show();
          $res = $table->where($where)->limit($page->firstRow.','.$page->listRows)->select();
          foreach ($res as $k => $v) {
               $res[$k]['istop'] = $v['istop'] == 0 ? '<span>不置顶</span> <a href="javascript:void(0)" class="istop" data="1" date='.$v['id'].'>置顶</a>' : '置顶 <a href="javascript:void(0)" class="istop" data="0" date='.$v['id'].'>取消置顶</a>';
               $res[$k]['status'] = '<a href="javascript:void(0)" class="deleteh" data='.$v['id'].'>删除</a>'; 
               $res[$k]['isrecommend'] = $v['isrecommend'] == 0 ? '<span>不推荐</span> <a href="javascript:void(0)" class="isrecommend" date='.$v['id'].'>推荐</a>' : '推荐 <a href="javascript:void(0)" class="isrecommend" date='.$v['id'].'>取消推荐</a>';
          }
          $this->assign('guestmsgs', $res);
          $this->assign('time1', $red[2]);
          $this->assign('time2', $red[3]);
          $this->assign('name', $red[4]);
          $this->assign('show', $show);
          $this->display();
     }

     /*
          添加页
      */
     public function add(){
          $this->display();
     }

     /*
          执行添加操作
      */
     public function add_post(){
          if (IS_POST) {
               $data['img'] = '/data/upload/'.I('post.img');
               $post= I('post.post');
               $data['title'] = $post['title'];
               $data['start_time'] = $post['start_time'];
               $data['end_time']   = $post['end_time'];
               $data['istop']      = $post['istop'];
               $data['content']    = htmlspecialchars_decode($post['content']);
               $table = M('favorable');
               $result= $table->add($data);
               if ($result) {
                    $this->success("添加成功！");
               } else {
                    $this->error("添加失败！");
               }
          }
     }

     /*
          修改置顶
      */
     public function istop(){
          // print_r(I('post.'));die;
          $id = I('post.id');
          $demo = I('post.istop');

          if($demo == 1 || $demo == 0){
               $data['istop'] = $demo;
               $res = M('favorable')->where("id = $id")->save($data);
          }
          if($demo == 3){
               $dat['isrecommend'] = 1;
               $res = M('favorable')->where("id = $id")->save($dat);
          }
          if($demo == 4){
               $dat['isrecommend'] = 0;
               $res = M('favorable')->where("id = $id")->save($dat);
          }
          if($res){
               echo '1';
          }
     }

     /*
          过期优惠
      */
     public function overdue(){
          $where = "status = 2";
          $post = I('post.');
          if(!empty($post)){
               $red = $this->search($post);
               $where = $red[1];
               if(!empty($where)){
                    $where .= " and status = 2";
               }else{
                    $where = 'status = 2';
               }
          }
          $table = M('favorable');
          $count = $table->where($where)->count();
          $page = new \Think\Page($count, 10);
          $show = $page->show();
          $res = $table->where($where)->limit($page->firstRow.','.$page->listRows)->select();
          foreach ($res as $k => $v) {
               $res[$k]['istop'] = $v['istop'] == 0 ? '<span>不置顶</span> <a href="javascript:void(0)" class="istop" data="1" date='.$v['id'].'>置顶</a>' : '置顶 <a href="javascript:void(0)" class="istop" data="0" date='.$v['id'].'>取消置顶</a>';
               $res[$k]['status'] = '<a href="javascript:void(0)" class="deleteh" data='.$v['id'].'>删除</a>';

               $res[$k]['isrecommend'] = $v['isrecommend'] == 0 ? '<span>不推荐</span> <a href="javascript:void(0)" class="istop" data="1" date='.$v['id'].'>推荐</a>' : '推荐 <a href="javascript:void(0)" class="istop" data="0" date='.$v['id'].'>取消推荐</a>';
          }
          $this->assign('guestmsgs', $res);
          $this->assign('time1', $red[2]);
          $this->assign('time2', $red[3]);
          $this->assign('name', $red[4]);
          $this->assign('show', $show);
          $this->display();
     }

     /*
          全选设置数据
      */
     public function check(){
          $check = I('get.check');
          $id = explode(',',I('get.id'));

          $len = count($id);
          switch ($check) {
               // 置顶
               case '1':
                    $data['istop'] = 1;
                    for ($i=0; $i < $len; $i++) { 
                         $pid = $id[$i];
                         $res = M('favorable')->where("id = $pid")->save($data);
                    }
                    header('Location:'.U('Favorable/index'));
               break;
               // 取消置顶
               case '2':
                    $data['istop'] = 0;
                    for ($i=0; $i < $len; $i++) { 
                         $pid = $id[$i];     
                         $res = M('favorable')->where("id = $pid")->save($data);
                    } 
                    header('Location:'.U('Favorable/index'));
               break;
               // 删除
               case '3':
                    $data['status'] = 1;
                    for ($i=0; $i < $len; $i++) { 
                         $pid = $id[$i];     
                         $res = M('favorable')->where("id = $pid")->save($data);
                    }
                    header('Location:'.U('Favorable/index'));
               break;
               // 推荐
               case '4':
                    $data['isrecommend'] = 1;
                    for ($i=0; $i < $len; $i++) { 
                         $pid = $id[$i];     
                         $res = M('favorable')->where("id = $pid")->save($data);
                    }
                    header('Location:'.U('Favorable/index'));
               break;
               // 取消推荐
               case '5':
                    $data['isrecommend'] = 0;
                    for ($i=0; $i < $len; $i++) { 
                         $pid = $id[$i];     
                         $res = M('favorable')->where("id = $pid")->save($data);
                    }
                    header('Location:'.U('Favorable/index'));
               break;
          }
     }

     /*
          删除
      */
     public function deletes(){
          $id = I('post.id');
          $res = M('favorable')->where("id = $id")->save(array('status' => 1));
          if($res){
               echo '1';
          }
     }

     /*
          搜索模块
      */
     public function search($post){
          $title = $post['name'];
          $time1 = $post['start_time'];
          $time2 = $post['end_time'];

          if($title){
               $where = "title = '$title'";
          }
          if($time1){
               $where = "start_time > '$time1'";
          }
          if($time2){
               $where = "end_time < '$time2'";
          }
          if($title && $time1){
               $where = "title = '$title' and start_time > '$time1'";
          }
          if($title && $time2){
               $where = "title = '$title' and end_time < '$time2'";
          }
          if($time1 && $time2){
               $where = "start_time > '$time1' and end_time < '$time2'";
          }

          if($title && $time1 && $time2){
               $where = "title = '$title' and start_time > '$time1' and end_time < '$time2'";
          }
          $red[1] = $where;
          $red[2] = $time1;
          $red[3] = $time2;
          $red[4] = $title;
          return $red;
     }    
}