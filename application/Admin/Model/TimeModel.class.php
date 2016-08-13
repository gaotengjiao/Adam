<?php
namespace Common\Model;
use Common\Model\CommonModel;
class TimeModel extends CommonModel {
    public function Time(){
        $Table = M("time");
        $count      = $Table->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $Table->where("id = 1")->limit($Page->firstRow.','.$Page->listRows)->select();
        $facearr = M("goods_type")->where("state = 1")->select();
        $arr = $this->sortOut($facearr);
        $Order['1'] = $list;
        $Order['2'] = $show;
        $Order['3'] = $arr;
        return $Order;
    }
    //显示move页面
    public function MoveType(){
            $facearr = M("time")->where("id")->select();
            $arr = $this->sortOut($facearr);
            return $arr;
	}
    //修改状态
    public function MoveEdit($date){
        $table  = M("date");
        $state = $date['state'];
        $id 	= $date['ids'];
        $Edata['state'] = "$state";
        $Edit = $table->where("id = $id")->save($Edata);
        return $Edit;
    }
    //无限极分类查询分类内容
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
}