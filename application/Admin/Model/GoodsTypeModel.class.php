<?php
namespace Common\Model;
use Common\Model\CommonModel;
class GoodsTypeModel extends CommonModel {
  /*
    分类查询
  */
  public function MoveType(){
    $Pren = M('goods_type')->select();
    foreach ($Pren as $k => $v) {
      $Pren[$k]['level'] = $v['pid'];
    }
    $sta = $this->sortOut($Pren);

    return $sta;
  } 
 
  /*
    无限极分类查询分类内容
  */
  static public function sortOut($cate,$pid=0,$level=0,$html='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){
        $tree = array();
        foreach($cate as $v){
            if($v['pid'] == $pid){
              $v['pid'] = $level + 1;
                $v['html'] = str_repeat($html, $level);
                $tree[] = $v;
                $tree = array_merge($tree, self::sortOut($cate,$v['id'],$level+1,$html));
               }
            }
        return $tree; 
    } 

    /*
      禁用分类
    */
   public function edits($data){

      // $id['id'] = implode(',', $data['id']);
      $id['id'] = $data;

      $edit = M('goods_type');

      $status['state'] = '99';
      $res = $edit->where($id)->save($status);

      return $res;
   }

   // AJAX 修改启用，禁用
   public function editState($post){

        $id = $post['id'];  // 接收的id  【如果是一个2】

        $state['state'] = $post['state']; // 接收要改的权限

        if($state['state'] == 1){ // 如果开启则关闭，关闭则开启
          $state['state'] = 0;
        }else{
          $state['state'] = 1;
        }

        $demo = M('goods_type');  // 实例化分类表

        // 第一次查询下面有没有子类 【查询所有数据等于pid=2】
        $Pren = $demo->where("pid = $id")->field("id")->select();
          
        $len = count($Pren);
       
        if($len == 0){
            // 如果等于零，则没有子类，直接修改现在的数据
            $res = $demo->where("id = $id")->save($state);
        }else{
            for($i = 0; $i < $len; $i++){
              $ids = $Pren[$i]['id'];
              
              $res[1] = $demo->where("id = $ids")->save($state);
              $res[2] = $demo->where("pid = $ids")->save($state);
              $res[3] = $demo->where("id = $id")->save($state);
            }
        }
        
        return $res;
   }


   /*
        执行验证账号操作
    */
   public function editname($post){
      // 实例化
      $table = M('goods_type');

      // 要添加的数据
      $typename = $post['typename'];

      // 查询有没有存在
      $demo = $table->where("typename = '$typename'")->field('id')->select();
      $len = count($demo);
      
      if($len == 0){
          // 如果有则返回一个3
          $res = 0;
      }else{
          $res = 1;
      }

      
      return $res;
   }

   /*
        执行添加操作
    */
   public function addpost($arr){

        $post['typename'] = $arr['typename'];
        $post['pid']      = $arr['parentid'];
        $post['state']    = $arr['status'];
        // 实例化分类
        $table = M('goods_type');
        // 执行添加
        $res = $table->add($post);
        // 返回值
        return $res;
   }


   /*
        修改分类的Model
    */
   public function edit_type($post){
        $id['id'] = $post['id'];

        $data['typename'] = $post['typename'];

        $data['pid'] = $post['parentid'];

        $data['state'] = $post['status'];

        $table = M('goods_type');

        $res = $table->where($id)->save($data);

        return $res;
   }

   /*
      修改分类名信息
    */
   public function editnametype($post){
        $id   = $post['id'];
        $name['typename'] = $post['typename'];  
        $table = M('goods_type');
        $table->where("id = $id")->save($name);
        return $table;
   }

   /*
     全选删除
    */
   public function DelDates($id){
      $len = count($id);
      $table = M('goods_type')->select();
      $result = $this->sortOut($table);
      foreach($result as $k=>$v){
        for ($i=0; $i < $len; $i++) { 
          if($v['id'] == $id[$i]){
            $arr[] = $v;
           } 
        }
      }
      print_r($arr);
      for ($j=0; $j < $len; $j++) { 
        if($arr[$j]['html'] != '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){
            $res .= 0;
        }else{
            $res .= 1;
        }
      }
      // if($res)
   }

   /*
      批量移动分类
    */
   public function typemove($get){
      $pid['pid'] = $get['value'];
      $id = $get['id'];
      $id = explode(',', $id);
      $len = count($id);
      for($i=0; $i<$len; $i++){
        $res = M('goods_type')->where("id = $id[$i]")->save($pid);
      }
      return $res;
   }
}