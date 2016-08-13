<?php
/*
 * 商品管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GoodsController extends AdminbaseController {
    /* 
     * 列表
     */ 
    public function goods(){
        $post = I('post.');
        $goodsinfo = D('Goods');//实例化Model
        $goodsinfoo = $goodsinfo->goods($post);//调用Model方法
        foreach ($goodsinfoo[1] as $k => $v) {
            $goodsinfoo[1][$k]['content'] = strip_tags(htmlspecialchars_decode($v['content']));
            $goodsinfoo[1][$k]['price'] = number_format($v['price'], 2);
        }
        $this->assign('lists',$goodsinfoo['1']);
        $this->assign('page',$goodsinfoo['2']);
        $this->assign('arr',$goodsinfoo['3']); 
        $this->assign('test',$goodsinfoo['4']);
        $this->assign('wordkey',$goodsinfoo['5']);
        $this->assign('time1',$goodsinfoo['6']);
        $this->assign('time2',$goodsinfoo['7']);
        $this->assign('service', $goodsinfoo['8']);
        $this->display('index');
    } 
    /*
     * 添加
     */
    public function Goodsadd(){
        $GoodsModel = D("Goods");
        $lists = $GoodsModel->Seltype();
        $this->assign("type",$lists['cate']);
        $this->assign("face",$lists['face']);
        $this->assign("users", $lists['users']);
        $this->assign("service",$lists['service']);
        $this->display('add');
    }

    /*
 * 添加模板一
 */
    public function Goodsaddone(){
        $GoodsModel = D("Goods");
        $lists = $GoodsModel->Seltype();
        $this->assign("type",$lists['cate']);
        $this->assign("face",$lists['face']);
        $this->assign("doctor",$lists['doctor']);
        $this->assign("users", $lists['users']);
        $this->assign("service",$lists['service']);
        $this->display('addone');
    }

    /*
         添加产品
     */
    public function Goodsadd_post(){
        // dump(I('post.'));die;
        if(IS_POST){
            $post = I('post.');
            $Goods = D('Goods');
            $result = $Goods->GoodsAdd($post);
            $this->success();
        }
    }
    /*
     * 删除
     */
    public function Goodsdelete(){
        $date = I('get.');
        $DateAC = D('Goods');
        $result = $DateAC->DelDate($date);
        $this->success();
    }
    /*
     * 修改
     */
    public function Goodswedit(){

        $id = I('get.id');
        $Table = D("Goods");
        $lists = $Table->Editype($id);
        $this->assign("type",$lists['cate']); // 分类
        $this->assign("face",$lists['face']); // 活动
        $this->assign('test', $lists['a']);   // 详细信息
        $this->assign("lists", $lists['b']);  // 
        $this->assign("users", $lists['users']);
        $this->assign("service",$lists['service']);
        $this->assign('user', $lists['usersid']);
        $this->assign('aid', $lists['aid']);
        $this->display('edittwo');

    }

    public function Goodsedit(){
        if(IS_POST){
            $date = I("post.");
            $Goods = D("Goods");
            $result = $Goods->GoodsEdit($date);
            $this->success('修改成功', U('goods/goods'));
        }
    }

    /*
        修改产品2
     */
    public function Goodsedit2(){
        if(IS_POST){
            $date = I('post.'); 
            $goods = D('Goods');
            $res = $goods->GoodsEdit2($date);
            if($res){
                $this->success('修改成功', U('Goods/goods'));
            }else{
                $this->error();
            }
        }
    }
    /*
     * 查询
     */
    public function Goodsselect(){
        $date = I('post.');
        $Table = D("Goods");
        $result = $Table->GoodsSelect($date);
        $this->assign('lists',$result['1']);
        $this->assign('page',$result['2']);
        $this->display("quota");
    }
    //状态  上下架
    public function Datedac(){
        $date = I("get.");
        $DateAC = D("Goods");
        $result = $DateAC->editState($date);
        $this->success();
    }

    /*
        ajax传值，修改排序
     */
    public function Price(){
        $get['id'] = I('post.id');
        $get['gorder'] = I('post.value');
        $model = D('goods');
        $res = $model->SaveOrder($get);
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    // 修改上下架
    public function state(){
        $post = I('get.');

        $table = D('goods');

        $res = $table->editState($post);

        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        产品回收站
     */
    public function recycle(){
        $post = I('post.');
        $goodsinfo = D('Goods');//实例化Model
        $goodsinfoo = $goodsinfo->recycle($post);//调用Model方法
        foreach ($goodsinfoo[1] as $k => $v) {
            $goodsinfoo[1][$k]['content'] = strip_tags(htmlspecialchars_decode($v['content']));
            $goodsinfoo[1][$k]['price'] = number_format($v['price'], 2);
        }
        $this->assign('lists',$goodsinfoo['1']);
        $this->assign('page',$goodsinfoo['2']);
        $this->assign('arr',$goodsinfoo['3']); 
        $this->assign('test',$goodsinfoo['4']);
        $this->assign('wordkey',$goodsinfoo['5']);
        $this->assign('time1',$goodsinfoo['6']);
        $this->assign('time2',$goodsinfoo['7']);
        $this->assign('service', $goodsinfoo['8']);
        $this->display('recycle');
    }

    /*
        全选下架
     */
    public function outs(){
        $id = I('get.');
        $table = D('goods');
        $res = $table->outs($id);
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        全选上架
     */
    public function tests(){
        $id = I('get.');
        $table = D('goods');
        $res = $table->tests($id);
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        全选删除
     */
    public function DelDates(){
        $id = I('get.id');
        $table = D('goods');
        $resurt = $table->DelDates($id);
        if($resurt){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        点击查看详情
     */
    public function move(){
        $id = I('get.id');
        $table  = D('Goods');
        $result = $table->details($id);
        $this->assign('list', $result);
        $this->display("move");
    }

    /*
        下拉选择服务流程
     */
    public function serviceedit(){
        $id = I('get.id');
        $service['service'] = I('get.service');
        $res = M('goods')->where("id = $id")->save($service);
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        页面医师遍历
     */
    public function usersid(){
        $id = explode(',',I('get.value'));
        
        $len = count($id);
        for($i=0; $i<$len; $i++){
            $sid = $id[$i];
            $data[] = M('users')->field('user_login')->where("id = $sid")->find();
        }
        foreach ($data as $k => $v) {
            $a .= $v['user_login'].' | ';
        }
        print_r($a);
    }


    /*
        置顶
     */
    public function editstick(){
        $post = I('post.');
        $table  = D('Goods');
        $result = $table->editstick($post);
        if($result){
            echo '1';
        }else{
            echo '2';
        }
    }


    /*
        测试
     */
    public function ceshi(){
        echo '1';
    }
}